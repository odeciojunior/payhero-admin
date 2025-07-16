#!/bin/bash
# PayHero Production-Minimal Build Script
# This script builds Docker images for the application and nginx containers

set -euo pipefail

# --- Configuration ---
PROJECT_NAME="${PROJECT_NAME:-velana}"
ENVIRONMENT="${ENVIRONMENT:-production-minimal}"
BUILD_CONTEXT="${BUILD_CONTEXT:-.}"
DOCKERFILE_PATH="${DOCKERFILE_PATH:-docker/production-minimal}"

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
PROJECT_ROOT=$(cd -- "$SCRIPT_DIR/../../" &> /dev/null && pwd)

# --- Argument parsing ---
IMAGE_TAG="${1:-latest}"
BUILD_APP="${BUILD_APP:-true}"
BUILD_NGINX="${BUILD_NGINX:-true}"
NO_CACHE="${NO_CACHE:-false}"

# --- Build Configuration ---
BUILD_ID=$(date +%s)
TIMESTAMP=$(date +%Y%m%d%H%M%S)
GIT_COMMIT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
FULL_TAG="${IMAGE_TAG}-${TIMESTAMP}"

# Repository names - include module name
MODULE_NAME="${MODULE_NAME:-admin}"
APP_REPOSITORY="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-app"
NGINX_REPOSITORY="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-nginx"

# --- Main functions ---
check_prerequisites() {
    log_step "Checking prerequisites..."
    
    if ! command -v docker &> /dev/null; then
        log_error "Docker not found. Please install Docker."
        exit 1
    fi
    
    if ! docker info &> /dev/null; then
        log_error "Docker daemon is not running."
        exit 1
    fi
    
    log_info "Prerequisites check passed."
}

build_frontend_assets() {
    if [ -f "$PROJECT_ROOT/package.json" ]; then
        log_step "Building frontend assets..."
        
        # Check if node_modules exists
        if [ ! -d "$PROJECT_ROOT/node_modules" ]; then
            log_info "Installing npm dependencies..."
            (cd "$PROJECT_ROOT" && npm ci --prefer-offline --no-audit) || {
                log_warn "npm ci failed, trying npm install..."
                (cd "$PROJECT_ROOT" && npm install)
            }
        fi
        
        # Build production assets
        log_info "Building production assets..."
        (cd "$PROJECT_ROOT" && npm run prod) || {
            log_error "Frontend build failed"
            exit 1
        }
        
        log_info "Frontend assets built successfully."
    else
        log_warn "No package.json found, skipping frontend build."
    fi
}

build_app_image() {
    if [ "$BUILD_APP" != "true" ]; then
        log_info "Skipping app image build (BUILD_APP=false)"
        return
    fi
    
    log_step "Building application Docker image..."
    
    local cache_flag=""
    if [ "$NO_CACHE" = "true" ]; then
        cache_flag="--no-cache"
    fi
    
    # Check for module-specific Dockerfile
    APP_DOCKERFILE="$SCRIPT_DIR/Dockerfile.app"
    if [ -f "$SCRIPT_DIR/Dockerfile.app.$MODULE_NAME" ]; then
        APP_DOCKERFILE="$SCRIPT_DIR/Dockerfile.app.$MODULE_NAME"
        log_info "Using module-specific Dockerfile: $APP_DOCKERFILE"
    fi
    
    docker build \
        $cache_flag \
        --build-arg BUILD_ID="$BUILD_ID" \
        --build-arg BUILD_DATE="$(date -u +"%Y-%m-%dT%H:%M:%SZ")" \
        --build-arg GIT_COMMIT="$GIT_COMMIT" \
        --build-arg PROJECT_NAME="$PROJECT_NAME" \
        --build-arg MODULE_NAME="$MODULE_NAME" \
        --build-arg ENVIRONMENT="$ENVIRONMENT" \
        --target "$ENVIRONMENT" \
        -f "$APP_DOCKERFILE" \
        -t "$APP_REPOSITORY:$FULL_TAG" \
        -t "$APP_REPOSITORY:$IMAGE_TAG" \
        -t "$APP_REPOSITORY:latest" \
        "$PROJECT_ROOT" || {
            log_error "Application image build failed."
            exit 1
        }
    
    log_info "Application image built successfully."
    log_info "Tagged as:"
    log_info "  - $APP_REPOSITORY:$FULL_TAG"
    log_info "  - $APP_REPOSITORY:$IMAGE_TAG"
    log_info "  - $APP_REPOSITORY:latest"
}

build_nginx_image() {
    if [ "$BUILD_NGINX" != "true" ]; then
        log_info "Skipping nginx image build (BUILD_NGINX=false)"
        return
    fi
    
    log_step "Building nginx Docker image..."
    
    local cache_flag=""
    if [ "$NO_CACHE" = "true" ]; then
        cache_flag="--no-cache"
    fi
    
    # Check for module-specific Nginx Dockerfile
    NGINX_DOCKERFILE="$SCRIPT_DIR/Dockerfile.nginx"
    if [ -f "$SCRIPT_DIR/Dockerfile.nginx.$MODULE_NAME" ]; then
        NGINX_DOCKERFILE="$SCRIPT_DIR/Dockerfile.nginx.$MODULE_NAME"
        log_info "Using module-specific Nginx Dockerfile: $NGINX_DOCKERFILE"
    fi
    
    docker build \
        $cache_flag \
        --build-arg BUILD_ID="$BUILD_ID" \
        --build-arg BUILD_DATE="$(date -u +"%Y-%m-%dT%H:%M:%SZ")" \
        --build-arg GIT_COMMIT="$GIT_COMMIT" \
        --build-arg MODULE_NAME="$MODULE_NAME" \
        -f "$NGINX_DOCKERFILE" \
        -t "$NGINX_REPOSITORY:$FULL_TAG" \
        -t "$NGINX_REPOSITORY:$IMAGE_TAG" \
        -t "$NGINX_REPOSITORY:latest" \
        "$PROJECT_ROOT" || {
            log_error "Nginx image build failed."
            exit 1
        }
    
    log_info "Nginx image built successfully."
    log_info "Tagged as:"
    log_info "  - $NGINX_REPOSITORY:$FULL_TAG"
    log_info "  - $NGINX_REPOSITORY:$IMAGE_TAG"
    log_info "  - $NGINX_REPOSITORY:latest"
}

show_build_summary() {
    log_step "Build Summary"
    echo "================================================"
    echo "Project: $PROJECT_NAME"
    echo "Module: $MODULE_NAME"
    echo "Environment: $ENVIRONMENT"
    echo "Build ID: $BUILD_ID"
    echo "Git Commit: $GIT_COMMIT"
    echo "Image Tag: $IMAGE_TAG"
    echo "Full Tag: $FULL_TAG"
    echo ""
    
    if [ "$BUILD_APP" = "true" ]; then
        echo "Application Image:"
        echo "  - $APP_REPOSITORY:$IMAGE_TAG"
    fi
    
    if [ "$BUILD_NGINX" = "true" ]; then
        echo "Nginx Image:"
        echo "  - $NGINX_REPOSITORY:$IMAGE_TAG"
    fi
    
    echo "================================================"
}

# --- Main execution ---
main() {
    log_info "Starting PayHero $ENVIRONMENT build process..."
    log_info "Project root: $PROJECT_ROOT"
    
    check_prerequisites
    build_frontend_assets
    build_app_image
    build_nginx_image
    show_build_summary
    
    log_info "Build completed successfully!"
    
    # Suggest next steps
    echo ""
    log_info "Next steps:"
    log_info "  1. Push images: ./push.sh $IMAGE_TAG"
    log_info "  2. Deploy to ECS: ./deploy.sh $IMAGE_TAG"
    log_info "  Or run the full pipeline: ./pipeline.sh $IMAGE_TAG"
}

# Show help if requested
if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    echo "Usage: $0 [IMAGE_TAG]"
    echo ""
    echo "Build Docker images for PayHero production-minimal environment"
    echo ""
    echo "Arguments:"
    echo "  IMAGE_TAG    Tag for the Docker images (default: latest)"
    echo ""
    echo "Environment variables:"
    echo "  PROJECT_NAME    Project name (default: velana)"
    echo "  ENVIRONMENT     Environment name (default: production-minimal)"
    echo "  BUILD_APP       Build app image (default: true)"
    echo "  BUILD_NGINX     Build nginx image (default: true)"
    echo "  NO_CACHE        Build without cache (default: false)"
    exit 0
fi

# Run main function
main