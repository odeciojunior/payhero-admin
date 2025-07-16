#!/bin/bash
set -e

echo "[Startup] Starting PayHero application container..."
echo "[Startup] Environment: ${APP_ENV:-production-minimal}"
echo "[Startup] PHP Version: $(php -v | head -n 1)"

# Function to wait for a service
wait_for_service() {
    local host=$1
    local port=$2
    local service=$3
    local timeout=${4:-30}
    
    echo "[Startup] Waiting for $service at $host:$port..."
    
    for i in $(seq 1 $timeout); do
        if nc -z -w 1 "$host" "$port" 2>/dev/null; then
            echo "[Startup] $service is available"
            return 0
        fi
        echo "[Startup] Waiting for $service... ($i/$timeout)"
        sleep 1
    done
    
    return 1
}

# Generate .env file from environment variables
echo "[Startup] Generating .env file from environment variables..."

# First, let's make sure we have the main REDIS_HOST
MAIN_REDIS_HOST="${REDIS_HOST}"
# AWS ElastiCache doesn't use password auth
MAIN_REDIS_PORT="${REDIS_PORT:-6379}"

# Check if we should use password authentication
if [ -n "${REDIS_PASSWORD}" ] && [ "${REDIS_PASSWORD}" != "" ]; then
    echo "[Startup] Redis password authentication enabled"
    USE_REDIS_AUTH=true
else
    echo "[Startup] Redis password authentication disabled (ElastiCache mode)"
    USE_REDIS_AUTH=false
fi

# Start building the .env file
cat > .env << 'EOF'
# Application
EOF

# Add application config
cat >> .env << EOF
APP_NAME="${APP_NAME:-PayHero}"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL}

# Database
DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

# Redis configuration
REDIS_HOST=${MAIN_REDIS_HOST}
REDIS_PORT=${MAIN_REDIS_PORT}
REDIS_DB=${REDIS_DB:-0}
EOF

# Only add Redis password if authentication is needed
if [ "$USE_REDIS_AUTH" = "true" ]; then
    echo "REDIS_PASSWORD=${REDIS_PASSWORD}" >> .env
fi

# Redis for sessions
cat >> .env << EOF

# Redis for sessions - use main Redis if not specified
REDIS_SESSION_HOST=${REDIS_SESSION_HOST:-${MAIN_REDIS_HOST}}
REDIS_SESSION_PORT=${REDIS_SESSION_PORT:-${MAIN_REDIS_PORT}}
REDIS_SESSION_DB=${REDIS_SESSION_DB:-1}
EOF

if [ "$USE_REDIS_AUTH" = "true" ]; then
    echo "REDIS_SESSION_PASSWORD=${REDIS_PASSWORD}" >> .env
fi

# Redis for Horizon
cat >> .env << EOF

# Redis for Horizon - use main Redis if not specified
REDIS_HORIZON_HOST=${REDIS_HORIZON_HOST:-${MAIN_REDIS_HOST}}
REDIS_HORIZON_PORT=${REDIS_HORIZON_PORT:-${MAIN_REDIS_PORT}}
REDIS_HORIZON_DB=${REDIS_HORIZON_DB:-2}
EOF

if [ "$USE_REDIS_AUTH" = "true" ]; then
    echo "REDIS_HORIZON_PASSWORD=${REDIS_PASSWORD}" >> .env
fi

# Redis for cache
cat >> .env << EOF

# Redis for cache - use main Redis if not specified
REDIS_CACHE_HOST=${REDIS_CACHE_HOST:-${MAIN_REDIS_HOST}}
REDIS_CACHE_PORT=${REDIS_CACHE_PORT:-${MAIN_REDIS_PORT}}
REDIS_CACHE_DB=${REDIS_CACHE_DB:-3}
EOF

if [ "$USE_REDIS_AUTH" = "true" ]; then
    echo "REDIS_CACHE_PASSWORD=${REDIS_PASSWORD}" >> .env
fi

# Redis for statement
cat >> .env << EOF

# Redis for statement - use main Redis if not specified
REDIS_STATEMENT_HOST=${REDIS_STATEMENT_HOST:-${MAIN_REDIS_HOST}}
REDIS_STATEMENT_PORT=${REDIS_STATEMENT_PORT:-${MAIN_REDIS_PORT}}
REDIS_STATEMENT_DB=${REDIS_STATEMENT_DB:-3}
EOF

if [ "$USE_REDIS_AUTH" = "true" ]; then
    echo "REDIS_STATEMENT_PASSWORD=${REDIS_PASSWORD}" >> .env
fi

# Add the rest of the configuration
cat >> .env << EOF

# Cache and session configuration
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
QUEUE_FAILED_DRIVER=redis
SESSION_LIFETIME=120

# Logging
LOG_CHANNEL=${LOG_CHANNEL:-stderr}
LOG_LEVEL=${LOG_LEVEL:-info}

# AWS configuration (if provided)
AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID:-}
AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY:-}
AWS_DEFAULT_REGION=${AWS_DEFAULT_REGION:-us-east-1}
AWS_BUCKET=${AWS_BUCKET:-}

# Mail configuration (if provided)
MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME:-}
MAIL_PASSWORD=${MAIL_PASSWORD:-}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-}
MAIL_FROM_NAME="${MAIL_FROM_NAME:-PayHero}"
EOF

# Debug: Show Redis configuration
echo "[Startup] Redis configuration:"
echo "REDIS_HOST=${MAIN_REDIS_HOST}"
echo "REDIS_SESSION_HOST=${REDIS_SESSION_HOST:-${MAIN_REDIS_HOST}}"
echo "REDIS_HORIZON_HOST=${REDIS_HORIZON_HOST:-${MAIN_REDIS_HOST}}"
echo "REDIS_CACHE_HOST=${REDIS_CACHE_HOST:-${MAIN_REDIS_HOST}}"

# Wait for required services
if [ -n "$DB_HOST" ]; then
    DB_HOSTNAME=$(echo "$DB_HOST" | cut -d':' -f1)
    wait_for_service "$DB_HOSTNAME" "${DB_PORT:-3306}" "MySQL" 15 || {
        echo "[Startup] WARNING: Could not connect to MySQL, but continuing..."
    }
fi

if [ -n "$MAIN_REDIS_HOST" ]; then
    wait_for_service "$MAIN_REDIS_HOST" "${MAIN_REDIS_PORT}" "Redis" 15 || {
        echo "[Startup] WARNING: Could not connect to Redis, but continuing..."
    }
fi



# Regenerate composer autoload to ensure helpers are loaded
echo "[Startup] Regenerating composer autoload..."
composer dump-autoload --no-dev --optimize 2>/dev/null || {
    echo "[Startup] WARNING: composer dump-autoload failed, trying without optimization..."
    composer dump-autoload --no-dev 2>/dev/null || true
}

# Ensure package discovery is run
echo "[Startup] Running package discovery..."
php artisan package:discover --ansi 2>/dev/null || {
    echo "[Startup] WARNING: Package discovery failed, but continuing..."
}

# Clear all caches to ensure fresh configuration
echo "[Startup] Clearing all caches..."
# Use array cache driver temporarily to avoid Redis auth issues during initial clear
CACHE_DRIVER=array php artisan config:clear 2>/dev/null || true
CACHE_DRIVER=array php artisan cache:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true

# Remove any cached config files
rm -f bootstrap/cache/config.php
rm -f bootstrap/cache/services.php
rm -f bootstrap/cache/packages.php
rm -f bootstrap/cache/routes-*.php

# Environment-specific operations
if [ "$APP_ENV" = "production-minimal" ] || [ "$APP_ENV" = "production" ]; then
    echo "[Startup] Running production optimizations..."
    
    # Migrations are handled outside the container startup
    # if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    #     echo "[Startup] Running database migrations..."
    #     php artisan migrate --force || {
    #         echo "[Startup] WARNING: Migrations failed, but continuing..."
    #     }
    # fi
    
    # Discover and enable modules
    echo "[Startup] Discovering modules..."
    # Enable all modules without checking status
    php artisan module:enable 2>/dev/null || true
    # Publish module configurations and assets
    php artisan module:publish 2>/dev/null || true
    php artisan module:publish-config 2>/dev/null || true
    php artisan module:publish-translation 2>/dev/null || true
    echo "[Startup] Module discovery completed"
    
    # Clear config cache before recaching to ensure fresh config
    echo "[Startup] Clearing config cache before optimization..."
    php artisan config:clear 2>/dev/null || true
    
    # Cache configuration for production
    echo "[Startup] Caching configuration..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
elif [ "$APP_ENV" = "staging" ]; then
    echo "[Startup] Running in staging mode..."
    
    # Run migrations if enabled
    if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
        echo "[Startup] Running database migrations..."
        php artisan migrate --force || {
            echo "[Startup] WARNING: Migrations failed, but continuing..."
        }
    fi
    
    # Discover modules
    echo "[Startup] Discovering modules..."
    php artisan module:discover || echo "[Startup] Module discovery completed"
    
    # Optionally cache configuration for staging
    if [ "${CACHE_CONFIG:-false}" = "true" ]; then
        php artisan config:cache
    fi
else
    echo "[Startup] Running in ${APP_ENV} mode - skipping optimization"
    
    # Discover modules for all environments
    echo "[Startup] Discovering modules..."
    php artisan module:discover || echo "[Startup] Module discovery completed"
fi

# Test Redis connection
echo "[Startup] Testing Redis connection..."
php -r "
try {
    \$redis = new Redis();
    \$host = '${MAIN_REDIS_HOST}' ?: '127.0.0.1';
    \$port = ${MAIN_REDIS_PORT} ?: 6379;
    \$connected = @\$redis->connect(\$host, \$port, 1);
    if (\$connected) {
        echo '[Startup] Redis connection test: SUCCESS - Connected to ' . \$host . ':' . \$port . PHP_EOL;
        \$redis->close();
    } else {
        echo '[Startup] Redis connection test: FAILED - Could not connect to ' . \$host . ':' . \$port . PHP_EOL;
    }
} catch (Exception \$e) {
    echo '[Startup] Redis connection test: ERROR - ' . \$e->getMessage() . PHP_EOL;
}
"

# Run Redis diagnostic if available
if [ -f "/usr/local/bin/diagnose-redis.php" ]; then
    echo "[Startup] Running Redis diagnostics..."
    php /usr/local/bin/diagnose-redis.php
fi

echo "[Startup] Application container started successfully!"

# Execute the main command
exec "$@"