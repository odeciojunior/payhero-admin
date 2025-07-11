#!/bin/bash

# Velana Production-Minimal ECS Deployment Script
# Project: Velana
# Environment: production-minimal

set -euo pipefail

# Configuration
AWS_REGION=${AWS_REGION:-us-east-1}
AWS_ACCOUNT_ID=${AWS_ACCOUNT_ID:-983877353757}  # Replace with actual account ID
PROJECT_NAME=${PROJECT_NAME:-velana}
ENVIRONMENT="production-minimal"
CLUSTER_NAME="${PROJECT_NAME}-${ENVIRONMENT}-cluster"
SERVICE_NAME="${PROJECT_NAME}-${ENVIRONMENT}-service"
TASK_FAMILY="${PROJECT_NAME}-${ENVIRONMENT}-app"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Helper functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check prerequisites
check_prerequisites() {
    log_info "Checking prerequisites..."
    
    if ! command -v aws &> /dev/null; then
        log_error "AWS CLI not found. Please install AWS CLI."
        exit 1
    fi
    
    if ! command -v jq &> /dev/null; then
        log_error "jq not found. Please install jq."
        exit 1
    fi
    
    # Check AWS credentials
    if ! aws sts get-caller-identity &> /dev/null; then
        log_error "AWS credentials not configured properly."
        exit 1
    fi
    
    log_info "Prerequisites check passed."
}

# Get latest image tags
get_latest_images() {
    local image_tag=${1:-latest}
    
    log_info "Getting latest image tags..."
    
    APP_IMAGE="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/${PROJECT_NAME}-${ENVIRONMENT}-app:$image_tag"
    NGINX_IMAGE="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/${PROJECT_NAME}-${ENVIRONMENT}-nginx:$image_tag"
    
    # Verify images exist
    if ! aws ecr describe-images --repository-name ${PROJECT_NAME}-${ENVIRONMENT}-app --image-ids imageTag=$image_tag --region $AWS_REGION &> /dev/null; then
        log_error "App image not found: $APP_IMAGE"
        exit 1
    fi
    
    if ! aws ecr describe-images --repository-name ${PROJECT_NAME}-${ENVIRONMENT}-nginx --image-ids imageTag=$image_tag --region $AWS_REGION &> /dev/null; then
        log_error "Nginx image not found: $NGINX_IMAGE"
        exit 1
    fi
    
    log_info "App image: $APP_IMAGE"
    log_info "Nginx image: $NGINX_IMAGE"
}

# Create new task definition
create_task_definition() {
    log_info "Creating new task definition..."
    
    # Try to get current task definition
    CURRENT_TASK_DEF=$(aws ecs describe-task-definition \
        --task-definition $TASK_FAMILY \
        --region $AWS_REGION \
        --query 'taskDefinition' 2>/dev/null || echo "")
    
    # If no task definition exists, use the template
    if [ -z "$CURRENT_TASK_DEF" ] || [ "$CURRENT_TASK_DEF" = "" ]; then
        log_info "No existing task definition found. Using template..."
        if [ -f "task-definition.json" ]; then
            CURRENT_TASK_DEF=$(cat task-definition.json)
        else
            log_error "No task definition template found at task-definition.json"
            exit 1
        fi
    fi
    
    # Create new task definition with updated images
    NEW_TASK_DEF=$(echo $CURRENT_TASK_DEF | jq --arg app_image "$APP_IMAGE" --arg nginx_image "$NGINX_IMAGE" '
        .containerDefinitions[0].image = $app_image |
        .containerDefinitions[1].image = $nginx_image |
        del(.taskDefinitionArn) |
        del(.revision) |
        del(.status) |
        del(.requiresAttributes) |
        del(.compatibilities) |
        del(.registeredAt) |
        del(.registeredBy)
    ')
    
    # Add environment variables
    NEW_TASK_DEF=$(echo $NEW_TASK_DEF | jq --arg project_name "$PROJECT_NAME" --arg env "$ENVIRONMENT" '
        .containerDefinitions[0].environment += [
            {"name": "PROJECT_NAME", "value": $project_name},
            {"name": "ENVIRONMENT", "value": $env}
        ]
    ')
    
    # Register new task definition
    TASK_DEF_ARN=$(aws ecs register-task-definition \
        --cli-input-json "$NEW_TASK_DEF" \
        --region $AWS_REGION \
        --query 'taskDefinition.taskDefinitionArn' \
        --output text)
    
    log_info "New task definition registered: $TASK_DEF_ARN"
}

# Stop all running tasks
stop_all_tasks() {
    log_info "Stopping all running tasks..."
    
    # Get all running tasks
    RUNNING_TASKS=$(aws ecs list-tasks \
        --cluster $CLUSTER_NAME \
        --service-name $SERVICE_NAME \
        --region $AWS_REGION \
        --desired-status RUNNING \
        --query 'taskArns[]' \
        --output text 2>/dev/null || echo "")
    
    if [ -n "$RUNNING_TASKS" ] && [ "$RUNNING_TASKS" != "None" ]; then
        log_info "Found running tasks, stopping them..."
        for task in $RUNNING_TASKS; do
            task_id=$(echo $task | rev | cut -d'/' -f1 | rev)
            log_info "Stopping task: $task_id"
            aws ecs stop-task \
                --cluster $CLUSTER_NAME \
                --task $task \
                --region $AWS_REGION \
                --output text >/dev/null 2>&1 || true
        done
        
        # Wait for tasks to stop
        log_info "Waiting for tasks to stop..."
        sleep 10
    else
        log_info "No running tasks found."
    fi
}

# Update ECS service
update_ecs_service() {
    log_info "Updating ECS service..."
    
    # Check if service exists
    SERVICE_EXISTS=$(aws ecs describe-services \
        --cluster $CLUSTER_NAME \
        --services $SERVICE_NAME \
        --region $AWS_REGION \
        --query 'services[0].status' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$SERVICE_EXISTS" ] || [ "$SERVICE_EXISTS" = "None" ]; then
        log_error "ECS service '$SERVICE_NAME' not found in cluster '$CLUSTER_NAME'"
        log_info "Please create the ECS service first using AWS Console or CLI"
        log_info "Example: aws ecs create-service --cluster $CLUSTER_NAME --service-name $SERVICE_NAME --task-definition $TASK_DEF_ARN --desired-count 1 --launch-type FARGATE --network-configuration 'awsvpcConfiguration={subnets=[subnet-xxx],securityGroups=[sg-xxx],assignPublicIp=DISABLED}'"
        exit 1
    fi
    
    # Stop all running tasks first
    stop_all_tasks
    
    # Update service with new task definition
    aws ecs update-service \
        --cluster $CLUSTER_NAME \
        --service $SERVICE_NAME \
        --task-definition $TASK_DEF_ARN \
        --region $AWS_REGION \
        --force-new-deployment \
        --query 'service.serviceName' \
        --output text
    
    log_info "Service update initiated."
}

# Wait for deployment
wait_for_deployment() {
    log_info "Waiting for deployment to complete..."
    
    local max_attempts=60
    local attempt=0
    
    while [ $attempt -lt $max_attempts ]; do
        # Get service status
        DEPLOYMENT_STATUS=$(aws ecs describe-services \
            --cluster $CLUSTER_NAME \
            --services $SERVICE_NAME \
            --region $AWS_REGION \
            --query 'services[0].deployments[?status==`PRIMARY`].rolloutState' \
            --output text)
        
        RUNNING_COUNT=$(aws ecs describe-services \
            --cluster $CLUSTER_NAME \
            --services $SERVICE_NAME \
            --region $AWS_REGION \
            --query 'services[0].runningCount' \
            --output text)
        
        DESIRED_COUNT=$(aws ecs describe-services \
            --cluster $CLUSTER_NAME \
            --services $SERVICE_NAME \
            --region $AWS_REGION \
            --query 'services[0].desiredCount' \
            --output text)
        
        log_info "Deployment status: $DEPLOYMENT_STATUS (Running: $RUNNING_COUNT/$DESIRED_COUNT)"
        
        if [ "$DEPLOYMENT_STATUS" = "COMPLETED" ] && [ "$RUNNING_COUNT" = "$DESIRED_COUNT" ]; then
            log_info "Deployment completed successfully!"
            return 0
        elif [ "$DEPLOYMENT_STATUS" = "FAILED" ]; then
            log_error "Deployment failed!"
            return 1
        fi
        
        sleep 10
        ((attempt++))
    done
    
    log_error "Deployment timed out after $max_attempts attempts."
    return 1
}

# Get deployment info
get_deployment_info() {
    log_info "Getting deployment information..."
    
    # Get ALB DNS
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --names ${PROJECT_NAME}-${ENVIRONMENT}-alb \
        --region $AWS_REGION \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null || echo "ALB not found")
    
    # Get task details
    TASK_ARNS=$(aws ecs list-tasks \
        --cluster $CLUSTER_NAME \
        --service-name $SERVICE_NAME \
        --region $AWS_REGION \
        --query 'taskArns' \
        --output json)
    
    if [ "$TASK_ARNS" != "[]" ]; then
        TASKS=$(aws ecs describe-tasks \
            --cluster $CLUSTER_NAME \
            --tasks $(echo $TASK_ARNS | jq -r '.[]') \
            --region $AWS_REGION)
        
        log_info "Running tasks:"
        echo $TASKS | jq -r '.tasks[] | "  - \(.taskArn | split("/") | .[-1]): \(.lastStatus)"'
    fi
    
    log_info "Application URL: http://$ALB_DNS"
    log_info "Project: $PROJECT_NAME"
    log_info "Environment: $ENVIRONMENT"
}

# Run health checks
run_health_checks() {
    log_info "Running health checks..."
    
    # Get ALB DNS
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --names ${PROJECT_NAME}-${ENVIRONMENT}-alb \
        --region $AWS_REGION \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null)
    
    if [ -z "$ALB_DNS" ] || [ "$ALB_DNS" = "None" ]; then
        log_warn "ALB not found, skipping health checks."
        return 0
    fi
    
    # Check health endpoints
    local endpoints=("/health" "/health.php" "/nginx-health")
    
    for endpoint in "${endpoints[@]}"; do
        if curl -f -s -o /dev/null "http://$ALB_DNS$endpoint"; then
            log_info "Health check passed: $endpoint"
        else
            log_warn "Health check failed: $endpoint"
        fi
    done
}

# Main deployment flow
main() {
    log_info "Starting ${PROJECT_NAME} ${ENVIRONMENT} deployment"
    log_info "Project: $PROJECT_NAME"
    log_info "Environment: $ENVIRONMENT"
    log_info "Region: $AWS_REGION"
    
    # Parse command line arguments
    IMAGE_TAG=${1:-latest}
    SKIP_HEALTH_CHECK=${2:-false}
    
    # Run deployment steps
    check_prerequisites
    get_latest_images "$IMAGE_TAG"
    create_task_definition
    update_ecs_service
    
    if wait_for_deployment; then
        get_deployment_info
        
        if [ "$SKIP_HEALTH_CHECK" != "true" ]; then
            sleep 30  # Wait for ALB to update
            run_health_checks
        fi
        
        log_info "Deployment completed successfully!"
        exit 0
    else
        log_error "Deployment failed!"
        exit 1
    fi
}

# Run main function
main "$@"