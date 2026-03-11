# ============ OPTIMIZED FOR LOW RAM VPS (2GB, 2 cores) ============

# ============ STAGE 1: Builder ============
# Use multi-stage build to keep final image small
FROM php:8.3-cli-alpine AS builder

WORKDIR /var/www

# Install build dependencies
RUN apk add --no-cache \
    bash \
    git \
    curl \
    autoconf \
    g++ \
    make \
    linux-headers \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    openssl-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# Copy dependency files FIRST (critical for caching!)
# This layer only rebuilds when composer.json/composer.lock changes
COPY composer.json composer.lock ./

# Install dependencies - skip scripts, use dist for faster download
RUN composer install --no-dev --prefer-dist --no-scripts --no-interaction --no-cache-dir

# Copy source code
COPY . .

# Generate optimized autoload
RUN composer dump-autoload --optimize --no-interaction --no-cache-dir


# ============ STAGE 2: Runtime ============
# Use phpswoole image - has Swoole extension PRE-INSTALLED!
# This saves ~20-30 minutes compile time on each build
FROM phpswoole/swoole:php8.3-alpine

WORKDIR /var/www

# Install additional runtime dependencies only
RUN apk add --no-cache \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    oniguruma \
    libpq

# Copy application from builder
COPY --from=builder /var/www /var/www

# Create non-root user for security
RUN addgroup -g 1000 -S appgroup && \
    adduser -u 1000 -S appuser -G appgroup && \
    chown -R appuser:appgroup /var/www

USER appuser

# Expose port
EXPOSE 8000

CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]
