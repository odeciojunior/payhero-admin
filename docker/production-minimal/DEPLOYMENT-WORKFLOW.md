# PayHero Admin Module - Production-Minimal Deployment Workflow

This document describes the refactored build, push, and deploy workflow for the PayHero **admin module** container in the production-minimal AWS environment. The workflow ensures proper isolation from other modules with dedicated naming conventions.

## Overview

The deployment workflow has been modularized into separate scripts for better maintainability, error handling, and flexibility:

1. **build.sh** - Builds Docker images locally with admin-specific tags
2. **push.sh** - Pushes images to AWS ECR with module isolation
3. **deploy.sh** - Deploys to AWS ECS with rollback capabilities
4. **pipeline.sh** - Orchestrates the complete CI/CD pipeline
5. **create-ecs-service-admin.sh** - Creates the admin-specific ECS service

## Module Naming Conventions

To avoid conflicts with other modules, the admin module uses specific naming:

- **ECR Repositories**: 
  - `velana-production-minimal-admin-app`
  - `velana-production-minimal-admin-nginx`
- **ECS Resources**:
  - Service: `velana-production-minimal-admin-service`
  - Task Family: `velana-production-minimal-admin-task`
  - Container Names: `admin-app`, `admin-nginx`
- **CloudWatch Logs**: `/ecs/velana-production-minimal-admin`
- **ALB Path**: `/admin/*`

## Prerequisites

- Docker installed and running
- AWS CLI v2 installed and configured
- jq installed for JSON processing
- npm/node installed (for frontend builds)
- Appropriate AWS credentials with permissions for ECR and ECS

## Individual Scripts

### 1. Build Script (`build.sh`)

Builds the application and nginx Docker images locally.

```bash
# Build with default tag (latest)
./build.sh

# Build with specific tag
./build.sh v1.2.3

# Build without cache
NO_CACHE=true ./build.sh

# Build only app image
BUILD_NGINX=false ./build.sh
```

**Features:**
- Builds frontend assets automatically
- Supports multi-stage Docker builds
- Tags images with multiple tags (full timestamp, specified tag, latest)
- Clean error handling and logging

### 2. Push Script (`push.sh`)

Pushes Docker images to AWS ECR.

```bash
# Push images with default tag
./push.sh

# Push specific tag
./push.sh v1.2.3

# Push only app image
PUSH_NGINX=false ./push.sh
```

**Features:**
- Automatic ECR login
- Creates ECR repositories if they don't exist
- Sets lifecycle policies to keep only last 10 images
- Verifies images exist locally before pushing
- Verifies images in ECR after push

### 3. Deploy Script (`deploy.sh`)

Deploys the application to AWS ECS with advanced features.

```bash
# Deploy latest images
./deploy.sh

# Deploy specific tag
./deploy.sh v1.2.3

# Deploy without health checks
./deploy.sh v1.2.3 true

# Force new deployment
FORCE_DEPLOYMENT=true ./deploy.sh

# Deploy without rollback
ENABLE_ROLLBACK=false ./deploy.sh
```

**Features:**
- Automatic rollback on failure
- Health check validation
- Deployment progress monitoring
- Zero-downtime deployments
- Configurable wait times and retry attempts

### 4. Pipeline Script (`pipeline.sh`)

Orchestrates the complete CI/CD pipeline.

```bash
# Run complete pipeline with auto-generated tag
./pipeline.sh

# Run pipeline with specific tag
./pipeline.sh v1.2.3

# Run pipeline without tests
RUN_TESTS=false ./pipeline.sh

# Run pipeline with migrations
RUN_MIGRATIONS=true ./pipeline.sh

# Run pipeline with Slack notifications
SEND_NOTIFICATIONS=true SLACK_WEBHOOK_URL="https://..." ./pipeline.sh
```

**Features:**
- Pre-flight checks
- Optional test execution
- Build, push, and deploy orchestration
- Optional database migrations
- Post-deployment tasks (cache clearing, git tagging)
- Slack notifications support
- Comprehensive error handling

## Environment Variables

### Common Variables

```bash
# AWS Configuration
AWS_REGION=us-east-1           # AWS region
AWS_ACCOUNT_ID=983877353757    # AWS account ID

# Project Configuration
PROJECT_NAME=velana            # Project name
MODULE_NAME=admin              # Module name (admin, api, web, etc.)
ENVIRONMENT=production-minimal # Environment name
```

### Build Variables

```bash
BUILD_APP=true      # Build application image
BUILD_NGINX=true    # Build nginx image
NO_CACHE=false      # Build without Docker cache
```

### Push Variables

```bash
PUSH_APP=true       # Push application image
PUSH_NGINX=true     # Push nginx image
```

### Deploy Variables

```bash
CLUSTER_NAME=velana-production-minimal-cluster  # ECS cluster name
SERVICE_NAME=velana-production-minimal-service  # ECS service name
ENABLE_ROLLBACK=true                            # Enable automatic rollback
FORCE_DEPLOYMENT=false                          # Force new deployment
MAX_WAIT_ATTEMPTS=60                            # Max deployment wait attempts
WAIT_INTERVAL=10                                # Wait interval in seconds
```

### Pipeline Variables

```bash
RUN_TESTS=true              # Run tests before build
RUN_MIGRATIONS=false        # Run database migrations
CLEANUP_IMAGES=true         # Cleanup old Docker images
SEND_NOTIFICATIONS=false    # Send Slack notifications
SLACK_WEBHOOK_URL=""        # Slack webhook URL
```

## Typical Workflows

### First-Time Setup

```bash
# Create the admin-specific ECS service
./create-ecs-service-admin.sh

# Then deploy
./pipeline.sh
```

### Development Deployment

```bash
# Quick deployment with auto-generated tag
./pipeline.sh
```

### Production Deployment

```bash
# Full pipeline with specific version
RUN_TESTS=true ./pipeline.sh v1.2.3

# With migrations (use with caution!)
RUN_MIGRATIONS=true ./pipeline.sh v1.2.3
```

### Hotfix Deployment

```bash
# Skip tests for urgent fixes
RUN_TESTS=false FORCE_DEPLOYMENT=true ./deploy.sh hotfix-1.2.4
```

### Rollback

The deploy script automatically rolls back on failure. For manual rollback:

```bash
# Deploy previous version
./deploy.sh v1.2.2
```

## CI/CD Integration

### GitHub Actions Example

```yaml
name: Deploy to Production-Minimal

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      
      - name: Configure AWS credentials
        uses: aws-actions/configure-aws-credentials@v1
        with:
          aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
          aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
          aws-region: us-east-1
      
      - name: Run deployment pipeline
        run: |
          cd docker/production-minimal
          ./pipeline.sh
```

### GitLab CI Example

```yaml
deploy:production-minimal:
  stage: deploy
  only:
    - main
  script:
    - cd docker/production-minimal
    - ./pipeline.sh
  environment:
    name: production-minimal
    url: http://velana-production-minimal-alb.us-east-1.elb.amazonaws.com
```

## Monitoring and Debugging

### View Deployment Status

```bash
# Check admin service status
aws ecs describe-services \
  --cluster velana-production-minimal-cluster \
  --services velana-production-minimal-admin-service \
  --region us-east-1

# View running tasks
aws ecs list-tasks \
  --cluster velana-production-minimal-cluster \
  --service-name velana-production-minimal-admin-service \
  --region us-east-1
```

### View Logs

```bash
# View CloudWatch logs for admin module
aws logs tail /ecs/velana-production-minimal-admin --follow
```

### SSH into Container

```bash
# Execute command in running container
aws ecs execute-command \
  --cluster velana-production-minimal-cluster \
  --task <TASK_ARN> \
  --container app \
  --command "/bin/bash" \
  --interactive
```

## Troubleshooting

### Build Failures

1. Check Docker daemon is running
2. Verify Dockerfile syntax
3. Check for missing build dependencies
4. Review build logs for specific errors

### Push Failures

1. Verify AWS credentials
2. Check ECR repository permissions
3. Ensure images exist locally
4. Check network connectivity

### Deployment Failures

1. Check ECS service and cluster status
2. Verify task definition is valid
3. Check container health checks
4. Review CloudWatch logs
5. Verify security groups and network configuration

### Common Issues

**Issue**: Deployment times out
**Solution**: Increase `MAX_WAIT_ATTEMPTS` or check task logs for startup issues

**Issue**: Health checks fail
**Solution**: Verify ALB target group health check settings match application endpoints

**Issue**: Rollback fails
**Solution**: Ensure previous task definition is still valid and images exist in ECR

## Security Best Practices

1. Never commit AWS credentials to version control
2. Use IAM roles for ECS tasks instead of access keys
3. Enable ECR image scanning
4. Regularly update base images
5. Use secrets manager for sensitive environment variables
6. Enable CloudTrail for audit logging

## Maintenance

### Cleaning Up Old Images

```bash
# Manual cleanup
docker image prune -a -f --filter "until=24h"

# ECR cleanup (automatic via lifecycle policy)
```

### Updating Scripts

When updating deployment scripts:
1. Test in a staging environment first
2. Keep backwards compatibility
3. Update this documentation
4. Tag the repository with script version

## Support

For issues or questions:
1. Check CloudWatch logs
2. Review ECS service events
3. Consult AWS documentation
4. Contact DevOps team

---

Last Updated: $(date)
Version: 1.0.0