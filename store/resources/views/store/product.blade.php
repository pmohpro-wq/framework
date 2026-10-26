@extends('layouts.store')

@section('title', $product->name . ' — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', ['items' => [['label' => $product->name]]])

        <div class="page product-page">
            <div class="gallery">
                <img src="{{ $product->getImageUrl('medium') ?? '/images/placeholder.svg' }}"
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
                    @php($stock = (int) ($product->stock ?? 0))
                    @php($avgRating = \App\Models\ProductReview::averageFor($product->id))
                    @php($reviewCount = \App\Models\ProductReview::where('product_id', $product->id)->where('is_approved', true)->count())
                    <div class="price-row">
                        <div>
                            @if($hasDiscount)
                                <span class="discount-badge discount-badge--inline">
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

                    @if($reviewCount > 0)
                        <div class="rating-row">
                            <span class="stars" dir="ltr">{{ str_repeat('★', (int) round($avgRating)) }}{{ str_repeat('☆', 5 - (int) round($avgRating)) }}</span>
                            <span class="muted">{{ \App\Support\Persian::digits($avgRating) }} از ۵ ({{ \App\Support\Persian::digits($reviewCount) }} نظر)</span>
                        </div>
                    @endif

                    @if($stock > 0)
                    <form action="{{ route('cart.add') }}" method="post" class="buy-actions">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="qty-input">
                            <button type="button" onclick="this.nextElementSibling.stepUp()">+</button>
                            <input type="number" name="quantity" value="1" min="1" max="{{ min($stock, 99) }}">
                            <button type="button" onclick="this.previousElementSibling.stepDown()">−</button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="18" cy="21" r="1.6"/><path d="M2.5 3h2l2.4 12.2a2 2 0 0 0 2 1.8h8.6a2 2 0 0 0 2-1.6L21 7H6"/></svg>
                            افزودن به سبد خرید
                        </button>
                    </form>
                    @else
                    <div class="out-of-stock">
                        <button type="button" class="btn btn-secondary btn-lg btn-block" disabled>
                            این کالا فعلاً ناموجود است
                        </button>
                    </div>
                    @endif
                </div>

                <div class="trust-chips">
                    <span class="trust-chip">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                        @if($stock > 0)
                            موجود در انبار{{ $stock <= 5 ? ' (' . \App\Support\Persian::digits($stock) . ' عدد)' : '' }}
                        @else
                            ناموجود
                        @endif
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

        {{-- نظرات کاربران --}}
        <section class="section" id="reviews">
            <div class="section-head">
                <h2>نظرات کاربران
                    @if($reviewCount > 0)
                        <span class="section-note">({{ \App\Support\Persian::digits($reviewCount) }} نظر)</span>
                    @endif
                </h2>
            </div>

            @if(($reviews ?? collect())->isNotEmpty())
                <div class="reviews-grid">
                    @foreach($reviews as $review)
                        <div class="review-card">
                            <div class="review-head">
                                <span class="review-author">{{ $review->name }}</span>
                                <span class="stars" dir="ltr">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                            </div>
                            <p class="review-body">{{ $review->comment }}</p>
                            <div class="review-date muted">{{ $review->created_at->translatedFormat('Y/m/d') }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="muted">هنوز نظری برای این کالا ثبت نشده است. اولین نفر باشید!</p>
            @endif

            <div class="checkout-box review-form-box">
                <h3>ثبت نظر جدید</h3>
                <form action="{{ route('review.store', ['slug' => $product->slug]) }}" method="post">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label>نام شما <span class="required">*</span></label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" class="form-control" required>
                            @error('name')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>امتیاز <span class="required">*</span></label>
                            <select name="rating" class="form-control" required>
                                <option value="5">۵ — عالی</option>
                                <option value="4">۴ — خوب</option>
                                <option value="3">۳ — متوسط</option>
                                <option value="2">۲ — ضعیف</option>
                                <option value="1">۱ — بد</option>
                            </select>
                            @error('rating')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group full">
                            <label>ایمیل (اختیاری — منتشر نمی‌شود)</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" class="form-control" dir="ltr">
                            @error('email')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group full">
                            <label>متن نظر <span class="required">*</span></label>
                            <textarea name="comment" rows="4" class="form-control" required>{{ old('comment') }}</textarea>
                            @error('comment')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">ثبت نظر</button>
                    </div>
                </form>
            </div>
        </section>

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
