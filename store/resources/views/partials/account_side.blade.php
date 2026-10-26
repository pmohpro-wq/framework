{{-- منوی کناری حساب کاربری — پارامتر: user، active = 'orders' | 'profile' --}}
<aside class="account-side">
    <div class="account-user">
        <div class="avatar">{{ mb_substr($user->name, 0, 1) }}</div>
        <div>
            <div class="name">{{ $user->name }}</div>
            <div class="muted email" dir="ltr">{{ $user->email }}</div>
        </div>
    </div>
    <nav class="account-nav">
        <a href="{{ route('account.dashboard') }}" @class(['active' => ($active ?? '') === 'orders'])>سفارش‌های من</a>
        <a href="{{ route('account.profile') }}" @class(['active' => ($active ?? '') === 'profile'])>اطلاعات حساب</a>
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="account-logout">خروج از حساب</button>
        </form>
    </nav>
</aside>
