# ----------- STAGE 1: Build environment -----------
FROM php:8.3-fpm-alpine AS build

WORKDIR /var/www

# Cài dependency để build PHP extensions
RUN apk add --no-cache \
    bash git zip unzip curl autoconf g++ make linux-headers \
    libpng-dev libjpeg-turbo-dev freetype-dev \
    oniguruma-dev libzip-dev openssl-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql mbstring zip bcmath gd \
 && pecl install swoole \
 && docker-php-ext-enable swoole

# Copy composer trước để cache dependency layer
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY . .
RUN composer install --no-dev --optimize-autoloader



# ----------- STAGE 2: Runtime environment -----------
FROM php:8.3-cli-alpine
WORKDIR /var/www

COPY --from=build /var/www /var/www

EXPOSE 8000
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]
