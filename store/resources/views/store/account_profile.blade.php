@extends('layouts.store')

@section('title', 'اطلاعات حساب — فروشگاه من')

@section('content')
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">خانه</a>
            <span>/</span>
            <a href="{{ route('account.dashboard') }}">حساب کاربری</a>
            <span>/</span>
            <span>اطلاعات حساب</span>
        </div>

        <div class="account-layout">
            <aside class="account-side">
                <div class="account-user">
                    <div class="avatar">{{ mb_substr($user->name, 0, 1) }}</div>
                    <div>
                        <div style="font-weight:600">{{ $user->name }}</div>
                        <div class="muted" style="font-size:12px" dir="ltr">{{ $user->email }}</div>
                    </div>
                </div>
                <nav class="account-nav">
                    <a href="{{ route('account.dashboard') }}">سفارش‌های من</a>
                    <a href="{{ route('account.profile') }}" class="active">اطلاعات حساب</a>
                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="account-logout">خروج از حساب</button>
                    </form>
                </nav>
            </aside>

            <div class="account-content">
                <h2 style="margin-bottom:16px">اطلاعات حساب</h2>

                <form action="{{ route('account.profile.update') }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="checkout-box">
                        <h3>مشخصات شخصی</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>نام <span class="required">*</span></label>
                                <input type="text" name="firstname" value="{{ old('firstname', $customer->firstname ?? '') }}" class="form-control" required>
                                @error('firstname')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>نام خانوادگی <span class="required">*</span></label>
                                <input type="text" name="lastname" value="{{ old('lastname', $customer->lastname ?? '') }}" class="form-control" required>
                                @error('lastname')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>شماره تماس <span class="required">*</span></label>
                                <input type="tel" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="form-control" dir="ltr" required>
                                @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>ایمیل (غیرقابل تغییر)</label>
                                <input type="email" value="{{ $user->email }}" class="form-control" dir="ltr" disabled>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-box" style="margin-top:16px">
                        <h3>تغییر گذرواژه (اختیاری)</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>گذرواژهٔ جدید</label>
                                <input type="password" name="password" class="form-control" dir="ltr" autocomplete="new-password" placeholder="حداقل ۸ کاراکتر">
                                @error('password')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>تکرار گذرواژهٔ جدید</label>
                                <input type="password" name="password_confirmation" class="form-control" dir="ltr" autocomplete="new-password">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="margin-top:16px">ذخیرهٔ تغییرات</button>
                </form>
            </div>
        </div>
    </div>
@endsection
