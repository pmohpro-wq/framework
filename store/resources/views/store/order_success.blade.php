@extends('layouts.store')

@section('title', 'نتیجه سفارش — فروشگاه من')

@section('content')
    <div class="container">
        <div class="page success-hero">
            <div class="check-circle">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <h1>سفارش شما ثبت شد!</h1>
            <p class="muted">از اعتماد شما سپاسگزاریم. جزئیات سفارش در ادامه آمده است.</p>

            <div class="order-meta">
                <div class="row">
                    <span>شماره سفارش</span>
                    <span class="value" dir="ltr">{{ $order->number }}</span>
                </div>
                <div class="row">
                    <span>وضعیت سفارش</span>
                    <span class="value">{{ $order->status->label() ?? 'در حال بررسی' }}</span>
                </div>
                <div class="row">
                    <span>مبلغ کل</span>
                    <span class="value">{{ \App\Support\Persian::toman($order->total()) }}</span>
                </div>
            </div>

            <div class="success-actions">
                <a href="{{ route('track.show') }}" class="btn btn-primary">پیگیری سفارش</a>
                <a href="{{ route('home') }}" class="btn btn-secondary">بازگشت به فروشگاه</a>
            </div>

            <p class="note muted">
                شمارهٔ سفارش را یادداشت کنید؛ ایمیل تأیید نیز برای شما ارسال شد.
            </p>
        </div>
    </div>
@endsection
