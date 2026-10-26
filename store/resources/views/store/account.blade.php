@extends('layouts.store')

@section('title', 'حساب کاربری — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', ['items' => [['label' => 'حساب کاربری']]])

        <div class="account-layout">
            @include('partials.account_side', ['user' => $user, 'active' => 'orders'])

            {{-- محتوا --}}
            <div class="account-content">
                <h2 class="content-title">سفارش‌های من</h2>

                @if($orders->isEmpty())
                    <div class="empty-state">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8.5v9a2 2 0 0 1-1.2 1.8l-7 3a2 2 0 0 1-1.6 0l-7-3A2 2 0 0 1 3 17.5v-9a2 2 0 0 1 1.2-1.8l7-3a2 2 0 0 1 1.6 0l7 3A2 2 0 0 1 21 8.5z"/><path d="m3.5 7.5 8.5 3.5 8.5-3.5"/><path d="M12 21.5V11"/></svg>
                        </div>
                        <h3>هنوز سفارشی ثبت نکرده‌اید</h3>
                        <p class="muted">اولین خرید خود را انجام دهید؛ پیشنهادهای ویژه در صفحهٔ اصلی منتظر شماست.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">شروع خرید</a>
                    </div>
                @else
                    <div class="order-list">
                        @foreach($orders as $order)
                            @php($ps = \App\Support\OrderPresenter::paymentStatus($order))
                            <a href="{{ route('account.orders.show', ['number' => $order->number]) }}" class="order-card">
                                <div class="order-card-head">
                                    <span class="order-number" dir="ltr">{{ $order->number }}</span>
                                    @include('partials.status_badge', ['order' => $order, 'paymentStatus' => $ps])
                                </div>
                                <div class="order-card-body">
                                    <span class="muted">{{ \App\Support\Persian::digits($order->items->count()) }} قلم کالا</span>
                                    @if($order->ordered_at)
                                        <span class="muted">{{ $order->ordered_at->translatedFormat('Y/m/d') }}</span>
                                    @endif
                                    <span class="order-card-total">{{ \App\Support\Persian::toman($order->total()) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
