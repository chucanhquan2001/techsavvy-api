FROM php:8.4-cli-alpine AS build

WORKDIR /var/www

# cài extension ít thay đổi
RUN apk add --no-cache git unzip libzip-dev libpng-dev oniguruma-dev autoconf g++ make openssl-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath \
    && pecl install swoole \
    && docker-php-ext-enable swoole

COPY composer.json composer.lock ./

RUN composer install --no-dev --optimize-autoloader

COPY . .

FROM php:8.4-cli-alpine
WORKDIR /var/www
RUN apk add --no-cache bash libzip libpng oniguruma openssl \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath \
    && pecl install swoole \
    && docker-php-ext-enable swoole

COPY --from=build /var/www /var/www
EXPOSE 8000
CMD php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000