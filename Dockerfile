FROM phpswoole/swoole:php8.3-alpine AS swoole

# -------- STAGE 1: Build --------
FROM php:8.3-cli-alpine AS build
WORKDIR /var/www

RUN apk add --no-cache \
    bash git zip unzip curl \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    oniguruma-dev libzip-dev openssl-dev \
    autoconf g++ make linux-headers

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath gd pcntl

COPY --from=swoole /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=swoole /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --prefer-dist --no-scripts

COPY . .
RUN composer dump-autoload --optimize


# ----------- STAGE 2: Runtime environment -----------
FROM php:8.3-cli-alpine
WORKDIR /var/www

RUN apk add --no-cache \
    bash libpng libjpeg-turbo freetype \
    libzip oniguruma openssl libstdc++

COPY --from=build /var/www /var/www
COPY --from=build /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=build /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]
