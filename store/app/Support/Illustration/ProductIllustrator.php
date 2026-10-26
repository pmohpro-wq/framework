<?php

namespace App\Support\Illustration;

use Vanilo\Foundation\Models\Product;

/**
 * موتور تولید تصویر اختصاصی محصولات (۱۰۰۰×۱۰۰۰) با GD.
 *
 * برای هر نوع کالا یک رسم پارامتری دارد؛ بدون متن و بدون نیاز به فونت.
 * در Seeder و هر جای دیگری که محصول جدیدی بدون تصویر ساخته می‌شود استفاده می‌شود.
 *
 * نکتهٔ مهم: تصویر باید در کالکشن 'default' ذخیره شود چون trait
 * HasImagesFromMediaLibrary وانیلو تصاویر را از همان کالکشن می‌خواند.
 */
class ProductIllustrator
{
    private const SIZE = 1000;

    /** پالت هر نوع: [بالای پس‌زمینه، پایین پس‌زمینه، رنگ اصلی کالا، رنگ تیرهٔ کالا] */
    private const PALETTES = [
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

    /** نگاشت SKU به نوع تصویر */
    public static function typeForSku(string $sku): string
    {
        return match ($sku) {
            'SG-A55-256' => 'phone',
            'SN-CH720N'  => 'headphone',
            'AS-VB15-I5' => 'laptop',
            'XM-RW5'     => 'watch',
            'CL-ATL-M-NV' => 'jacket',
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

    /** رندر تصویر یک نوع کالا و بازگرداندن هندل GD */
    public static function render(string $type)
    {
        $img = imagecreatetruecolor(self::SIZE, self::SIZE);
        imagealphablending($img, true);

        [$bgTop, $bgBot, $main, $dark] = self::PALETTES[$type] ?? self::PALETTES['phone'];

        // ۱) پس‌زمینهٔ گرادیان عمودی
        GdCanvas::vGradient($img, self::SIZE, GdCanvas::hex($bgTop), GdCanvas::hex($bgBot));

        // ۲) دایره‌های تزئینی محو
        GdCanvas::softCircle($img, 130, 120, 240, GdCanvas::hex($main), 26);
        GdCanvas::softCircle($img, 900, 880, 300, GdCanvas::hex($main), 22);
        GdCanvas::softCircle($img, 880, 140, 110, '#FFFFFF', 60);

        // ۳) هالهٔ پشت محصول
        GdCanvas::softCircle($img, 500, 470, 315, '#FFFFFF', 72);

        // ۴) سایهٔ زمین
        GdCanvas::softEllipse($img, 500, 812, 300, 42, GdCanvas::hex($dark), 50);

        // ۵) رسم خود کالا بر اساس نوع
        (new self())->draw($img, $type, $main, $dark);

        return $img;
    }

    /**
     * رندر + اتصال تصویر به محصول در مدیا لایبرری (فقط اگر محصول تصویر ندارد).
     */
    public static function attachToProduct(Product $product, ?string $type = null, ?string $title = null): void
    {
        if ($product->hasImage()) {
            return;
        }

        $img  = self::render($type ?? self::typeForSku($product->sku));
        $path = storage_path("app/seed-{$product->sku}.png");
        imagepng($img, $path);
        imagedestroy($img);

        $product->addMedia($path)
            ->usingName($title ?? $product->name)
            ->usingFileName("{$product->sku}.png")
            ->toMediaCollection('default');
    }

    // ------------------------------------------------------------------

    private function draw($img, string $type, string $main, string $dark): void
    {
        match ($type) {
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
    }

    private function drawPhone($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // بدنه
        GdCanvas::rrect($img, 350, 195, 650, 805, 66, GdCanvas::col($img, $d));
        // لبهٔ روشن
        GdCanvas::rrect($img, 356, 201, 644, 799, 60, GdCanvas::col($img, $m));
        // صفحه
        GdCanvas::rrect($img, 372, 217, 628, 783, 48, GdCanvas::col($img, [248, 250, 255]));
        // ناچ
        imagefilledellipse($img, 500, 221, 88, 22, GdCanvas::col($img, $d));
        imagefilledellipse($img, 536, 221, 10, 10, GdCanvas::col($img, [30, 41, 82]));
        // هدر UI
        GdCanvas::rrect($img, 396, 262, 604, 300, 14, GdCanvas::col($img, $m, 60));
        // گرید اپ‌ها
        $tiles = [$m, [239, 64, 86], [15, 163, 107], [245, 166, 35], [45, 127, 184], [108, 92, 231], [18, 165, 165], [214, 51, 108], [69, 90, 100], [239, 64, 86], $d, [15, 163, 107]];
        $i = 0;
        for ($row = 0; $row < 4; $row++) {
            for ($col = 0; $col < 3; $col++) {
                $x = 400 + $col * 82;
                $y = 330 + $row * 88;
                GdCanvas::rrect($img, $x, $y, $x + 62, $y + 62, 16, GdCanvas::col($img, $tiles[$i++], 210));
            }
        }
        // داک پایین
        GdCanvas::rrect($img, 432, 738, 568, 754, 8, GdCanvas::col($img, [30, 41, 82], 70));
        // دکمهٔ کناری
        GdCanvas::rrect($img, 648, 330, 658, 424, 5, GdCanvas::col($img, $d));
    }

    private function drawLaptop($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // کیس صفحه
        GdCanvas::rrect($img, 268, 255, 732, 566, 20, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 284, 271, 716, 544, 8, GdCanvas::col($img, [248, 250, 255]));
        // نوار بالای صفحه
        GdCanvas::rrect($img, 284, 271, 716, 312, 8, GdCanvas::col($img, $m, 220));
        // سایدبار
        imagefilledrectangle($img, 284, 316, 372, 544, GdCanvas::col($img, $m, 70));
        // خطوط محتوا
        foreach ([[392, 340, 690], [392, 372, 640], [392, 404, 668]] as $i => [$x1, $y, $x2]) {
            GdCanvas::rrect($img, $x1, $y, $x2, $y + 14, 7, GdCanvas::col($img, [30, 41, 82], 60 + $i * 20));
        }
        // کارت‌های پایین صفحه
        GdCanvas::rrect($img, 392, 448, 530, 530, 12, GdCanvas::col($img, $m, 55));
        GdCanvas::rrect($img, 550, 448, 688, 530, 12, GdCanvas::col($img, [239, 64, 86], 45));
        // بدنهٔ کیبورد
        imagefilledpolygon($img, [228, 566, 772, 566, 806, 632, 194, 632], 4, GdCanvas::col($img, $d));
        imagefilledpolygon($img, [194, 632, 806, 632, 796, 654, 204, 654], 4, GdCanvas::col($img, $m));
        // تاچ‌پد
        GdCanvas::rrect($img, 452, 570, 548, 582, 6, GdCanvas::col($img, [255, 255, 255], 90));
    }

    private function drawHeadphone($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // هدبند
        imagesetthickness($img, 52);
        imagearc($img, 500, 490, 350, 350, 178, 362, GdCanvas::col($img, $d));
        imagesetthickness($img, 20);
        imagearc($img, 500, 490, 350, 350, 200, 340, GdCanvas::col($img, $m, 120));
        imagesetthickness($img, 1);
        // اتصال به کاپ
        imagefilledrectangle($img, 305, 460, 355, 520, GdCanvas::col($img, $d));
        imagefilledrectangle($img, 645, 460, 695, 520, GdCanvas::col($img, $d));
        // کاپ‌ها
        GdCanvas::rrect($img, 245, 480, 405, 700, 66, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 595, 480, 755, 700, 66, GdCanvas::col($img, $d));
        // روکش داخلی
        GdCanvas::rrect($img, 275, 512, 375, 668, 48, GdCanvas::col($img, $m));
        GdCanvas::rrect($img, 625, 512, 725, 668, 48, GdCanvas::col($img, $m));
        // براقی
        GdCanvas::rrect($img, 291, 528, 313, 650, 12, GdCanvas::col($img, [255, 255, 255], 80));
        GdCanvas::rrect($img, 641, 528, 663, 650, 12, GdCanvas::col($img, [255, 255, 255], 80));
    }

    private function drawWatch($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // بند بالا و پایین
        GdCanvas::rrect($img, 432, 138, 568, 372, 34, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 432, 628, 568, 862, 34, GdCanvas::col($img, $d));
        // سوراخ‌های بند
        foreach ([700, 748, 796] as $y) {
            imagefilledellipse($img, 500, $y, 16, 16, GdCanvas::col($img, [255, 255, 255], 70));
        }
        // بدنه
        GdCanvas::rrect($img, 362, 322, 638, 678, 84, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 372, 332, 628, 668, 76, GdCanvas::col($img, $m));
        // صفحه نمایش
        GdCanvas::rrect($img, 396, 356, 604, 644, 62, GdCanvas::col($img, [16, 20, 44]));
        // صفحهٔ ساعت
        imagefilledellipse($img, 500, 500, 170, 170, GdCanvas::col($img, $m));
        imagefilledellipse($img, 500, 500, 146, 146, GdCanvas::col($img, [16, 20, 44]));
        // عقربه‌ها
        GdCanvas::thickLine($img, 500, 500, 500, 432, 10, GdCanvas::col($img, [255, 255, 255]));
        GdCanvas::thickLine($img, 500, 500, 552, 522, 8, GdCanvas::col($img, $m));
        imagefilledellipse($img, 500, 500, 18, 18, GdCanvas::col($img, [255, 255, 255]));
        // تاج کناری
        GdCanvas::rrect($img, 636, 430, 660, 494, 9, GdCanvas::col($img, $m));
    }

    private function drawJacket($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // آستین‌ها
        imagefilledpolygon($img, [388, 330, 286, 372, 268, 656, 352, 668, 388, 486], 5, GdCanvas::col($img, $d));
        imagefilledpolygon($img, [612, 330, 714, 372, 732, 656, 648, 668, 612, 486], 5, GdCanvas::col($img, $d));
        // بدنه
        imagefilledpolygon($img, [388, 330, 612, 330, 640, 486, 632, 792, 368, 792, 360, 486], 6, GdCanvas::col($img, $m));
        // یقه
        imagefilledpolygon($img, [430, 330, 500, 286, 570, 330, 500, 392], 4, GdCanvas::col($img, $d));
        // زیپ
        for ($y = 400; $y < 780; $y += 34) {
            imagefilledrectangle($img, 496, $y, 504, $y + 18, GdCanvas::col($img, [255, 255, 255], 150));
        }
        // جیب‌ها
        GdCanvas::thickLine($img, 396, 660, 452, 700, 10, GdCanvas::col($img, $d, 120));
        GdCanvas::thickLine($img, 604, 660, 548, 700, 10, GdCanvas::col($img, $d, 120));
        // درز شانه
        GdCanvas::thickLine($img, 388, 344, 286, 386, 6, GdCanvas::col($img, $d, 80));
        GdCanvas::thickLine($img, 612, 344, 714, 386, 6, GdCanvas::col($img, $d, 80));
    }

    private function drawTshirt($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // بدنهٔ تی‌شرت
        imagefilledpolygon($img, [
            352, 348, 446, 300, 554, 300, 648, 348, 724, 442, 652, 512, 634, 452,
            634, 786, 366, 786, 366, 452, 348, 512, 276, 442,
        ], 12, GdCanvas::col($img, $m));
        // یقه
        imagefilledellipse($img, 500, 316, 118, 62, GdCanvas::col($img, $d));
        imagefilledellipse($img, 500, 312, 96, 46, GdCanvas::col($img, [250, 251, 253]));
        // درز آستین
        GdCanvas::thickLine($img, 352, 352, 380, 470, 6, GdCanvas::col($img, $d, 90));
        GdCanvas::thickLine($img, 648, 352, 620, 470, 6, GdCanvas::col($img, $d, 90));
        // طرح ساده روی سینه
        GdCanvas::rrect($img, 448, 480, 552, 556, 16, GdCanvas::col($img, [255, 255, 255], 120));
        GdCanvas::rrect($img, 468, 502, 532, 534, 8, GdCanvas::col($img, $d, 130));
        // لبهٔ پایین
        GdCanvas::thickLine($img, 368, 764, 632, 764, 6, GdCanvas::col($img, $d, 70));
    }

    private function drawSneaker($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // رویهٔ کفش
        imagefilledpolygon($img, [
            262, 648, 280, 540, 382, 468, 520, 438, 662, 498, 764, 598, 772, 648,
        ], 7, GdCanvas::col($img, $m));
        // پاشنه
        imagefilledpolygon($img, [262, 648, 278, 552, 340, 528, 356, 648], 4, GdCanvas::col($img, $d));
        // پنجه
        imagefilledarc($img, 700, 610, 150, 120, 200, 320, GdCanvas::col($img, $d, 110), IMG_ARC_PIE);
        // بند کفش
        foreach ([[420, 500, 480, 528], [452, 486, 516, 516], [486, 474, 550, 502]] as [$x1, $y1, $x2, $y2]) {
            GdCanvas::thickLine($img, $x1, $y1, $x2, $y2, 12, GdCanvas::col($img, [255, 255, 255], 200));
        }
        // نوار برند
        imagesetthickness($img, 16);
        imagearc($img, 520, 690, 300, 260, 210, 320, GdCanvas::col($img, [255, 255, 255], 220));
        imagesetthickness($img, 1);
        // کفی و زیره
        GdCanvas::rrect($img, 232, 644, 772, 690, 22, GdCanvas::col($img, [255, 255, 255]));
        GdCanvas::rrect($img, 232, 686, 772, 734, 24, GdCanvas::col($img, $d));
        // خطوط زیره
        foreach ([560, 610, 660] as $x) {
            GdCanvas::thickLine($img, $x, 692, $x + 26, 728, 6, GdCanvas::col($img, [255, 255, 255], 60));
        }
    }

    private function drawVacuum($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // شلنگ
        imagesetthickness($img, 30);
        imagearc($img, 470, 540, 380, 380, 190, 335, GdCanvas::col($img, $d));
        imagesetthickness($img, 1);
        // دسته
        GdCanvas::rrect($img, 626, 240, 668, 470, 18, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 636, 252, 658, 380, 10, GdCanvas::col($img, $m));
        // بدنه
        GdCanvas::rrect($img, 322, 540, 678, 756, 64, GdCanvas::col($img, $m));
        GdCanvas::rrect($img, 322, 540, 678, 600, 30, GdCanvas::col($img, $d, 90));
        // پنجرهٔ گردوغبار
        imagefilledellipse($img, 500, 668, 130, 130, GdCanvas::col($img, [255, 255, 255], 190));
        imagefilledellipse($img, 500, 668, 104, 104, GdCanvas::col($img, $m, 60));
        // چرخ‌ها
        imagefilledellipse($img, 392, 766, 76, 76, GdCanvas::col($img, $d));
        imagefilledellipse($img, 608, 766, 76, 76, GdCanvas::col($img, $d));
        imagefilledellipse($img, 392, 766, 34, 34, GdCanvas::col($img, [255, 255, 255], 110));
        imagefilledellipse($img, 608, 766, 34, 34, GdCanvas::col($img, [255, 255, 255], 110));
    }

    private function drawCookware($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // قابلمهٔ بزرگ
        GdCanvas::rrect($img, 268, 512, 610, 760, 30, GdCanvas::col($img, $d));
        // درب
        imagefilledellipse($img, 439, 512, 360, 64, GdCanvas::col($img, $m));
        imagefilledellipse($img, 439, 470, 44, 44, GdCanvas::col($img, $d));
        // دسته‌ها
        GdCanvas::rrect($img, 200, 528, 272, 570, 20, GdCanvas::col($img, $m));
        GdCanvas::rrect($img, 606, 528, 678, 570, 20, GdCanvas::col($img, $m));
        // براقی بدنه
        GdCanvas::rrect($img, 300, 540, 336, 736, 18, GdCanvas::col($img, [255, 255, 255], 55));
        // قابلمهٔ کوچک
        GdCanvas::rrect($img, 660, 590, 830, 760, 22, GdCanvas::col($img, $m));
        imagefilledellipse($img, 745, 592, 190, 44, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 606, 600, 664, 636, 18, GdCanvas::col($img, $d));
        // بخار
        GdCanvas::softCircle($img, 470, 400, 46, [255, 255, 255], 130);
        GdCanvas::softCircle($img, 540, 360, 30, [255, 255, 255], 110);
    }

    private function drawKettle($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // پایه
        GdCanvas::rrect($img, 330, 738, 680, 768, 14, GdCanvas::col($img, $d));
        // بدنهٔ کتری
        imagefilledpolygon($img, [396, 430, 614, 430, 648, 730, 362, 730], 4, GdCanvas::col($img, $m));
        GdCanvas::rrect($img, 396, 424, 614, 470, 22, GdCanvas::col($img, $d));
        // درب و دستگیره
        GdCanvas::rrect($img, 440, 386, 570, 428, 16, GdCanvas::col($img, $d));
        imagefilledellipse($img, 505, 380, 40, 40, GdCanvas::col($img, $d));
        // لولهٔ خروجی
        imagefilledpolygon($img, [398, 488, 318, 428, 300, 458, 398, 548], 4, GdCanvas::col($img, $d));
        // دسته
        imagesetthickness($img, 26);
        imagearc($img, 616, 540, 210, 210, 275, 445, GdCanvas::col($img, $d));
        imagesetthickness($img, 1);
        // پنجرهٔ آب
        GdCanvas::rrect($img, 452, 540, 536, 700, 16, GdCanvas::col($img, [255, 255, 255], 170));
        // بخار
        GdCanvas::softCircle($img, 320, 380, 36, [255, 255, 255], 130);
        GdCanvas::softCircle($img, 372, 330, 24, [255, 255, 255], 110);
    }

    private function drawCream($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // بدنهٔ شیشه
        GdCanvas::rrect($img, 348, 470, 652, 764, 52, GdCanvas::col($img, [255, 255, 255]));
        // برچسب
        GdCanvas::rrect($img, 348, 570, 652, 690, 0, GdCanvas::col($img, $m, 60));
        GdCanvas::rrect($img, 420, 606, 580, 628, 10, GdCanvas::col($img, $d, 150));
        GdCanvas::rrect($img, 448, 642, 552, 660, 8, GdCanvas::col($img, $d, 90));
        // درب
        GdCanvas::rrect($img, 336, 408, 664, 486, 34, GdCanvas::col($img, $m));
        GdCanvas::rrect($img, 336, 408, 664, 436, 20, GdCanvas::col($img, [255, 255, 255], 60));
        // براقی
        GdCanvas::rrect($img, 386, 500, 428, 740, 20, GdCanvas::col($img, [255, 255, 255], 110));
        // گل کوچک تزئینی
        imagefilledellipse($img, 588, 520, 54, 54, GdCanvas::col($img, $m, 90));
        imagefilledellipse($img, 588, 520, 26, 26, GdCanvas::col($img, [255, 255, 255], 160));
    }

    private function drawHaircare($img, string $main, string $dark): void
    {
        $m = GdCanvas::hex($main);
        $d = GdCanvas::hex($dark);

        // شامپو (پمپ‌دار بلند)
        GdCanvas::rrect($img, 316, 424, 448, 764, 30, GdCanvas::col($img, [255, 255, 255]));
        GdCanvas::rrect($img, 348, 366, 404, 428, 10, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 348, 374, 442, 396, 9, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 316, 540, 448, 690, 0, GdCanvas::col($img, $m, 55));
        // ماسک (کوتاه‌تر)
        GdCanvas::rrect($img, 472, 500, 596, 764, 28, GdCanvas::col($img, $m));
        GdCanvas::rrect($img, 484, 458, 584, 504, 16, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 472, 590, 596, 700, 0, GdCanvas::col($img, [255, 255, 255], 70));
        // سرم (سرم‌دار کوچک)
        GdCanvas::rrect($img, 628, 586, 724, 764, 22, GdCanvas::col($img, [255, 255, 255]));
        GdCanvas::rrect($img, 658, 522, 694, 588, 9, GdCanvas::col($img, $d));
        imagefilledellipse($img, 676, 516, 34, 34, GdCanvas::col($img, $d));
        GdCanvas::rrect($img, 628, 646, 724, 716, 0, GdCanvas::col($img, $m, 55));
        // قطرهٔ تزئینی
        GdCanvas::softCircle($img, 500, 380, 40, [255, 255, 255], 120);
    }
}
