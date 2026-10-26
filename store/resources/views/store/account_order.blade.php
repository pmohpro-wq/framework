@extends('layouts.store')

@section('title', 'سفارش ' . $order->number . ' — حساب کاربری')

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <a href="{{ route('account.dashboard') }}">حساب کاربری</a>
            <span>/</span>
            <span>{{ $order->number }}</span>
        </div>

        <div class="page page-narrow">
            <div class="track-head" style="margin-bottom:20px">
                <div>
                    <div class="muted" style="font-size:13px">سفارش</div>
                    <div class="order-number" dir="ltr">{{ $order->number }}</div>
                </div>
                <span class="status-badge {{ \App\Support\OrderPresenter::statusClass($order->status?->value(), $paymentStatus) }}">
                    {{ \App\Support\OrderPresenter::statusLabel($order->status?->value(), $paymentStatus) }}
                </span>
            </div>

            <table class="order-table">
                <thead>
                    <tr><th>کالا</th><th>تعداد</th><th>مبلغ واحد</th><th>مبلغ کل</th></tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ \App\Support\Persian::digits($item->qty) }}</td>
                            <td>{{ \App\Support\Persian::toman($item->price) }}</td>
                            <td>{{ \App\Support\Persian::toman($item->total) }}</td>
                        </tr>
                    @endforeach
                    <tr class="order-total">
                        <td colspan="3">جمع کل (شامل هزینهٔ ارسال و تخفیف‌ها)</td>
                        <td>{{ \App\Support\Persian::toman($order->total()) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="track-meta">
                @if($order->billpayer)
                    <div><span class="muted">خریدار:</span> {{ $order->billpayer->getFullName() }}</div>
                    <div><span class="muted">ایمیل:</span> <span dir="ltr">{{ $order->billpayer->getEmail() }}</span></div>
                @endif
                @if($order->shippingAddress)
                    <div><span class="muted">تحویل در:</span> {{ $order->shippingAddress->city }}، {{ $order->shippingAddress->address }}</div>
                @endif
                @if($order->ordered_at)
                    <div><span class="muted">تاریخ ثبت:</span> {{ $order->ordered_at->translatedFormat('Y/m/d H:i') }}</div>
                @endif
                @if($order->notes)
                    <div><span class="muted">توضیحات:</span> {{ $order->notes }}</div>
                @endif
            </div>

            <div class="page-cta">
                <a href="{{ route('account.dashboard') }}" class="btn btn-secondary">بازگشت به سفارش‌ها</a>
            </div>
        </div>
    </div>
@endsection
