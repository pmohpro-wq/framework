<header class="site-header">
    <div class="container">
        <div class="header-top">
            <a href="{{ route('home') }}" class="logo">
                <span class="logo-mark">ف</span>
                <span>فروشگاه من</span>
            </a>

            <form class="header-search" action="{{ route('home') }}" method="get">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></svg>
                <input type="text" name="q" placeholder="جستجو در محصولات..." value="{{ request('q') }}">
            </form>

            <div class="header-actions">
                <a href="{{ route('cart.index') }}" class="btn-cart">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1.6"/><circle cx="18" cy="21" r="1.6"/><path d="M2.5 3h2l2.4 12.2a2 2 0 0 0 2 1.8h8.6a2 2 0 0 0 2-1.6L21 7H6"/></svg>
                    سبد خرید
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
