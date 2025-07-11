# Velana Production-Minimal Deployment Guide

## Quick Deployment Steps

### 1. Prerequisites Check

First, ensure your AWS CLI is configured with the correct account:

```bash
aws sts get-caller-identity
# Should show Account: 983877353757
```

### 2. Infrastructure Setup

Run the infrastructure setup script to verify all AWS resources exist:

```bash
./setup-infrastructure.sh
```

This will check for:
- ECR repositories
- ECS cluster
- IAM roles
- CloudWatch log groups
- Systems Manager parameters
- CodeBuild project

### 3. Configure Secrets

Create the required parameters in AWS Systems Manager Parameter Store:

```bash
# Example commands (replace with actual values)
aws ssm put-parameter --name "/payhero/production-minimal/APP_KEY" --value "base64:your-key-here" --type SecureString
aws ssm put-parameter --name "/payhero/production-minimal/DB_PASSWORD" --value "your-password" --type SecureString
aws ssm put-parameter --name "/payhero/production-minimal/DB_HOST" --value "your-rds-endpoint.rds.amazonaws.com" --type String
# ... add all other required parameters
```

### 4. Deploy Application

Run the main deployment script:

```bash
# Full deployment (build, deploy, health check)
./deploy-velana.sh

# Skip build if images already exist
./deploy-velana.sh --skip-build

# Use local Docker build instead of CodeBuild
./deploy-velana.sh --use-local-build
```

### 5. Alternative Deployment Methods

#### Using CodeBuild directly:
```bash
./trigger-codebuild.sh --deploy --wait
```

#### Using ECS deployment only:
```bash
./deploy-ecs.sh
```

### 6. Post-Deployment

After deployment:

1. **Check health status:**
   ```bash
   ./health-check.sh
   ```

2. **Monitor costs:**
   ```bash
   ./monitor-costs.sh
   ```

3. **Run migrations (if needed):**
   ```bash
   ./run-migrations.sh
   ```

## Key Information

- **Project Name**: velana
- **Environment**: production-minimal
- **AWS Account**: 983877353757
- **Region**: us-east-1
- **Target Cost**: ~$109.50/month

## Resource Naming Convention

All resources follow this pattern:
- **ECS Cluster**: payhero-production-minimal-cluster
- **ECS Service**: payhero-production-minimal-service
- **ECR Repos**: payhero-production-minimal-app, payhero-production-minimal-nginx
- **ALB**: payhero-production-minimal-alb
- **Log Group**: /ecs/payhero-production-minimal

## Troubleshooting

### Build Issues
```bash
# Check CodeBuild logs
aws codebuild batch-get-builds --ids <build-id> --query 'builds[0].logs'
```

### Deployment Issues
```bash
# Check ECS service events
aws ecs describe-services --cluster payhero-production-minimal-cluster --services payhero-production-minimal-service
```

### Application Issues
```bash
# View application logs
aws logs tail /ecs/payhero-production-minimal --follow
```

## Important Notes

1. The deployment uses the existing Docker images from the dev environment
2. Ensure all environment variables are properly set in Systems Manager
3. The production-minimal configuration is optimized for cost, not high availability
4. Database migrations are not run automatically - use `./run-migrations.sh` when needed

## Support

For issues:
1. Check the health status with `./health-check.sh`
2. Review CloudWatch logs
3. Verify all SSM parameters are set correctly
4. Check the README.md for detailed documentation