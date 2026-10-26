<?php

declare(strict_types=1);

namespace App\Payment\Gateways;

use Illuminate\Http\Request;
use Vanilo\Contracts\Address;
use Vanilo\Payment\Contracts\Payment;
use Vanilo\Payment\Contracts\PaymentGateway;
use Vanilo\Payment\Contracts\PaymentRequest;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Contracts\TransactionHandler;

/**
 * اسکلت درگاه پرداخت ایرانی
 *
 * این درگاه یک صفحه شبیه‌ساز داخلی دارد (/payment/iranian/pay/{paymentId})
 * تا چرخه کامل پرداخت (درخواست → بانک → بازگشت → تأیید) قابل تست باشد.
 *
 * برای اتصال واقعی (مثلاً زرین‌پال):
 *  1. کلیدهای پذیرنده را در .env قرار دهید:
 *     IRANIAN_GATEWAY_MERCHANT_ID=xxxx
 *     IRANIAN_GATEWAY_URL=https://payment.zarinpal.com/pg/v4/payment/request.json
 *  2. در متد createPaymentRequest به‌جای آدرس شبیه‌ساز، درخواست به سرویس بانک ارسال کنید
 *     و authority را به‌عنوان remote_id ذخیره کنید.
 *  3. در processPaymentResponse پاسخ بانک را راستی‌آزمایی (verify) کنید.
 */
class IranianGateway implements PaymentGateway
{
    public const ID = 'iranian';

    public static function getName(): string
    {
        return __('درگاه پرداخت آنلاین');
    }

    public static function svgIcon(): string
    {
        return '<svg viewBox="0 0 576 512"><path d="M64 32C28.7 32 0 60.7 0 96V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V192c0-35.3-28.7-64-64-64H80c-8.8 0-16-7.2-16-16s7.2-16 16-16H496c8.8 0 16-7.2 16-16s-7.2-16-16-16H64zM448 336a32 32 0 1 0 0-64 32 32 0 1 0 0 64z"/></svg>';
    }

    public function createPaymentRequest(
        Payment $payment,
        ?Address $shippingAddress = null,
        array $options = []
    ): PaymentRequest {
        return new IranianPaymentRequest($payment, $options);
    }

    public function processPaymentResponse(Request $request, array $options = []): PaymentResponse
    {
        return new IranianPaymentResponse($request);
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
