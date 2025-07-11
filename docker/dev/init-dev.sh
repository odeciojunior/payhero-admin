#!/bin/bash
# Development initialization script

echo "🚀 Initializing PayHero development environment..."

# Run composer install
echo "📦 Installing PHP dependencies..."
docker-compose -f docker-compose.payheroDev.yml exec app composer install

# Run npm install
echo "📦 Installing Node dependencies..."
docker-compose -f docker-compose.payheroDev.yml exec app npm install

# Run migrations
echo "🗄️ Running database migrations..."
docker-compose -f docker-compose.payheroDev.yml exec app php artisan migrate

# Generate application key if needed
echo "🔑 Generating application key..."
docker-compose -f docker-compose.payheroDev.yml exec app php artisan key:generate

# Clear caches
echo "🧹 Clearing caches..."
docker-compose -f docker-compose.payheroDev.yml exec app php artisan config:clear
docker-compose -f docker-compose.payheroDev.yml exec app php artisan cache:clear

echo "✅ Development environment ready!"
echo "Access the application at: http://localhost:8080"