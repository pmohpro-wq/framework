<?php

namespace App\Support\Illustration;

/**
 * ابزارهای پایهٔ رسم روی تصویر GD.
 *
 * تمام متدها استاتیک و بدون وضعیت‌اند؛ برای رسم هر نوع کالا
 * ProductIllustrator از این ابزارها استفاده می‌کند.
 */
final class GdCanvas
{
    private function __construct()
    {
    }

    /** تبدیل رشتهٔ هگز (#RGB یا #RRGGBB) به آرایهٔ RGB */
    public static function hex(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /** ساخت رنگ با آلفا — ورودی 0 = شفاف، 255 = مات (مثل CSS)؛ خروجی مقیاس GD */
    public static function col($img, array $rgb, int $alpha = 255): int
    {
        $gd = 127 - (int) round(min(255, max(0, $alpha)) * 127 / 255);

        return imagecolorallocatealpha($img, $rgb[0], $rgb[1], $rgb[2], $gd);
    }

    /** گرادیان عمودی ساده بین دو رنگ */
    public static function vGradient($img, int $size, array $top, array $bottom): void
    {
        for ($y = 0; $y < $size; $y++) {
            $t = $y / ($size - 1);
            $c = self::col($img, [
                (int) round($top[0] + ($bottom[0] - $top[0]) * $t),
                (int) round($top[1] + ($bottom[1] - $top[1]) * $t),
                (int) round($top[2] + ($bottom[2] - $top[2]) * $t),
            ]);
            imageline($img, 0, $y, $size, $y, $c);
        }
    }

    /** دایره محو (بدون لبهٔ سخت) با چند لایهٔ آلفا */
    public static function softCircle($img, int $cx, int $cy, int $r, array|string $rgb, int $alpha): void
    {
        $rgb = is_string($rgb) ? self::hex($rgb) : $rgb;
        $layers = 14;

        for ($i = $layers; $i >= 1; $i--) {
            $rr = (int) round($r * $i / $layers);
            $a  = (int) round($alpha * (1 - $i / ($layers + 2)));

            if ($a <= 0) {
                continue;
            }

            imagefilledellipse($img, $cx, $cy, $rr * 2, $rr * 2, self::col($img, $rgb, $a));
        }
    }

    /** بیضی محو — مناسب سایهٔ زمین */
    public static function softEllipse($img, int $cx, int $cy, int $w, int $h, array|string $rgb, int $alpha): void
    {
        $rgb = is_string($rgb) ? self::hex($rgb) : $rgb;
        $layers = 10;

        for ($i = $layers; $i >= 1; $i--) {
            $ww = (int) round($w * $i / $layers);
            $hh = (int) round($h * $i / $layers);
            $a  = (int) round($alpha * (1 - $i / ($layers + 3)));

            if ($a <= 0) {
                continue;
            }

            imagefilledellipse($img, $cx, $cy, $ww * 2, $hh * 2, self::col($img, $rgb, $a));
        }
    }

    /** مستطیل توپر با گوشهٔ گرد */
    public static function rrect($img, float $x1, float $y1, float $x2, float $y2, int $r, int $color): void
    {
        $x1 = (int) round($x1);
        $y1 = (int) round($y1);
        $x2 = (int) round($x2);
        $y2 = (int) round($y2);
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

    /** خط با ضخامت مشخص */
    public static function thickLine($img, float $x1, float $y1, float $x2, float $y2, int $t, int $color): void
    {
        imagesetthickness($img, $t);
        imageline($img, (int) $x1, (int) $y1, (int) $x2, (int) $y2, $color);
        imagesetthickness($img, 1);
    }
}
