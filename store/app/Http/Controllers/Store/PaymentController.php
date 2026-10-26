<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Payment\Gateways\IranianPaymentResponse;
use App\Payment\Gateways\ZarinpalGateway;
use App\Services\PaymentFinalizer;
use Illuminate\Http\Request;
use Vanilo\Payment\Models\PaymentProxy;

/**
 * کنترلر پرداخت: صفحهٔ شبیه‌ساز درگاه ایرانی + بازگشت از درگاه‌ها + موفقیت.
 *
 * منطق نهایی‌سازی (تاریخچه/وضعیت/event/انتقال سفارش) در PaymentFinalizer
 * است؛ این کنترلر فقط پاسخ درگاه را می‌سازد و کاربر را هدایت می‌کند.
 */
class PaymentController extends Controller
{
    public function __construct(private readonly PaymentFinalizer $finalizer)
    {
    }

    /** صفحهٔ شبیه‌ساز درگاه (نقش «صفحهٔ بانک» را بازی می‌کند) */
    public function iranianPay(string $paymentHash)
    {
        $payment = PaymentProxy::where('hash', $paymentHash)->firstOrFail();

        return view('store.pay_simulator', ['payment' => $payment]);
    }

    /** بازگشت از درگاه ایرانی (شبیه‌ساز) */
    public function iranianCallback(Request $request)
    {
        $request->validate([
            'payment' => ['required', 'string'],
            'status'  => ['required', 'in:success,failed'],
        ]);

        $payment = PaymentProxy::where('hash', $request->input('payment'))->firstOrFail();

        $response = new IranianPaymentResponse($request->merge([
            'amount' => $payment->amount,
        ]));

        $this->finalizer->finalize($payment, $response);

        return $this->redirectAfterPayment($payment, $response->wasSuccessful());
    }

    /** بازگشت از زرین‌پال (v4): Authority + Status=OK|NOK */
    public function zarinpalCallback(string $paymentHash, Request $request)
    {
        $payment = PaymentProxy::where('hash', $paymentHash)->firstOrFail();

        $response = app(ZarinpalGateway::class)->processPaymentResponse($request, [
            'payment'    => $payment,
            'payment_id' => $payment->getPaymentId(),
            'amount'     => $payment->amount,
        ]);

        $this->finalizer->finalize($payment, $response);

        return $this->redirectAfterPayment($payment, $response->wasSuccessful());
    }

    // ------------------------------------------------------------------

    private function redirectAfterPayment($payment, bool $successful)
    {
        $order = $payment->payable;

        return redirect()
            ->route('checkout.success', ['orderNumber' => $order->number])
            ->with(
                $successful ? 'success' : 'error',
                $successful
                    ? 'پرداخت با موفقیت انجام شد. سفارش شما در حال آماده‌سازی است.'
                    : 'پرداخت ناموفق بود یا لغو شد. می‌توانید مجدداً تلاش کنید.'
            );
    }
}
