# ---------------------------------------------------------------
# ایمیج اپلیکیشن فروشگاه (PHP-FPM 8.4 + Nginx در همان ایمیج)
# Build context = ریشه ریپو (برای دسترسی path-repo به فریمورک)
# ---------------------------------------------------------------
FROM php:8.4-fpm-alpine

# اکستنشن‌های موردنیاز لاراول + وانیلو + مدیا لایبرری
RUN apk add --no-cache \
        nginx \
        bash \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        bcmath \
        gd \
        intl \
        zip \
        exif \
        opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# تنظیمات PHP
COPY store/docker/php.ini /usr/local/etc/php/conf.d/zz-store.ini

WORKDIR /var/www/html

# کل ریپو کپی می‌شود تا path-repo (../) در store/composer.json کار کند
COPY . /var/www/html

WORKDIR /var/www/html/store

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-scripts \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod +x docker/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/var/www/html/store/docker/entrypoint.sh"]
CMD ["php-fpm"]
