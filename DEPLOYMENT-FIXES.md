# PayHero Admin - Deployment Fixes & Troubleshooting

This document addresses specific fixes needed based on the current deployment configuration and common issues encountered during AWS deployment.

## Critical Fixes Required

### 1. Laravel Modules Configuration

**Issue**: Modules may not load properly in production environment.

**Fix**: Update `config/modules.php` to ensure proper path resolution:

```php
<?php

return [
    'namespace' => 'Modules',
    'stubs' => [
        'enabled' => false,
        'path' => base_path() . '/vendor/nwidart/laravel-modules/src/Commands/stubs',
        'files' => [
            'routes/web' => 'Routes/web.php',
            'routes/api' => 'Routes/api.php',
            'views/index' => 'Resources/views/index.blade.php',
            'views/master' => 'Resources/views/layouts/master.blade.php',
            'scaffold/config' => 'Config/config.php',
            'composer' => 'composer.json',
            'assets/js/app' => 'Resources/assets/js/app.js',
            'assets/sass/app' => 'Resources/assets/sass/app.scss',
            'webpack' => 'webpack.mix.js',
            'package' => 'package.json',
        ],
        'replacements' => [
            'routes/web' => ['LOWER_NAME', 'STUDLY_NAME'],
            'routes/api' => ['LOWER_NAME'],
            'webpack' => ['LOWER_NAME'],
            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
            'views/index' => ['LOWER_NAME'],
            'views/master' => ['LOWER_NAME', 'STUDLY_NAME'],
            'scaffold/config' => ['STUDLY_NAME'],
            'composer' => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'MODULE_NAMESPACE',
                'PROVIDER_NAMESPACE',
            ],
        ],
        'gitkeep' => true,
    ],
    'paths' => [
        'modules' => base_path('Modules'),
        'assets' => public_path('modules'),
        'migration' => base_path('database/migrations'),
        'generator' => [
            'config' => ['path' => 'Config', 'generate' => true],
            'command' => ['path' => 'Console', 'generate' => true],
            'migration' => ['path' => 'Database/Migrations', 'generate' => true],
            'seeder' => ['path' => 'Database/Seeders', 'generate' => true],
            'factory' => ['path' => 'Database/factories', 'generate' => true],
            'model' => ['path' => 'Entities', 'generate' => true],
            'routes' => ['path' => 'Routes', 'generate' => true],
            'controller' => ['path' => 'Http/Controllers', 'generate' => true],
            'filter' => ['path' => 'Http/Middleware', 'generate' => true],
            'request' => ['path' => 'Http/Requests', 'generate' => true],
            'provider' => ['path' => 'Providers', 'generate' => true],
            'assets' => ['path' => 'Resources/assets', 'generate' => true],
            'lang' => ['path' => 'Resources/lang', 'generate' => true],
            'views' => ['path' => 'Resources/views', 'generate' => true],
            'test' => ['path' => 'Tests/Unit', 'generate' => true],
            'test-feature' => ['path' => 'Tests/Feature', 'generate' => true],
            'repository' => ['path' => 'Repositories', 'generate' => false],
            'event' => ['path' => 'Events', 'generate' => false],
            'listener' => ['path' => 'Listeners', 'generate' => false],
            'policies' => ['path' => 'Policies', 'generate' => false],
            'rules' => ['path' => 'Rules', 'generate' => false],
            'jobs' => ['path' => 'Jobs', 'generate' => false],
            'emails' => ['path' => 'Emails', 'generate' => false],
            'notifications' => ['path' => 'Notifications', 'generate' => false],
            'resource' => ['path' => 'Transformers', 'generate' => false],
            'component-view' => ['path' => 'Resources/views/components', 'generate' => false],
            'component-class' => ['path' => 'View/Components', 'generate' => false],
        ],
    ],
    'scan' => [
        'enabled' => true,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],
    'composer' => [
        'vendor' => 'nwidart',
        'author' => [
            'name' => 'Nicolas Widart',
            'email' => 'n.widart@gmail.com',
        ],
        'composer-output' => false,
    ],
    'cache' => [
        'enabled' => true,
        'key' => 'laravel-modules',
        'lifetime' => 60,
    ],
    'register' => [
        'translations' => true,
        'files' => 'register',
    ],
    'activators' => [
        'file' => [
            'class' => \Nwidart\Modules\Activators\FileActivator::class,
            'statuses-file' => base_path('modules_statuses.json'),
            'cache-key' => 'activator.installed',
            'cache-lifetime' => 604800,
        ],
    ],
    'activator' => 'file',
];
```

### 2. Modules Status File

**Issue**: `modules_statuses.json` file missing or incorrect.

**Fix**: Create/update the file with proper module status:

```json
{
    "ActiveCampaign": true,
    "AdooreiCheckout": true,
    "Affiliates": true,
    "Api": true,
    "Apps": true,
    "AstronMembers": true,
    "Attendance": true,
    "Authentication": true,
    "Chargebacks": true,
    "CheckoutEditor": true,
    "Checkouts": true,
    "ConvertaX": true,
    "Core": true,
    "Customers": true,
    "Dashboard": true,
    "Deliveries": true,
    "DemoAccount": true,
    "DiscountCoupons": true,
    "Domains": true,
    "Finances": true,
    "GatewayIntegrations": true,
    "GeradorRastreio": true,
    "HotBillet": true,
    "HotZapp": true,
    "Integrations": true,
    "Invites": true,
    "Melhorenvio": true,
    "Mobile": true,
    "Notazz": true,
    "NotificacoesInteligentes": true,
    "Notifications": true,
    "Nuvemshop": true,
    "OrderBump": true,
    "Pixels": true,
    "Plans": true,
    "PostBack": true,
    "Products": true,
    "ProjectNotification": true,
    "ProjectReviews": true,
    "ProjectReviewsConfig": true,
    "Projects": true,
    "ProjectUpsellConfig": true,
    "ProjectUpsellRule": true,
    "Reportana": true,
    "Reports": true,
    "Sales": true,
    "SalesBlackListAntifraud": true,
    "SalesRecovery": true,
    "Shipping": true,
    "Shopify": true,
    "Smartfunnel": true,
    "Tickets": true,
    "Trackings": true,
    "Transfers": true,
    "Unicodrop": true,
    "UserInformations": true,
    "Users": true,
    "Utmify": true,
    "VegaCheckout": true,
    "Webhooks": true,
    "Whatsapp2": true,
    "Withdrawals": true,
    "WooCommerce": true
}
```

### 3. Docker Build Context Fix

**Issue**: Large build context causing slow builds and potential timeouts.

**Fix**: Create `.dockerignore` file:

```dockerignore
# Version control
.git
.gitignore

# Dependencies
node_modules
vendor

# Development files
.env
.env.local
.env.example
docker-compose.yml
docker-compose.*.yml

# IDE files
.vscode
.idea
*.swp
*.swo
*~

# OS generated files
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
ehthumbs.db
Thumbs.db

# Logs
*.log
logs

# Runtime data
pids
*.pid
*.seed

# Coverage directory used by tools like istanbul
coverage

# Dependency directories
node_modules
bower_components

# Build outputs
build
dist
public/build
public/hot
public/mix-manifest.json

# Storage directories
storage/app/public/*
storage/framework/cache/*
storage/framework/sessions/*
storage/framework/views/*
storage/logs/*

# Laravel specific
bootstrap/cache/*
.phpunit.result.cache

# Testing
tests
phpunit.xml
.phpunit.result.cache

# Documentation
*.md
docs

# Docker files
Dockerfile*
docker
```

### 4. Environment Variables Fix

**Issue**: Inconsistent environment variable naming between Docker and application.

**Fix**: Update `docker-compose.payhero-dev.yml`:

```yaml
services:
  app:
    build:
      context: .
      dockerfile: docker/dev/dockerfile.app.dev
    container_name: admin
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
    networks:
      - payhero_network
    environment:
      # Laravel Configuration
      - APP_NAME=${APP_NAME:-"PayHero Admin"}
      - APP_ENV=${APP_ENV:-production}
      - APP_KEY=${APP_KEY}
      - APP_DEBUG=${APP_DEBUG:-false}
      - APP_URL=${APP_URL}
      
      # Project Identification
      - PROJECT_NAME=${PROJECT_NAME:-velana}
      - ENVIRONMENT=${ENVIRONMENT:-production-minimal}
      
      # Database Configuration
      - DB_CONNECTION=${DB_CONNECTION:-mysql}
      - DB_HOST=${DB_HOST}
      - DB_PORT=${DB_PORT:-3306}
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}
      
      # Redis Configuration
      - REDIS_HOST=${REDIS_HOST}
      - REDIS_PORT=${REDIS_PORT:-6379}
      - REDIS_PASSWORD=${REDIS_PASSWORD}
      
      # Cache and Session Configuration
      - CACHE_DRIVER=${CACHE_DRIVER:-redis}
      - SESSION_DRIVER=${SESSION_DRIVER:-redis}
      - QUEUE_CONNECTION=${QUEUE_CONNECTION:-redis}
      
      # Performance Settings
      - COMPOSER_PROCESS_TIMEOUT=${COMPOSER_PROCESS_TIMEOUT:-600}
      - PHP_MEMORY_LIMIT=${PHP_MEMORY_LIMIT:-256M}
      
      # Development Settings
      - XDEBUG_MODE=${XDEBUG_MODE:-off}
```

### 5. Task Definition Memory Allocation

**Issue**: Insufficient memory allocation causing OOM kills.

**Fix**: Update `corrected-task-def.json`:

```json
{
    "family": "velana-production-minimal-admin-task",
    "taskRoleArn": "arn:aws:iam::983877353757:role/velana-production-minimal-admin-task-role",
    "executionRoleArn": "arn:aws:iam::983877353757:role/velana-production-minimal-admin-execution-role",
    "networkMode": "awsvpc",
    "requiresCompatibilities": ["FARGATE"],
    "cpu": "2048",
    "memory": "4096",
    "containerDefinitions": [
        {
            "name": "admin-app",
            "image": "983877353757.dkr.ecr.us-east-1.amazonaws.com/velana-production-minimal-admin-app:latest",
            "cpu": 1536,
            "memory": 3072,
            "memoryReservation": 2048,
            "essential": true,
            "portMappings": [
                {
                    "containerPort": 9000,
                    "protocol": "tcp"
                }
            ],
            "environment": [
                {
                    "name": "APP_ENV",
                    "value": "production"
                },
                {
                    "name": "MODULE_NAME",
                    "value": "admin"
                },
                {
                    "name": "ENVIRONMENT",
                    "value": "production-minimal"
                },
                {
                    "name": "PROJECT_NAME",
                    "value": "velana"
                }
            ],
            "secrets": [
                {
                    "name": "APP_KEY",
                    "valueFrom": "arn:aws:ssm:us-east-1:983877353757:parameter/velana/production-minimal/APP_KEY"
                },
                {
                    "name": "DB_HOST",
                    "valueFrom": "arn:aws:ssm:us-east-1:983877353757:parameter/velana/production-minimal/DB_HOST"
                },
                {
                    "name": "DB_DATABASE",
                    "valueFrom": "arn:aws:ssm:us-east-1:983877353757:parameter/velana/production-minimal/DB_DATABASE"
                },
                {
                    "name": "DB_USERNAME",
                    "valueFrom": "arn:aws:ssm:us-east-1:983877353757:parameter/velana/production-minimal/DB_USERNAME"
                },
                {
                    "name": "DB_PASSWORD",
                    "valueFrom": "arn:aws:ssm:us-east-1:983877353757:parameter/velana/production-minimal/DB_PASSWORD"
                },
                {
                    "name": "REDIS_HOST",
                    "valueFrom": "arn:aws:ssm:us-east-1:983877353757:parameter/velana/production-minimal/REDIS_HOST"
                }
            ],
            "healthCheck": {
                "command": [
                    "CMD-SHELL",
                    "curl -f http://localhost:9000/health || exit 1"
                ],
                "interval": 30,
                "timeout": 10,
                "retries": 3,
                "startPeriod": 120
            },
            "logConfiguration": {
                "logDriver": "awslogs",
                "options": {
                    "awslogs-group": "/ecs/velana-production-minimal-admin",
                    "awslogs-region": "us-east-1",
                    "awslogs-stream-prefix": "ecs",
                    "awslogs-create-group": "true"
                }
            }
        },
        {
            "name": "admin-nginx",
            "image": "983877353757.dkr.ecr.us-east-1.amazonaws.com/velana-production-minimal-admin-nginx:latest",
            "cpu": 512,
            "memory": 1024,
            "memoryReservation": 512,
            "essential": true,
            "portMappings": [
                {
                    "containerPort": 80,
                    "protocol": "tcp"
                }
            ],
            "dependsOn": [
                {
                    "containerName": "admin-app",
                    "condition": "HEALTHY"
                }
            ],
            "logConfiguration": {
                "logDriver": "awslogs",
                "options": {
                    "awslogs-group": "/ecs/velana-production-minimal-admin",
                    "awslogs-region": "us-east-1",
                    "awslogs-stream-prefix": "nginx",
                    "awslogs-create-group": "true"
                }
            }
        }
    ]
}
```

### 6. Storage Permissions Fix

**Issue**: Laravel storage directory permissions causing write failures.

**Fix**: Update Dockerfile to set proper permissions:

```dockerfile
# In your Dockerfile.app
FROM php:8.2-fpm-alpine

# ... other instructions ...

# Create Laravel storage directories
RUN mkdir -p /var/www/storage/app/public \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/storage/logs

# Set proper permissions
RUN chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage \
    && chown -R www-data:www-data /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/bootstrap/cache

# Create symbolic link for public storage
RUN ln -sf /var/www/storage/app/public /var/www/public/storage
```

### 7. Health Check Endpoint

**Issue**: Missing health check endpoint causing deployment failures.

**Fix**: Add health check route in `routes/web.php`:

```php
// Health check endpoint for AWS ALB
Route::get('/health', function () {
    try {
        // Check database connection
        DB::connection()->getPdo();
        
        // Check if modules are loaded
        $moduleCount = count(Module::allEnabled());
        
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'app' => config('app.name'),
            'environment' => config('app.env'),
            'modules_loaded' => $moduleCount,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version()
        ], 200);
        
    } catch (Exception $e) {
        return response()->json([
            'status' => 'unhealthy',
            'timestamp' => now()->toISOString(),
            'error' => $e->getMessage()
        ], 503);
    }
})->name('health.check');
```

### 8. Asset Compilation Fix

**Issue**: Frontend assets not building properly in Docker.

**Fix**: Update `package.json` build scripts:

```json
{
    "scripts": {
        "dev": "npm run development",
        "development": "mix",
        "watch": "mix watch",
        "watch-poll": "mix watch -- --watch-options-poll=1000",
        "hot": "mix watch --hot",
        "prod": "npm run production",
        "production": "mix --production",
        "build": "npm run production"
    },
    "engines": {
        "node": ">=16.0.0",
        "npm": ">=8.0.0"
    }
}
```

### 9. Cache Configuration Fix

**Issue**: Cache configuration not optimized for production.

**Fix**: Update `config/cache.php` for production:

```php
<?php

return [
    'default' => env('CACHE_DRIVER', 'redis'),
    
    'stores' => [
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
        
        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
        ],
        
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
        
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],
    ],
    
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache'),
];
```

### 10. Queue Configuration Fix

**Issue**: Queue workers not configured properly for production.

**Fix**: Update `config/queue.php`:

```php
<?php

return [
    'default' => env('QUEUE_CONNECTION', 'redis'),
    
    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for' => null,
            'after_commit' => false,
        ],
    ],
    
    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table' => 'failed_jobs',
    ],
];
```

## Deployment Script Fixes

### 1. Updated deploy.sh

```bash
#!/bin/bash
set -euo pipefail

# Configuration
PROJECT_NAME="${PROJECT_NAME:-velana}"
ENVIRONMENT="${ENVIRONMENT:-production-minimal}"
MODULE_NAME="${MODULE_NAME:-admin}"
AWS_REGION="${AWS_REGION:-us-east-1}"
AWS_ACCOUNT_ID="${AWS_ACCOUNT_ID:-983877353757}"

# Image tag from parameter or latest
IMAGE_TAG="${1:-latest}"

echo "==> Deploying PayHero Admin Module"
echo "    Project: $PROJECT_NAME"
echo "    Environment: $ENVIRONMENT"
echo "    Module: $MODULE_NAME"
echo "    Image Tag: $IMAGE_TAG"
echo "    AWS Region: $AWS_REGION"

# Function to wait for deployment
wait_for_deployment() {
    local cluster="$1"
    local service="$2"
    
    echo "==> Waiting for deployment to complete..."
    
    aws ecs wait services-stable \
        --cluster "$cluster" \
        --services "$service" \
        --region "$AWS_REGION"
    
    if [ $? -eq 0 ]; then
        echo "✓ Deployment completed successfully"
        return 0
    else
        echo "✗ Deployment failed or timed out"
        return 1
    fi
}

# Main deployment
CLUSTER_NAME="payhero-${ENVIRONMENT}-cluster"
SERVICE_NAME="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-service"
TASK_FAMILY="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-task"

# Update task definition with new image
TASK_DEFINITION=$(aws ecs describe-task-definition \
    --task-definition "$TASK_FAMILY" \
    --region "$AWS_REGION" \
    --query 'taskDefinition' \
    --output json)

# Update image URIs
NEW_TASK_DEFINITION=$(echo "$TASK_DEFINITION" | jq --arg tag "$IMAGE_TAG" '
    .containerDefinitions |= map(
        if .name == "admin-app" then
            .image = "'"$AWS_ACCOUNT_ID"'.dkr.ecr.'"$AWS_REGION"'.amazonaws.com/'"$PROJECT_NAME"'-'"$ENVIRONMENT"'-'"$MODULE_NAME"'-app:" + $tag
        elif .name == "admin-nginx" then
            .image = "'"$AWS_ACCOUNT_ID"'.dkr.ecr.'"$AWS_REGION"'.amazonaws.com/'"$PROJECT_NAME"'-'"$ENVIRONMENT"'-'"$MODULE_NAME"'-nginx:" + $tag
        else
            .
        end
    ) |
    del(.taskDefinitionArn, .revision, .status, .requiresAttributes, .placementConstraints, .compatibilities, .registeredAt, .registeredBy)
')

# Register new task definition
echo "==> Registering new task definition..."
NEW_REVISION=$(aws ecs register-task-definition \
    --cli-input-json "$NEW_TASK_DEFINITION" \
    --region "$AWS_REGION" \
    --query 'taskDefinition.revision' \
    --output text)

echo "✓ Registered task definition revision: $NEW_REVISION"

# Update service
echo "==> Updating ECS service..."
aws ecs update-service \
    --cluster "$CLUSTER_NAME" \
    --service "$SERVICE_NAME" \
    --task-definition "$TASK_FAMILY:$NEW_REVISION" \
    --region "$AWS_REGION" \
    --query 'service.serviceName' \
    --output text

# Wait for deployment
wait_for_deployment "$CLUSTER_NAME" "$SERVICE_NAME"

echo "==> Deployment completed successfully!"
```

## Common Issues and Quick Fixes

### Issue 1: "Module [X] not found"

**Quick Fix**:
```bash
# Check if modules_statuses.json exists
ls -la modules_statuses.json

# If missing, create it
php artisan module:list --only=enabled > modules_statuses.json

# Verify module paths
php artisan module:check
```

### Issue 2: "Storage directory not writable"

**Quick Fix**:
```bash
# In the container
chown -R www-data:www-data /var/www/storage
chmod -R 775 /var/www/storage
```

### Issue 3: "Health check failing"

**Quick Fix**:
```bash
# Test health endpoint locally
curl -f http://localhost/health

# Check logs
docker logs container_name

# Verify database connection
php artisan tinker
> DB::connection()->getPdo();
```

### Issue 4: "Assets not loading"

**Quick Fix**:
```bash
# Rebuild assets
npm install
npm run production

# Check asset paths
php artisan config:clear
php artisan view:clear
```

### Issue 5: "Database migration fails"

**Quick Fix**:
```bash
# Check database connection
php artisan migrate:status

# Run migrations step by step
php artisan migrate --step

# If foreign key issues
php artisan migrate:refresh --seed
```

## Monitoring Commands

```bash
# Check ECS service status
aws ecs describe-services \
    --cluster payhero-production-minimal-cluster \
    --services velana-production-minimal-admin-service

# View recent logs
aws logs tail /ecs/velana-production-minimal-admin --follow

# Check ALB target health
aws elbv2 describe-target-health \
    --target-group-arn YOUR_TARGET_GROUP_ARN

# Monitor resource utilization
aws cloudwatch get-metric-statistics \
    --namespace AWS/ECS \
    --metric-name CPUUtilization \
    --dimensions Name=ServiceName,Value=velana-production-minimal-admin-service \
    --start-time 2025-07-16T00:00:00Z \
    --end-time 2025-07-16T23:59:59Z \
    --period 300 \
    --statistics Average
```

This troubleshooting guide should address the most common deployment issues and provide quick fixes for rapid resolution.
