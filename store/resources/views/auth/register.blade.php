@extends('layouts.store')

@section('title', 'ثبت‌نام — فروشگاه من')

@section('content')
    <div class="container">
        <div class="auth-page">
            <div class="auth-card">
                <div class="auth-logo"><span class="logo-mark">ف</span></div>
                <h1>ساخت حساب کاربری</h1>
                <p class="muted">با ساخت حساب، سفارش‌هایتان را همیشه در دسترس دارید.</p>

                <form action="{{ route('account.register.store') }}" method="post">
                    @csrf
                    <div class="form-grid" style="grid-template-columns:1fr 1fr">
                        <div class="form-group">
                            <label>نام <span class="required">*</span></label>
                            <input type="text" name="firstname" value="{{ old('firstname') }}" class="form-control" required>
                            @error('firstname')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>نام خانوادگی <span class="required">*</span></label>
                            <input type="text" name="lastname" value="{{ old('lastname') }}" class="form-control" required>
                            @error('lastname')<div class="field-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label>ایمیل <span class="required">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" dir="ltr" required>
                        @error('email')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>شماره تماس <span class="required">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="form-control" dir="ltr" required>
                        @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>گذرواژه <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control" dir="ltr" autocomplete="new-password" required>
                        @error('password')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label>تکرار گذرواژه <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" dir="ltr" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block" style="margin-top:8px">
                        ثبت‌نام
                    </button>
                </form>

                <p class="auth-alt">
                    حساب دارید؟
                    <a href="{{ route('login') }}">وارد شوید</a>
                </p>
            </div>
        </div>
    </div>
@endsection
