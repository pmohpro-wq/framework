<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Store\CartController;
use App\Http\Controllers\Store\CategoryController;
use App\Http\Controllers\Store\CheckoutController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Controllers\Store\PaymentController;
use App\Http\Controllers\Store\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| ویترین فروشگاه (Storefront)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/category/{taxonomySlug}/{taxonSlug}', [CategoryController::class, 'show'])
    ->name('category.show');

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
| پرداخت (درگاه ایرانی — شبیه‌ساز)
|--------------------------------------------------------------------------
*/
Route::prefix('payment/iranian')->name('payment.iranian.')->group(function () {
    Route::get('/pay/{paymentHash}', [PaymentController::class, 'iranianPay'])->name('pay');
    Route::post('/callback', [PaymentController::class, 'iranianCallback'])->name('callback');
});

/*
|--------------------------------------------------------------------------
| احراز هویت (ورود به پنل مدیریت)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');
