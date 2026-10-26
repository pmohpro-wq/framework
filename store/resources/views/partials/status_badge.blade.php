{{-- نشان وضعیت سفارش — پارامتر: order (و اختیاری paymentStatus برای جلوگیری از کوئری تکراری) --}}
@php($badgePaymentStatus = $paymentStatus ?? \App\Support\OrderPresenter::paymentStatus($order))
<span class="status-badge {{ \App\Support\OrderPresenter::statusClass($order->status?->value(), $badgePaymentStatus) }}">
    {{ \App\Support\OrderPresenter::statusLabel($order->status?->value(), $badgePaymentStatus) }}
</span>
