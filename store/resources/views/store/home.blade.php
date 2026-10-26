@extends('layouts.store')

@section('title', 'فروشگاه من — خرید آنلاین')

@section('content')
    <div class="container">
        <section class="hero">
            <div>
                <h1>خرید آسان، تحویل سریع</h1>
                <p>
                    هزاران کالای اورجینال با بهترین قیمت؛ ارسال سریع به سراسر کشور
                    و پرداخت امن از طریق درگاه‌های بانکی.
                </p>
                <a href="{{ route('home') }}" class="hero-cta">شروع خرید ←</a>
            </div>
            <div class="hero-badge">🛍️</div>
        </section>

        <section class="section">
            <div class="section-head">
                <h2>محصولات جدید</h2>
                <a href="{{ route('home') }}" class="see-all">مشاهده همه ←</a>
            </div>

            @if($products->isEmpty())
                <div class="page empty-state">
                    <div class="icon">📦</div>
                    <p>هنوز محصولی برای نمایش وجود ندارد.<br>از پنل مدیریت (/admin) محصولات را اضافه کنید.</p>
                </div>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        @include('partials.product_card', ['product' => $product])
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
