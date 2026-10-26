@php($img = $product->getImageUrl('thumbnail'))
@php($hasDiscount = isset($product->original_price) && $product->original_price && $product->original_price > $product->price)
@php($discountPercent = $hasDiscount ? round(100 - ($product->price / $product->original_price) * 100) : 0)

<div class="product-card">
    <a href="{{ route('product.show', $product->slug) }}" class="card-img">
        @if($img)
            <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <img src="{{ asset('images/placeholder.svg') }}" alt="{{ $product->name }}" loading="lazy">
        @endif
    </a>
    <div class="card-body">
        <a href="{{ route('product.show', $product->slug) }}" class="card-title">{{ $product->name }}</a>
        <div class="card-footer">
            @if($hasDiscount)
                <span class="discount-badge">{{ \App\Support\Persian::digits($discountPercent) }}٪</span>
            @endif
            <div class="price-box">
                @if($hasDiscount)
                    <span class="price-old">{{ \App\Support\Persian::toman($product->original_price, false) }}</span>
                @endif
                <div class="price-current">
                    {{ \App\Support\Persian::toman($product->price, false) }}
                    <span class="unit">تومان</span>
                </div>
            </div>
        </div>
    </div>
</div>
