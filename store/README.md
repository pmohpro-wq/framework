# فروشگاه فارسی (Vanilo Store)

فروشگاه کامل مبتنی بر **فریمورک وانیلو 6** و **لاراول 12** با رابط فارسی (RTL)،
واحد پول تومان و استقرار روی Docker.

> کد فریمورک (فورک وانیلو) در ریشه همین ریپو است و اپ فروشگاه در پوشه `store/`
> از طریق composer path-repository به آن متصل می‌شود.

## امکانات

- ویترین فروشگاه فارسی: صفحه اصلی، دسته‌بندی، صفحه محصول، سبد خرید
- فرآیند تسویه‌حساب با آدرس، روش ارسال و روش پرداخت
- پنل مدیریت Vanilo Admin (`/admin`) برای مدیریت محصولات، سفارش‌ها، تخفیف‌ها و…
- درگاه پرداخت آنلاین (اسکلت ایرانی + شبیه‌ساز داخلی) و «پرداخت در محل»
- ارقام و قیمت‌ها به فارسی/تومان، فونت وزیرمتن
- داده نمونه: ۱۲ محصول در ۴ دسته‌بندی + دو روش ارسال

## استقرار با Docker

```bash
# 1) (اختیاری) تنظیم مقادیر — کپی از نمونه
cp .env.example .env

# 2) ساخت و اجرا
docker compose up -d --build

# 3) مشاهده لاگ راه‌اندازی
docker compose logs -f app
```

پس از بالا آمدن سرویس‌ها:

| بخش | آدرس |
|---|---|
| ویترین فروشگاه | `http://localhost:8000` |
| پنل مدیریت | `http://localhost:8000/admin` |
| ورود مدیر | `admin@example.com` / `admin1234` (قابل تغییر با `ADMIN_PASSWORD`) |

پورت، نام دیتابیس و گذرواژه‌ها را می‌توانید در فایل `.env` ریشه (کنار docker-compose.yml) تنظیم کنید.

## ساختار سرویس‌ها

| سرویس | تصویر | نقش |
|---|---|---|
| `web` | ایمیج سفارشی (nginx داخل همان ایمیج) | وب‌سرور، پورت 8000 |
| `app` | php:8.4-fpm-alpine + اکستنشن‌ها | اجرای PHP، مایگریشن و seed خودکار |
| `db` | mysql:8.4 | دیتابیس با دیتای فارسی (utf8mb4) |

اسکریپت ورود (`store/docker/entrypoint.sh`) به‌صورت خودکار: فایل `.env` می‌سازد،
`APP_KEY` تولید می‌کند، منتظر دیتابیس می‌ماند، مایگریشن و seed (idempotent) را اجرا می‌کند
و کش‌ها را می‌سازد.

## توسعه محلی (بدون داکر)

```bash
cd store
composer install
cp .env.example .env   # یا ساخت دستی با DB_CONNECTION=sqlite
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve      # http://localhost:8000
```

## اتصال درگاه پرداخت واقعی (مثلاً زرین‌پال)

اسکلت درگاه در `store/app/Payment/Gateways/IranianGateway.php` است. مراحل:

1. متغیرهای `IRANIAN_GATEWAY_MERCHANT_ID` و `IRANIAN_GATEWAY_URL` را در `.env` تنظیم کنید.
2. در `createPaymentRequest` به‌جای آدرس شبیه‌ساز، درخواست به سرویس بانک بزنید و
   `authority` را به‌عنوان `remote_id` ذخیره کنید.
3. در `processPaymentResponse` پاسخ بازگشتی بانک را verify کنید.

## نکات

- تغییرات هر ریپو (فورک وانیلو یا اپ store) با push به شاخه `master` به‌صورت
  خودکار روی سرور Docker مستقر می‌شود.
- برای افزودن محصول جدید: پنل مدیریت → محصولات → ایجاد.
- داده نمونه فقط بار اول seed می‌شود و اجرای مجدد مشکلی ایجاد نمی‌کند.
