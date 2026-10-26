#!/bin/bash
set -e

cd /var/www/html/store

# کانتینر nginx بدون راه‌اندازی اولیه مستقیم اجرا می‌شود
if [ "$1" = "nginx" ]; then
    exec nginx -g "daemon off;"
fi

# ---------- ساخت .env از متغیرهای محیطی (فقط بار اول) ----------
if [ ! -f .env ]; then
    cat > .env <<ENV
APP_NAME="${APP_NAME:-فروشگاه من}"
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost:8000}
ASSET_URL=/
APP_LOCALE=fa
APP_FALLBACK_LOCALE=fa
APP_TIMEZONE=Asia/Tehran

LOG_CHANNEL=stack

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-store}
DB_USERNAME=${DB_USERNAME:-store}
DB_PASSWORD=${DB_PASSWORD:-store_secret}

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public

IRANIAN_GATEWAY_MERCHANT_ID=${IRANIAN_GATEWAY_MERCHANT_ID:-}
IRANIAN_GATEWAY_URL=
ZARINPAL_MERCHANT_ID=${IRANIAN_GATEWAY_URL:-}
ENV
    echo "[entrypoint] .env ساخته شد"
fi

# کلید اپلیکیشن
if ! grep -q "^APP_KEY=base64" .env; then
    php artisan key:generate --force
    echo "[entrypoint] APP_KEY تولید شد"
fi

# ---------- انتظار برای دیتابیس ----------
echo "[entrypoint] در انتظار دیتابیس..."
for i in $(seq 1 30); do
    if php -r '
        try {
            new PDO(
                sprintf("mysql:host=%s;port=%s;dbname=%s", getenv("DB_HOST"), getenv("DB_PORT") ?: "3306", getenv("DB_DATABASE")),
                getenv("DB_USERNAME"),
                getenv("DB_PASSWORD"),
                [PDO::ATTR_TIMEOUT => 3]
            );
            exit(0);
        } catch (Throwable $e) {
            exit(1);
        }
    ' 2>/dev/null; then
        echo "[entrypoint] دیتابیس آماده است"
        break
    fi
    [ "$i" = "30" ] && { echo "[entrypoint] دیتابیس پاسخگو نبود"; exit 1; }
    sleep 2
done

# ---------- مایگریشن و داده اولیه (idempotent) ----------
php artisan migrate --force
php artisan db:seed --force

# کش‌ها و لینک storage
php artisan package:discover --ansi || true
php artisan vendor:publish --tag=laravel-assets --force --no-interaction || true
php artisan storage:link --force || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[entrypoint] راه‌اندازی کامل شد: $*"
exec "$@"
