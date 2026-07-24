# ============================================================
# Stage 1: Build Frontend Assets
# ============================================================
FROM node:20-alpine AS frontend

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --prefer-offline

COPY vite.config.js ./
COPY resources/ resources/
RUN npm run build

# ============================================================
# Stage 2: Install PHP Dependencies
# ============================================================
FROM composer:2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist

# ============================================================
# Stage 3: Production Image (PHP-FPM + Nginx)
# ============================================================
FROM php:8.2-fpm-alpine AS production

LABEL maintainer="Lincoln-ResearchAndDevelopment"
LABEL description="Lincoln Hostel Management System"

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        mbstring \
        xml \
        gd \
        curl \
        zip \
        intl \
        bcmath \
        exif \
        opcache \
    && apk del --no-cache libpng-dev libjpeg-turbo-dev freetype-dev libxml2-dev libzip-dev oniguruma-dev icu-dev postgresql-dev

# Configure PHP for production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    echo "opcache.enable=1" >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.memory_consumption=128" >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.interned_strings_buffer=8" >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.max_accelerated_files=10000" >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.revalidate_freq=2" >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "opcache.fast_shutdown=1" >> "$PHP_INI_DIR/conf.d/opcache.ini" && \
    echo "upload_max_filesize=10M" >> "$PHP_INI_DIR/conf.d/uploads.ini" && \
    echo "post_max_size=10M" >> "$PHP_INI_DIR/conf.d/uploads.ini"

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY . .

# Copy vendor from composer stage
COPY --from=vendor /app/vendor ./vendor

# Copy built frontend from node stage
COPY --from=frontend /app/public/build ./public/build

# Copy nginx & supervisor config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache && \
    rm -rf /var/www/html/docker

# Create storage symlink (if not already done)
RUN php artisan storage:link --force || true

# Optimize Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=5s --start-period=10s --retries=3 \
    CMD curl -f http://localhost/api/health || exit 1

# Start supervisor (runs nginx + php-fpm)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
