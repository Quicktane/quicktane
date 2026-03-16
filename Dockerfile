FROM dunglas/frankenphp:latest

RUN install-php-extensions \
    pdo_mysql \
    redis \
    pcntl \
    intl \
    gd \
    zip \
    opcache \
    bcmath

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
COPY packages/ packages/
RUN composer install --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --optimize
RUN php artisan package:discover --ansi

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
