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
        $this->seedPromotion();
        $this->seedReviews();
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
            ['دسته' => 'digital', 'name' => 'گوشی موبایل سامسونگ Galaxy A55 ظرفیت ۲۵۶ گیگابایت', 'sku' => 'SG-A55-256', 'price' => 28450000, 'old' => 31200000, 'stock' => 12, 'desc' => "نمایشگر Super AMOLED 6.6 اینچی با نرخ نوسازی ۱۲۰ هرتز\nدوربین اصلی ۵۰ مگاپیکسل با لرزشگیر اپتیکال\nباتری ۵۰۰۰ میلی‌آمپر با شارژ سریع ۲۵ وات"],
            ['دسته' => 'digital', 'name' => 'هدفون بی‌سیم سونی مدل WH-CH720N', 'sku' => 'SN-CH720N', 'price' => 4890000, 'old' => 5600000, 'stock' => 34, 'desc' => "حذف نویز فعال (ANC)\nتا ۳۵ ساعت پخش موسیقی\nاتصال همزمان دو دستگاه"],
            ['دسته' => 'digital', 'name' => 'لپ‌تاپ ایسوس مدل VivoBook 15 Core i5', 'sku' => 'AS-VB15-I5', 'price' => 32700000, 'old' => null, 'stock' => 6, 'desc' => "پردازنده Core i5-1235U نسل دوازدهم\nرم ۱۶ گیگابایت DDR4\nحافظه SSD ۵۱۲ گیگابایت"],
            ['دسته' => 'digital', 'name' => 'ساعت هوشمند شیائومی Redmi Watch 5', 'sku' => 'XM-RW5', 'price' => 3250000, 'old' => 3800000, 'stock' => 3, 'desc' => "نمایشگر AMOLED 2.07 اینچ\nپایش ضربان قلب و اکسیژن خون\nباتری تا ۱۸ روز"],
            ['دسته' => 'clothing', 'name' => 'کاپشن مردانه مدل آتلانتیک رنگ سرمه‌ای', 'sku' => 'CL-ATL-M-NV', 'price' => 1890000, 'old' => 2450000, 'desc' => "جنس پارچه: نانو فوتر درجه یک\nقابل استفاده در فصل پاییز و زمستان\nضدآب تا حدی و بادگیر"],
            ['دسته' => 'clothing', 'name' => 'تی‌شرت زنانه نخی مدل بلوسوم', 'sku' => 'CL-TS-W-BL', 'price' => 420000, 'old' => null, 'desc' => "صد درصد نخ پنبه\nطراحی آزاد و راحت\nقابل شستشو در ماشین لباسشویی"],
            ['دسته' => 'clothing', 'name' => 'کفش ورزشی نایک مدل Air Zoom', 'sku' => 'NK-AZ-42', 'price' => 4580000, 'old' => 5300000, 'desc' => "طراحی ارگونومیک برای دویدن\nکفی طبی با فناوری Zoom Air\nوزن سبک و تنفس‌پذیر"],
            ['دسته' => 'home', 'name' => 'جاروبرقی فیلیپس مدل 2000 Series', 'sku' => 'PH-VC2000', 'price' => 6890000, 'old' => null, 'desc' => "قدرت مکش ۱۹۰۰ وات\nفیلتر HEPA ضدحساسیت\nتوان نظافت جاری: ۴۵۰ وات"],
            ['دسته' => 'home', 'name' => 'سرویس قابلمه ۱۰ پارچه گرانیتی', 'sku' => 'HM-POT-10', 'price' => 5750000, 'old' => 6900000, 'desc' => "پوشش گرانیتی نچسب سه لایه\nمناسب انواع اجاق گاز از جمله القایی\nقابلمه‌های اکسترودی با بدنه ضخیم"],
            ['دسته' => 'home', 'name' => 'چای‌ساز و کتری برقی فلر مدل TS-909', 'sku' => 'FL-TS909', 'price' => 2140000, 'old' => null, 'desc' => "توان ۲۲۰۰ وات\nقوری پیرکس ۱ لیتری\nخاموشی خودکار پس از جوشیدن"],
            ['دسته' => 'beauty', 'name' => 'کرم مرطوب‌کننده لافارر مناسب پوست خشک', 'sku' => 'BT-LF-CRM', 'price' => 396000, 'old' => 450000, 'stock' => 18, 'desc' => "آبرسانی ۲۴ ساعته\nبدون چربی و جاذب سریع\nحاوی هیالورونیک اسید"],
            ['دسته' => 'beauty', 'name' => 'ست مراقبت مو سراسر گیاهی گلوا', 'sku' => 'BT-GW-HR3', 'price' => 780000, 'old' => 920000, 'stock' => 0, 'desc' => "شامپو + ماسک + سرم مو\nفاقد سولفات و پارابن\nمناسب موهای رنگ‌شده"],
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
                    'stock'          => $p['stock'] ?? 25,
                ]
            );

            // به‌روزرسانی موجودی در اجراهای بعدی هم اعمال می‌شود
            if (isset($p['stock'])) {
                $product->update(['stock' => $p['stock']]);
            }

            // اتصال دسته‌بندی (به‌صورت idempotent)
            $taxon = $taxons[$p['دسته']];
            if (! $product->taxons()->where('taxons.id', $taxon->id)->exists()) {
                $product->addTaxon($taxon);
            }

            if (! $product->hasImage()) {
                $this->attachGeneratedImage($product, $this->imageTypeFor($p['sku']), $p['name']);
            }
        }
    }

    /** نگاشت SKU به نوع تصویر برای رسم اختصاصی هر کالا */
    private function imageTypeFor(string $sku): string
    {
        return match ($sku) {
            'SG-A55-256' => 'phone',
            'SN-CH720N'  => 'headphone',
            'AS-VB15-I5' => 'laptop',
            'XM-RW5'     => 'watch',
            'CL-ATL-M-NV'=> 'jacket',
            'CL-TS-W-BL' => 'tshirt',
            'NK-AZ-42'   => 'sneaker',
            'PH-VC2000'  => 'vacuum',
            'HM-POT-10'  => 'cookware',
            'FL-TS909'   => 'kettle',
            'BT-LF-CRM'  => 'cream',
            'BT-GW-HR3'  => 'haircare',
            default      => 'phone',
        };
    }

    /* =====================================================================
     |  موتور تولید تصویر محصول — پس‌زمینه گرادیانی + هاله نور + سایه +
     |  تصویر اختصاصی هر نوع کالا (بدون متن، بدون فونت)
     ===================================================================== */

    /** تولید تصویر ساده با GD و اتصال آن به محصول (مدیا لایبرری) */
    private function attachGeneratedImage(Product $product, string $type, string $title): void
    {
        $S  = 1000;
        $img = imagecreatetruecolor($S, $S);
        imagealphablending($img, true);

        // پالت هر نوع: [بالای پس‌زمینه، پایین پس‌زمینه، رنگ اصلی کالا، رنگ تیره کالا]
        $palettes = [
            'phone'     => ['#EAF0FF', '#D3DFF8', '#3D4EC7', '#232C6E'],
            'headphone' => ['#FFF0F3', '#FFD9E1', '#EF4056', '#A82438'],
            'laptop'    => ['#E9F7F1', '#D2EDDF', '#0FA36B', '#076B44'],
            'watch'     => ['#FFF7E6', '#FFE8C2', '#F5A623', '#B8780B'],
            'jacket'    => ['#EEF0F7', '#D9DDEC', '#33406B', '#1A2140'],
            'tshirt'    => ['#EAF6F6', '#D0EBEB', '#12A5A5', '#0A6E6E'],
            'sneaker'   => ['#FFF3EC', '#FFDFCB', '#F0632A', '#A83F12'],
            'vacuum'    => ['#F1EEFF', '#DED9FA', '#6C5CE7', '#453AA0'],
            'cookware'  => ['#EFF4F8', '#DAE5EE', '#455A64', '#263238'],
            'kettle'    => ['#EDF4FA', '#D6E5F1', '#2D7FB8', '#1A557D'],
            'cream'     => ['#FDF0F5', '#F9D9E6', '#D6336C', '#8F1F4B'],
            'haircare'  => ['#EFF9F0', '#D9EEDD', '#2F9E44', '#1C6629'],
        ];
        [$bgTop, $bgBot, $main, $dark] = $palettes[$type] ?? $palettes['phone'];

        // ۱) پس‌زمینه گرادیان عمودی
        $this->vGradient($img, $S, $this->hex($bgTop), $this->hex($bgBot));

        // ۲) دایره‌های تزئینی محو
        $this->softCircle($img, 130, 120, 240, $this->hex($main), 26);
        $this->softCircle($img, 900, 880, 300, $this->hex($main), 22);
        $this->softCircle($img, 880, 140, 110, '#FFFFFF', 60);

        // ۳) هاله پشت محصول
        $this->softCircle($img, 500, 470, 315, '#FFFFFF', 72);

        // ۴) سایه زمین
        $this->softEllipse($img, 500, 812, 300, 42, $this->hex($dark), 50);

        // ۵) رسم خود کالا بر اساس نوع
        match ($type) {
            'phone'     => $this->drawPhone($img, $main, $dark),
            'headphone' => $this->drawHeadphone($img, $main, $dark),
            'laptop'    => $this->drawLaptop($img, $main, $dark),
            'watch'     => $this->drawWatch($img, $main, $dark),
            'jacket'    => $this->drawJacket($img, $main, $dark),
            'tshirt'    => $this->drawTshirt($img, $main, $dark),
            'sneaker'   => $this->drawSneaker($img, $main, $dark),
            'vacuum'    => $this->drawVacuum($img, $main, $dark),
            'cookware'  => $this->drawCookware($img, $main, $dark),
            'kettle'    => $this->drawKettle($img, $main, $dark),
            'cream'     => $this->drawCream($img, $main, $dark),
            'haircare'  => $this->drawHaircare($img, $main, $dark),
            default     => $this->drawPhone($img, $main, $dark),
        };

        $path = storage_path("app/seed-{$product->sku}.png");
        imagepng($img, $path);
        imagedestroy($img);

        // نکته: trait وانیلو (HasImagesFromMediaLibrary) تصاویر را از کالکشن
        // پیش‌فرض 'default' می‌خواند؛ ذخیره در کالکشن دیگر باعث placeholder می‌شود.
        $product->addMedia($path)
            ->usingName($title)
            ->usingFileName("{$product->sku}.png")
            ->toMediaCollection('default');
    }

    /* ---------------- ابزارهای کمکی رسم ---------------- */

    private function hex(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    private function col($img, array $rgb, int $alpha = 255): int
    {
        // ورودی: 0 = کاملاً شفاف، 255 = کاملاً مات (مثل CSS) → تبدیل به مقیاس GD (0..127)
        $gd = 127 - (int) round(min(255, max(0, $alpha)) * 127 / 255);

        return imagecolorallocatealpha($img, $rgb[0], $rgb[1], $rgb[2], $gd);
    }

    /** گرادیان عمودی ساده */
    private function vGradient($img, int $size, array $top, array $bottom): void
    {
        for ($y = 0; $y < $size; $y++) {
            $t = $y / ($size - 1);
            $c = $this->col($img, [
                (int) round($top[0] + ($bottom[0] - $top[0]) * $t),
                (int) round($top[1] + ($bottom[1] - $top[1]) * $t),
                (int) round($top[2] + ($bottom[2] - $top[2]) * $t),
            ]);
            imageline($img, 0, $y, $size, $y, $c);
        }
    }

    /** دایره محو (بدون لبه سخت) با چند لایه آلفا */
    private function softCircle($img, int $cx, int $cy, int $r, array|string $rgb, int $alpha): void
    {
        $rgb = is_string($rgb) ? $this->hex($rgb) : $rgb;
        $layers = 14;
        for ($i = $layers; $i >= 1; $i--) {
            $rr = (int) round($r * $i / $layers);
            $a  = (int) round($alpha * (1 - $i / ($layers + 2)));
            if ($a <= 0) {
                continue;
            }
            imagefilledellipse($img, $cx, $cy, $rr * 2, $rr * 2, $this->col($img, $rgb, $a));
        }
    }

    /** بیضی محو برای سایه */
    private function softEllipse($img, int $cx, int $cy, int $w, int $h, array|string $rgb, int $alpha): void
    {
        $rgb = is_string($rgb) ? $this->hex($rgb) : $rgb;
        $layers = 10;
        for ($i = $layers; $i >= 1; $i--) {
            $ww = (int) round($w * $i / $layers);
            $hh = (int) round($h * $i / $layers);
            $a  = (int) round($alpha * (1 - $i / ($layers + 3)));
            if ($a <= 0) {
                continue;
            }
            imagefilledellipse($img, $cx, $cy, $ww * 2, $hh * 2, $this->col($img, $rgb, $a));
        }
    }

    /** مستطیل با گوشه گرد توپر */
    private function rrect($img, float $x1, float $y1, float $x2, float $y2, int $r, int $color): void
    {
        $x1 = (int) round($x1); $y1 = (int) round($y1);
        $x2 = (int) round($x2); $y2 = (int) round($y2);
        $r  = min($r, (int) (($x2 - $x1) / 2), (int) (($y2 - $y1) / 2));
        if ($r < 1) {
            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $color);

            return;
        }
        imagefilledrectangle($img, $x1 + $r, $y1, $x2 - $r, $y2, $color);
        imagefilledrectangle($img, $x1, $y1 + $r, $x2, $y2 - $r, $color);
        imagefilledarc($img, $x1 + $r, $y1 + $r, $r * 2, $r * 2, 180, 270, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x2 - $r, $y1 + $r, $r * 2, $r * 2, 270, 360, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x2 - $r, $y2 - $r, $r * 2, $r * 2, 0, 90, $color, IMG_ARC_PIE);
        imagefilledarc($img, $x1 + $r, $y2 - $r, $r * 2, $r * 2, 90, 180, $color, IMG_ARC_PIE);
    }

    /** خط با ضخامت */
    private function thickLine($img, float $x1, float $y1, float $x2, float $y2, int $t, int $color): void
    {
        imagesetthickness($img, $t);
        imageline($img, (int) $x1, (int) $y1, (int) $x2, (int) $y2, $color);
        imagesetthickness($img, 1);
    }

    /* ---------------- رسم انواع کالا ---------------- */

    private function drawPhone($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // بدنه
        $this->rrect($img, 350, 195, 650, 805, 66, $this->col($img, $d));
        // لبه روشن
        $this->rrect($img, 356, 201, 644, 799, 60, $this->col($img, $m));
        // صفحه
        $this->rrect($img, 372, 217, 628, 783, 48, $this->col($img, [248, 250, 255]));
        // ناچ
        imagefilledellipse($img, 500, 221, 88, 22, $this->col($img, $d));
        imagefilledellipse($img, 536, 221, 10, 10, $this->col($img, [30, 41, 82]));
        // هدر UI
        $this->rrect($img, 396, 262, 604, 300, 14, $this->col($img, $m, 60));
        // گرید اپ‌ها
        $tiles = [$m, [239, 64, 86], [15, 163, 107], [245, 166, 35], [45, 127, 184], [108, 92, 231], [18, 165, 165], [214, 51, 108], [69, 90, 100], [239, 64, 86], $d, [15, 163, 107]];
        $i = 0;
        for ($row = 0; $row < 4; $row++) {
            for ($col = 0; $col < 3; $col++) {
                $x = 400 + $col * 82;
                $y = 330 + $row * 88;
                $this->rrect($img, $x, $y, $x + 62, $y + 62, 16, $this->col($img, $tiles[$i++], 210));
            }
        }
        // داک پایین
        $this->rrect($img, 432, 738, 568, 754, 8, $this->col($img, [30, 41, 82], 70));
        // دکمه کناری
        $this->rrect($img, 648, 330, 658, 424, 5, $this->col($img, $d));
    }

    private function drawLaptop($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // کیس صفحه
        $this->rrect($img, 268, 255, 732, 566, 20, $this->col($img, $d));
        $this->rrect($img, 284, 271, 716, 544, 8, $this->col($img, [248, 250, 255]));
        // نوار بالای صفحه
        $this->rrect($img, 284, 271, 716, 312, 8, $this->col($img, $m, 220));
        // سایدبار
        imagefilledrectangle($img, 284, 316, 372, 544, $this->col($img, $m, 70));
        // خطوط محتوا
        foreach ([[392, 340, 690], [392, 372, 640], [392, 404, 668]] as $i => [$x1, $y, $x2]) {
            $this->rrect($img, $x1, $y, $x2, $y + 14, 7, $this->col($img, [30, 41, 82], 60 + $i * 20));
        }
        // کارت‌های پایین صفحه
        $this->rrect($img, 392, 448, 530, 530, 12, $this->col($img, $m, 55));
        $this->rrect($img, 550, 448, 688, 530, 12, $this->col($img, [239, 64, 86], 45));
        // بدنه کیبورد
        imagefilledpolygon($img, [228, 566, 772, 566, 806, 632, 194, 632], 4, $this->col($img, $d));
        imagefilledpolygon($img, [194, 632, 806, 632, 796, 654, 204, 654], 4, $this->col($img, $m));
        // تاچ‌پد
        $this->rrect($img, 452, 570, 548, 582, 6, $this->col($img, [255, 255, 255], 90));
    }

    private function drawHeadphone($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // هدبند
        imagesetthickness($img, 52);
        imagearc($img, 500, 490, 350, 350, 178, 362, $this->col($img, $d));
        imagesetthickness($img, 20);
        imagearc($img, 500, 490, 350, 350, 200, 340, $this->col($img, $m, 120));
        imagesetthickness($img, 1);
        // اتصال به کاپ
        imagefilledrectangle($img, 305, 460, 355, 520, $this->col($img, $d));
        imagefilledrectangle($img, 645, 460, 695, 520, $this->col($img, $d));
        // کاپ‌ها
        $this->rrect($img, 245, 480, 405, 700, 66, $this->col($img, $d));
        $this->rrect($img, 595, 480, 755, 700, 66, $this->col($img, $d));
        // روکش داخلی
        $this->rrect($img, 275, 512, 375, 668, 48, $this->col($img, $m));
        $this->rrect($img, 625, 512, 725, 668, 48, $this->col($img, $m));
        // براقی
        $this->rrect($img, 291, 528, 313, 650, 12, $this->col($img, [255, 255, 255], 80));
        $this->rrect($img, 641, 528, 663, 650, 12, $this->col($img, [255, 255, 255], 80));
    }

    private function drawWatch($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // بند بالا و پایین
        $this->rrect($img, 432, 138, 568, 372, 34, $this->col($img, $d));
        $this->rrect($img, 432, 628, 568, 862, 34, $this->col($img, $d));
        // سوراخ‌های بند
        foreach ([700, 748, 796] as $y) {
            imagefilledellipse($img, 500, $y, 16, 16, $this->col($img, [255, 255, 255], 70));
        }
        // بدنه
        $this->rrect($img, 362, 322, 638, 678, 84, $this->col($img, $d));
        $this->rrect($img, 372, 332, 628, 668, 76, $this->col($img, $m));
        // صفحه نمایش
        $this->rrect($img, 396, 356, 604, 644, 62, $this->col($img, [16, 20, 44]));
        // صفحه ساعت
        imagefilledellipse($img, 500, 500, 170, 170, $this->col($img, $m));
        imagefilledellipse($img, 500, 500, 146, 146, $this->col($img, [16, 20, 44]));
        // عقربه‌ها
        $this->thickLine($img, 500, 500, 500, 432, 10, $this->col($img, [255, 255, 255]));
        $this->thickLine($img, 500, 500, 552, 522, 8, $this->col($img, $m));
        imagefilledellipse($img, 500, 500, 18, 18, $this->col($img, [255, 255, 255]));
        // تاج کناری
        $this->rrect($img, 636, 430, 660, 494, 9, $this->col($img, $m));
    }

    private function drawJacket($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // آستین‌ها
        imagefilledpolygon($img, [388, 330, 286, 372, 268, 656, 352, 668, 388, 486], 5, $this->col($img, $d));
        imagefilledpolygon($img, [612, 330, 714, 372, 732, 656, 648, 668, 612, 486], 5, $this->col($img, $d));
        // بدنه
        imagefilledpolygon($img, [388, 330, 612, 330, 640, 486, 632, 792, 368, 792, 360, 486], 6, $this->col($img, $m));
        // یقه
        imagefilledpolygon($img, [430, 330, 500, 286, 570, 330, 500, 392], 4, $this->col($img, $d));
        // زیپ
        for ($y = 400; $y < 780; $y += 34) {
            imagefilledrectangle($img, 496, $y, 504, $y + 18, $this->col($img, [255, 255, 255], 150));
        }
        // جیب‌ها
        $this->thickLine($img, 396, 660, 452, 700, 10, $this->col($img, $d, 120));
        $this->thickLine($img, 604, 660, 548, 700, 10, $this->col($img, $d, 120));
        // درز شانه
        $this->thickLine($img, 388, 344, 286, 386, 6, $this->col($img, $d, 80));
        $this->thickLine($img, 612, 344, 714, 386, 6, $this->col($img, $d, 80));
    }

    private function drawTshirt($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // بدنه تی‌شرت
        imagefilledpolygon($img, [
            352, 348, 446, 300, 554, 300, 648, 348, 724, 442, 652, 512, 634, 452,
            634, 786, 366, 786, 366, 452, 348, 512, 276, 442,
        ], 12, $this->col($img, $m));
        // یقه
        imagefilledellipse($img, 500, 316, 118, 62, $this->col($img, $d));
        imagefilledellipse($img, 500, 312, 96, 46, $this->col($img, [250, 251, 253]));
        // درز آستین
        $this->thickLine($img, 352, 352, 380, 470, 6, $this->col($img, $d, 90));
        $this->thickLine($img, 648, 352, 620, 470, 6, $this->col($img, $d, 90));
        // طرح ساده روی سینه
        $this->rrect($img, 448, 480, 552, 556, 16, $this->col($img, [255, 255, 255], 120));
        $this->rrect($img, 468, 502, 532, 534, 8, $this->col($img, $d, 130));
        // لبه پایین
        $this->thickLine($img, 368, 764, 632, 764, 6, $this->col($img, $d, 70));
    }

    private function drawSneaker($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // رویه کفش
        imagefilledpolygon($img, [
            262, 648, 280, 540, 382, 468, 520, 438, 662, 498, 764, 598, 772, 648,
        ], 7, $this->col($img, $m));
        // پاشنه
        imagefilledpolygon($img, [262, 648, 278, 552, 340, 528, 356, 648], 4, $this->col($img, $d));
        // پنجه
        imagefilledarc($img, 700, 610, 150, 120, 200, 320, $this->col($img, $d, 110), IMG_ARC_PIE);
        // بند کفش
        foreach ([[420, 500, 480, 528], [452, 486, 516, 516], [486, 474, 550, 502]] as [$x1, $y1, $x2, $y2]) {
            $this->thickLine($img, $x1, $y1, $x2, $y2, 12, $this->col($img, [255, 255, 255], 200));
        }
        // نوار برند
        imagesetthickness($img, 16);
        imagearc($img, 520, 690, 300, 260, 210, 320, $this->col($img, [255, 255, 255], 220));
        imagesetthickness($img, 1);
        // کفی و زیره
        $this->rrect($img, 232, 644, 772, 690, 22, $this->col($img, [255, 255, 255]));
        $this->rrect($img, 232, 686, 772, 734, 24, $this->col($img, $d));
        // خطوط زیره
        foreach ([560, 610, 660] as $x) {
            $this->thickLine($img, $x, 692, $x + 26, 728, 6, $this->col($img, [255, 255, 255], 60));
        }
    }

    private function drawVacuum($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // شلنگ
        imagesetthickness($img, 30);
        imagearc($img, 470, 540, 380, 380, 190, 335, $this->col($img, $d));
        imagesetthickness($img, 1);
        // دسته
        $this->rrect($img, 626, 240, 668, 470, 18, $this->col($img, $d));
        $this->rrect($img, 636, 252, 658, 380, 10, $this->col($img, $m));
        // بدنه
        $this->rrect($img, 322, 540, 678, 756, 64, $this->col($img, $m));
        $this->rrect($img, 322, 540, 678, 600, 30, $this->col($img, $d, 90));
        // پنجره گردوغبار
        imagefilledellipse($img, 500, 668, 130, 130, $this->col($img, [255, 255, 255], 190));
        imagefilledellipse($img, 500, 668, 104, 104, $this->col($img, $m, 60));
        // چرخ‌ها
        imagefilledellipse($img, 392, 766, 76, 76, $this->col($img, $d));
        imagefilledellipse($img, 608, 766, 76, 76, $this->col($img, $d));
        imagefilledellipse($img, 392, 766, 34, 34, $this->col($img, [255, 255, 255], 110));
        imagefilledellipse($img, 608, 766, 34, 34, $this->col($img, [255, 255, 255], 110));
    }

    private function drawCookware($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // قابلمه بزرگ
        $this->rrect($img, 268, 512, 610, 760, 30, $this->col($img, $d));
        // درب
        imagefilledellipse($img, 439, 512, 360, 64, $this->col($img, $m));
        imagefilledellipse($img, 439, 470, 44, 44, $this->col($img, $d));
        // دسته‌ها
        $this->rrect($img, 200, 528, 272, 570, 20, $this->col($img, $m));
        $this->rrect($img, 606, 528, 678, 570, 20, $this->col($img, $m));
        // براقی بدنه
        $this->rrect($img, 300, 540, 336, 736, 18, $this->col($img, [255, 255, 255], 55));
        // قابلمه کوچک
        $this->rrect($img, 660, 590, 830, 760, 22, $this->col($img, $m));
        imagefilledellipse($img, 745, 592, 190, 44, $this->col($img, $d));
        $this->rrect($img, 606, 600, 664, 636, 18, $this->col($img, $d));
        // بخار
        $this->softCircle($img, 470, 400, 46, [255, 255, 255], 130);
        $this->softCircle($img, 540, 360, 30, [255, 255, 255], 110);
    }

    private function drawKettle($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // پایه
        $this->rrect($img, 330, 738, 680, 768, 14, $this->col($img, $d));
        // بدنه کتری
        imagefilledpolygon($img, [396, 430, 614, 430, 648, 730, 362, 730], 4, $this->col($img, $m));
        $this->rrect($img, 396, 424, 614, 470, 22, $this->col($img, $d));
        // درب و دستگیره
        $this->rrect($img, 440, 386, 570, 428, 16, $this->col($img, $d));
        imagefilledellipse($img, 505, 380, 40, 40, $this->col($img, $d));
        // لوله خروجی
        imagefilledpolygon($img, [398, 488, 318, 428, 300, 458, 398, 548], 4, $this->col($img, $d));
        // دسته
        imagesetthickness($img, 26);
        imagearc($img, 616, 540, 210, 210, 275, 445, $this->col($img, $d));
        imagesetthickness($img, 1);
        // پنجره آب
        $this->rrect($img, 452, 540, 536, 700, 16, $this->col($img, [255, 255, 255], 170));
        // بخار
        $this->softCircle($img, 320, 380, 36, [255, 255, 255], 130);
        $this->softCircle($img, 372, 330, 24, [255, 255, 255], 110);
    }

    private function drawCream($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // بدنه شیشه
        $this->rrect($img, 348, 470, 652, 764, 52, $this->col($img, [255, 255, 255]));
        // برچسب
        $this->rrect($img, 348, 570, 652, 690, 0, $this->col($img, $m, 60));
        $this->rrect($img, 420, 606, 580, 628, 10, $this->col($img, $d, 150));
        $this->rrect($img, 448, 642, 552, 660, 8, $this->col($img, $d, 90));
        // درب
        $this->rrect($img, 336, 408, 664, 486, 34, $this->col($img, $m));
        $this->rrect($img, 336, 408, 664, 436, 20, $this->col($img, [255, 255, 255], 60));
        // براقی
        $this->rrect($img, 386, 500, 428, 740, 20, $this->col($img, [255, 255, 255], 110));
        // گل کوچک تزئینی
        imagefilledellipse($img, 588, 520, 54, 54, $this->col($img, $m, 90));
        imagefilledellipse($img, 588, 520, 26, 26, $this->col($img, [255, 255, 255], 160));
    }

    private function drawHaircare($img, string $main, string $dark): void
    {
        $m = $this->hex($main); $d = $this->hex($dark);
        // شامپو (پمپ‌دار بلند)
        $this->rrect($img, 316, 424, 448, 764, 30, $this->col($img, [255, 255, 255]));
        $this->rrect($img, 348, 366, 404, 428, 10, $this->col($img, $d));
        $this->rrect($img, 348, 374, 442, 396, 9, $this->col($img, $d));
        $this->rrect($img, 316, 540, 448, 690, 0, $this->col($img, $m, 55));
        // ماسک (کوتاه‌تر)
        $this->rrect($img, 472, 500, 596, 764, 28, $this->col($img, $m));
        $this->rrect($img, 484, 458, 584, 504, 16, $this->col($img, $d));
        $this->rrect($img, 472, 590, 596, 700, 0, $this->col($img, [255, 255, 255], 70));
        // سرم (سرم‌دار کوچک)
        $this->rrect($img, 628, 586, 724, 764, 22, $this->col($img, [255, 255, 255]));
        $this->rrect($img, 658, 522, 694, 588, 9, $this->col($img, $d));
        imagefilledellipse($img, 676, 516, 34, 34, $this->col($img, $d));
        $this->rrect($img, 628, 646, 724, 716, 0, $this->col($img, $m, 55));
        // قطره تزئینی
        $this->softCircle($img, 500, 380, 40, [255, 255, 255], 120);
    }

    /**
     * پروموشن خوش‌آمدگویی + کد تخفیف WELCOME10 (۱۰٪ تخفیف کل سبد)
     * مدیریت بعدی از پنل: فروشگاه > پروموشن‌ها
     */
    private function seedPromotion(): void
    {
        $promotion = \Vanilo\Promotion\Models\PromotionProxy::firstOrCreate(
            ['name' => 'تخفیف خوش‌آمدگویی'],
            [
                'description'     => '۱۰٪ تخفیف برای اولین خرید با کد WELCOME10',
                'priority'        => 1,
                'is_exclusive'    => false,
                'usage_limit'     => null,
                'is_coupon_based' => true,
                'starts_at'       => now()->subDay(),
                'ends_at'         => now()->addYear(),
            ]
        );

        if (! \Vanilo\Promotion\Models\CouponProxy::findByCode('WELCOME10')) {
            \Vanilo\Promotion\Models\Coupon::create([
                'promotion_id' => $promotion->id,
                'code'         => 'WELCOME10',
                'usage_limit'  => null,
            ]);
        }

        if ($promotion->actions()->count() === 0) {
            \Vanilo\Promotion\Models\PromotionAction::create([
                'promotion_id'  => $promotion->id,
                'type'          => 'cart_percentage_discount',
                'configuration' => ['percent' => 10],
            ]);
        }
    }

    /** چند نظر نمونهٔ منتشرشده برای پر بودن ویترین */
    private function seedReviews(): void
    {
        if (\App\Models\ProductReview::count() > 0) {
            return;
        }

        $samples = [
            ['SG-A55-256', 'مهدی رضایی', 5, 'کیفیت ساخت عالیه و باتری واقعاً یک روز و نیم دوام میاره. ارسال هم سریع بود.'],
            ['SN-CH720N', 'سارا محمدی', 4, 'حذف نویزش برای این قیمت فوق‌العاده‌ست؛ فقط کیف حملش کمی کوچک است.'],
            ['NK-AZ-42', 'علی کریمی', 5, 'برای دویدن روزانه عالیه، سبک و راحته. سایز هم دقیق بود.'],
            ['BT-LF-CRM', 'نگار حسینی', 4, 'برای پوست خشک زمستون من مناسبه، بو خوبی هم داره.'],
            ['FL-TS909', 'رضا توکلی', 5, 'چای‌ساز خوبیه، قوری شیشه‌ای‌ش باکیفیته و خاموشی خودکار خیلی به کار میاد.'],
        ];

        foreach ($samples as [$sku, $name, $rating, $comment]) {
            $product = Product::where('sku', $sku)->first();

            if ($product) {
                \App\Models\ProductReview::create([
                    'product_id'  => $product->id,
                    'name'        => $name,
                    'rating'      => $rating,
                    'comment'     => $comment,
                    'is_approved' => true,
                ]);
            }
        }
    }
}
