@extends('layouts.store')

@section('title', 'نتیجه سفارش — فروشگاه من')

@section('content')
    <div class="container">
        <div class="page success-hero">
            <div class="check-circle">✓</div>
            <h1>سفارش شما ثبت شد!</h1>
            <p class="muted">از اعتماد شما سپاسگزاریم. جزئیات سفارش در ادامه آمده است.</p>

            <div class="order-meta">
                <div class="item">
                    <div class="label">شماره سفارش</div>
                    <div class="value" dir="ltr">{{ $order->number }}</div>
                </div>
                <div class="item">
                    <div class="label">وضعیت سفارش</div>
                    <div class="value">{{ $order->status->label() ?? 'در حال بررسی' }}</div>
                </div>
                <div class="item">
                    <div class="label">مبلغ کل</div>
                    <div class="value">{{ \App\Support\Persian::toman($order->total()) }}</div>
                </div>
            </div>

            <div>
                <a href="{{ route('home') }}" class="btn btn-primary">بازگشت به فروشگاه</a>
            </div>
        </div>
    </div>
@endsection
