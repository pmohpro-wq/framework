@extends('layouts.store')

@section('title', $product->name . ' — فروشگاه من')

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <span>{{ $product->name }}</span>
        </div>

        <div class="page product-page">
            <div class="gallery">
                <img src="{{ $product->getImageUrl('medium') ?? asset('images/placeholder.svg') }}"
                     alt="{{ $product->name }}">
            </div>

            <div class="info">
                <h1>{{ $product->name }}</h1>
                <div class="sku">کد کالا: {{ \App\Support\Persian::digits($product->sku) }}</div>

                @if($product->excerpt)
                    <p class="muted">{{ $product->excerpt }}</p>
                @endif

                <div class="buy-box">
                    @php($hasDiscount = isset($product->original_price) && $product->original_price && $product->original_price > $product->price)
                    <div class="price-row">
                        <div>
                            @if($hasDiscount)
                                <span class="discount-badge" style="position:static;display:inline-block;margin-bottom:6px">
                                    {{ \App\Support\Persian::digits(round(100 - ($product->price / $product->original_price) * 100)) }}٪ تخفیف
                                </span>
                            @endif
                            <div class="price-current">
                                {{ \App\Support\Persian::toman($product->price) }}
                            </div>
                            @if($hasDiscount)
                                <span class="price-old">{{ \App\Support\Persian::toman($product->original_price) }}</span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('cart.add') }}" method="post" style="display:flex;gap:12px;align-items:center;margin-top:18px;flex-wrap:wrap">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="qty-input">
                            <button type="button" onclick="this.nextElementSibling.stepUp()">+</button>
                            <input type="number" name="quantity" value="1" min="1" max="99">
                            <button type="button" onclick="this.previousElementSibling.stepDown()">−</button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg" style="flex:1;min-width:200px">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="18" cy="21" r="1.6"/><path d="M2.5 3h2l2.4 12.2a2 2 0 0 0 2 1.8h8.6a2 2 0 0 0 2-1.6L21 7H6"/></svg>
                            افزودن به سبد خرید
                        </button>
                    </form>
                </div>

                <div class="trust-chips">
                    <span class="trust-chip">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        موجود در انبار
                    </span>
                    <span class="trust-chip">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 18H3c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h10c.6 0 1 .4 1 1v11"/><path d="M14 9h4l4 4v4c0 .6-.4 1-1 1h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>
                        ارسال سریع
                    </span>
                    <span class="trust-chip">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        پرداخت امن
                    </span>
                    <span class="trust-chip">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M3 4v5h5"/><path d="M12 7v5l3 3"/></svg>
                        ۷ روز ضمانت بازگشت
                    </span>
                </div>

                <div class="description">{{ $product->description }}</div>
            </div>
        </div>

        @if($related->isNotEmpty())
            <section class="section">
                <div class="section-head"><h2>محصولات مشابه</h2></div>
                <div class="product-grid">
                    @foreach($related as $item)
                        @include('partials.product_card', ['product' => $item])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
