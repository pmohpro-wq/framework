<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Konekt\User\Models\User;
use Vanilo\Channel\Models\Channel;
use Vanilo\Category\Models\Taxonomy;
use Vanilo\Payment\Models\PaymentMethod;
use Vanilo\Foundation\Models\Product;
use Vanilo\Shipment\Models\ShippingCategory;
use Vanilo\Shipment\Models\ShippingMethod;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();
        $this->seedChannel();
        $this->seedPaymentMethods();
        $this->seedShipping();
        $this->seedCatalog();
    }

    private function seedAdmin(): void
    {
        if (! User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name'     => 'مدیر فروشگاه',
                'email'    => 'admin@example.com',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin1234')),
                'type'     => 'admin',
                'is_active'=> true,
            ]);
        }
    }

    private function seedChannel(): void
    {
        if (! Channel::where('slug', 'default')->exists()) {
            Channel::create([
                'name'     => 'فروشگاه اینترنتی',
                'slug'     => 'default',
                'currency' => 'IRR',
            ]);
        }
    }

    private function seedPaymentMethods(): void
    {
        PaymentMethod::query()->firstOrCreate(
            ['gateway' => 'iranian'],
            [
                'name'        => 'پرداخت آنلاین (درگاه بانکی)',
                'description' => 'پرداخت امن از طریق درگاه پرداخت اینترنتی',
                'configuration' => [],
                'is_enabled'  => true,
            ]
        );

        PaymentMethod::query()->firstOrCreate(
            ['gateway' => 'null'],
            [
                'name'        => 'پرداخت در محل',
                'description' => 'پرداخت هنگام تحویل کالا',
                'configuration' => [],
                'is_enabled'  => true,
            ]
        );
    }

    private function seedShipping(): void
    {
        $category = ShippingCategory::query()->firstOrCreate(
            ['name' => 'کالای عادی'],
            ['is_not_shippable' => false],
        );

        ShippingMethod::query()->firstOrCreate(
            ['name' => 'پست پیشتاز (۲ تا ۴ روز کاری)'],
            [
                'calculator'    => 'flat_fee',
                'configuration' => [
                    'cost' => 49000,
                    'title' => 'هزینه ارسال',
                ],
                'shipping_category_id' => $category->id,
            ]
        );

        ShippingMethod::query()->firstOrCreate(
            ['name' => 'پیک موتوری تهران (همان روز)'],
            [
                'calculator'    => 'flat_fee',
                'configuration' => [
                    'cost' => 85000,
                    'title' => 'هزینه ارسال',
                ],
                'shipping_category_id' => $category->id,
            ]
        );
    }

    private function seedCatalog(): void
    {
        // کشورها (برای آدرس‌ها لازم است؛ فقط بار اول)
        if (\Konekt\Address\Models\Country::count() === 0) {
            $this->call(\Konekt\Address\Seeds\Countries::class);
        }

        $taxonomy = Taxonomy::query()->firstOrCreate(
            ['slug' => 'main'],
            ['name' => 'دسته‌بندی محصولات'],
        );

        $categories = [
            'digital'   => 'کالای دیجیتال',
            'clothing'  => 'پوشاک',
            'home'      => 'لوازم خانگی',
            'beauty'    => 'زیبایی و سلامت',
        ];

        $taxons = [];
        foreach ($categories as $slug => $name) {
            $taxons[$slug] = $taxonomy->taxons()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'priority' => count($taxons)],
            );
        }

        $products = [
            ['دسته' => 'digital', 'name' => 'گوشی موبایل سامسونگ Galaxy A55 ظرفیت ۲۵۶ گیگابایت', 'sku' => 'SG-A55-256', 'price' => 28450000, 'old' => 31200000, 'desc' => "نمایشگر Super AMOLED 6.6 اینچی با نرخ نوسازی ۱۲۰ هرتز\nدوربین اصلی ۵۰ مگاپیکسل با لرزشگیر اپتیکال\nباتری ۵۰۰۰ میلی‌آمپر با شارژ سریع ۲۵ وات"],
            ['دسته' => 'digital', 'name' => 'هدفون بی‌سیم سونی مدل WH-CH720N', 'sku' => 'SN-CH720N', 'price' => 4890000, 'old' => 5600000, 'desc' => "حذف نویز فعال (ANC)\nتا ۳۵ ساعت پخش موسیقی\nاتصال همزمان دو دستگاه"],
            ['دسته' => 'digital', 'name' => 'لپ‌تاپ ایسوس مدل VivoBook 15 Core i5', 'sku' => 'AS-VB15-I5', 'price' => 32700000, 'old' => null, 'desc' => "پردازنده Core i5-1235U نسل دوازدهم\nرم ۱۶ گیگابایت DDR4\nحافظه SSD ۵۱۲ گیگابایت"],
            ['دسته' => 'digital', 'name' => 'ساعت هوشمند شیائومی Redmi Watch 5', 'sku' => 'XM-RW5', 'price' => 3250000, 'old' => 3800000, 'desc' => "نمایشگر AMOLED 2.07 اینچ\nپایش ضربان قلب و اکسیژن خون\nباتری تا ۱۸ روز"],
            ['دسته' => 'clothing', 'name' => 'کاپشن مردانه مدل آتلانتیک رنگ سرمه‌ای', 'sku' => 'CL-ATL-M-NV', 'price' => 1890000, 'old' => 2450000, 'desc' => "جنس پارچه: نانو فوتر درجه یک\nقابل استفاده در فصل پاییز و زمستان\nضدآب تا حدی و بادگیر"],
            ['دسته' => 'clothing', 'name' => 'تی‌شرت زنانه نخی مدل بلوسوم', 'sku' => 'CL-TS-W-BL', 'price' => 420000, 'old' => null, 'desc' => "صد درصد نخ پنبه\nطراحی آزاد و راحت\nقابل شستشو در ماشین لباسشویی"],
            ['دسته' => 'clothing', 'name' => 'کفش ورزشی نایک مدل Air Zoom', 'sku' => 'NK-AZ-42', 'price' => 4580000, 'old' => 5300000, 'desc' => "طراحی ارگونومیک برای دویدن\nکفی طبی با فناوری Zoom Air\nوزن سبک و تنفس‌پذیر"],
            ['دسته' => 'home', 'name' => 'جاروبرقی فیلیپس مدل 2000 Series', 'sku' => 'PH-VC2000', 'price' => 6890000, 'old' => null, 'desc' => "قدرت مکش ۱۹۰۰ وات\nفیلتر HEPA ضدحساسیت\nتوان نظافت جاری: ۴۵۰ وات"],
            ['دسته' => 'home', 'name' => 'سرویس قابلمه ۱۰ پارچه گرانیتی', 'sku' => 'HM-POT-10', 'price' => 5750000, 'old' => 6900000, 'desc' => "پوشش گرانیتی نچسب سه لایه\nمناسب انواع اجاق گاز از جمله القایی\nقابلمه‌های اکسترودی با بدنه ضخیم"],
            ['دسته' => 'home', 'name' => 'چای‌ساز و کتری برقی فلر مدل TS-909', 'sku' => 'FL-TS909', 'price' => 2140000, 'old' => null, 'desc' => "توان ۲۲۰۰ وات\nقوری پیرکس ۱ لیتری\nخاموشی خودکار پس از جوشیدن"],
            ['دسته' => 'beauty', 'name' => 'کرم مرطوب‌کننده لافارر مناسب پوست خشک', 'sku' => 'BT-LF-CRM', 'price' => 396000, 'old' => 450000, 'desc' => "آبرسانی ۲۴ ساعته\nبدون چربی و جاذب سریع\nحاوی هیالورونیک اسید"],
            ['دسته' => 'beauty', 'name' => 'ست مراقبت مو سراسر گیاهی گلوا', 'sku' => 'BT-GW-HR3', 'price' => 780000, 'old' => 920000, 'desc' => "شامپو + ماسک + سرم مو\nفاقد سولفات و پارابن\nمناسب موهای رنگ‌شده"],
        ];

        foreach ($products as $p) {
            $product = Product::query()->firstOrCreate(
                ['sku' => $p['sku']],
                [
                    'name'           => $p['name'],
                    'slug'           => null, // به‌صورت خودکار از نام ساخته می‌شود
                    'price'          => $p['price'],
                    'original_price' => $p['old'],
                    'excerpt'        => mb_substr(str_replace("\n", ' ', $p['desc']), 0, 120) . '...',
                    'description'    => $p['desc'],
                    'state'          => 'active',
                    'stock'          => 25,
                ]
            );

            // اتصال دسته‌بندی (به‌صورت idempotent)
            $taxon = $taxons[$p['دسته']];
            if (! $product->taxons()->where('taxons.id', $taxon->id)->exists()) {
                $product->addTaxon($taxon);
            }

            if (! $product->hasImage()) {
                $this->attachGeneratedImage($product, $p['name']);
            }
        }
    }

    /** تولید تصویر ساده با GD و اتصال آن به محصول (مدیا لایبرری) */
    private function attachGeneratedImage(Product $product, string $title): void
    {
        $palette = [
            [35, 37, 78],   // سرمه‌ای
            [239, 64, 86],  // قرمز برند
            [0, 160, 73],   // سبز
            [91, 95, 158],  // بنفش
            [249, 168, 37], // طلایی
            [0, 132, 168],  // آبی
        ];
        $color = $palette[crc32($product->sku) % count($palette)];
        $light = array_map(fn ($c) => (int) min(255, $c + 90), $color);

        $size = 800;
        $img = imagecreatetruecolor($size, $size);

        // پس‌زمینه روشن
        $bg = imagecolorallocate($img, 248, 249, 251);
        imagefill($img, 0, 0, $bg);

        // دایره تزئینی
        $main = imagecolorallocate($img, ...$color);
        $soft = imagecolorallocate($img, ...$light);
        imagefilledellipse($img, 400, 380, 640, 640, $soft);
        imagefilledellipse($img, 400, 400, 380, 380, $main);

        // جعبه سفید کالا
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 280, 260, 520, 470, $white);
        imagerectangle($img, 280, 260, 520, 470, $soft);
        imagefilledrectangle($img, 330, 300, 470, 330, $soft);
        imagefilledrectangle($img, 330, 350, 430, 365, $soft);
        imagefilledrectangle($img, 330, 380, 450, 395, $soft);
        imagerectangle($img, 355, 410, 445, 445, $main);

        $path = storage_path("app/seed-{$product->sku}.png");
        imagepng($img, $path);
        imagedestroy($img);

        $product->addMedia($path)
            ->usingName($title)
            ->usingFileName("{$product->sku}.png")
            ->toMediaCollection('images');
    }
}
