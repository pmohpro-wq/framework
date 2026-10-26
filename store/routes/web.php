<?php

use App\Http\Controllers\Admin\MessageAdminController;
use App\Http\Controllers\Admin\ReviewAdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Store\AccountController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CategoryController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\OrderTrackingController;
use App\Http\Controllers\Store\PageController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Store\ProductController;
use App\Http\Controllers\Store\ReviewController;
use App\Http\Controllers\Store\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ویترین فروشگاه (Storefront)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// پنل مدیریت صفحهٔ «/» ندارد؛ به فهرست محصولات هدایت می‌کنیم
Route::redirect('/admin', '/admin/product');

Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/category/{taxonomySlug}/{taxonSlug}', [CategoryController::class, 'show'])
    ->name('category.show');

Route::get('/search', [SearchController::class, 'results'])->name('search.results');

/*
|--------------------------------------------------------------------------
| صفحات ایستا و پیگیری سفارش
|--------------------------------------------------------------------------
*/

Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/faq', [PageController::class, 'faq'])->name('pages.faq');
Route::get('/shipping', [PageController::class, 'shipping'])->name('pages.shipping');
Route::get('/returns', [PageController::class, 'returns'])->name('pages.returns');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [PageController::class, 'submitContact'])->name('pages.contact.submit');

Route::get('/track', [OrderTrackingController::class, 'show'])->name('track.show');
Route::post('/track', [OrderTrackingController::class, 'track'])->name('track.order');

/*
|--------------------------------------------------------------------------
| نظرات محصولات
|--------------------------------------------------------------------------
*/

Route::post('/product/{slug}/review', [ReviewController::class, 'store'])->name('review.store');

/*
|--------------------------------------------------------------------------
| سبد خرید و تسویه‌حساب
|--------------------------------------------------------------------------
*/

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
});

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place-order');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])
    ->name('checkout.success');

/*
|--------------------------------------------------------------------------
| پرداخت (درگاه ایرانی — شبیه‌ساز / زرین‌پال)
|--------------------------------------------------------------------------
*/

Route::prefix('payment/iranian')->name('payment.iranian.')->group(function () {
    Route::get('/pay/{paymentHash}', [PaymentController::class, 'iranianPay'])->name('pay');
    Route::post('/callback', [PaymentController::class, 'iranianCallback'])->name('callback');
});

// بازگشت از زرین‌پال (در اتصال واقعی؛ در حالت دمو به شبیه‌ساز برمی‌گردد)
Route::get('/payment/zarinpal/callback/{paymentHash}', [PaymentController::class, 'zarinpalCallback'])
    ->name('payment.zarinpal.callback');

/*
|--------------------------------------------------------------------------
| حساب کاربری مشتری (ثبت‌نام / ورود / پنل مشتری)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

    Route::get('/account/register', [RegisterController::class, 'show'])->name('account.register');
    Route::post('/account/register', [RegisterController::class, 'register'])->name('account.register.store');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders/{number}', [AccountController::class, 'orderDetail'])->name('orders.show');
    Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| مدیریت نظرات و پیام‌ها (بخش اختصاصی ادمین)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin/custom')->name('admin.custom.')->group(function () {
    Route::get('/reviews', [ReviewAdminController::class, 'index'])->name('reviews.index');
    Route::put('/reviews/{review}/approve', [ReviewAdminController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [ReviewAdminController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/messages', [MessageAdminController::class, 'index'])->name('messages.index');
    Route::put('/messages/{message}/read', [MessageAdminController::class, 'markRead'])->name('messages.read');
    Route::delete('/messages/{message}', [MessageAdminController::class, 'destroy'])->name('messages.destroy');
});
