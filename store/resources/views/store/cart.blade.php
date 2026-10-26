@extends('layouts.store')

@section('title', 'سبد خرید — فروشگاه من')

@section('content')
    <div class="container">
        <div class="steps">
            <div class="step done"><div class="dot">۱</div>انتخاب کالا</div>
            <div class="step active"><div class="dot">۲</div>سبد خرید</div>
            <div class="step"><div class="dot">۳</div>پرداخت</div>
        </div>

        @if($items->isEmpty())
            <div class="page empty-state">
                <div class="icon">🛒</div>
                <h2>سبد خرید شما خالی است</h2>
                <p style="margin:10px 0 20px">می‌توانید از صفحه اصلی محصولات را انتخاب کنید.</p>
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
                                    <a href="{{ route('product.show', $item->product->slug) }}">
                                        {{ $item->product->name }}
                                    </a>
                                </div>
                            </td>
                            <td>{{ \App\Support\Persian::toman($item->price) }}</td>
                            <td>
                                <form action="{{ route('cart.update') }}" method="post" class="qty-input">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                    <button type="submit" title="به‌روزرسانی">⟳</button>
                                    <input type="number" name="quantity" value="{{ \App\Support\Persian::digits($item->quantity) }}" min="0" max="99">
                                    <button type="button" onclick="this.closest('form').submit()">−</button>
                                </form>
                            </td>
                            <td style="font-weight:700">{{ \App\Support\Persian::toman($item->total) }}</td>
                            <td>
                                <form action="{{ route('cart.remove') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                                    <button type="submit" class="link-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div style="margin-top:16px">
                    <form action="{{ route('cart.clear') }}" method="post">
                        @csrf
                        <button type="submit" class="link-danger">🗑 خالی کردن سبد</button>
                    </form>
                </div>
            </div>

            <div class="cart-summary">
                <div class="row">
                    <span>مبلغ کل</span>
                    <span>{{ \App\Support\Persian::toman($total) }}</span>
                </div>
                <div class="row muted" style="font-size:13px">
                    <span>هزینه ارسال</span>
                    <span>در مرحله بعد محاسبه می‌شود</span>
                </div>
                <div class="row total">
                    <span>مبلغ قابل پرداخت</span>
                    <span>{{ \App\Support\Persian::toman($total) }}</span>
                </div>
                <a href="{{ route('checkout.show') }}" class="btn btn-primary btn-block btn-lg">
                    ادامه فرآیند خرید ←
                </a>
            </div>
        @endif
    </div>
@endsection
