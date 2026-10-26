<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Payment\Gateways\IranianPaymentResponse;
use Illuminate\Http\Request;
use Vanilo\Payment\Models\PaymentProxy;
use Vanilo\Payment\Processing\PaymentResponseHandler;

/**
 * کنترلر پرداخت: صفحه شبیه‌ساز درگاه ایرانی + بازگشت از پرداخت + صفحه موفقیت
 */
class PaymentController extends Controller
{
    /** صفحه شبیه‌ساز درگاه (نقش «صفحه بانک» را بازی می‌کند) */
    public function iranianPay(string $paymentHash)
    {
        $payment = PaymentProxy::where('hash', $paymentHash)->firstOrFail();

        return view('store.pay_simulator', ['payment' => $payment]);
    }

    /** بازگشت از درگاه (callback) */
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

        $handler = new PaymentResponseHandler($payment, $response);
        $handler->writeResponseToHistory();
        $handler->updatePayment();
        $handler->fireEvents();

        if (! $response->wasSuccessful()) {
            $order = $payment->payable;

            return redirect()
                ->route('checkout.success', ['orderNumber' => $order->number])
                ->with('error', 'پرداخت ناموفق بود. می‌توانید مجدداً تلاش کنید.');
        }

        $order = $payment->payable;

        return redirect()
            ->route('checkout.success', ['orderNumber' => $order->number])
            ->with('success', 'پرداخت با موفقیت انجام شد. سفارش شما در حال آماده‌سازی است.');
    }

    /** بازگشت از زرین‌پال (v4): Authority + Status=OK|NOK */
    public function zarinpalCallback(string $paymentHash, Request $request)
    {
        $payment = PaymentProxy::where('hash', $paymentHash)->firstOrFail();

        $gateway = app(\App\Payment\Gateways\ZarinpalGateway::class);

        $response = $gateway->processPaymentResponse($request, [
            'payment'     => $payment,
            'payment_id'  => $payment->getPaymentId(),
            'amount'      => $payment->amount,
        ]);

        $handler = new PaymentResponseHandler($payment, $response);
        $handler->writeResponseToHistory();
        $handler->updatePayment();
        $handler->fireEvents();

        $order = $payment->payable;

        if (! $response->wasSuccessful()) {
            return redirect()
                ->route('checkout.success', ['orderNumber' => $order->number])
                ->with('error', 'پرداخت ناموفق بود یا لغو شد. می‌توانید مجدداً تلاش کنید.');
        }

        return redirect()
            ->route('checkout.success', ['orderNumber' => $order->number])
            ->with('success', 'پرداخت با موفقیت انجام شد. سفارش شما در حال آماده‌سازی است.');
    }
}
