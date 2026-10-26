@extends('layouts.store')

@section('title', 'سفارش ' . $order->number . ' — حساب کاربری')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', [
            'items' => [
                ['label' => 'حساب کاربری', 'url' => route('account.dashboard')],
                ['label' => $order->number],
            ],
        ])

        <div class="page page-narrow">
            @include('partials.order_status_head', ['order' => $order, 'class' => 'track-head--page'])

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
