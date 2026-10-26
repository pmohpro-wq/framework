<?php

namespace App\Providers;

use App\Payment\Gateways\IranianGateway;
use App\Payment\Gateways\ZarinpalGateway;
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
        // درگاه‌های پرداخت: شبیه‌ساز ایرانی + زرین‌پال (در صورت تنظیم MERCHANT_ID اتصال واقعی می‌شود)
        PaymentGateways::register(IranianGateway::ID, IranianGateway::class);
        PaymentGateways::register(ZarinpalGateway::ID, ZarinpalGateway::class);

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

        // افزودن آیتم‌های اختصاصی به منوی پنل مدیریت
        $this->addAdminMenuItems();
    }

    private function addAdminMenuItems(): void
    {
        if (!class_exists(\Konekt\Menu\Facades\Menu::class) || app()->runningInConsole()) {
            return;
        }

        $menu = \Konekt\Menu\Facades\Menu::get('appshell');

        if (!$menu || !$menu->getItem('shop')) {
            return;
        }

        // مسیرها به‌صورت لفظی داده می‌شوند (با کلید url) تا به زمان لود شدن
        // روت‌های app وابسته نباشند؛ روت‌های app پس از boot شدن پرووایدرها لود می‌شوند
        $menu->getItem('shop')
            ->addSubItem('product_reviews', 'نظرات محصولات', ['url' => '/admin/custom/reviews'])
            ->data('icon', 'chat')
            ->activateOnUrls('/admin/custom/reviews*')
            ->allowIfUserCan('list products');

        $menu->getItem('shop')
            ->addSubItem('contact_messages', 'پیام‌های مشتریان', ['url' => '/admin/custom/messages'])
            ->data('icon', 'mail')
            ->activateOnUrls('/admin/custom/messages*')
            ->allowIfUserCan('list products');
    }
}

