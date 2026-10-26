@extends('layouts.store')

@section('title', ($query ? 'جستجوی «' . $query . '»' : 'جستجو') . ' — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', ['items' => [['label' => 'جستجو']]])

        <div class="page">
            <h1 class="page-title">
                @if($query !== '')
                    نتایج جستجو برای «{{ $query }}»
                @else
                    جستجو در محصولات
                @endif
            </h1>

            <form action="{{ route('search.results') }}" method="get" class="inline-form">
                <input type="text" name="q" value="{{ $query }}" class="form-control"
                       placeholder="نام کالا، دسته یا کد کالا..." autofocus>
                <button type="submit" class="btn btn-primary">جستجو</button>
            </form>

            @if($query !== '')
                @if($products->isEmpty())
                    <div class="empty-state">
                        <div class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/><path d="M8.5 11h5"/></svg>
                        </div>
                        <h2>نتیجه‌ای پیدا نشد</h2>
                        <p class="muted">
                            برای «{{ $query }}» کالایی یافت نشد.
                            املای عبارت را بررسی کنید یا از دسته‌بندی‌های بالای صفحه استفاده کنید.
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-primary">بازگشت به فروشگاه</a>
                    </div>
                @else
                    <p class="muted result-count">
                        {{ \App\Support\Persian::digits($products->total()) }} کالا پیدا شد
                    </p>
                    <div class="product-grid">
                        @foreach($products as $product)
                            @include('partials.product_card', ['product' => $product])
                        @endforeach
                    </div>
                    @if($products->hasPages())
                        <div class="pagination">
                            {{ $products->links() }}
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
@endsection
