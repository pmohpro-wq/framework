@extends('layouts.store')

@section('title', 'پیگیری سفارش — فروشگاه من')

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <span>پیگیری سفارش</span>
        </div>

        <div class="page page-narrow">
            <h1>پیگیری سفارش</h1>
            <p class="muted">شمارهٔ سفارش خود را وارد کنید تا وضعیت فعلی و تاریخچهٔ آن را ببینید.</p>

            <form action="{{ route('track.order') }}" method="post" style="display:flex;gap:10px;max-width:520px;margin:20px 0 28px">
                @csrf
                <input type="text" name="order_number" value="{{ old('order_number', $searchedFor) }}"
                       class="form-control" placeholder="مثال: ۱۲۳۴۵ یا 12345" dir="ltr" required>
                <button type="submit" class="btn btn-primary" style="white-space:nowrap">پیگیری</button>
            </form>

            @if($searchedFor && !$order)
                <div class="empty-state">
                    <h2>سفارشی با این شماره پیدا نشد</h2>
                    <p class="muted">
                        شمارهٔ سفارش را از ایمیل تأیید خرید یا صفحهٔ حساب کاربری خود بردارید.
                        دقت کنید که «O» و «0» با هم اشتباه نشوند.
                    </p>
                    <a href="{{ route('pages.contact') }}" class="btn btn-secondary" style="margin-top:14px">تماس با پشتیبانی</a>
                </div>
            @endif

            @if($order)
                <div class="track-result">
                    <div class="track-head">
                        <div>
                            <div class="muted" style="font-size:13px">شمارهٔ سفارش</div>
                            <div class="order-number" dir="ltr">{{ $order->number }}</div>
                        </div>
                        <span class="status-badge status-{{ $order->status->value() ?? 'pending' }}">
                            {{ [
                                'pending'   => 'در انتظار پرداخت',
                                'on_hold'   => 'در حال بررسی',
                                'completed' => 'تکمیل شده',
                                'cancelled' => 'لغو شده',
                            ][$order->status->value() ?? 'pending'] ?? 'در حال پردازش' }}
                        </span>
                    </div>

                    <h3 style="margin:22px 0 12px">مراحل سفارش</h3>
                    @php($steps = [
                        ['ثبت سفارش', true],
                        ['پرداخت', in_array($order->status->value() ?? '', ['on_hold', 'completed'])],
                        ['آماده‌سازی و ارسال', in_array($order->status->value() ?? '', ['on_hold', 'completed'])],
                        ['تحویل', ($order->status->value() ?? '') === 'completed'],
                    ])
                    <div class="track-steps">
                        @foreach($steps as $i => [$label, $done])
                            <div class="track-step {{ $done ? 'done' : '' }}">
                                <div class="dot">{{ \App\Support\Persian::digits($i + 1) }}</div>
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>

                    <h3 style="margin:26px 0 12px">اقلام سفارش</h3>
                    <table class="order-table">
                        <thead>
                            <tr><th>کالا</th><th>تعداد</th><th>مبلغ</th></tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ \App\Support\Persian::digits($item->qty) }}</td>
                                    <td>{{ \App\Support\Persian::toman($item->total) }}</td>
                                </tr>
                            @endforeach
                            <tr class="order-total">
                                <td colspan="2">جمع کل</td>
                                <td>{{ \App\Support\Persian::toman($order->total()) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="track-meta">
                        @if($order->billpayer)
                            <div><span class="muted">خریدار:</span> {{ $order->billpayer->getFullName() }}</div>
                        @endif
                        @if($order->shippingAddress)
                            <div><span class="muted">تحویل در:</span> {{ $order->shippingAddress->city }}، {{ $order->shippingAddress->address }}</div>
                        @endif
                        @if($order->ordered_at)
                            <div><span class="muted">تاریخ ثبت:</span> {{ $order->ordered_at->translatedFormat('Y/m/d') }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
