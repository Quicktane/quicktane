ARG PHP_BASE_IMAGE=ghcr.io/quicktane/quicktane/php-base:latest

# -------------------------------------------------------------------
# Stage: composer dependencies
# -------------------------------------------------------------------
FROM ${PHP_BASE_IMAGE} AS vendor

COPY composer.json composer.lock ./
COPY packages/ packages/

RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# -------------------------------------------------------------------
# Stage: production image
# -------------------------------------------------------------------
FROM ${PHP_BASE_IMAGE} AS production

COPY --from=vendor /app/vendor /app/vendor

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi \
    && php artisan view:cache

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8000

ENTRYPOINT ["php", "artisan", "octane:start", "--server=frankenphp", "--host=0.0.0.0", "--port=8000"]
