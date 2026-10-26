<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'فروشگاه من')</title>
    <link rel="stylesheet" href="{{ asset('assets/store/css/store.css') }}">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='22' fill='%23ef4056'/><text x='50' y='68' font-size='52' text-anchor='middle' fill='white' font-family='sans-serif'>ف</text></svg>">
</head>
<body>

@include('partials.header')

<main>
    @if (session('success'))
        <div class="container"><div class="alert alert-success">✅ {{ session('success') }}</div></div>
    @endif
    @if (session('error'))
        <div class="container"><div class="alert alert-error">⚠️ {{ session('error') }}</div></div>
    @endif
    @if (session('warning'))
        <div class="container"><div class="alert alert-warning">🔔 {{ session('warning') }}</div></div>
    @endif

    @yield('content')
</main>

@include('partials.footer')

</body>
</html>
