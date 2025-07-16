# PayHero Admin - AWS Deployment Quick Reference

## 🚀 Quick Start Commands

### 1. Automated Deployment (Recommended)
```bash
# Full automated deployment
./deploy-to-aws.sh v1.0.0

# Deploy latest tag
./deploy-to-aws.sh

# Deploy with custom options
./deploy-to-aws.sh v1.0.0 --no-cleanup
```

### 2. Manual Step-by-Step Deployment
```bash
# Navigate to deployment directory
cd docker/production-minimal

# One-time setup (first deployment only)
./create-ecs-service-admin.sh

# Build, push, and deploy
./pipeline.sh v1.0.0
```

### 3. Individual Commands
```bash
# Build images only
./build.sh v1.0.0

# Push to ECR only
./push.sh v1.0.0

# Deploy to ECS only
./deploy.sh v1.0.0
```

## 📋 Pre-Deployment Checklist

### Environment Setup
- [ ] AWS CLI configured with proper credentials
- [ ] Docker daemon running
- [ ] Node.js 16+ and npm installed
- [ ] PHP 8.2+ and Composer installed
- [ ] jq tool installed

### AWS Infrastructure
- [ ] VPC with public/private subnets created
- [ ] Security groups configured
- [ ] RDS MySQL cluster created
- [ ] ElastiCache Redis cluster created
- [ ] Application Load Balancer created
- [ ] ECS cluster created
- [ ] ECR repositories created
- [ ] IAM roles and policies configured

### Application Configuration
- [ ] `.env.production-minimal` file configured
- [ ] `modules_statuses.json` file exists
- [ ] SSM parameters created in AWS
- [ ] SSL certificate (if using HTTPS)

## 🔧 Environment Variables

### Required Variables
```bash
export PROJECT_NAME="velana"
export ENVIRONMENT="production-minimal"
export MODULE_NAME="admin"
export AWS_REGION="us-east-1"
export AWS_ACCOUNT_ID="983877353757"
```

### Optional Variables
```bash
export SKIP_BUILD=false
export SKIP_PUSH=false
export SKIP_DEPLOY=false
export CLEANUP_LOCAL_IMAGES=true
```

## 📊 Monitoring Commands

### ECS Service Status
```bash
aws ecs describe-services \
  --cluster payhero-production-minimal-cluster \
  --services velana-production-minimal-admin-service
```

### View Logs
```bash
# Real-time logs
aws logs tail /ecs/velana-production-minimal-admin --follow

# Specific time range
aws logs get-log-events \
  --log-group-name /ecs/velana-production-minimal-admin \
  --log-stream-name ecs/admin-app/TASK_ID
```

### Health Check
```bash
# Test health endpoint
curl -f http://your-alb-dns/admin/health

# Check ALB target health
aws elbv2 describe-target-health \
  --target-group-arn arn:aws:elasticloadbalancing:us-east-1:983877353757:targetgroup/payhero-admin-tg/xxx
```

## 🛠️ Troubleshooting Quick Fixes

### Module Loading Issues
```bash
# Recreate modules status file
php artisan module:list --only=enabled > modules_statuses.json

# Check module configuration
php artisan module:check
```

### Storage Permission Issues
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

### Asset Compilation Issues
```bash
# Clear and rebuild assets
npm install
npm run production
php artisan view:clear
```

### Database Connection Issues
```bash
# Test database connection
php artisan tinker
> DB::connection()->getPdo();

# Run migrations
php artisan migrate --step
```

### Memory Issues
```bash
# Increase task memory in task definition
# cpu: "2048" (2 vCPU)
# memory: "4096" (4 GB)
```

## 🔍 Health Check Endpoints

### Application Health
```bash
GET /health
Response: {
  "status": "healthy",
  "timestamp": "2025-07-16T10:00:00Z",
  "app": "PayHero Admin",
  "environment": "production",
  "modules_loaded": 45,
  "php_version": "8.2.x",
  "laravel_version": "9.x"
}
```

### Component Health Checks
```bash
# Database
GET /health/database

# Redis
GET /health/redis

# Storage
GET /health/storage
```

## 📈 Performance Optimization

### Resource Allocation
```yaml
# Minimum (Development)
cpu: "512"
memory: "1024"

# Recommended (Production)
cpu: "1024"
memory: "2048"

# High Load (Production)
cpu: "2048"
memory: "4096"
```

### Auto Scaling Configuration
```bash
# Set up auto scaling
aws application-autoscaling register-scalable-target \
  --service-namespace ecs \
  --scalable-dimension ecs:service:DesiredCount \
  --resource-id service/payhero-production-minimal-cluster/velana-production-minimal-admin-service \
  --min-capacity 1 \
  --max-capacity 3
```

## 🔒 Security Checklist

### Network Security
- [ ] Security groups restrict access to necessary ports only
- [ ] Database not publicly accessible
- [ ] Redis not publicly accessible
- [ ] VPC properly configured with private subnets

### Application Security
- [ ] APP_DEBUG=false in production
- [ ] FORCE_HTTPS=true
- [ ] SESSION_SECURE_COOKIE=true
- [ ] Strong database passwords
- [ ] APP_KEY properly generated

### AWS Security
- [ ] IAM roles follow principle of least privilege
- [ ] SSM parameters encrypted
- [ ] CloudTrail logging enabled
- [ ] VPC Flow Logs enabled

## 📞 Emergency Procedures

### Rollback Deployment
```bash
# Get previous task definition revision
aws ecs describe-task-definition --task-definition velana-production-minimal-admin-task

# Rollback to previous revision
aws ecs update-service \
  --cluster payhero-production-minimal-cluster \
  --service velana-production-minimal-admin-service \
  --task-definition velana-production-minimal-admin-task:PREVIOUS_REVISION
```

### Scale Down/Up Service
```bash
# Scale down to 0 (emergency stop)
aws ecs update-service \
  --cluster payhero-production-minimal-cluster \
  --service velana-production-minimal-admin-service \
  --desired-count 0

# Scale up to 2 instances
aws ecs update-service \
  --cluster payhero-production-minimal-cluster \
  --service velana-production-minimal-admin-service \
  --desired-count 2
```

### View Recent Errors
```bash
# Check last 100 log events with errors
aws logs filter-log-events \
  --log-group-name /ecs/velana-production-minimal-admin \
  --filter-pattern "ERROR" \
  --max-items 100
```

## 📝 Common File Locations

### Configuration Files
```
├── config/
│   ├── app.php                 # Application configuration
│   ├── database.php           # Database configuration
│   ├── cache.php              # Cache configuration
│   ├── queue.php              # Queue configuration
│   └── modules.php            # Laravel modules configuration
├── .env.production-minimal    # Environment variables
├── modules_statuses.json      # Module activation status
└── docker/
    └── production-minimal/
        ├── Dockerfile.app     # Application container
        ├── Dockerfile.nginx   # Nginx container
        └── config/            # Container configuration
```

### Deployment Scripts
```
├── deploy-to-aws.sh           # Automated deployment script
├── docker/production-minimal/
│   ├── build.sh              # Build Docker images
│   ├── push.sh               # Push to ECR
│   ├── deploy.sh             # Deploy to ECS
│   ├── pipeline.sh           # Full deployment pipeline
│   └── create-ecs-service-admin.sh  # Create ECS service
```

### AWS Resources
```
ECS Cluster: payhero-production-minimal-cluster
ECS Service: velana-production-minimal-admin-service
Task Family: velana-production-minimal-admin-task
ECR Repos:   velana-production-minimal-admin-app
             velana-production-minimal-admin-nginx
Log Group:   /ecs/velana-production-minimal-admin
ALB:         payhero-production-minimal-alb
```

## 🆘 Support Contacts

### Internal Team
- Infrastructure Team: infrastructure@payhero.com
- Development Team: dev@payhero.com
- DevOps Lead: devops@payhero.com

### Emergency Escalation
- On-call Engineer: +1-xxx-xxx-xxxx
- Technical Director: +1-xxx-xxx-xxxx

### AWS Support
- AWS Account ID: 983877353757
- Support Plan: Business/Enterprise
- Support Case: https://console.aws.amazon.com/support/

---

**Last Updated**: July 16, 2025
**Version**: 1.0.0
**Maintained by**: DevOps Team
