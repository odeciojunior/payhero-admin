#!/bin/bash
set -e

echo "Starting PayHero Nginx production container..."

# Source EFS mount helper
source /usr/local/bin/efs-mount-helper.sh

# Wait for PHP-FPM to be ready
echo "Waiting for PHP-FPM to be ready..."
# In ECS Fargate, containers in the same task communicate via localhost
PHP_FPM_HOST="${PHP_FPM_HOST:-127.0.0.1}"
while ! nc -z $PHP_FPM_HOST 9000; do
  echo "PHP-FPM is not ready yet, waiting..."
  sleep 2
done
echo "PHP-FPM is ready!"

# Create necessary directories
mkdir -p /var/cache/nginx /var/log/nginx

# Test nginx configuration
nginx -t

# Start nginx
echo "Starting Nginx..."
exec "$@"