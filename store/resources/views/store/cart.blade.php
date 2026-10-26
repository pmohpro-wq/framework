@extends('layouts.store')

@section('title', 'سبد خرید — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.checkout_steps', ['current' => 2])

        @if($items->isEmpty())
            <div class="page empty-state">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="18" cy="21" r="1.6"/><path d="M2.5 3h2l2.4 12.2a2 2 0 0 0 2 1.8h8.6a2 2 0 0 0 2-1.6L21 7H6"/></svg>
                </div>
                <h2>سبد خرید شما خالی است</h2>
                <p>می‌توانید از صفحه اصلی محصولات را انتخاب کنید.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">رفتن به فروشگاه</a>
            </div>
        @else
            <div class="page">
                <table class="cart-table">
                    <thead>
                    <tr>
                        <th>کالا</th>
                        <th>قیمت واحد</th>
                        <th>تعداد</th>
                        <th>جمع</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>
                                <div class="cart-item-info">
                                    <img src="{{ $item->product->getImageUrl('thumbnail') ?? '/images/placeholder.svg' }}"
                                         alt="{{ $item->product->name }}">
                                    <a href="{{ route('product.show', $item->product->slug) }}" class="name">
                                        {{ $item->product->name }}
                                    </a>
                                </div>
                            </td>
                            <td>{{ \App\Support\Persian::toman($item->price) }}</td>
                            <td>
                                <form action="{{ route('cart.update') }}" method="post" class="qty-input sm">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                    <button type="button" title="کاهش" onclick="cartStep(this.form, -1)">−</button>
                                    {{-- مقدار عددی لاتین: مرورگر ارقام فارسی را در input[type=number] نمی‌پذیرد --}}
                                    <input type="number" name="quantity" value="{{ (int) $item->quantity }}" min="0" max="99">
                                    <button type="button" title="افزایش" onclick="cartStep(this.form, 1)">+</button>
                                </form>
                            </td>
                            <td class="cell-total">{{ \App\Support\Persian::toman($item->total) }}</td>
                            <td>
                                <form action="{{ route('cart.remove') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                    <button type="submit" class="link-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6"/><path d="M5.5 6l1 13a1.5 1.5 0 0 0 1.5 1.4h8a1.5 1.5 0 0 0 1.5-1.4l1-13"/><path d="M10 11v6M14 11v6"/></svg>
                                        حذف
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="cart-actions">
                    <form action="{{ route('cart.clear') }}" method="post">
                        @csrf
                        <button type="submit" class="link-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4.5A1.5 1.5 0 0 1 9.5 3h5A1.5 1.5 0 0 1 16 4.5V6"/><path d="M5.5 6l1 13a1.5 1.5 0 0 0 1.5 1.4h8a1.5 1.5 0 0 0 1.5-1.4l1-13"/></svg>
                            خالی کردن سبد
                        </button>
                    </form>
                </div>
            </div>

            <div class="cart-summary">
                <div class="row">
                    <span>مبلغ کل</span>
                    <span>{{ \App\Support\Persian::toman($total) }}</span>
                </div>
                <div class="row muted">
                    <span>هزینه ارسال</span>
                    <span>در مرحله بعد محاسبه می‌شود</span>
                </div>
                <div class="row total">
                    <span>مبلغ قابل پرداخت</span>
                    <span>{{ \App\Support\Persian::toman($total) }}</span>
                </div>
                <a href="{{ route('checkout.show') }}" class="btn btn-primary btn-block btn-lg">
                    ادامه فرآیند خرید
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="m11 18-6-6 6-6"/></svg>
                </a>
            </div>
        @endif
    </div>

    <script>
        // گام افزایش/کاهش تعداد در سبد — بعد از تغییر، فرم به‌روزرسانی سابمیت می‌شود
        function cartStep(form, delta) {
            var input = form.querySelector('input[name="quantity"]');
            var max = parseInt(input.max || '99', 10);
            var value = (parseInt(input.value, 10) || 0) + delta;
            input.value = Math.min(max, Math.max(0, value));
            form.submit();
        }
    </script>
@endsection
