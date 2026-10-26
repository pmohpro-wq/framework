<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * پشت پروکسی پلتفرم، هدر Host دامنهٔ داخلی است و سرور هرگز
 * دامنهٔ عمومی را نمی‌بیند؛ بنابراین لینک‌های مطلقی که لاراول
 * با Host درخواست می‌سازد از مرورگر قابل دسترس نیستند.
 *
 * این میدل‌ور همهٔ URLهای مطلقِ متعلق به همین اپ (بر اساس Host درخواست)
 * را به لینک نسبی از ریشه تبدیل می‌کند — در بدنهٔ HTML/JSON و هدر Location.
 * URLهای خارجی (مثل درگاه پرداخت) دست‌نخورده باقی می‌مانند.
 */
class MakeUrlsRelative
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $host = $request->getHost();
        if (! $host) {
            return $response;
        }

        // پورت غیراستاندارد (مثل 3000 در artisan serve) هم بخشی از URL مطلق است
        $port    = $request->getPort();
        $hasPort = $port && ! in_array($port, [80, 443], true);

        // ترتیب مهم است: ابتدا پیشوند طولانی‌تر (با پورت)
        $absolutePrefixes = array_values(array_unique(array_filter([
            'http://'  . $host . ($hasPort ? ':' . $port : ''),
            'https://' . $host . ($hasPort ? ':' . $port : ''),
            'http://'  . $host,
            'https://' . $host,
        ])));

        // ۱) هدر Location ریدایرکت‌ها — فقط اگر متعلق به همین اپ باشد
        if ($response->headers->has('Location')) {
            $loc = (string) $response->headers->get('Location');
            foreach ($absolutePrefixes as $prefix) {
                if (str_starts_with($loc, $prefix)) {
                    $response->headers->set('Location', substr($loc, strlen($prefix)) ?: '/');
                    break;
                }
            }
        }

        // ۲) بدنهٔ پاسخ (HTML/JSON) — فقط پیشوند URLهای همین اپ
        $type = (string) $response->headers->get('Content-Type');
        if (preg_match('~text/html|application/json~i', $type)) {
            $content = $response->getContent();
            if ($content !== false && str_contains($content, $host)) {
                $response->setContent(str_replace($absolutePrefixes, '', $content));
            }
        }

        return $response;
    }
}
