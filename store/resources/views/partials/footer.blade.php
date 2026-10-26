<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-logo">
                    <span class="logo-mark">ف</span>
                    <span>فروشگاه من</span>
                </div>
                <p>
                    فروشگاه من بر پایه فریمورک وانیلو و لاراول ساخته شده است؛
                    تجربه‌ای سریع و مطمئن برای خرید آنلاین با تمرکز بر کیفیت
                    کالا و رضایت مشتری.
                </p>
                <div class="footer-trust">
                    <span>پرداخت امن</span>
                    <span>ضمانت اصالت کالا</span>
                    <span>۷ روز بازگشت</span>
                </div>
            </div>
            <div>
                <h4>خدمات مشتریان</h4>
                <ul>
                    <li><a href="{{ route('track.show') }}">پیگیری سفارش</a></li>
                    <li><a href="{{ route('pages.returns') }}">شرایط بازگشت کالا</a></li>
                    <li><a href="{{ route('pages.faq') }}">پاسخ به پرسش‌های متداول</a></li>
                    <li><a href="{{ route('pages.shipping') }}">راهنمای خرید</a></li>
                </ul>
            </div>
            <div>
                <h4>دسته‌بندی‌ها</h4>
                <ul>
                    @foreach(($headerTaxons ?? collect()) as $taxon)
                        <li>
                            <a href="{{ route('category.show', ['taxonomySlug' => $taxon->taxonomy->slug ?? 'main', 'taxonSlug' => $taxon->slug]) }}">
                                {{ $taxon->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4>فروشگاه ما</h4>
                <ul>
                    <li><a href="{{ route('pages.about') }}">درباره ما</a></li>
                    <li><a href="{{ route('pages.contact') }}">تماس با ما</a></li>
                    <li><a href="{{ route('pages.terms') }}">قوانین و مقررات</a></li>
                    <li>
                        <a href="{{ auth()->check() ? route('account.dashboard') : route('account.register') }}">حساب کاربری</a>
                    </li>
                </ul>
            </div>
            <div>
                <h4>راه‌های ارتباطی</h4>
                <ul>
                    <li>پشتیبانی: ۰۲۱-۱۲۳۴۵۶۷۸</li>
                    <li>ایمیل: support@example.com</li>
                    <li>پاسخ‌گویی: ۹ صبح تا ۶ عصر</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            © {{ now()->translatedFormat('Y') }} فروشگاه من — تمامی حقوق محفوظ است.
        </div>
    </div>
</footer>
