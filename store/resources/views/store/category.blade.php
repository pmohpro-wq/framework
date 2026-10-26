@extends('layouts.store')

@section('title', $taxon->name . ' — فروشگاه من')

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <span>{{ $taxon->name }}</span>
        </div>

        <section class="section">
            <div class="section-head"><h2>{{ $taxon->name }}</h2></div>

            @if($products->isEmpty())
                <div class="page empty-state">
                    <div class="icon">📦</div>
                    <p>در این دسته‌بندی هنوز محصولی موجود نیست.</p>
                </div>
            @else
                <div class="product-grid">
                    @foreach($products as $product)
                        @include('partials.product_card', ['product' => $product])
                    @endforeach
                </div>

                <div class="pagination">{{ $products->links() }}</div>
            @endif
        </section>
    </div>
@endsection
