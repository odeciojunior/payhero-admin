# PayHero Admin - AWS Deployment Guide

This comprehensive guide provides all the necessary fixes, configurations, and steps required to successfully deploy the PayHero Admin application to AWS using ECS Fargate.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [AWS Infrastructure Setup](#aws-infrastructure-setup)
3. [Environment Configuration](#environment-configuration)
4. [Docker Configuration](#docker-configuration)
5. [Database & Redis Setup](#database--redis-setup)
6. [Security & IAM Configuration](#security--iam-configuration)
7. [Deployment Process](#deployment-process)
8. [Monitoring & Health Checks](#monitoring--health-checks)
9. [Troubleshooting](#troubleshooting)
10. [Cost Optimization](#cost-optimization)

## Prerequisites

### Local Development Environment

```bash
# Required tools
- Docker 20.x+
- AWS CLI v2.x
- Node.js 16.x+ & npm
- PHP 8.2+
- Composer 2.x
- jq (for JSON processing)

# Verify installations
docker --version
aws --version
node --version
php --version
composer --version
jq --version
```

### AWS Account Setup

- AWS Account with appropriate permissions
- AWS CLI configured with credentials
- Access to us-east-1 region
- Account ID: 983877353757 (update as needed)

## AWS Infrastructure Setup

### 1. VPC and Networking

```bash
# Create VPC (if not exists)
aws ec2 create-vpc \
  --cidr-block 10.0.0.0/16 \
  --tag-specifications 'ResourceType=vpc,Tags=[{Key=Name,Value=payhero-production-minimal-vpc}]'

# Create subnets in multiple AZs
aws ec2 create-subnet \
  --vpc-id vpc-xxxxxxxx \
  --cidr-block 10.0.1.0/24 \
  --availability-zone us-east-1a \
  --tag-specifications 'ResourceType=subnet,Tags=[{Key=Name,Value=payhero-subnet-1a}]'

aws ec2 create-subnet \
  --vpc-id vpc-xxxxxxxx \
  --cidr-block 10.0.2.0/24 \
  --availability-zone us-east-1b \
  --tag-specifications 'ResourceType=subnet,Tags=[{Key=Name,Value=payhero-subnet-1b}]'
```

### 2. Security Groups

```bash
# ECS Security Group
aws ec2 create-security-group \
  --group-name payhero-ecs-sg \
  --description "Security group for PayHero ECS tasks" \
  --vpc-id vpc-xxxxxxxx

# ALB Security Group
aws ec2 create-security-group \
  --group-name payhero-alb-sg \
  --description "Security group for PayHero ALB" \
  --vpc-id vpc-xxxxxxxx

# Add ingress rules
aws ec2 authorize-security-group-ingress \
  --group-id sg-xxxxxxxx \
  --protocol tcp \
  --port 80 \
  --cidr 0.0.0.0/0

aws ec2 authorize-security-group-ingress \
  --group-id sg-xxxxxxxx \
  --protocol tcp \
  --port 443 \
  --cidr 0.0.0.0/0
```

### 3. ECS Cluster

```bash
# Create ECS cluster
aws ecs create-cluster \
  --cluster-name payhero-production-minimal-cluster \
  --capacity-providers FARGATE \
  --default-capacity-provider-strategy capacityProvider=FARGATE,weight=1
```

### 4. Application Load Balancer

```bash
# Create ALB
aws elbv2 create-load-balancer \
  --name payhero-production-minimal-alb \
  --subnets subnet-xxxxxxxx subnet-yyyyyyyy \
  --security-groups sg-xxxxxxxx \
  --scheme internet-facing \
  --type application

# Create target group
aws elbv2 create-target-group \
  --name payhero-admin-tg \
  --protocol HTTP \
  --port 80 \
  --vpc-id vpc-xxxxxxxx \
  --target-type ip \
  --health-check-path /health
```

## Environment Configuration

### 1. SSM Parameter Store

Create the following parameters in AWS Systems Manager Parameter Store:

```bash
# Application parameters
aws ssm put-parameter \
  --name "/velana/production-minimal/APP_KEY" \
  --value "base64:YOUR_GENERATED_APP_KEY_HERE" \
  --type "SecureString"

aws ssm put-parameter \
  --name "/velana/production-minimal/DB_HOST" \
  --value "payhero-production-minimal-db.cluster-xxxxxxxxx.us-east-1.rds.amazonaws.com" \
  --type "SecureString"

aws ssm put-parameter \
  --name "/velana/production-minimal/DB_DATABASE" \
  --value "payhero" \
  --type "SecureString"

aws ssm put-parameter \
  --name "/velana/production-minimal/DB_USERNAME" \
  --value "admin" \
  --type "SecureString"

aws ssm put-parameter \
  --name "/velana/production-minimal/DB_PASSWORD" \
  --value "YOUR_SECURE_PASSWORD" \
  --type "SecureString"

aws ssm put-parameter \
  --name "/velana/production-minimal/REDIS_HOST" \
  --value "payhero-production-minimal-redis.xxxxxx.ng.0001.use1.cache.amazonaws.com" \
  --type "SecureString"
```

### 2. Environment File (.env.production)

Create `.env.production` file:

```bash
# Application Settings
APP_NAME="Manage Velana"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Project Configuration
PROJECT_NAME=velana
ENVIRONMENT=production-minimal

# Performance Settings
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
BROADCAST_DRIVER=redis

# Laravel Modules
MODULES_STATUSES_PATH=modules_statuses.json

# Security Settings
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120

# Asset Configuration
ASSET_URL=${APP_URL}
MIX_ASSET_URL=${APP_URL}
```

## Docker Configuration

### 1. Multi-Stage Dockerfile (Dockerfile.app)

The application uses a multi-stage Docker build. Key fixes needed:

```dockerfile
# Stage 1: Build dependencies
FROM php:8.2-fpm-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Stage 2: Production image
FROM php:8.2-fpm-alpine AS production

WORKDIR /var/www

# Copy application code
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

EXPOSE 9000

CMD ["php-fpm"]
```

### 2. Nginx Configuration (Dockerfile.nginx)

```dockerfile
FROM nginx:alpine

# Copy nginx configuration
COPY docker/production-minimal/config/nginx.conf /etc/nginx/nginx.conf
COPY docker/production-minimal/config/default.conf /etc/nginx/conf.d/default.conf

# Copy static assets
COPY public /var/www/public

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

## Database & Redis Setup

### 1. RDS MySQL Configuration

```bash
# Create RDS subnet group
aws rds create-db-subnet-group \
  --db-subnet-group-name payhero-db-subnet-group \
  --db-subnet-group-description "Subnet group for PayHero RDS" \
  --subnet-ids subnet-xxxxxxxx subnet-yyyyyyyy

# Create RDS cluster
aws rds create-db-cluster \
  --db-cluster-identifier payhero-production-minimal-db \
  --engine aurora-mysql \
  --engine-version 8.0.mysql_aurora.3.02.0 \
  --master-username admin \
  --master-user-password YOUR_SECURE_PASSWORD \
  --database-name payhero \
  --db-subnet-group-name payhero-db-subnet-group \
  --vpc-security-group-ids sg-xxxxxxxx
```

### 2. ElastiCache Redis

```bash
# Create Redis subnet group
aws elasticache create-cache-subnet-group \
  --cache-subnet-group-name payhero-redis-subnet-group \
  --cache-subnet-group-description "Subnet group for PayHero Redis" \
  --subnet-ids subnet-xxxxxxxx subnet-yyyyyyyy

# Create Redis cluster
aws elasticache create-cache-cluster \
  --cache-cluster-id payhero-production-minimal-redis \
  --engine redis \
  --cache-node-type cache.t3.micro \
  --num-cache-nodes 1 \
  --cache-subnet-group-name payhero-redis-subnet-group \
  --security-group-ids sg-xxxxxxxx
```

## Security & IAM Configuration

### 1. ECS Task Role

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "ssm:GetParameter",
        "ssm:GetParameters",
        "ssm:GetParameterHistory",
        "ssm:GetParametersByPath"
      ],
      "Resource": [
        "arn:aws:ssm:us-east-1:983877353757:parameter/velana/production-minimal/*"
      ]
    },
    {
      "Effect": "Allow",
      "Action": [
        "kms:Decrypt"
      ],
      "Resource": [
        "arn:aws:kms:us-east-1:983877353757:key/*"
      ]
    }
  ]
}
```

### 2. ECS Execution Role

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "ecr:GetAuthorizationToken",
        "ecr:BatchCheckLayerAvailability",
        "ecr:GetDownloadUrlForLayer",
        "ecr:BatchGetImage",
        "logs:CreateLogStream",
        "logs:PutLogEvents",
        "ssm:GetParameters",
        "secretsmanager:GetSecretValue"
      ],
      "Resource": "*"
    }
  ]
}
```

### 3. Create IAM Roles

```bash
# Run the provided script
chmod +x create-iam-roles.sh
./create-iam-roles.sh
```

## Deployment Process

### 1. Prepare Application

```bash
# Navigate to project directory
cd /home/hero/projects/payhero/admin

# Install dependencies
composer install --no-dev --optimize-autoloader

# Build frontend assets
npm install
npm run production

# Generate application key (if needed)
php artisan key:generate
```

### 2. Build and Push Docker Images

```bash
# Navigate to deployment directory
cd docker/production-minimal

# Build Docker images
./build.sh v1.0.0

# Push to ECR
./push.sh v1.0.0
```

### 3. Deploy to ECS

```bash
# Create ECS service (first time only)
./create-ecs-service-admin.sh

# Deploy application
./deploy.sh v1.0.0

# Or use the complete pipeline
./pipeline.sh v1.0.0
```

### 4. Run Database Migrations

```bash
# Run migrations through ECS task
./run-migrations.sh
```

## Monitoring & Health Checks

### 1. Health Check Endpoint

Add to `routes/web.php`:

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now(),
        'app' => config('app.name'),
        'environment' => config('app.env')
    ]);
});
```

### 2. CloudWatch Monitoring

```bash
# Create CloudWatch log group
aws logs create-log-group \
  --log-group-name /ecs/velana-production-minimal-admin
```

### 3. Health Check Script

```bash
# Run health checks
./health-check.sh
```

## Troubleshooting

### Common Issues and Solutions

#### 1. Module Loading Issues

**Problem**: Laravel modules not loading properly
**Solution**: 
```bash
# Ensure modules_statuses.json exists
php artisan module:publish-config

# Check module status
php artisan module:list
```

#### 2. Permission Issues

**Problem**: Storage directory permissions
**Solution**:
```bash
# In Dockerfile
RUN chown -R www-data:www-data /var/www/storage
RUN chmod -R 775 /var/www/storage
```

#### 3. Database Connection Issues

**Problem**: Cannot connect to RDS
**Solution**:
- Verify security group rules
- Check RDS endpoint in SSM parameters
- Validate VPC configuration

#### 4. Redis Connection Issues

**Problem**: Cannot connect to ElastiCache
**Solution**:
- Verify ElastiCache security group
- Check Redis endpoint configuration
- Validate subnet group settings

### Debugging Commands

```bash
# Check ECS service status
aws ecs describe-services \
  --cluster payhero-production-minimal-cluster \
  --services velana-production-minimal-admin-service

# View ECS task logs
aws logs get-log-events \
  --log-group-name /ecs/velana-production-minimal-admin \
  --log-stream-name ecs/admin-app/task-id

# Check ALB target health
aws elbv2 describe-target-health \
  --target-group-arn arn:aws:elasticloadbalancing:us-east-1:983877353757:targetgroup/payhero-admin-tg/xxx
```

## Cost Optimization

### 1. Resource Sizing

```yaml
# ECS Task Definition - Cost-optimized settings
cpu: "512"      # 0.5 vCPU
memory: "1024"  # 1 GB RAM

# For production workloads
cpu: "1024"     # 1 vCPU  
memory: "2048"  # 2 GB RAM
```

### 2. Auto Scaling

```bash
# Configure ECS service auto scaling
aws application-autoscaling register-scalable-target \
  --service-namespace ecs \
  --scalable-dimension ecs:service:DesiredCount \
  --resource-id service/payhero-production-minimal-cluster/velana-production-minimal-admin-service \
  --min-capacity 1 \
  --max-capacity 3
```

### 3. Monitoring Costs

```bash
# Run cost monitoring script
./monitor-costs.sh
```

## Final Deployment Checklist

- [ ] AWS infrastructure created
- [ ] VPC and networking configured
- [ ] Security groups properly configured
- [ ] RDS database cluster created and accessible
- [ ] ElastiCache Redis cluster created
- [ ] SSM parameters configured
- [ ] IAM roles and policies created
- [ ] ECR repositories created
- [ ] Docker images built and pushed
- [ ] ECS cluster and service created
- [ ] ALB and target groups configured
- [ ] Health checks implemented
- [ ] Database migrations executed
- [ ] SSL/TLS certificates configured (if using HTTPS)
- [ ] Monitoring and logging configured
- [ ] Auto scaling policies configured
- [ ] Backup strategies implemented

## Support and Maintenance

### Regular Maintenance Tasks

1. **Security Updates**
   - Update base Docker images monthly
   - Update PHP dependencies regularly
   - Review and rotate secrets quarterly

2. **Performance Monitoring**
   - Monitor CloudWatch metrics
   - Review application logs
   - Check database performance

3. **Backup Verification**
   - Test RDS automated backups
   - Verify data recovery procedures
   - Test disaster recovery plans

### Contact Information

For deployment issues or questions:
- Infrastructure Team: infrastructure@payhero.com
- Development Team: dev@payhero.com
- On-call Support: +1-xxx-xxx-xxxx

---

**Last Updated**: July 16, 2025
**Version**: 1.0.0
**Environment**: production-minimal
**AWS Region**: us-east-1
