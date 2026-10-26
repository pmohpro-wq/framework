@extends('layouts.store')

@section('title', 'تسویه‌حساب — فروشگاه من')

@section('content')
    <div class="container">
        <div class="steps">
            <div class="step done"><div class="dot">۱</div>انتخاب کالا</div>
            <div class="step done"><div class="dot">۲</div>سبد خرید</div>
            <div class="step active"><div class="dot">۳</div>پرداخت</div>
        </div>

        <h2 style="margin:16px 0">اطلاعات ارسال و پرداخت</h2>

        <form action="{{ route('checkout.place-order') }}" method="post">
            @csrf

            <div class="checkout-layout">
                <div>
                    {{-- اطلاعات خریدار --}}
                    <div class="checkout-box">
                        <h3>اطلاعات خریدار</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>نام <span class="required">*</span></label>
                                <input type="text" name="firstname" value="{{ old('firstname', $billpayer->firstname ?? '') }}" class="form-control" required>
                                @error('firstname')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>نام خانوادگی <span class="required">*</span></label>
                                <input type="text" name="lastname" value="{{ old('lastname', $billpayer->lastname ?? '') }}" class="form-control" required>
                                @error('lastname')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>ایمیل <span class="required">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $billpayer->email ?? '') }}" class="form-control" dir="ltr" required>
                                @error('email')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>شماره تماس <span class="required">*</span></label>
                                <input type="tel" name="phone" value="{{ old('phone', $billpayer->phone ?? '') }}" class="form-control" dir="ltr" required>
                                @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- آدرس ارسال --}}
                    <div class="checkout-box">
                        <h3>آدرس تحویل سفارش</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>شهر <span class="required">*</span></label>
                                <input type="text" name="city" value="{{ old('city') }}" class="form-control" required>
                                @error('city')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>کد پستی</label>
                                <input type="text" name="postalcode" value="{{ old('postalcode') }}" class="form-control" dir="ltr" maxlength="12">
                                @error('postalcode')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group" style="grid-column:1/-1">
                                <label>نشانی کامل <span class="required">*</span></label>
                                <textarea name="address" class="form-control" required>{{ old('address') }}</textarea>
                                @error('address')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- روش ارسال --}}
                    <div class="checkout-box">
                        <h3>روش ارسال</h3>
                        @forelse($shippingMethods as $i => $method)
                            <label class="option-card">
                                <input type="radio" name="shipping_method" value="{{ $method->id }}"
                                       @checked($i === 0 || old('shipping_method') == $method->id) required>
                                <span>🚚 {{ $method->name }}</span>
                                <span class="opt-price">{{ \App\Support\Persian::toman($method->configuration['cost'] ?? 0, false) }} تومان</span>
                            </label>
                        @empty
                            <div class="alert alert-warning">روش ارسالی تعریف نشده است.</div>
                        @endforelse
                        @error('shipping_method')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- روش پرداخت --}}
                    <div class="checkout-box">
                        <h3>روش پرداخت</h3>
                        @forelse($paymentMethods as $i => $method)
                            <label class="option-card">
                                <input type="radio" name="payment_method" value="{{ $method->id }}"
                                       @checked($i === 0 || old('payment_method') == $method->id) required>
                                <span>💳 {{ $method->name }}</span>
                                @if($method->description)
                                    <span class="muted" style="font-size:12px">{{ $method->description }}</span>
                                @endif
                            </label>
                        @empty
                            <div class="alert alert-warning">روش پرداختی تعریف نشده است.</div>
                        @endforelse
                        @error('payment_method')<div class="field-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- توضیحات --}}
                    <div class="checkout-box">
                        <h3>توضیحات سفارش (اختیاری)</h3>
                        <textarea name="notes" class="form-control" placeholder="مثلاً زمان مناسب تحویل...">{{ old('notes') }}</textarea>
                    </div>

                    {{-- کد تخفیف --}}
                    <div class="checkout-box">
                        <h3>کد تخفیف</h3>
                        @if(session('success') && str_contains(session('success'), 'تخفیف'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <div style="display:flex;gap:10px">
                            <input type="text" name="coupon" value="{{ old('coupon') }}" class="form-control"
                                   dir="ltr" placeholder="مثال: WELCOME10" style="text-transform:uppercase">
                        </div>
                        @error('coupon')<div class="field-error">{{ $message }}</div>@enderror
                        <p class="muted" style="font-size:12px;margin-top:8px">
                            اگر کد تخفیف دارید وارد کنید؛ مبلغ کسر پیش از ثبت سفارش اعمال می‌شود.
                        </p>
                    </div>
                </div>

                {{-- خلاصه سفارش --}}
                <div>
                    <div class="checkout-box" style="position:sticky;top:90px">
                        <h3>خلاصه سفارش</h3>
                        <div class="row" style="display:flex;justify-content:space-between;margin-bottom:10px">
                            <span>مبلغ کالاها</span>
                            <span>{{ \App\Support\Persian::toman($total) }}</span>
                        </div>
                        <div class="row muted" style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:16px">
                            <span>هزینه ارسال</span>
                            <span>پس از انتخاب روش ارسال</span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            ثبت سفارش و پرداخت 💳
                        </button>
                        <p class="muted" style="font-size:12px;margin-top:12px;text-align:center">
                            با ثبت سفارش، قوانین فروشگاه را می‌پذیرید.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
