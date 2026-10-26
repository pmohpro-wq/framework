@extends('layouts.store')

@section('title', 'تسویه‌حساب — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.checkout_steps', ['current' => 3])

        <h2 class="page-title">اطلاعات ارسال و پرداخت</h2>

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
                            <div class="form-group full">
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
                                <span class="opt-body">
                                    <span class="label">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 18H3c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h10c.6 0 1 .4 1 1v11"/><path d="M14 9h4l4 4v4c0 .6-.4 1-1 1h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>
                                        {{ $method->name }}
                                    </span>
                                </span>
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
                                <span class="opt-body">
                                    <span class="label">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/></svg>
                                        {{ $method->name }}
                                    </span>
                                    @if($method->description)
                                        <span class="muted">{{ $method->description }}</span>
                                    @endif
                                </span>
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
                        <div class="coupon-field">
                            <input type="text" name="coupon" value="{{ old('coupon') }}" class="form-control"
                                   dir="ltr" placeholder="مثال: WELCOME10">
                        </div>
                        @error('coupon')<div class="field-error">{{ $message }}</div>@enderror
                        <p class="muted-sm mt-8">
                            اگر کد تخفیف دارید وارد کنید؛ مبلغ کسر پیش از ثبت سفارش اعمال می‌شود.
                        </p>
                    </div>
                </div>

                {{-- خلاصه سفارش --}}
                <div>
                    <div class="checkout-box checkout-box--sticky">
                        <h3>خلاصه سفارش</h3>
                        <div class="sum-row">
                            <span>مبلغ کالاها</span>
                            <span>{{ \App\Support\Persian::toman($total) }}</span>
                        </div>
                        <div class="sum-row sum-row--muted">
                            <span>هزینه ارسال</span>
                            <span>پس از انتخاب روش ارسال</span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2.5"/><path d="M2 10h20"/></svg>
                            ثبت سفارش و پرداخت
                        </button>
                        <p class="fine-print">
                            با ثبت سفارش، قوانین فروشگاه را می‌پذیرید.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
