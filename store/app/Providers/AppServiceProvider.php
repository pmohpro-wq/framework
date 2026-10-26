<?php

namespace App\Providers;

use App\Payment\Gateways\IranianGateway;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Vanilo\Cart\Facades\Cart;
use Vanilo\Category\Models\TaxonomyProxy;
use Vanilo\Payment\PaymentGateways;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // درگاه پرداخت ایرانی (اسکلت آماده اتصال به زرین‌پال و مشابه)
        PaymentGateways::register(IranianGateway::ID, IranianGateway::class);

        // کاربران admin از تمام محدودیت‌های ACL پنل مدیریت عبور می‌کنند
        Gate::before(function ($user, $ability) {
            return $user->type === 'admin' ? true : null;
        });

        // داده‌های مشترک ویترین: دسته‌بندی‌ها و تعداد اقلام سبد
        View::composer(['layouts.store', 'partials.header'], function ($view) {
            $taxonomy = TaxonomyProxy::where('slug', 'main')->first();

            $view->with([
                'headerTaxons' => $taxonomy?->taxons()->orderBy('priority')->get() ?? collect(),
                'cartCount'    => Cart::itemCount(),
            ]);
        });
    }
}

