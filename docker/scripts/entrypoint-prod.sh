#!/bin/bash
# PayHero Production Environment Entrypoint Script
#
# This script handles initialization of the production environment
# with EFS mounting and robust error handling

set -e

# Function to log messages
log() {
    echo "[$(date "+%Y-%m-%d %H:%M:%S")] [Entrypoint] $1"
}

# Function to log errors
error() {
    echo "[$(date "+%Y-%m-%d %H:%M:%S")] [Entrypoint] ERROR: $1" >&2
}

# Source the EFS mount helper functions
if [[ -f "/usr/local/bin/efs-mount-helper.sh" ]]; then
    source /usr/local/bin/efs-mount-helper.sh
else
    error "EFS mount helper script not found"
    exit 1
fi

# Function to wait for a service to be ready
wait_for_service() {
    local host=$1
    local port=$2
    local service=$3
    local timeout=${4:-30}
    local retry_interval=${5:-1}
    local max_retries=$((timeout / retry_interval))

    log "Waiting for $service to be ready at $host:$port (timeout: ${timeout}s)"
    
    for i in $(seq 1 $max_retries); do
        if nc -z $host $port > /dev/null 2>&1; then
            log "$service is available at $host:$port"
            return 0
        fi
        sleep $retry_interval
        if (( i % 5 == 0 )); then
            log "Still waiting for $service... ($i/$max_retries)"
        fi
    done
    
    error "Timeout reached waiting for $service at $host:$port"
    return 1
}

# Function to create Laravel storage directories with proper permissions
create_storage_dirs() {
    local base_dir=${1:-/var/www/storage}
    
    # Main storage directories
    local dirs=(
        "$base_dir/framework/cache/data"
        "$base_dir/framework/sessions"
        "$base_dir/framework/views"
        "$base_dir/logs"
        "$base_dir/logs/php-fpm"
        "/var/www/bootstrap/cache"
    )

    for dir in "${dirs[@]}"; do
        if [[ ! -d "$dir" ]]; then
            log "Creating directory: $dir"
            mkdir -p "$dir"
            chown -R www-data:www-data "$dir"
            chmod -R 775 "$dir"
        fi
    done

    # Create specific EFS directories if they're mounted
    if is_mounted "$base_dir"; then
        log "Setting up EFS storage directories"
        
        # Create EFS subdirectories if they don't exist
        for efs_dir in "efs/mysql" "efs/redis" "efs/logs"; do
            if [[ ! -d "$base_dir/$efs_dir" ]]; then
                log "Creating EFS directory: $base_dir/$efs_dir"
                mkdir -p "$base_dir/$efs_dir"
            fi
        done
        
        # Set appropriate permissions
        chown -R 999:999 "$base_dir/efs/mysql"
        chmod 700 "$base_dir/efs/mysql"
        
        chown -R 999:999 "$base_dir/efs/redis"
        chmod 700 "$base_dir/efs/redis"
        
        chown -R www-data:www-data "$base_dir/efs/logs"
        chmod 755 "$base_dir/efs/logs"
    fi
}

# Function to setup Laravel application for production
setup_laravel() {
    # Check if app is already setup by looking for .env file
    if [[ ! -f "/var/www/.env" || "${FORCE_SETUP:-false}" == "true" ]]; then
        log "Setting up Laravel application"
        
        # Copy environment file
        if [[ -f "/var/www/.env.production" ]]; then
            cp /var/www/.env.production /var/www/.env
        fi
        
        # Generate application key if needed
        if grep -q "APP_KEY=base64:.*" /var/www/.env; then
            log "Application key already set"
        else
            log "Generating application key"
            php artisan key:generate --force
        fi
        
        # Optimize the application
        log "Optimizing application"
        php artisan optimize
        php artisan config:cache
        php artisan route:cache
        
        # Create a file to indicate that setup is complete
        touch /var/www/storage/.setup_complete
    else
        log "Laravel application already set up"
    fi
}

# Function to check application health
check_application_health() {
    log "Checking application health"
    
    # Check PHP-FPM
    if ! php-fpm -t; then
        error "PHP-FPM configuration test failed"
        return 1
    fi
    
    # Check Laravel application
    if ! php /var/www/artisan --version > /dev/null; then
        error "Laravel application check failed"
        return 1
    fi
    
    # Check storage directories
    if [[ ! -d "/var/www/storage" || ! -w "/var/www/storage" ]]; then
        error "Storage directory not accessible or writable"
        return 1
    fi
    
    log "Application health check passed"
    return 0
}

# Main initialization function
initialize() {
    log "Starting production initialization process"
    
    # Mount EFS if enabled (default false for production-minimal)
    if [[ "${EFS_MOUNT_ENABLED:-false}" == "true" ]]; then
        log "Mounting EFS filesystem"
        handle_efs_mount
    else
        log "Skipping EFS mount (disabled by configuration)"
    fi
    
    # Create required directories
    create_storage_dirs
    
    # Wait for required services
    if [[ "${WAIT_FOR_DB:-true}" == "true" ]]; then
        wait_for_service ${DB_HOST:-mysql} ${DB_PORT:-3306} "MySQL" 60 5
    fi
    
    if [[ "${WAIT_FOR_REDIS:-true}" == "true" ]]; then
        wait_for_service ${REDIS_HOST:-redis} ${REDIS_PORT:-6379} "Redis" 30 2
    fi
    
    # Setup Laravel application
    setup_laravel
    
    # Run database migrations if requested
    if [[ "${RUN_MIGRATIONS:-false}" == "true" ]]; then
        log "Running database migrations"
        php artisan migrate --force
    fi
    
    # Check application health
    check_application_health
    
    log "Production initialization completed"
}

# Handle errors
handle_error() {
    error "Error occurred at line $1"
    exit 1
}

# Set error handler
trap 'handle_error $LINENO' ERR

# Run initialization
initialize

# Execute the command passed to the script
log "Executing command: $@"
exec "$@"