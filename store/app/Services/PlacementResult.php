<?php

namespace App\Services;

use Vanilo\Order\Contracts\Order;
use Vanilo\Payment\Models\Payment;
use Vanilo\Payment\Models\PaymentMethod;

/**
 * نتیجهٔ ثبت موفق یک سفارش؛ کنترلر بر اساس آن به درگاه پرداخت
 * یا صفحهٔ موفقیت هدایت می‌کند.
 */
final class PlacementResult
{
    public function __construct(
        public readonly Order $order,
        public readonly PaymentMethod $paymentMethod,
        public readonly Payment $payment,
    ) {
    }
}
