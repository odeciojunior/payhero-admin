#!/bin/bash
set -e

echo "Rebuilding PayHero Admin Docker images with module fix..."

# First, ensure composer.lock is updated with the new autoload configuration
echo "Updating composer autoload locally..."
cd /home/hero/projects/payhero/admin
composer dump-autoload

# Build the images
echo "Building Docker images..."
cd docker/production-minimal

# Build app image
docker build -t payhero-admin-app:latest -f Dockerfile.app ../../

# Build nginx image 
docker build -t payhero-admin-nginx:latest -f Dockerfile.nginx.admin ../../

echo "Build complete!"
echo ""
echo "To test locally, run:"
echo "docker run -it --rm payhero-admin-app:latest php artisan module:list"
echo ""
echo "To push to ECR:"
echo "./push.sh"