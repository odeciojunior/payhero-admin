# PayHero Production-Minimal AWS Deployment

This directory contains all the necessary scripts and configurations for deploying PayHero Manager to AWS using the production-minimal configuration for the Velana project.

## Overview

- **Project Name**: velana
- **Environment**: production-minimal
- **AWS Account**: 0 (replace with actual account ID)
- **Region**: us-east-1
- **Monthly Cost Target**: ~$109.50

## Directory Structure

```
docker/production-minimal/
├── buildspec.yml           # AWS CodeBuild specification
├── task-definition.json    # ECS task definition template
├── deploy-ecs.sh          # Main deployment script
├── trigger-codebuild.sh   # CodeBuild trigger script
├── health-check.sh        # Health monitoring script
├── monitor-costs.sh       # Cost monitoring script
├── run-migrations.sh      # Database migration runner
├── .env.production-minimal # Environment configuration template
└── README.md              # This file
```

## Prerequisites

1. AWS CLI configured with appropriate credentials
2. Docker images already tested in dev environment
3. AWS infrastructure provisioned (ECS cluster, ALB, RDS, etc.)
4. Environment variables configured in AWS Systems Manager Parameter Store

## Quick Start

### 1. Initial Setup

Update the AWS Account ID in all scripts:
```bash
find . -type f -name "*.sh" -o -name "*.yml" -o -name "*.json" | xargs sed -i 's/AWS_ACCOUNT_ID:-0/AWS_ACCOUNT_ID:-YOUR_ACCOUNT_ID/g'
```

### 2. Configure Environment Variables

Copy and update the environment file:
```bash
cp .env.production-minimal .env.production-minimal.local
# Edit the file with your actual values
```

### 3. Set up AWS Systems Manager Parameters

Create all required parameters in AWS Systems Manager:
```bash
aws ssm put-parameter --name "/payhero/production-minimal/APP_KEY" --value "your-app-key" --type SecureString
aws ssm put-parameter --name "/payhero/production-minimal/DB_PASSWORD" --value "your-db-password" --type SecureString
# ... add all other parameters
```

### 4. Deploy

Trigger a build and deployment:
```bash
# Build only
./trigger-codebuild.sh

# Build and deploy
./trigger-codebuild.sh --deploy --wait

# Full deployment with migrations
./trigger-codebuild.sh --deploy --wait --migrations
```

## Deployment Workflow

1. **Build Phase** (CodeBuild):
   - Pulls source code from repository
   - Builds frontend assets (`npm run prod`)
   - Creates Docker images (app and nginx)
   - Pushes images to Amazon ECR

2. **Deploy Phase** (ECS):
   - Updates task definition with new images
   - Updates ECS service
   - Performs rolling deployment
   - Waits for health checks

3. **Post-Deployment**:
   - Runs database migrations (if requested)
   - Performs health checks
   - Monitors deployment status

## Scripts Usage

### trigger-codebuild.sh

Triggers AWS CodeBuild to build and optionally deploy the application.

```bash
# Basic usage
./trigger-codebuild.sh

# Build from specific branch
./trigger-codebuild.sh --branch feature/my-feature

# Build and deploy
./trigger-codebuild.sh --deploy --wait

# Full deployment with migrations
./trigger-codebuild.sh --deploy --wait --migrations

# Custom image tag
./trigger-codebuild.sh --tag v1.2.3 --deploy
```

### deploy-ecs.sh

Deploys new Docker images to ECS cluster.

```bash
# Deploy latest images
./deploy-ecs.sh

# Deploy specific tag
./deploy-ecs.sh v1.2.3

# Deploy without health checks
./deploy-ecs.sh latest true
```

### health-check.sh

Performs comprehensive health checks on the deployment.

```bash
# Run health check
./health-check.sh

# Verbose mode
./health-check.sh --verbose
```

### monitor-costs.sh

Monitors AWS costs for the production-minimal environment.

```bash
# Show current costs
./monitor-costs.sh

# Generate detailed report
./monitor-costs.sh --report
```

### run-migrations.sh

Runs database migrations as an ECS task.

```bash
# Run migrations only
./run-migrations.sh

# Run migrations and seeders
./run-migrations.sh --seed
```

## Configuration

### Environment Variables

Key environment variables (configured in AWS Systems Manager):

- `APP_NAME`: "Manage Velana"
- `PROJECT_NAME`: velana
- `ENVIRONMENT`: production-minimal
- `APP_ENV`: production
- `APP_DEBUG`: false

### AWS Resources

Expected AWS resources:

- **CodeBuild Project**: payhero-production-minimal-build
- **ECS Cluster**: payhero-production-minimal-cluster
- **ECS Service**: payhero-production-minimal-service
- **Task Definition**: payhero-production-minimal-app
- **ECR Repositories**: 
  - payhero-production-minimal-app
  - payhero-production-minimal-nginx
- **ALB**: payhero-production-minimal-alb

### Cost Optimization

The production-minimal configuration uses:

- Fargate Spot (80%) for cost savings
- Single AZ deployment
- t4g.micro instances (ARM-based)
- Minimal resource allocations
- File-based caching instead of Redis for some features

## Monitoring

### CloudWatch Logs

- Log Group: `/ecs/payhero-production-minimal`
- App logs: `app/*` stream prefix
- Nginx logs: `nginx/*` stream prefix

### Metrics

Key metrics to monitor:
- ECS CPU/Memory utilization
- ALB request count and latency
- RDS connections and CPU
- Application health endpoints

## Troubleshooting

### Common Issues

1. **Build Failures**
   - Check CodeBuild logs in AWS Console
   - Verify npm dependencies are installed
   - Check Docker build context

2. **Deployment Failures**
   - Check ECS service events
   - Verify task definition is valid
   - Check container health checks

3. **Application Errors**
   - Check CloudWatch logs
   - Verify environment variables
   - Test database connectivity

### Debug Commands

```bash
# Check ECS service status
aws ecs describe-services --cluster payhero-production-minimal-cluster --services payhero-production-minimal-service

# View recent logs
aws logs tail /ecs/payhero-production-minimal --follow

# Check task status
aws ecs list-tasks --cluster payhero-production-minimal-cluster --service-name payhero-production-minimal-service
```

## Security Notes

- Never commit `.env` files with actual values
- Use AWS Systems Manager for all secrets
- Regularly rotate database passwords and API keys
- Monitor AWS CloudTrail for audit logs

## Support

For issues or questions:
1. Check CloudWatch logs first
2. Run health-check.sh for diagnostics
3. Review this README and script help messages
4. Contact the DevOps team if needed