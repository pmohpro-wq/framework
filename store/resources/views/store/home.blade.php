@extends('layouts.store')

@section('title', 'فروشگاه من — خرید آنلاین')

@section('content')
    <div class="container">
        <section class="hero">
            <div class="hero-inner">
                <div class="hero-copy">
                    <span class="hero-eyebrow">✦ تجربه خرید آنلاین ایرانی</span>
                    <h1>خرید آسان، تحویل <em>سریع</em></h1>
                    <p>
                        هزاران کالای اورجینال با بهترین قیمت؛ ارسال سریع به سراسر کشور
                        و پرداخت امن از طریق درگاه‌های بانکی.
                    </p>
                    <div class="hero-actions">
                        <a href="#products" class="hero-cta">
                            شروع خرید
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m11 18-6-6 6-6"/></svg>
                        </a>
                        <a href="/admin" class="hero-ghost">پنل مدیریت</a>
                    </div>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat"><b>+۵۰</b><span>برند معتبر</span></div>
                    <div class="hero-stat"><b>۲۴/۷</b><span>پشتیبانی</span></div>
                    <div class="hero-stat"><b>۹۹٪</b><span>رضایت خرید</span></div>
                </div>
            </div>
        </section>

        <div class="cat-chips">
            @foreach(($headerTaxons ?? collect()) as $i => $taxon)
                <a class="cat-chip" style="--chip: {{ ['#ef4056', '#0fa36b', '#2d7fb8', '#f5a623'][$i % 4] }}"
                   href="{{ route('category.show', ['taxonomySlug' => $taxon->taxonomy->slug ?? 'main', 'taxonSlug' => $taxon->slug]) }}">
                    <i></i>{{ $taxon->name }}
                </a>
            @endforeach
        </div>

        <section class="section" id="products">
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
