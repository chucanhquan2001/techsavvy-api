# ----------- STAGE 1: Build environment -----------
FROM php:8.3-cli-alpine AS build

WORKDIR /var/www

RUN apk add --no-cache \
    bash git zip unzip curl autoconf g++ make linux-headers \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    oniguruma-dev libzip-dev openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath gd pcntl \
    && pecl install swoole \
    && docker-php-ext-enable swoole

COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . .
RUN composer install --no-dev --optimize-autoloader


# ----------- STAGE 2: Runtime environment -----------
FROM php:8.3-cli-alpine
WORKDIR /var/www

COPY --from=build /var/www /var/www

RUN apk add --no-cache bash libzip libpng oniguruma openssl \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath \
    && pecl install swoole \
    && docker-php-ext-enable swoole

EXPOSE 8000

CMD ["php", "vendor/bin/octane", "start", "--server=swoole", "--host=0.0.0.0", "--port=8000", "--no-interaction"]
