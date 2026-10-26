@extends('layouts.store')

@section('title', ($query ? 'جستجوی «' . $query . '»' : 'جستجو') . ' — فروشگاه من')

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <span>جستجو</span>
        </div>

        <div class="page">
            <h1 style="margin-bottom:6px">
                @if($query !== '')
                    نتایج جستجو برای «{{ $query }}»
                @else
                    جستجو در محصولات
                @endif
            </h1>

            <form action="{{ route('search.results') }}" method="get" style="display:flex;gap:10px;margin:18px 0 26px;max-width:520px">
                <input type="text" name="q" value="{{ $query }}" class="form-control"
                       placeholder="نام کالا، دسته یا کد کالا..." autofocus>
                <button type="submit" class="btn btn-primary" style="white-space:nowrap">جستجو</button>
            </form>

            @if($query !== '')
                @if($products->isEmpty())
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/><path d="M8.5 11h5"/></svg>
                        <h2>نتیجه‌ای پیدا نشد</h2>
                        <p class="muted">
                            برای «{{ $query }}» کالایی یافت نشد.
                            املای عبارت را بررسی کنید یا از دسته‌بندی‌های بالای صفحه استفاده کنید.
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top:14px">بازگشت به فروشگاه</a>
                    </div>
                @else
                    <p class="muted" style="margin-bottom:16px">
                        {{ \App\Support\Persian::digits($products->total()) }} کالا پیدا شد
                    </p>
                    <div class="product-grid">
                        @foreach($products as $product)
                            @include('partials.product_card', ['product' => $product])
                        @endforeach
                    </div>
                    @if($products->hasPages())
                        <div style="margin-top:24px" class="muted">
                            {{ $products->links() }}
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
@endsection
