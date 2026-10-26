<?php

namespace App\Services;

use Vanilo\Order\Models\OrderStatus;
use Vanilo\Payment\Contracts\PaymentResponse;
use Vanilo\Payment\Models\Payment;
use Vanilo\Payment\Processing\PaymentResponseHandler;

/**
 * نهایی‌سازی یک پرداخت پس از بازگشت از درگاه:
 *   ثبت در تاریخچهٔ پرداخت → به‌روزرسانی وضعیت پرداخت → fire event ها →
 *   انتقال سفارشِ در انتظار به «در حال آماده‌سازی» (processing).
 *
 * همهٔ درگاه‌ها (شبیه‌ساز، زرین‌پال و درگاه‌های آینده) از همین مسیر
 * نهایی می‌شوند تا رفتار سفارش‌ها یکدست بماند.
 */
class PaymentFinalizer
{
    public function finalize(Payment $payment, PaymentResponse $response): void
    {
        $handler = new PaymentResponseHandler($payment, $response);
        $handler->writeResponseToHistory();
        $handler->updatePayment();
        $handler->fireEvents();

        if ($response->wasSuccessful()) {
            $this->markProcessingIfPending($payment->payable);
        }
    }

    // ------------------------------------------------------------------

    /**
     * پس از پرداخت موفق، سفارش از «در انتظار پرداخت» به «در حال آماده‌سازی»
     * (processing) منتقل می‌شود تا پیگیری سفارش و پنل ادمین وضعیت درست را نشان دهند.
     */
    private function markProcessingIfPending($order): void
    {
        if (! $order) {
            return;
        }

        if (($order->status?->value() ?? OrderStatus::PENDING) === OrderStatus::PENDING) {
            $order->status = OrderStatus::PROCESSING();
            $order->save();
        }
    }
}
