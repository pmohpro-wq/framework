<?php

namespace App\Support;

use Vanilo\Order\Models\OrderStatus;
use Vanilo\Payment\Models\PaymentProxy;

/**
 * نمایش یکدست وضعیت سفارش/پرداخت در ویترین (پیگیری سفارش، حساب کاربری)
 *
 * منطق: وضعیت سفارش (OrderStatus وانیلو) و وضعیت پرداخت (Payment) دو چیز جدایند.
 * این کلاس هر دو را کنار هم تفسیر می‌کند تا مشتری همیشه برچسب درست را ببیند؛
 * حتی برای سفارش‌های قدیمی که قبل از افزودن انتقال خودکار، پرداخت شده بودند.
 */
class OrderPresenter
{
    /** آخرین وضعیت پرداخت سفارش (paid/timeout/declined/...) یا null */
    public static function paymentStatus($order): ?string
    {
        if (! $order) {
            return null;
        }

        $payment = PaymentProxy::where('payable_id', $order->id)
            ->where('payable_type', 'order')
            ->orderByDesc('id')
            ->first();

        return $payment?->status?->value();
    }

    /** برچسب فارسی وضعیت برای نمایش به مشتری */
    public static function statusLabel(?string $status, ?string $paymentStatus = null): string
    {
        // سفارش ثبت شده و پرداخت هم وصل شده ولی هنوز توسط ادمین پردازش نشده
        if ($status === OrderStatus::PENDING && $paymentStatus === 'paid') {
            return 'پرداخت شده — در حال آماده‌سازی';
        }

        return match ($status) {
            OrderStatus::PENDING    => 'در انتظار پرداخت',
            OrderStatus::PROCESSING => 'در حال آماده‌سازی',
            OrderStatus::COMPLETED  => 'تحویل شده',
            OrderStatus::CANCELLED  => 'لغو شده',
            OrderStatus::WITHDRAWN  => 'مرجوع شده',
            default                 => 'در حال پردازش',
        };
    }

    /** کلاس CSS نشان وضعیت */
    public static function statusClass(?string $status, ?string $paymentStatus = null): string
    {
        if ($status === OrderStatus::PENDING && $paymentStatus === 'paid') {
            return 'status-processing';
        }

        return 'status-' . ($status ?? 'pending');
    }

    /** مراحل تایم‌لاین پیگیری سفارش: [برچسب، انجام‌شده؟] */
    public static function trackingSteps(?string $status, ?string $paymentStatus): array
    {
        $paid       = $paymentStatus === 'paid';
        $processing = in_array($status, [OrderStatus::PROCESSING, OrderStatus::COMPLETED], true)
            || ($paid && $status === OrderStatus::PENDING);

        return [
            ['ثبت سفارش', true],
            ['پرداخت', $paid],
            ['آماده‌سازی و ارسال', $processing],
            ['تحویل', $status === OrderStatus::COMPLETED],
        ];
    }
}
