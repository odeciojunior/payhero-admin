#!/bin/bash
set -e

echo "[Production-Minimal] Starting container..."

# Set environment
export APP_ENV=${APP_ENV:-production-minimal}
export LOG_CHANNEL=${LOG_CHANNEL:-stderr}

/usr/local/bin/startup.sh || true

# Only run artisan commands if the app is properly installed
if [ -f "artisan" ]; then
    # Ensure composer autoload is up to date
    echo "[Production-Minimal] Ensuring composer autoload is current..."
    composer dump-autoload --no-dev --optimize 2>/dev/null || true
    
    # Run package discovery
    echo "[Production-Minimal] Running package discovery..."
    php artisan package:discover --ansi 2>/dev/null || true
    
    echo "[Production-Minimal] Clearing caches..."
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
    
    if [ "$APP_ENV" = "production-minimal" ] || [ "$APP_ENV" = "production" ]; then
        echo "[Production-Minimal] Caching configuration..."
        php artisan config:cache || true
        php artisan route:cache || true
        php artisan view:cache || true
    fi
fi

echo "[Production-Minimal] Starting PHP-FPM..."
exec php-fpm