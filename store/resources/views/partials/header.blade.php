<header class="site-header">
    <div class="container">
        <div class="header-top">
            <a href="{{ route('home') }}" class="logo">
                <span class="logo-mark">ف</span>
                <span>فروشگاه من</span>
            </a>

            <form class="header-search" action="{{ route('home') }}" method="get">
                <span class="search-icon">🔍</span>
                <input type="text" name="q" placeholder="جستجو در محصولات..." value="{{ request('q') }}">
            </form>

            <div class="header-actions">
                <a href="{{ route('cart.index') }}" class="btn-cart">
                    🛒 سبد خرید
                    @if(($cartCount ?? 0) > 0)
                        <span class="cart-badge">{{ \App\Support\Persian::digits($cartCount) }}</span>
                    @endif
                </a>
                <a href="/admin" class="btn-admin-link">ورود مدیر</a>
            </div>
        </div>
    </div>

    <nav class="site-nav">
        <div class="container">
            <ul>
                @foreach(($headerTaxons ?? collect()) as $taxon)
                    <li>
                        <a href="{{ route('category.show', ['taxonomySlug' => $taxon->taxonomy->slug ?? 'main', 'taxonSlug' => $taxon->slug]) }}">
                            {{ $taxon->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>
</header>
