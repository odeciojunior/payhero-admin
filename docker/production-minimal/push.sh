#!/bin/bash
# PayHero Production-Minimal Push Script
# This script pushes Docker images to AWS ECR

set -euo pipefail

# --- Configuration ---
AWS_REGION="${AWS_REGION:-us-east-1}"
AWS_ACCOUNT_ID="${AWS_ACCOUNT_ID:-983877353757}"
PROJECT_NAME="${PROJECT_NAME:-velana}"
ENVIRONMENT="${ENVIRONMENT:-production-minimal}"

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
PUSH_APP="${PUSH_APP:-true}"
PUSH_NGINX="${PUSH_NGINX:-true}"

# Repository names - include module name
MODULE_NAME="${MODULE_NAME:-admin}"
APP_REPOSITORY="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-app"
NGINX_REPOSITORY="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-nginx"
ECR_REGISTRY="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com"

# --- Main functions ---
check_prerequisites() {
    log_step "Checking prerequisites..."
    
    if ! command -v docker &> /dev/null; then
        log_error "Docker not found. Please install Docker."
        exit 1
    fi
    
    if ! command -v aws &> /dev/null; then
        log_error "AWS CLI not found. Please install AWS CLI."
        exit 1
    fi
    
    # Check AWS credentials
    if ! aws sts get-caller-identity &> /dev/null; then
        log_error "AWS credentials not configured properly."
        exit 1
    fi
    
    # Verify AWS account ID
    ACTUAL_ACCOUNT_ID=$(aws sts get-caller-identity --query Account --output text)
    if [ "$ACTUAL_ACCOUNT_ID" != "$AWS_ACCOUNT_ID" ]; then
        log_warn "AWS account ID mismatch. Expected: $AWS_ACCOUNT_ID, Actual: $ACTUAL_ACCOUNT_ID"
        read -p "Continue with actual account ID? (y/N) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
        AWS_ACCOUNT_ID="$ACTUAL_ACCOUNT_ID"
        ECR_REGISTRY="$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com"
    fi
    
    log_info "Prerequisites check passed."
}

login_to_ecr() {
    log_step "Logging in to Amazon ECR..."
    
    aws ecr get-login-password --region "$AWS_REGION" | \
        docker login --username AWS --password-stdin "$ECR_REGISTRY" || {
            log_error "ECR login failed."
            exit 1
        }
    
    log_info "Successfully logged in to ECR."
}

verify_local_images() {
    log_step "Verifying local images..."
    
    local missing_images=()
    
    if [ "$PUSH_APP" = "true" ]; then
        if ! docker image inspect "$APP_REPOSITORY:$IMAGE_TAG" &> /dev/null; then
            missing_images+=("$APP_REPOSITORY:$IMAGE_TAG")
        fi
    fi
    
    if [ "$PUSH_NGINX" = "true" ]; then
        if ! docker image inspect "$NGINX_REPOSITORY:$IMAGE_TAG" &> /dev/null; then
            missing_images+=("$NGINX_REPOSITORY:$IMAGE_TAG")
        fi
    fi
    
    if [ ${#missing_images[@]} -gt 0 ]; then
        log_error "The following images are not found locally:"
        for img in "${missing_images[@]}"; do
            echo "  - $img"
        done
        log_error "Please build the images first: ./build.sh $IMAGE_TAG"
        exit 1
    fi
    
    log_info "All required images found locally."
}

create_ecr_repositories() {
    log_step "Ensuring ECR repositories exist..."
    
    # Function to create repository if it doesn't exist
    create_repo_if_not_exists() {
        local repo_name=$1
        
        if ! aws ecr describe-repositories --repository-names "$repo_name" --region "$AWS_REGION" &> /dev/null; then
            log_info "Creating ECR repository: $repo_name"
            aws ecr create-repository \
                --repository-name "$repo_name" \
                --region "$AWS_REGION" \
                --image-scanning-configuration scanOnPush=true \
                --tags Key=Project,Value="$PROJECT_NAME" Key=Environment,Value="$ENVIRONMENT" || {
                    log_error "Failed to create repository: $repo_name"
                    exit 1
                }
            
            # Set lifecycle policy to keep only last 10 images
            aws ecr put-lifecycle-policy \
                --repository-name "$repo_name" \
                --region "$AWS_REGION" \
                --lifecycle-policy-text '{
                    "rules": [
                        {
                            "rulePriority": 1,
                            "description": "Keep last 10 images",
                            "selection": {
                                "tagStatus": "any",
                                "countType": "imageCountMoreThan",
                                "countNumber": 10
                            },
                            "action": {
                                "type": "expire"
                            }
                        }
                    ]
                }' &> /dev/null || log_warn "Failed to set lifecycle policy for $repo_name"
        else
            log_info "ECR repository already exists: $repo_name"
        fi
    }
    
    if [ "$PUSH_APP" = "true" ]; then
        create_repo_if_not_exists "$APP_REPOSITORY"
    fi
    
    if [ "$PUSH_NGINX" = "true" ]; then
        create_repo_if_not_exists "$NGINX_REPOSITORY"
    fi
}

tag_images() {
    log_step "Tagging images for ECR..."
    
    TIMESTAMP=$(date +%Y%m%d%H%M%S)
    FULL_TAG="${IMAGE_TAG}-${TIMESTAMP}"
    
    if [ "$PUSH_APP" = "true" ]; then
        # Tag with full timestamp
        docker tag "$APP_REPOSITORY:$IMAGE_TAG" "$ECR_REGISTRY/$APP_REPOSITORY:$FULL_TAG"
        # Tag with provided tag
        docker tag "$APP_REPOSITORY:$IMAGE_TAG" "$ECR_REGISTRY/$APP_REPOSITORY:$IMAGE_TAG"
        # Tag as latest
        docker tag "$APP_REPOSITORY:$IMAGE_TAG" "$ECR_REGISTRY/$APP_REPOSITORY:latest"
        
        log_info "Tagged app image for ECR"
    fi
    
    if [ "$PUSH_NGINX" = "true" ]; then
        # Tag with full timestamp
        docker tag "$NGINX_REPOSITORY:$IMAGE_TAG" "$ECR_REGISTRY/$NGINX_REPOSITORY:$FULL_TAG"
        # Tag with provided tag
        docker tag "$NGINX_REPOSITORY:$IMAGE_TAG" "$ECR_REGISTRY/$NGINX_REPOSITORY:$IMAGE_TAG"
        # Tag as latest
        docker tag "$NGINX_REPOSITORY:$IMAGE_TAG" "$ECR_REGISTRY/$NGINX_REPOSITORY:latest"
        
        log_info "Tagged nginx image for ECR"
    fi
}

push_images() {
    log_step "Pushing images to ECR..."
    
    if [ "$PUSH_APP" = "true" ]; then
        log_info "Pushing application image..."
        docker push "$ECR_REGISTRY/$APP_REPOSITORY:$FULL_TAG" || {
            log_error "Failed to push app image with full tag"
            exit 1
        }
        docker push "$ECR_REGISTRY/$APP_REPOSITORY:$IMAGE_TAG" || {
            log_error "Failed to push app image with tag"
            exit 1
        }
        docker push "$ECR_REGISTRY/$APP_REPOSITORY:latest" || {
            log_error "Failed to push app image as latest"
            exit 1
        }
        log_info "Application image pushed successfully"
    fi
    
    if [ "$PUSH_NGINX" = "true" ]; then
        log_info "Pushing nginx image..."
        docker push "$ECR_REGISTRY/$NGINX_REPOSITORY:$FULL_TAG" || {
            log_error "Failed to push nginx image with full tag"
            exit 1
        }
        docker push "$ECR_REGISTRY/$NGINX_REPOSITORY:$IMAGE_TAG" || {
            log_error "Failed to push nginx image with tag"
            exit 1
        }
        docker push "$ECR_REGISTRY/$NGINX_REPOSITORY:latest" || {
            log_error "Failed to push nginx image as latest"
            exit 1
        }
        log_info "Nginx image pushed successfully"
    fi
}

verify_pushed_images() {
    log_step "Verifying pushed images..."
    
    if [ "$PUSH_APP" = "true" ]; then
        if aws ecr describe-images \
            --repository-name "$APP_REPOSITORY" \
            --image-ids imageTag="$IMAGE_TAG" \
            --region "$AWS_REGION" &> /dev/null; then
            log_info "App image verified in ECR"
        else
            log_error "App image not found in ECR"
            exit 1
        fi
    fi
    
    if [ "$PUSH_NGINX" = "true" ]; then
        if aws ecr describe-images \
            --repository-name "$NGINX_REPOSITORY" \
            --image-ids imageTag="$IMAGE_TAG" \
            --region "$AWS_REGION" &> /dev/null; then
            log_info "Nginx image verified in ECR"
        else
            log_error "Nginx image not found in ECR"
            exit 1
        fi
    fi
}

show_push_summary() {
    log_step "Push Summary"
    echo "================================================"
    echo "Project: $PROJECT_NAME"
    echo "Module: $MODULE_NAME"
    echo "Environment: $ENVIRONMENT"
    echo "AWS Account: $AWS_ACCOUNT_ID"
    echo "AWS Region: $AWS_REGION"
    echo "Image Tag: $IMAGE_TAG"
    echo ""
    echo "Pushed images:"
    
    if [ "$PUSH_APP" = "true" ]; then
        echo "  Application:"
        echo "    - $ECR_REGISTRY/$APP_REPOSITORY:$IMAGE_TAG"
        echo "    - $ECR_REGISTRY/$APP_REPOSITORY:latest"
    fi
    
    if [ "$PUSH_NGINX" = "true" ]; then
        echo "  Nginx:"
        echo "    - $ECR_REGISTRY/$NGINX_REPOSITORY:$IMAGE_TAG"
        echo "    - $ECR_REGISTRY/$NGINX_REPOSITORY:latest"
    fi
    
    echo "================================================"
}

# --- Main execution ---
main() {
    log_info "Starting PayHero $ENVIRONMENT push process..."
    
    check_prerequisites
    verify_local_images
    login_to_ecr
    create_ecr_repositories
    tag_images
    push_images
    verify_pushed_images
    show_push_summary
    
    log_info "Push completed successfully!"
    
    # Suggest next steps
    echo ""
    log_info "Next steps:"
    log_info "  Deploy to ECS: ./deploy.sh $IMAGE_TAG"
}

# Show help if requested
if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    echo "Usage: $0 [IMAGE_TAG]"
    echo ""
    echo "Push Docker images to AWS ECR"
    echo ""
    echo "Arguments:"
    echo "  IMAGE_TAG    Tag of the images to push (default: latest)"
    echo ""
    echo "Environment variables:"
    echo "  AWS_REGION      AWS region (default: us-east-1)"
    echo "  AWS_ACCOUNT_ID  AWS account ID (default: 983877353757)"
    echo "  PROJECT_NAME    Project name (default: velana)"
    echo "  ENVIRONMENT     Environment name (default: production-minimal)"
    echo "  PUSH_APP        Push app image (default: true)"
    echo "  PUSH_NGINX      Push nginx image (default: true)"
    exit 0
fi

# Run main function
main