@extends('layouts.store')

@section('title', 'اطلاعات حساب — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', [
            'items' => [
                ['label' => 'حساب کاربری', 'url' => route('account.dashboard')],
                ['label' => 'اطلاعات حساب'],
            ],
        ])

        <div class="account-layout">
            @include('partials.account_side', ['user' => $user, 'active' => 'profile'])

            <div class="account-content">
                <h2 class="content-title">اطلاعات حساب</h2>

                <form action="{{ route('account.profile.update') }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="form-stack">
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

                        <div class="checkout-box">
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
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">ذخیرهٔ تغییرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
