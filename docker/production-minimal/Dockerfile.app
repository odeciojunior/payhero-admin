# Production-minimal Dockerfile for PayHero
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    bash \
    netcat-openbsd \
    libpng \
    libjpeg-turbo \
    freetype \
    libzip \
    icu \
    oniguruma \
    libxml2 \
    postgresql-libs \
    autoconf \
    g++ \
    make \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    linux-headers \
    curl \
    fcgi \
    file \
    supervisor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    calendar \
    soap && \
    pecl install -o -f redis && \
    docker-php-ext-enable redis

# Clean up
RUN apk del autoconf g++ make linux-headers && \
    rm -rf /var/cache/apk/*

# Copy PHP configuration
COPY docker/production-minimal/config/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/production-minimal/config/php/php-fpm.conf /etc/php/8.2/fpm/pool.d/www.conf

# Set working directory
WORKDIR /var/www

# Copy application code
COPY . .

# Copy vendor from composer build stage
FROM composer:2.5 AS composer-build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts --ignore-platform-reqs

# Back to base
FROM base AS production-minimal

# Copy vendor directory
COPY --from=composer-build /app/vendor ./vendor

# Generate module autoload files
RUN cd /var/www && composer dump-autoload --no-dev --optimize || true

# Discover and publish module assets
RUN cd /var/www && php artisan module:discover || true

# Copy helper scripts (without EFS scripts)
COPY docker/production-minimal/startup.sh /usr/local/bin/startup.sh
COPY docker/production-minimal/entrypoint-minimal.sh /usr/local/bin/entrypoint-minimal.sh
COPY docker/production-minimal/healthcheck.sh /usr/local/bin/healthcheck.sh
RUN chmod +x /usr/local/bin/startup.sh /usr/local/bin/entrypoint-minimal.sh /usr/local/bin/healthcheck.sh

# Create necessary directories
RUN mkdir -p storage/app/public \
    && mkdir -p storage/logs \
    && mkdir -p storage/logs/uploads \
    && mkdir -p storage/logs/php-fpm \
    && mkdir -p storage/logs/laravel \
    && mkdir -p storage/logs/laravel/queue \
    && mkdir -p storage/logs/laravel/scheduler \
    && mkdir -p storage/logs/laravel/worker \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p resources/views/modules/core \
    && mkdir -p bootstrap/cache \
    && mkdir -p public/modules

# Run post-install scripts
RUN php artisan vendor:publish --all --force || true && \
    php artisan module:publish || true && \
    php artisan module:publish-config || true && \
    php artisan module:publish-translation || true

# Expose port
EXPOSE 9000 9001

# Entry point - direct PHP-FPM start for production-minimal
ENTRYPOINT ["/usr/local/bin/entrypoint-minimal.sh"]