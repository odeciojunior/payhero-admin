#!/bin/bash

# Local build script for PayHero Production-Minimal
# Project: Velana

set -euo pipefail

# --- Configuration ---
AWS_REGION=${AWS_REGION:-us-east-1}
AWS_ACCOUNT_ID="983877353757"
PROJECT_NAME="velana"
ENVIRONMENT="production-minimal"
ECR_REPOSITORY_APP="$PROJECT_NAME-$ENVIRONMENT-app"
ECR_REPOSITORY_NGINX="$PROJECT_NAME-$ENVIRONMENT-nginx"

# --- Colors for output ---
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# --- Helper functions ---
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

# --- Script setup ---
# Determine the absolute path of the directory where the script is located
SCRIPT_DIR=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" &> /dev/null && pwd)
# Determine the project root, which is two levels up from the script's directory
PROJECT_ROOT=$(cd -- "$SCRIPT_DIR/../../" &> /dev/null && pwd)

# --- Main execution ---

# 1. Login to ECR
log_info "Logging in to Amazon ECR..."
aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com || log_error "ECR login failed."

# 2. Set build variables
BUILD_ID=$(date +%s)
IMAGE_TAG=${IMAGE_TAG:-latest}
TIMESTAMP=$(date +%Y%m%d%H%M%S)
FULL_TAG="${IMAGE_TAG}-${TIMESTAMP}"

log_info "Project root: $PROJECT_ROOT"
log_info "Build ID: $BUILD_ID"
log_info "Image tag: $IMAGE_TAG"
log_info "Full tag: $FULL_TAG"

# 3. Build frontend assets
if [ -f "$PROJECT_ROOT/package.json" ]; then
    log_info "Building frontend assets..."
    (cd "$PROJECT_ROOT" && (npm ci --prefer-offline --no-audit || npm install))
    (cd "$PROJECT_ROOT" && npm run prod) || log_error "Frontend build failed"
fi

# 4. Build application Docker image
log_info "Building application Docker image..."
docker build \
    --build-arg BUILD_ID=$BUILD_ID \
    --build-arg BUILD_DATE=$(date -u +"%Y-%m-%dT%H:%M:%SZ") \
    --build-arg PROJECT_NAME=$PROJECT_NAME \
    --build-arg ENVIRONMENT=$ENVIRONMENT \
    --target $ENVIRONMENT \
    -f "$SCRIPT_DIR/Dockerfile.app" \
    -t "$ECR_REPOSITORY_APP:$FULL_TAG" \
    -t "$ECR_REPOSITORY_APP:$IMAGE_TAG" \
    -t "$ECR_REPOSITORY_APP:latest" \
    "$PROJECT_ROOT" || log_error "Application image build failed."

# 5. Build nginx Docker image
log_info "Building nginx Docker image..."
docker build \
    --build-arg BUILD_ID=$BUILD_ID \
    --build-arg BUILD_DATE=$(date -u +"%Y-%m-%dT%H:%M:%SZ") \
    -f "$SCRIPT_DIR/Dockerfile.nginx" \
    -t "$ECR_REPOSITORY_NGINX:$FULL_TAG" \
    -t "$ECR_REPOSITORY_NGINX:$IMAGE_TAG" \
    -t "$ECR_REPOSITORY_NGINX:latest" \
    "$PROJECT_ROOT" || log_error "Nginx image build failed."

# 6. Tag images for ECR
log_info "Tagging images for ECR..."
docker tag "$ECR_REPOSITORY_APP:$FULL_TAG" "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_APP:$FULL_TAG"
docker tag "$ECR_REPOSITORY_APP:$IMAGE_TAG" "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_APP:$IMAGE_TAG"
docker tag "$ECR_REPOSITORY_APP:latest" "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_APP:latest"

docker tag "$ECR_REPOSITORY_NGINX:$FULL_TAG" "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_NGINX:$FULL_TAG"
docker tag "$ECR_REPOSITORY_NGINX:$IMAGE_TAG" "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_NGINX:$IMAGE_TAG"
docker tag "$ECR_REPOSITORY_NGINX:latest" "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_NGINX:latest"

# 7. Push images to ECR
log_info "Pushing application image to ECR..."
docker push "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_APP:$FULL_TAG"
docker push "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_APP:$IMAGE_TAG"
docker push "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_APP:latest"

log_info "Pushing nginx image to ECR..."
docker push "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_NGINX:$FULL_TAG"
docker push "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_NGINX:$IMAGE_TAG"
docker push "$AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_NGINX:latest"

log_info "Build and push completed successfully!"
log_info "Images pushed:"
log_info "  - $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_APP:$IMAGE_TAG"
log_info "  - $AWS_ACCOUNT_ID.dkr.ecr.$AWS_REGION.amazonaws.com/$ECR_REPOSITORY_NGINX:$IMAGE_TAG"

# 8. Deploy the new task definition
log_info "Executing deployment script..."
"$SCRIPT_DIR/deploy-ecs-task.sh" || log_error "Deployment script failed."

log_info "Deployment script executed successfully."