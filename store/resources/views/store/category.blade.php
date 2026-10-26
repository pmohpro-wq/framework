@extends('layouts.store')

@section('title', $taxon->name . ' — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', ['items' => [['label' => $taxon->name]]])

        <section class="section">
            <div class="section-head"><h2>{{ $taxon->name }}</h2></div>

            @if($products->isEmpty())
                <div class="page empty-state">
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8.5v9a2 2 0 0 1-1.2 1.8l-7 3a2 2 0 0 1-1.6 0l-7-3A2 2 0 0 1 3 17.5v-9a2 2 0 0 1 1.2-1.8l7-3a2 2 0 0 1 1.6 0l7 3A2 2 0 0 1 21 8.5z"/><path d="m3.5 7.5 8.5 3.5 8.5-3.5"/><path d="M12 21.5V11"/></svg>
                    </div>
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
