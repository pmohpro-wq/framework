<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود مدیر — فروشگاه من</title>
    <link rel="stylesheet" href="/assets/store/css/store.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='22' fill='%23ef4056'/><text x='50' y='68' font-size='52' text-anchor='middle' fill='white' font-family='sans-serif'>ف</text></svg>">
</head>
<body>

<div class="login-page">
    <div class="login-card">
        <span class="logo-mark">ف</span>
        <h1>ورود به پنل مدیریت</h1>
        <p class="sub">فروشگاه من — مبتنی بر وانیلو</p>

        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.attempt') }}" method="post">
            @csrf
            <div class="form-group">
                <label>ایمیل</label>
                <input type="email" name="email" value="{{ old('email', 'admin@example.com') }}" class="form-control" dir="ltr" required>
            </div>
            <div class="form-group">
                <label>گذرواژه</label>
                <input type="password" name="password" value="{{ old('password', 'admin1234') }}" class="form-control" dir="ltr" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" autofocus>ورود</button>
        </form>

        <p class="muted" style="font-size:12px;margin-top:18px;text-align:center">
            اطلاعات ورود به‌صورت پیش‌فرض پر شده است — فقط «ورود» را بزنید.<br>
            <a href="{{ route('home') }}">← بازگشت به فروشگاه</a>
        </p>
    </div>
</div>

</body>
</html>
