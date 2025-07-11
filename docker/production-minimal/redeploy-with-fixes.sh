#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Configuration
REGION="us-east-1"
ECR_REGISTRY="983877353757.dkr.ecr.us-east-1.amazonaws.com"
PROJECT_NAME="velana"
ENVIRONMENT="production-minimal"
CLUSTER_NAME="${PROJECT_NAME}-${ENVIRONMENT}-cluster"
SERVICE_NAME="${PROJECT_NAME}-${ENVIRONMENT}-service"

log_info "Starting redeployment with Redis authentication fixes..."

# Step 1: Remove Redis password parameters from SSM
log_info "Removing Redis password parameters from SSM Parameter Store..."
./docker/production-minimal/update-ssm-parameters.sh

# Step 2: Build Docker images
log_info "Building Docker images..."
docker build -f docker/production-minimal/Dockerfile.app -t ${PROJECT_NAME}-${ENVIRONMENT}-app .
docker build -f docker/production-minimal/Dockerfile.nginx -t ${PROJECT_NAME}-${ENVIRONMENT}-nginx .

# Step 3: Tag images
log_info "Tagging Docker images..."
docker tag ${PROJECT_NAME}-${ENVIRONMENT}-app:latest ${ECR_REGISTRY}/${PROJECT_NAME}-${ENVIRONMENT}-app:latest
docker tag ${PROJECT_NAME}-${ENVIRONMENT}-nginx:latest ${ECR_REGISTRY}/${PROJECT_NAME}-${ENVIRONMENT}-nginx:latest

# Step 4: Login to ECR
log_info "Logging in to ECR..."
aws ecr get-login-password --region ${REGION} | docker login --username AWS --password-stdin ${ECR_REGISTRY}

# Step 5: Push images
log_info "Pushing Docker images to ECR..."
docker push ${ECR_REGISTRY}/${PROJECT_NAME}-${ENVIRONMENT}-app:latest
docker push ${ECR_REGISTRY}/${PROJECT_NAME}-${ENVIRONMENT}-nginx:latest

# Step 6: Update task definition
log_info "Registering new task definition..."
TASK_DEFINITION_ARN=$(aws ecs register-task-definition \
    --cli-input-json file://docker/production-minimal/task-definition.json \
    --region ${REGION} \
    --query 'taskDefinition.taskDefinitionArn' \
    --output text)

log_info "New task definition registered: ${TASK_DEFINITION_ARN}"

# Step 7: Stop all running tasks
log_info "Stopping all running tasks..."
TASK_ARNS=$(aws ecs list-tasks --cluster ${CLUSTER_NAME} --service-name ${SERVICE_NAME} --query 'taskArns[]' --output text)
if [ -n "$TASK_ARNS" ]; then
    for TASK_ARN in $TASK_ARNS; do
        log_info "Stopping task: ${TASK_ARN}"
        aws ecs stop-task --cluster ${CLUSTER_NAME} --task ${TASK_ARN} --reason "Redeployment with Redis auth fix"
    done
    log_info "Waiting for tasks to stop..."
    sleep 30
fi

# Step 8: Update service with new task definition
log_info "Updating ECS service with new task definition..."
aws ecs update-service \
    --cluster ${CLUSTER_NAME} \
    --service ${SERVICE_NAME} \
    --task-definition ${TASK_DEFINITION_ARN} \
    --force-new-deployment \
    --region ${REGION}

# Step 9: Wait for service to stabilize
log_info "Waiting for service to stabilize..."
aws ecs wait services-stable \
    --cluster ${CLUSTER_NAME} \
    --services ${SERVICE_NAME} \
    --region ${REGION}

# Step 10: Get ALB URL
ALB_URL=$(aws elbv2 describe-load-balancers \
    --names "${PROJECT_NAME}-${ENVIRONMENT}-alb" \
    --query 'LoadBalancers[0].DNSName' \
    --output text 2>/dev/null || echo "ALB not found")

log_info "Deployment completed successfully!"
log_info "ALB URL: http://${ALB_URL}"
log_info "Please allow a few minutes for the containers to fully start."

# Step 11: Check service status
log_info "Current service status:"
aws ecs describe-services \
    --cluster ${CLUSTER_NAME} \
    --services ${SERVICE_NAME} \
    --query 'services[0].{RunningTasks:runningCount,DesiredTasks:desiredCount,PendingTasks:pendingCount}' \
    --output table