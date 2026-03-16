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
