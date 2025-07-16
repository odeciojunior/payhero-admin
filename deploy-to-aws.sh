#!/bin/bash

# PayHero Admin - AWS Deployment Automation Script
# This script automates the deployment process with proper error handling and validation

set -euo pipefail

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="${PROJECT_NAME:-velana}"
ENVIRONMENT="${ENVIRONMENT:-production-minimal}"
MODULE_NAME="${MODULE_NAME:-admin}"
AWS_REGION="${AWS_REGION:-us-east-1}"
AWS_ACCOUNT_ID="${AWS_ACCOUNT_ID:-983877353757}"
IMAGE_TAG="${1:-latest}"

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

log_step() {
    echo -e "\n${BLUE}===> $1${NC}\n"
}

# Error handling
trap 'log_error "Script failed at line $LINENO. Exit code: $?"' ERR

# Validation functions
check_prerequisites() {
    log_step "Checking prerequisites..."
    
    # Check required tools
    local tools=("docker" "aws" "jq" "node" "npm" "php" "composer")
    for tool in "${tools[@]}"; do
        if command -v "$tool" &> /dev/null; then
            log_info "✓ $tool is installed"
        else
            log_error "✗ $tool is not installed or not in PATH"
            exit 1
        fi
    done
    
    # Check AWS credentials
    if aws sts get-caller-identity &> /dev/null; then
        log_info "✓ AWS credentials are configured"
    else
        log_error "✗ AWS credentials not configured"
        exit 1
    fi
    
    # Check Docker daemon
    if docker info &> /dev/null; then
        log_info "✓ Docker daemon is running"
    else
        log_error "✗ Docker daemon is not running"
        exit 1
    fi
}

validate_environment() {
    log_step "Validating environment configuration..."
    
    # Check if .env file exists
    if [[ -f ".env.${ENVIRONMENT}" ]]; then
        log_info "✓ Environment file .env.${ENVIRONMENT} found"
    else
        log_warn "✗ Environment file .env.${ENVIRONMENT} not found"
        log_info "Creating default environment file..."
        create_env_file
    fi
    
    # Check modules_statuses.json
    if [[ -f "modules_statuses.json" ]]; then
        log_info "✓ modules_statuses.json found"
    else
        log_warn "✗ modules_statuses.json not found"
        log_info "Creating modules status file..."
        create_modules_status
    fi
    
    # Check storage permissions
    if [[ -d "storage" ]]; then
        log_info "✓ Storage directory exists"
        # Ensure proper permissions
        chmod -R 775 storage 2>/dev/null || log_warn "Could not set storage permissions"
    else
        log_error "✗ Storage directory not found"
        exit 1
    fi
}

create_env_file() {
    cat > ".env.${ENVIRONMENT}" << EOF
# PayHero Admin - ${ENVIRONMENT} Environment
APP_NAME="PayHero Admin"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

# Project Configuration
PROJECT_NAME=${PROJECT_NAME}
ENVIRONMENT=${ENVIRONMENT}

# Database Configuration (will be overridden by SSM)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=payhero
DB_USERNAME=admin
DB_PASSWORD=

# Redis Configuration (will be overridden by SSM)
REDIS_HOST=localhost
REDIS_PASSWORD=
REDIS_PORT=6379

# Performance Settings
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
BROADCAST_DRIVER=redis

# Security Settings
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120

# Laravel Modules
MODULES_STATUSES_PATH=modules_statuses.json
EOF
    log_info "✓ Created default environment file"
}

create_modules_status() {
    # Generate modules status from actual modules directory
    if [[ -d "Modules" ]]; then
        echo "{" > modules_statuses.json
        local first=true
        for module in Modules/*/; do
            if [[ -d "$module" ]]; then
                module_name=$(basename "$module")
                if [[ "$first" == "true" ]]; then
                    first=false
                else
                    echo "," >> modules_statuses.json
                fi
                echo -n "    \"$module_name\": true" >> modules_statuses.json
            fi
        done
        echo "" >> modules_statuses.json
        echo "}" >> modules_statuses.json
        log_info "✓ Created modules_statuses.json with $(grep -c "true" modules_statuses.json) modules"
    else
        log_error "✗ Modules directory not found"
        exit 1
    fi
}

prepare_application() {
    log_step "Preparing application for deployment..."
    
    # Install PHP dependencies
    log_info "Installing PHP dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Generate application key if needed
    if ! grep -q "APP_KEY=base64:" ".env.${ENVIRONMENT}" 2>/dev/null; then
        log_info "Generating application key..."
        php artisan key:generate --env="${ENVIRONMENT}" --force
    fi
    
    # Install Node.js dependencies
    log_info "Installing Node.js dependencies..."
    npm ci --only=production
    
    # Build frontend assets
    log_info "Building frontend assets..."
    npm run production
    
    # Clear and cache configuration
    log_info "Optimizing Laravel configuration..."
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    log_info "✓ Application prepared successfully"
}

build_docker_images() {
    log_step "Building Docker images..."
    
    # Build app image
    log_info "Building application image..."
    docker build -t "${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-app:${IMAGE_TAG}" \
        -f docker/production-minimal/Dockerfile.app .
    
    # Build nginx image
    log_info "Building nginx image..."
    docker build -t "${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-nginx:${IMAGE_TAG}" \
        -f docker/production-minimal/Dockerfile.nginx .
    
    log_info "✓ Docker images built successfully"
}

push_to_ecr() {
    log_step "Pushing images to ECR..."
    
    # Login to ECR
    log_info "Authenticating with ECR..."
    aws ecr get-login-password --region "${AWS_REGION}" | \
        docker login --username AWS --password-stdin "${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"
    
    # Create repositories if they don't exist
    for repo in app nginx; do
        local repo_name="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-${repo}"
        if ! aws ecr describe-repositories --repository-names "${repo_name}" --region "${AWS_REGION}" &>/dev/null; then
            log_info "Creating ECR repository: ${repo_name}"
            aws ecr create-repository --repository-name "${repo_name}" --region "${AWS_REGION}"
        fi
    done
    
    # Tag and push images
    for image in app nginx; do
        local local_tag="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-${image}:${IMAGE_TAG}"
        local ecr_tag="${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com/${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-${image}:${IMAGE_TAG}"
        
        log_info "Tagging ${image} image..."
        docker tag "${local_tag}" "${ecr_tag}"
        
        log_info "Pushing ${image} image..."
        docker push "${ecr_tag}"
    done
    
    log_info "✓ Images pushed to ECR successfully"
}

deploy_to_ecs() {
    log_step "Deploying to ECS..."
    
    local cluster_name="payhero-${ENVIRONMENT}-cluster"
    local service_name="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-service"
    local task_family="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-task"
    
    # Check if service exists
    if ! aws ecs describe-services \
        --cluster "${cluster_name}" \
        --services "${service_name}" \
        --region "${AWS_REGION}" &>/dev/null; then
        log_error "ECS service ${service_name} not found. Please create it first."
        log_info "Run: cd docker/production-minimal && ./create-ecs-service-admin.sh"
        exit 1
    fi
    
    # Get current task definition
    local task_definition=$(aws ecs describe-task-definition \
        --task-definition "${task_family}" \
        --region "${AWS_REGION}" \
        --query 'taskDefinition' \
        --output json)
    
    # Update image URIs
    local new_task_definition=$(echo "$task_definition" | jq --arg tag "$IMAGE_TAG" '
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
    log_info "Registering new task definition..."
    local new_revision=$(aws ecs register-task-definition \
        --cli-input-json "$new_task_definition" \
        --region "${AWS_REGION}" \
        --query 'taskDefinition.revision' \
        --output text)
    
    log_info "✓ Registered task definition revision: ${new_revision}"
    
    # Update service
    log_info "Updating ECS service..."
    aws ecs update-service \
        --cluster "${cluster_name}" \
        --service "${service_name}" \
        --task-definition "${task_family}:${new_revision}" \
        --region "${AWS_REGION}" \
        --query 'service.serviceName' \
        --output text
    
    # Wait for deployment to complete
    log_info "Waiting for deployment to complete..."
    aws ecs wait services-stable \
        --cluster "${cluster_name}" \
        --services "${service_name}" \
        --region "${AWS_REGION}"
    
    log_info "✓ ECS deployment completed successfully"
}

verify_deployment() {
    log_step "Verifying deployment..."
    
    local cluster_name="payhero-${ENVIRONMENT}-cluster"
    local service_name="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-service"
    
    # Check service status
    local running_count=$(aws ecs describe-services \
        --cluster "${cluster_name}" \
        --services "${service_name}" \
        --region "${AWS_REGION}" \
        --query 'services[0].runningCount' \
        --output text)
    
    local desired_count=$(aws ecs describe-services \
        --cluster "${cluster_name}" \
        --services "${service_name}" \
        --region "${AWS_REGION}" \
        --query 'services[0].desiredCount' \
        --output text)
    
    if [[ "${running_count}" == "${desired_count}" ]]; then
        log_info "✓ Service is running with ${running_count}/${desired_count} tasks"
    else
        log_warn "Service is running with ${running_count}/${desired_count} tasks"
    fi
    
    # Get ALB endpoint
    local alb_dns=$(aws elbv2 describe-load-balancers \
        --names "payhero-${ENVIRONMENT}-alb" \
        --region "${AWS_REGION}" \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null || echo "ALB not found")
    
    if [[ "${alb_dns}" != "ALB not found" ]]; then
        log_info "✓ Application should be available at: http://${alb_dns}/admin"
    fi
}

cleanup() {
    log_step "Cleaning up..."
    
    # Remove local images to save space
    if [[ "${CLEANUP_LOCAL_IMAGES:-true}" == "true" ]]; then
        log_info "Removing local Docker images..."
        docker rmi "${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-app:${IMAGE_TAG}" 2>/dev/null || true
        docker rmi "${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-nginx:${IMAGE_TAG}" 2>/dev/null || true
    fi
    
    log_info "✓ Cleanup completed"
}

print_summary() {
    log_step "Deployment Summary"
    echo -e "${GREEN}✓ Deployment completed successfully!${NC}"
    echo -e "  Project: ${BLUE}${PROJECT_NAME}${NC}"
    echo -e "  Environment: ${BLUE}${ENVIRONMENT}${NC}"
    echo -e "  Module: ${BLUE}${MODULE_NAME}${NC}"
    echo -e "  Image Tag: ${BLUE}${IMAGE_TAG}${NC}"
    echo -e "  AWS Region: ${BLUE}${AWS_REGION}${NC}"
    echo ""
    echo -e "${YELLOW}Next Steps:${NC}"
    echo -e "  1. Monitor deployment: aws ecs describe-services --cluster payhero-${ENVIRONMENT}-cluster --services ${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-service"
    echo -e "  2. Check logs: aws logs tail /ecs/${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME} --follow"
    echo -e "  3. Test health endpoint: curl http://your-alb-dns/admin/health"
}

# Main execution
main() {
    echo -e "${BLUE}PayHero Admin - AWS Deployment Automation${NC}"
    echo -e "=========================================="
    
    check_prerequisites
    validate_environment
    prepare_application
    build_docker_images
    push_to_ecr
    deploy_to_ecs
    verify_deployment
    cleanup
    print_summary
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        --skip-build)
            SKIP_BUILD=true
            shift
            ;;
        --skip-push)
            SKIP_PUSH=true
            shift
            ;;
        --skip-deploy)
            SKIP_DEPLOY=true
            shift
            ;;
        --no-cleanup)
            CLEANUP_LOCAL_IMAGES=false
            shift
            ;;
        --help)
            echo "Usage: $0 [IMAGE_TAG] [OPTIONS]"
            echo ""
            echo "Options:"
            echo "  --skip-build     Skip Docker image building"
            echo "  --skip-push      Skip pushing to ECR"
            echo "  --skip-deploy    Skip ECS deployment"
            echo "  --no-cleanup     Don't remove local images after deployment"
            echo "  --help           Show this help message"
            echo ""
            echo "Environment Variables:"
            echo "  PROJECT_NAME     Project name (default: velana)"
            echo "  ENVIRONMENT      Environment name (default: production-minimal)"
            echo "  MODULE_NAME      Module name (default: admin)"
            echo "  AWS_REGION       AWS region (default: us-east-1)"
            echo "  AWS_ACCOUNT_ID   AWS account ID (default: 983877353757)"
            exit 0
            ;;
        *)
            IMAGE_TAG="$1"
            shift
            ;;
    esac
done

# Run main function
main
