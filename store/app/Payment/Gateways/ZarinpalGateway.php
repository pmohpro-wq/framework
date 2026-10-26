<?php

declare(strict_types=1);

namespace App\Payment\Gateways;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Vanilo\Contracts\Address;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\TransactionHandler;
use Vanilo\Payment\Models\PaymentStatus;

/**
 * درگاه پرداخت زرین‌پال (v4 REST)
 *
 * پیکربندی در .env:
 *   ZARINPAL_MERCHANT_ID=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
 *
 * - اگر MERCHANT_ID تنظیم نشده باشد، به‌صورت خودکار به شبیه‌ساز داخلی
 *   برمی‌گردد تا فروشگاه در محیط آزمایشی هم کامل کار کند.
 * - مبالغ فروشگاه به تومان است و به زرین‌پال با واحد IRT (تومان) ارسال می‌شود.
 */
class ZarinpalGateway implements PaymentGateway
{
    public const ID = 'zarinpal';

    private const API_REQUEST  = 'https://api.zarinpal.com/pg/v4/payment/request.json';
    private const API_VERIFY   = 'https://api.zarinpal.com/pg/v4/payment/verify.json';
    private const START_PAY    = 'https://www.zarinpal.com/pg/StartPay/';

    public static function getName(): string
    {
        return __('درگاه پرداخت زرین‌پال');
    }

    public static function svgIcon(): string
    {
        return '<svg viewBox="0 0 576 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H80c-8.8 0-16-7.2-16-16s7.2-16 16-16H496c8.8 0 16-7.2 16-16s-7.2-16-16-16H64zM448 336a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/></svg>';
    }

    public static function isConfigured(): bool
    {
        return (bool) config('services.zarinpal.merchant_id');
    }

    public function createPaymentRequest(
        Payment $payment,
        ?Address $shippingAddress = null,
        array $options = []
    ): PaymentRequest {
        $authority = null;
        $redirectUrl = route('payment.iranian.pay', ['paymentHash' => $payment->getPaymentId()]);

        if (static::isConfigured()) {
            try {
                $response = Http::timeout(20)->post(self::API_REQUEST, [
                    'merchant_id'  => config('services.zarinpal.merchant_id'),
                    'amount'       => (int) round($payment->amount), // تومان (IRT)
                    'currency'     => 'IRT',
                    'callback_url' => route('payment.zarinpal.callback', ['paymentHash' => $payment->getPaymentId()]),
                    'description'  => 'پرداخت سفارش فروشگاه من — ' . $payment->getPayable()->getNumber(),
                ])->json();

                $authority = $response['data']['authority'] ?? null;

                if (($response['data']['code'] ?? 0) === 100 && $authority) {
                    $redirectUrl = self::START_PAY . $authority;
                } else {
                    Log::error('Zarinpal request failed', ['response' => $response]);
                    // fallback به شبیه‌ساز تا کاربر در مسیر پرداخت گیر نکند
                }
            } catch (\Throwable $e) {
                Log::error('Zarinpal request exception', ['error' => $e->getMessage()]);
            }
        }

        return new IranianPaymentRequest($payment, ['authority' => $authority, 'redirect_url' => $redirectUrl]);
    }

    /**
     * بازگشت از زرین‌پال: authority + Status=OK|NOK
     * در صورت موفقیت، پرداخت verify می‌شود.
     */
    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        $ok        = strtoupper((string) $request->input('Status', '')) === 'OK';
        $authority = (string) $request->input('Authority', '');
        $verified  = false;
        $refId     = null;
        $amountPaid = null;

        if ($ok && $authority && static::isConfigured() && isset($options['payment'])) {
            /** @var Payment $payment */
            $payment = $options['payment'];

            try {
                $response = Http::timeout(20)->post(self::API_VERIFY, [
                    'merchant_id' => config('services.zarinpal.merchant_id'),
                    'amount'      => (int) round($payment->amount),
                    'currency'    => 'IRT',
                    'authority'   => $authority,
                ])->json();

                $code  = $response['data']['code'] ?? 0;
                $refId = $response['data']['ref_id'] ?? null;

                // 100 = تأیید موفق، 101 = قبلاً تأیید شده
                if (in_array($code, [100, 101], true)) {
                    $verified   = true;
                    $amountPaid = (float) ($response['data']['amount'] ?? $payment->amount);
                } else {
                    Log::error('Zarinpal verify failed', ['response' => $response]);
                }
            } catch (\Throwable $e) {
                Log::error('Zarinpal verify exception', ['error' => $e->getMessage()]);
            }
        }

        $successful = $verified || ($ok && !static::isConfigured()); // در حالت دمو، OK کافی است

        return new ZarinpalPaymentResponse(
            successful: $successful,
            status: $successful ? PaymentStatus::PAID() : PaymentStatus::DECLINED(),
            transactionId: $refId ?? $authority,
            amountPaid: $amountPaid,
            transactionAmount: (float) $options['amount'],
            paymentId: (string) ($options['payment_id'] ?? ''),
        );
    }

    public function transactionHandler(): ?TransactionHandler
    {
        return null;
    }

    public function isOffline(): bool
    {
        return false;
    }
}

/**
 * پاسخ استاندارد درگاه زرین‌پال برای PaymentResponseHandler وانیلو
 */
final class ZarinpalPaymentResponse implements PaymentResponse
{
    public function __construct(
        protected bool $successful,
        protected PaymentStatus $status,
        protected ?string $transactionId,
        protected ?float $amountPaid,
        protected float $transactionAmount,
        protected string $paymentId,
    ) {
    }

    public function wasSuccessful(): bool { return $this->successful; }
    public function getMessage(): ?string
    {
        return $this->successful ? 'پرداخت با موفقیت انجام شد' : 'پرداخت ناموفق بود یا لغو شد';
    }
    public function getTransactionId(): ?string { return $this->transactionId; }
    public function getAmountPaid(): ?float { return $this->amountPaid; }
    public function getTransactionAmount(): float { return $this->transactionAmount; }
    public function getPaymentId(): string { return $this->paymentId; }
    public function getStatus(): PaymentStatus { return $this->status; }
    public function getNativeStatus(): \Konekt\Enum\Enum { return $this->status; }
}
