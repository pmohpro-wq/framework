@extends('layouts.store')

@section('title', 'حساب کاربری — فروشگاه من')

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <span>حساب کاربری</span>
        </div>

        <div class="account-layout">
            {{-- منوی کناری --}}
            <aside class="account-side">
                <div class="account-user">
                    <div class="avatar">{{ mb_substr($user->name, 0, 1) }}</div>
                    <div>
                        <div style="font-weight:600">{{ $user->name }}</div>
                        <div class="muted" style="font-size:12px" dir="ltr">{{ $user->email }}</div>
                    </div>
                </div>
                <nav class="account-nav">
                    <a href="{{ route('account.dashboard') }}" class="active">سفارش‌های من</a>
                    <a href="{{ route('account.profile') }}">اطلاعات حساب</a>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="account-logout">خروج از حساب</button>
                    </form>
                </nav>
            </aside>

            {{-- محتوا --}}
            <div class="account-content">
                <h2 style="margin-bottom:16px">سفارش‌های من</h2>

                @if($orders->isEmpty())
                    <div class="empty-state">
                        <h3>هنوز سفارشی ثبت نکرده‌اید</h3>
                        <p class="muted">اولین خرید خود را انجام دهید؛ پیشنهادهای ویژه در صفحهٔ اصلی منتظر شماست.</p>
                        <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:14px">شروع خرید</a>
                    </div>
                @else
                    <div class="order-list">
                        @foreach($orders as $order)
                            <a href="{{ route('account.orders.show', ['number' => $order->number]) }}" class="order-card">
                                <div class="order-card-head">
                                    <span class="order-number" dir="ltr">{{ $order->number }}</span>
                                    <span class="status-badge status-{{ $order->status->value() ?? 'pending' }}">
                                        {{ [
                                            'pending'   => 'در انتظار پرداخت',
                                            'on_hold'   => 'در حال بررسی',
                                            'completed' => 'تکمیل شده',
                                            'cancelled' => 'لغو شده',
                                        ][$order->status->value() ?? 'pending'] ?? 'در حال پردازش' }}
                                    </span>
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
