{{-- سربرگ شمارهٔ سفارش + نشان وضعیت (پیگیری / جزئیات حساب) — پارامتر: order، class اختیاری --}}
<div class="track-head {{ $class ?? '' }}">
    <div>
        <div class="muted">شمارهٔ سفارش</div>
        <div class="order-number" dir="ltr">{{ $order->number }}</div>
    </div>
    @include('partials.status_badge', ['order' => $order, 'paymentStatus' => $paymentStatus ?? null])
</div>
