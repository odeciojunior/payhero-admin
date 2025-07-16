# PayHero Admin Module - Quick Deployment Guide

This guide provides step-by-step instructions for deploying the PayHero Admin module to AWS ECS.

## Quick Start

### 1. First-Time Setup (One-time only)

```bash
# Navigate to the deployment directory
cd docker/production-minimal

# Create the admin-specific ECS service
./create-ecs-service-admin.sh
```

### 2. Deploy Admin Module

```bash
# Option A: Full pipeline (recommended)
./pipeline.sh

# Option B: Step-by-step deployment
./build.sh        # Build admin images
./push.sh         # Push to ECR
./deploy.sh       # Deploy to ECS
```

## Module-Specific Resources

The admin module uses dedicated resources to avoid conflicts:

| Resource Type | Name |
|--------------|------|
| ECR App Repo | `velana-production-minimal-admin-app` |
| ECR Nginx Repo | `velana-production-minimal-admin-nginx` |
| ECS Service | `velana-production-minimal-admin-service` |
| Task Family | `velana-production-minimal-admin-task` |
| CloudWatch Logs | `/ecs/velana-production-minimal-admin` |
| ALB Path | `/admin/*` |

## Environment Variables

All scripts use `MODULE_NAME=admin` by default. To deploy a different module:

```bash
MODULE_NAME=api ./pipeline.sh
```

## Access URLs

After deployment, access the admin module at:

- **Admin URL**: `http://<ALB-DNS>/admin`
- **Health Check**: `http://<ALB-DNS>/admin/health`

## Common Commands

### Check Deployment Status
```bash
aws ecs describe-services \
  --cluster velana-production-minimal-cluster \
  --services velana-production-minimal-admin-service \
  --region us-east-1 \
  --query 'services[0].deployments'
```

### View Logs
```bash
# Application logs
aws logs tail /ecs/velana-production-minimal-admin --follow --filter-pattern "admin-app"

# Nginx logs
aws logs tail /ecs/velana-production-minimal-admin --follow --filter-pattern "admin-nginx"
```

### Force Redeploy
```bash
FORCE_DEPLOYMENT=true ./deploy.sh
```

### Deploy Specific Version
```bash
./pipeline.sh v1.2.3
```

## Troubleshooting

### Service Won't Start
1. Check CloudWatch logs for errors
2. Verify task definition has correct image URIs
3. Ensure secrets in Parameter Store exist

### Health Checks Failing
1. Verify ALB target group health check path is `/health`
2. Check security group allows traffic on port 80
3. Ensure nginx configuration is correct

### Images Not Found
1. Verify images exist in ECR
2. Check AWS credentials and region
3. Ensure repository names match module name

## Rollback

The deployment automatically rolls back on failure. For manual rollback:

```bash
# Deploy previous version
./deploy.sh <previous-tag>
```

## Module Isolation

Each module (admin, api, web) has:
- Separate ECR repositories
- Dedicated ECS service
- Independent task definitions
- Isolated CloudWatch log streams
- Module-specific ALB routing rules

This ensures:
- No resource conflicts between modules
- Independent scaling and deployment
- Module-specific monitoring
- Isolated failure domains

---

For detailed documentation, see [DEPLOYMENT-WORKFLOW.md](DEPLOYMENT-WORKFLOW.md)