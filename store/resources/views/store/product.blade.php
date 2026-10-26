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

                <div class="price-row">
                    @php($hasDiscount = isset($product->original_price) && $product->original_price && $product->original_price > $product->price)
                    @if($hasDiscount)
                        <span class="discount-badge">
                            {{ \App\Support\Persian::digits(round(100 - ($product->price / $product->original_price) * 100)) }}٪ تخفیف
                        </span>
                    @endif
                    <div>
                        @if($hasDiscount)
                            <span class="price-old">{{ \App\Support\Persian::toman($product->original_price) }}</span>
                        @endif
                        <div class="price-current">
                            {{ \App\Support\Persian::toman($product->price) }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('cart.add') }}" method="post" style="display:flex;gap:12px;align-items:center;margin-top:18px">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="qty-input">
                        <button type="button" onclick="this.nextElementSibling.stepUp()">+</button>
                        <input type="number" name="quantity" value="1" min="1" max="99">
                        <button type="button" onclick="this.previousElementSibling.stepDown()">−</button>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">افزودن به سبد خرید 🛒</button>
                </form>

                <div class="description">{{ $product->description }}</div>

                <div class="muted" style="font-size:13px">
                    ✅ موجود در انبار &nbsp;|&nbsp; 🚚 ارسال سریع &nbsp;|&nbsp; 🔒 پرداخت امن
                </div>
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
