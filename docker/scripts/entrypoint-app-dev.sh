#!/bin/bash
# PayHero Development Environment Entrypoint Script
#
# This script handles initialization of the development environment

set -e

# Function to log messages
log() {
    echo "[$(date "+%Y-%m-%d %H:%M:%S")] [Entrypoint] $1"
}

# Function to log errors
error() {
    echo "[$(date "+%Y-%m-%d %H:%M:%S")] [Entrypoint] ERROR: $1" >&2
}

# Function to wait for a service to be ready
wait_for_service() {
    local host=$1
    local port=$2
    local service=$3
    local timeout=${4:-30}

    log "Waiting for $service to be ready at $host:$port (timeout: ${timeout}s)"
    
    for i in $(seq 1 $timeout); do
        if nc -z $host $port > /dev/null 2>&1; then
            log "$service is available at $host:$port"
            return 0
        fi
        sleep 1
        echo -n "."
    done
    
    error "Timeout reached waiting for $service at $host:$port"
    return 1
}

# Function to create Laravel storage directories
create_storage_dirs() {
    local dirs=(
        /var/www/storage/framework/cache/data
        /var/www/storage/framework/sessions
        /var/www/storage/framework/views
        /var/www/storage/framework/testing
        /var/www/storage/logs
        /var/www/storage/logs/php-fpm
        /var/www/bootstrap/cache
        /var/run/php-fpm
    )

    for dir in "${dirs[@]}"; do
        if [[ ! -d "$dir" ]]; then
            log "Creating directory: $dir"
            mkdir -p "$dir"
            chown -R www:www "$dir"
            chmod -R 775 "$dir"
        fi
    done
}

# Function to configure Xdebug based on environment variables
configure_xdebug() {
    local xdebug_mode=${XDEBUG_MODE:-off}
    
    log "Configuring Xdebug mode: $xdebug_mode"
    
    echo "xdebug.mode=$xdebug_mode" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
    
    if [[ "$xdebug_mode" != "off" ]]; then
        echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
        echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
        echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
        echo "xdebug.log=/var/www/storage/logs/xdebug.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
        
        log "Xdebug enabled with mode: $xdebug_mode"
    else
        log "Xdebug disabled"
    fi
}

# Main initialization function
initialize() {
    log "Starting initialization process"
    
    # Create required directories
    create_storage_dirs
    
    # Configure Xdebug
    configure_xdebug
    
    # Check if we're waiting for database
    if [[ "${WAIT_FOR_DB:-true}" == "true" ]]; then
        wait_for_service ${DB_HOST:-mysql} ${DB_PORT:-3306} "MySQL" 60
    fi
    
    # Check if we're waiting for Redis
    if [[ "${WAIT_FOR_REDIS:-true}" == "true" ]]; then
        wait_for_service ${REDIS_HOST:-redis} ${REDIS_PORT:-6379} "Redis" 30
    fi
    
    # Run database migrations if requested
    if [[ "${RUN_MIGRATIONS:-false}" == "true" ]]; then
        log "Running database migrations"
        php artisan migrate --force
    fi
    
    # Clear caches if requested
    if [[ "${CLEAR_CACHE:-false}" == "true" ]]; then
        log "Clearing application caches"
        php artisan cache:clear
        php artisan config:clear
        php artisan route:clear
        php artisan view:clear
    fi
    
    log "Initialization completed"
}

# Run initialization
initialize

# Execute the command passed to the script
log "Executing command: $@"
exec "$@"