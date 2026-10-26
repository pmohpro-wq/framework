<?php

namespace App\Support;

class Persian
{
    /** تبدیل ارقام لاتین به فارسی */
    public static function digits(string|int|float|null $value): string
    {
        return strtr((string) $value, [
            '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴',
            '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹',
        ]);
    }

    /** قالب‌بندی مبلغ به تومان با جداکننده هزارگان و ارقام فارسی */
    public static function toman(float|int|string|null $amount, bool $withUnit = true): string
    {
        $formatted = number_format((float) $amount, (fmod((float) $amount, 1.0) !== 0.0 ? 2 : 0), '٫', '٬');

        return self::digits($formatted) . ($withUnit ? ' تومان' : '');
    }
}
