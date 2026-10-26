@extends('layouts.store')

@section('title', 'پیگیری سفارش — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', ['items' => [['label' => 'پیگیری سفارش']]])

        <div class="page page-narrow">
            <h1>پیگیری سفارش</h1>
            <p class="muted">شمارهٔ سفارش خود را وارد کنید تا وضعیت فعلی و تاریخچهٔ آن را ببینید.</p>

            <form action="{{ route('track.order') }}" method="post" class="inline-form">
                @csrf
                <input type="text" name="order_number" value="{{ old('order_number', $searchedFor) }}"
                       class="form-control" placeholder="مثال: ۱۲۳۴۵ یا 12345" dir="ltr" required>
                <button type="submit" class="btn btn-primary">پیگیری</button>
            </form>

            @if($searchedFor && !$order)
                <div class="empty-state">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/><path d="M8.5 11h5"/></svg>
                    </div>
                    <h2>سفارشی با این شماره پیدا نشد</h2>
                    <p class="muted">
                        شمارهٔ سفارش را از ایمیل تأیید خرید یا صفحهٔ حساب کاربری خود بردارید.
                        دقت کنید که «O» و «0» با هم اشتباه نشوند.
                    </p>
                    <a href="{{ route('pages.contact') }}" class="btn btn-secondary">تماس با پشتیبانی</a>
                </div>
            @endif

            @if($order)
                <div class="track-result">
                    @include('partials.order_status_head', ['order' => $order])

                    <h3 class="subsection-title">مراحل سفارش</h3>
                    @php($steps = \App\Support\OrderPresenter::trackingSteps($order->status?->value(), $paymentStatus))
                    <div class="track-steps">
                        @foreach($steps as $i => [$label, $done])
                            <div class="track-step {{ $done ? 'done' : '' }}">
                                <div class="dot">{{ \App\Support\Persian::digits($i + 1) }}</div>
                                <span>{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>

                    <h3 class="subsection-title">اقلام سفارش</h3>
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
