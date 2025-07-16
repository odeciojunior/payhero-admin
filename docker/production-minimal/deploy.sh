#!/bin/bash
# PayHero Production-Minimal Deploy Script
# This script deploys the application to AWS ECS with rollback capabilities

set -euo pipefail

# --- Configuration ---
AWS_REGION="${AWS_REGION:-us-east-1}"
AWS_ACCOUNT_ID="${AWS_ACCOUNT_ID:-983877353757}"
PROJECT_NAME="${PROJECT_NAME:-velana}"
MODULE_NAME="${MODULE_NAME:-admin}"
ENVIRONMENT="${ENVIRONMENT:-production-minimal}"
CLUSTER_NAME="${CLUSTER_NAME:-${PROJECT_NAME}-${ENVIRONMENT}-cluster}"
SERVICE_NAME="${SERVICE_NAME:-${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-service}"
TASK_FAMILY="${TASK_FAMILY:-${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-task}"

# Deployment configuration
MAX_WAIT_ATTEMPTS="${MAX_WAIT_ATTEMPTS:-60}"
WAIT_INTERVAL="${WAIT_INTERVAL:-10}"
ENABLE_ROLLBACK="${ENABLE_ROLLBACK:-true}"
FORCE_DEPLOYMENT="${FORCE_DEPLOYMENT:-false}"

# --- Colors for output ---
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# --- Helper functions ---
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1" >&2
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# --- Script setup ---
SCRIPT_DIR=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" &> /dev/null && pwd)

# --- Argument parsing ---
IMAGE_TAG="${1:-latest}"
SKIP_HEALTH_CHECK="${2:-false}"

# --- State tracking ---
PREVIOUS_TASK_DEF_ARN=""
NEW_TASK_DEF_ARN=""
DEPLOYMENT_ID=""

# --- Main functions ---
check_prerequisites() {
    log_step "Checking prerequisites..."
    
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
    
    # Verify AWS account
    ACTUAL_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    if [ "$ACTUAL_ACCOUNT_ID" != "$AWS_ACCOUNT_ID" ]; then
        log_warn "AWS account mismatch. Expected: $AWS_ACCOUNT_ID, Actual: $ACTUAL_ACCOUNT_ID"
        AWS_ACCOUNT_ID="$ACTUAL_ACCOUNT_ID"
    fi
    
    log_info "Prerequisites check passed."
}

verify_cluster_and_service() {
    log_step "Verifying ECS cluster and service..."
    
    # Check cluster
    if ! aws ecs describe-clusters \
        --clusters "$CLUSTER_NAME" \
        --region "$AWS_REGION" \
        --query "clusters[?status=='ACTIVE'].clusterName" \
        --output text | grep -q "$CLUSTER_NAME"; then
        log_error "ECS cluster '$CLUSTER_NAME' not found or not active"
        exit 1
    fi
    
    # Check service
    SERVICE_STATUS=$(aws ecs describe-services \
        --cluster "$CLUSTER_NAME" \
        --services "$SERVICE_NAME" \
        --region "$AWS_REGION" \
        --query 'services[0].status' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$SERVICE_STATUS" ] || [ "$SERVICE_STATUS" = "None" ]; then
        log_error "ECS service '$SERVICE_NAME' not found"
        log_info "Please create the service first using:"
        log_info "  ./create-ecs-service.sh"
        exit 1
    fi
    
    if [ "$SERVICE_STATUS" != "ACTIVE" ]; then
        log_error "ECS service '$SERVICE_NAME' is not active (status: $SERVICE_STATUS)"
        exit 1
    fi
    
    log_info "Cluster and service verified."
}

get_current_deployment_info() {
    log_step "Getting current deployment information..."
    
    # Get current task definition
    PREVIOUS_TASK_DEF_ARN=$(aws ecs describe-services \
        --cluster "$CLUSTER_NAME" \
        --services "$SERVICE_NAME" \
        --region "$AWS_REGION" \
        --query 'services[0].taskDefinition' \
        --output text 2>/dev/null || echo "")
    
    if [ -n "$PREVIOUS_TASK_DEF_ARN" ] && [ "$PREVIOUS_TASK_DEF_ARN" != "None" ]; then
        log_info "Current task definition: $PREVIOUS_TASK_DEF_ARN"
        
        # Get current running tasks count
        CURRENT_RUNNING_COUNT=$(aws ecs describe-services \
            --cluster "$CLUSTER_NAME" \
            --services "$SERVICE_NAME" \
            --region "$AWS_REGION" \
            --query 'services[0].runningCount' \
            --output text)
        
        log_info "Current running tasks: $CURRENT_RUNNING_COUNT"
    else
        log_warn "No current task definition found."
    fi
}

verify_images_exist() {
    log_step "Verifying Docker images exist in ECR..."
    
    APP_REPO="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-app"
    NGINX_REPO="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-nginx"
    
    # Check app image
    if ! aws ecr describe-images \
        --repository-name "$APP_REPO" \
        --image-ids imageTag="$IMAGE_TAG" \
        --region "$AWS_REGION" &> /dev/null; then
        log_error "App image not found in ECR: $APP_REPO:$IMAGE_TAG"
        log_info "Please push the images first: ./push.sh $IMAGE_TAG"
        exit 1
    fi
    
    # Check nginx image
    if ! aws ecr describe-images \
        --repository-name "$NGINX_REPO" \
        --image-ids imageTag="$IMAGE_TAG" \
        --region "$AWS_REGION" &> /dev/null; then
        log_error "Nginx image not found in ECR: $NGINX_REPO:$IMAGE_TAG"
        log_info "Please push the images first: ./push.sh $IMAGE_TAG"
        exit 1
    fi
    
    log_info "Images verified in ECR."
}

create_new_task_definition() {
    log_step "Creating new task definition..."
    
    # Get the full image URIs
    APP_IMAGE="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-app:$IMAGE_TAG"
    NGINX_IMAGE="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-nginx:$IMAGE_TAG"
    
    # Get current task definition or use template
    if [ -n "$PREVIOUS_TASK_DEF_ARN" ] && [ "$PREVIOUS_TASK_DEF_ARN" != "None" ]; then
        log_info "Using existing task definition as base..."
        TASK_DEF_JSON=$(aws ecs describe-task-definition \
            --task-definition "$PREVIOUS_TASK_DEF_ARN" \
            --region "$AWS_REGION" \
            --query 'taskDefinition')
    else
        log_info "Using task definition template..."
        TASK_DEF_FILE="$SCRIPT_DIR/task-definition-${MODULE_NAME}.json"
        if [ -f "$TASK_DEF_FILE" ]; then
            TASK_DEF_JSON=$(cat "$TASK_DEF_FILE")
        elif [ -f "$SCRIPT_DIR/task-definition.json" ]; then
            log_warn "Module-specific task definition not found, using default"
            TASK_DEF_JSON=$(cat "$SCRIPT_DIR/task-definition.json")
        else
            log_error "No task definition template found"
            exit 1
        fi
    fi
    
    # Update the task definition with new images and metadata
    NEW_TASK_DEF=$(echo "$TASK_DEF_JSON" | jq \
        --arg app_image "$APP_IMAGE" \
        --arg nginx_image "$NGINX_IMAGE" \
        --arg build_date "$(date -u +"%Y-%m-%dT%H:%M:%SZ")" \
        --arg image_tag "$IMAGE_TAG" \
        '
        # Update container images
        .containerDefinitions[0].image = $app_image |
        .containerDefinitions[1].image = $nginx_image |
        
        # Add deployment metadata to environment variables
        (.containerDefinitions[0].environment[] | select(.name == "DEPLOYMENT_DATE")).value = $build_date |
        (.containerDefinitions[0].environment[] | select(.name == "IMAGE_TAG")).value = $image_tag |
        
        # Remove fields that cannot be specified when registering
        del(.taskDefinitionArn) |
        del(.revision) |
        del(.status) |
        del(.requiresAttributes) |
        del(.compatibilities) |
        del(.registeredAt) |
        del(.registeredBy) |
        del(.deregisteredAt)
        ')
    
    # Register the new task definition
    NEW_TASK_DEF_ARN=$(aws ecs register-task-definition \
        --cli-input-json "$NEW_TASK_DEF" \
        --region "$AWS_REGION" \
        --query 'taskDefinition.taskDefinitionArn' \
        --output text) || {
            log_error "Failed to register new task definition"
            exit 1
        }
    
    log_info "New task definition registered: $NEW_TASK_DEF_ARN"
}

update_service() {
    log_step "Updating ECS service..."
    
    # Build update command
    UPDATE_ARGS=(
        "--cluster" "$CLUSTER_NAME"
        "--service" "$SERVICE_NAME"
        "--task-definition" "$NEW_TASK_DEF_ARN"
        "--region" "$AWS_REGION"
    )
    
    if [ "$FORCE_DEPLOYMENT" = "true" ]; then
        UPDATE_ARGS+=("--force-new-deployment")
    fi
    
    # Update the service
    DEPLOYMENT_INFO=$(aws ecs update-service "${UPDATE_ARGS[@]}" \
        --query 'service.deployments[?status==`PRIMARY`]' \
        --output json) || {
            log_error "Failed to update service"
            exit 1
        }
    
    # Get deployment ID
    DEPLOYMENT_ID=$(echo "$DEPLOYMENT_INFO" | jq -r '.[0].id' 2>/dev/null || echo "")
    
    if [ -z "$DEPLOYMENT_ID" ] || [ "$DEPLOYMENT_ID" = "null" ]; then
        log_warn "Could not get deployment ID"
    else
        log_info "Deployment started: $DEPLOYMENT_ID"
    fi
    
    log_info "Service update initiated."
}

wait_for_deployment() {
    log_step "Waiting for deployment to complete..."
    
    local attempt=0
    local deployment_failed=false
    
    while [ $attempt -lt "$MAX_WAIT_ATTEMPTS" ]; do
        # Get service information
        SERVICE_INFO=$(aws ecs describe-services \
            --cluster "$CLUSTER_NAME" \
            --services "$SERVICE_NAME" \
            --region "$AWS_REGION" \
            --query 'services[0]')
        
        # Get deployment status
        PRIMARY_DEPLOYMENT=$(echo "$SERVICE_INFO" | jq -r '.deployments[] | select(.status == "PRIMARY")')
        ROLLOUT_STATE=$(echo "$PRIMARY_DEPLOYMENT" | jq -r '.rolloutState // "IN_PROGRESS"')
        
        # Get task counts
        RUNNING_COUNT=$(echo "$SERVICE_INFO" | jq -r '.runningCount // 0')
        DESIRED_COUNT=$(echo "$SERVICE_INFO" | jq -r '.desiredCount // 0')
        PENDING_COUNT=$(echo "$SERVICE_INFO" | jq -r '.pendingCount // 0')
        
        # Check for failed tasks
        FAILED_TASKS=$(echo "$PRIMARY_DEPLOYMENT" | jq -r '.failedTasks // 0')
        
        log_info "Deployment status: $ROLLOUT_STATE (Running: $RUNNING_COUNT/$DESIRED_COUNT, Pending: $PENDING_COUNT, Failed: $FAILED_TASKS)"
        
        # Check deployment status
        case "$ROLLOUT_STATE" in
            "COMPLETED")
                if [ "$RUNNING_COUNT" -eq "$DESIRED_COUNT" ]; then
                    log_info "Deployment completed successfully!"
                    return 0
                fi
                ;;
            "FAILED")
                log_error "Deployment failed!"
                deployment_failed=true
                break
                ;;
            "IN_PROGRESS"|"")
                # Check for excessive failed tasks
                if [ "$FAILED_TASKS" -gt 3 ]; then
                    log_error "Too many failed tasks: $FAILED_TASKS"
                    deployment_failed=true
                    break
                fi
                ;;
        esac
        
        sleep "$WAIT_INTERVAL"
        ((attempt++))
    done
    
    if [ $attempt -ge "$MAX_WAIT_ATTEMPTS" ]; then
        log_error "Deployment timed out after $((MAX_WAIT_ATTEMPTS * WAIT_INTERVAL)) seconds"
        deployment_failed=true
    fi
    
    # Handle deployment failure
    if [ "$deployment_failed" = true ]; then
        if [ "$ENABLE_ROLLBACK" = "true" ] && [ -n "$PREVIOUS_TASK_DEF_ARN" ]; then
            perform_rollback
        fi
        return 1
    fi
}

perform_rollback() {
    log_warn "Initiating rollback to previous task definition..."
    
    if [ -z "$PREVIOUS_TASK_DEF_ARN" ] || [ "$PREVIOUS_TASK_DEF_ARN" = "None" ]; then
        log_error "No previous task definition available for rollback"
        return 1
    fi
    
    # Update service with previous task definition
    aws ecs update-service \
        --cluster "$CLUSTER_NAME" \
        --service "$SERVICE_NAME" \
        --task-definition "$PREVIOUS_TASK_DEF_ARN" \
        --region "$AWS_REGION" \
        --force-new-deployment &> /dev/null || {
            log_error "Rollback failed!"
            return 1
        }
    
    log_info "Rollback initiated. Waiting for stabilization..."
    
    # Wait for rollback to complete
    local attempt=0
    while [ $attempt -lt 30 ]; do
        RUNNING_COUNT=$(aws ecs describe-services \
            --cluster "$CLUSTER_NAME" \
            --services "$SERVICE_NAME" \
            --region "$AWS_REGION" \
            --query 'services[0].runningCount' \
            --output text)
        
        DESIRED_COUNT=$(aws ecs describe-services \
            --cluster "$CLUSTER_NAME" \
            --services "$SERVICE_NAME" \
            --region "$AWS_REGION" \
            --query 'services[0].desiredCount' \
            --output text)
        
        log_info "Rollback status: Running $RUNNING_COUNT/$DESIRED_COUNT"
        
        if [ "$RUNNING_COUNT" -eq "$DESIRED_COUNT" ]; then
            log_info "Rollback completed successfully."
            return 0
        fi
        
        sleep 10
        ((attempt++))
    done
    
    log_error "Rollback timed out"
    return 1
}

check_deployment_health() {
    if [ "$SKIP_HEALTH_CHECK" = "true" ]; then
        log_info "Skipping health checks (SKIP_HEALTH_CHECK=true)"
        return 0
    fi
    
    log_step "Running post-deployment health checks..."
    
    # Get ALB DNS
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --names "${PROJECT_NAME}-${ENVIRONMENT}-alb" \
        --region "$AWS_REGION" \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$ALB_DNS" ] || [ "$ALB_DNS" = "None" ]; then
        log_warn "ALB not found, skipping health checks"
        return 0
    fi
    
    log_info "Waiting 30 seconds for ALB to update target health..."
    sleep 30
    
    # Check health endpoints
    local health_check_passed=true
    local endpoints=("/health" "/admin/health")
    
    # For admin module, check admin-specific endpoints
    if [ "$MODULE_NAME" = "admin" ]; then
        endpoints=("/admin/health" "/health")
    fi
    
    for endpoint in "${endpoints[@]}"; do
        if curl -f -s -o /dev/null -w "%{http_code}" "http://$ALB_DNS$endpoint" | grep -q "200"; then
            log_info "Health check passed: $endpoint"
        else
            log_warn "Health check failed: $endpoint"
            health_check_passed=false
        fi
    done
    
    if [ "$health_check_passed" = false ]; then
        log_error "Some health checks failed"
        if [ "$ENABLE_ROLLBACK" = "true" ]; then
            perform_rollback
            return 1
        fi
    fi
    
    log_info "All health checks passed."
    return 0
}

show_deployment_summary() {
    log_step "Deployment Summary"
    echo "================================================"
    echo "Project: $PROJECT_NAME"
    echo "Module: $MODULE_NAME"
    echo "Environment: $ENVIRONMENT"
    echo "Cluster: $CLUSTER_NAME"
    echo "Service: $SERVICE_NAME"
    echo "Image Tag: $IMAGE_TAG"
    echo ""
    
    # Get final service state
    SERVICE_INFO=$(aws ecs describe-services \
        --cluster "$CLUSTER_NAME" \
        --services "$SERVICE_NAME" \
        --region "$AWS_REGION" \
        --query 'services[0]')
    
    RUNNING_COUNT=$(echo "$SERVICE_INFO" | jq -r '.runningCount')
    DESIRED_COUNT=$(echo "$SERVICE_INFO" | jq -r '.desiredCount')
    
    echo "Service Status:"
    echo "  Running Tasks: $RUNNING_COUNT/$DESIRED_COUNT"
    echo "  Task Definition: $(echo "$SERVICE_INFO" | jq -r '.taskDefinition' | rev | cut -d'/' -f1 | rev)"
    
    # Get ALB URL
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --names "${PROJECT_NAME}-${ENVIRONMENT}-alb" \
        --region "$AWS_REGION" \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null || echo "Not found")
    
    echo ""
    if [ "$MODULE_NAME" = "admin" ]; then
        echo "Admin Module URL: http://$ALB_DNS/admin"
        echo "Health Check URL: http://$ALB_DNS/admin/health"
    else
        echo "Application URL: http://$ALB_DNS"
    fi
    echo "================================================"
}

# --- Main execution ---
main() {
    log_info "Starting PayHero $ENVIRONMENT deployment..."
    log_info "Image tag: $IMAGE_TAG"
    
    check_prerequisites
    verify_cluster_and_service
    get_current_deployment_info
    verify_images_exist
    create_new_task_definition
    update_service
    
    if wait_for_deployment; then
        check_deployment_health || exit 1
        show_deployment_summary
        log_info "Deployment completed successfully!"
        exit 0
    else
        log_error "Deployment failed!"
        exit 1
    fi
}

# Show help if requested
if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    echo "Usage: $0 [IMAGE_TAG] [SKIP_HEALTH_CHECK]"
    echo ""
    echo "Deploy PayHero to AWS ECS"
    echo ""
    echo "Arguments:"
    echo "  IMAGE_TAG           Tag of images to deploy (default: latest)"
    echo "  SKIP_HEALTH_CHECK   Skip health checks (default: false)"
    echo ""
    echo "Environment variables:"
    echo "  AWS_REGION          AWS region (default: us-east-1)"
    echo "  AWS_ACCOUNT_ID      AWS account ID (default: 983877353757)"
    echo "  PROJECT_NAME        Project name (default: velana)"
    echo "  ENVIRONMENT         Environment name (default: production-minimal)"
    echo "  CLUSTER_NAME        ECS cluster name"
    echo "  SERVICE_NAME        ECS service name"
    echo "  ENABLE_ROLLBACK     Enable automatic rollback (default: true)"
    echo "  FORCE_DEPLOYMENT    Force new deployment (default: false)"
    echo "  MAX_WAIT_ATTEMPTS   Max deployment wait attempts (default: 60)"
    echo "  WAIT_INTERVAL       Wait interval in seconds (default: 10)"
    exit 0
fi

# Run main function
main