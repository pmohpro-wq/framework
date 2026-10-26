<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود مدیر — فروشگاه من</title>
    <link rel="stylesheet" href="{{ asset('assets/store/css/store.css') }}">
</head>
<body>

<div class="login-page">
    <div class="login-card">
        <h1>🔐 ورود به پنل مدیریت</h1>
        <p class="sub">فروشگاه من — مبتنی بر وانیلو</p>

        @if($errors->any())
            <div class="alert alert-error">⚠️ {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.attempt') }}" method="post">
            @csrf
            <div class="form-group" style="margin-bottom:14px">
                <label>ایمیل</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" dir="ltr" required autofocus>
            </div>
            <div class="form-group" style="margin-bottom:20px">
                <label>گذرواژه</label>
                <input type="password" name="password" class="form-control" dir="ltr" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">ورود</button>
        </form>

        <p class="muted" style="font-size:12px;margin-top:18px;text-align:center">
            <a href="{{ route('home') }}" style="color:var(--primary)">← بازگشت به فروشگاه</a>
        </p>
    </div>
</div>

</body>
</html>
