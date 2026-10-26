@extends('layouts.store')

@section('title', 'تماس با ما — فروشگاه من')

@section('content')
    <div class="container">
        @include('partials.breadcrumb', ['items' => [['label' => 'تماس با ما']]])

        <h1 class="page-title">تماس با ما</h1>
        <p class="muted">پیشنهاد، انتقاد یا سوالی دارید؟ خوشحال می‌شویم بشنویم.</p>

        <div class="contact-layout">
            <div>
                <div class="checkout-box">
                    <h3>اطلاعات تماس</h3>
                    <ul class="contact-list">
                        <li>
                            <span class="muted">تلفن پشتیبانی:</span>
                            ۰۲۱-۱۲۳۴۵۶۷۸
                        </li>
                        <li>
                            <span class="muted">ایمیل:</span>
                            support@example.com
                        </li>
                        <li>
                            <span class="muted">نشانی:</span>
                            تهران، خیابان ولیعصر، برج تجاری نگین، طبقهٔ ۵
                        </li>
                        <li>
                            <span class="muted">ساعات پاسخ‌گویی:</span>
                            شنبه تا پنجشنبه، ۹ صبح تا ۶ عصر
                        </li>
                    </ul>
                </div>

                <div class="checkout-box">
                    <h3>سریع‌تر پاسخ بگیرید</h3>
                    <p class="lead">
                        برای پیگیری وضعیت سفارش، نیازی به تماس نیست؛
                        شمارهٔ سفارش را در <a href="{{ route('track.show') }}" class="text-brand">صفحهٔ پیگیری سفارش</a> وارد کنید.
                    </p>
                </div>
            </div>

            <div>
                <form action="{{ route('pages.contact.submit') }}" method="post">
                    @csrf
                    <div class="checkout-box">
                        <h3>فرم پیام</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>نام و نام خانوادگی <span class="required">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                                @error('name')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group">
                                <label>ایمیل <span class="required">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control" dir="ltr" required>
                                @error('email')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group full">
                                <label>موضوع</label>
                                <input type="text" name="subject" value="{{ old('subject') }}" class="form-control" placeholder="مثلاً: بازگشت کالا، سوال دربارهٔ سفارش...">
                                @error('subject')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group full">
                                <label>متن پیام <span class="required">*</span></label>
                                <textarea name="message" rows="6" class="form-control" required>{{ old('message') }}</textarea>
                                @error('message')<div class="field-error">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                ارسال پیام
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
