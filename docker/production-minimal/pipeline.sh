#!/bin/bash
# PayHero Production-Minimal CI/CD Pipeline Script
# This script orchestrates the complete build, push, and deploy process

set -euo pipefail

# --- Configuration ---
AWS_REGION="${AWS_REGION:-us-east-1}"
AWS_ACCOUNT_ID="${AWS_ACCOUNT_ID:-983877353757}"
PROJECT_NAME="${PROJECT_NAME:-velana}"
MODULE_NAME="${MODULE_NAME:-admin}"
ENVIRONMENT="${ENVIRONMENT:-production-minimal}"

# Pipeline configuration
RUN_TESTS="${RUN_TESTS:-true}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-false}"
CLEANUP_IMAGES="${CLEANUP_IMAGES:-true}"
SEND_NOTIFICATIONS="${SEND_NOTIFICATIONS:-false}"
SLACK_WEBHOOK_URL="${SLACK_WEBHOOK_URL:-}"

# --- Colors for output ---
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
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

log_phase() {
    echo ""
    echo -e "${PURPLE}============================================${NC}"
    echo -e "${PURPLE}  $1${NC}"
    echo -e "${PURPLE}============================================${NC}"
    echo ""
}

# --- Script setup ---
SCRIPT_DIR=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" &> /dev/null && pwd)
PROJECT_ROOT=$(cd -- "$SCRIPT_DIR/../../" &> /dev/null && pwd)

# --- State tracking ---
PIPELINE_START_TIME=$(date +%s)
PIPELINE_STATUS="running"
BUILD_STATUS="pending"
PUSH_STATUS="pending"
DEPLOY_STATUS="pending"
FAILED_STEP=""

# --- Argument parsing ---
IMAGE_TAG="${1:-}"
if [ -z "$IMAGE_TAG" ]; then
    # Generate tag from branch and timestamp
    GIT_BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")
    GIT_COMMIT=$(git rev-parse --short HEAD 2>/dev/null || echo "unknown")
    TIMESTAMP=$(date +%Y%m%d%H%M%S)
    IMAGE_TAG="${GIT_BRANCH}-${GIT_COMMIT}-${TIMESTAMP}"
    IMAGE_TAG=$(echo "$IMAGE_TAG" | sed 's/[^a-zA-Z0-9._-]/-/g') # Clean special characters
fi

# --- Notification functions ---
send_notification() {
    local message="$1"
    local status="${2:-info}" # info, success, error
    
    if [ "$SEND_NOTIFICATIONS" != "true" ] || [ -z "$SLACK_WEBHOOK_URL" ]; then
        return
    fi
    
    local color="#808080" # gray for info
    case "$status" in
        success) color="#36a64f" ;; # green
        error) color="#ff0000" ;;   # red
        warning) color="#ff9900" ;; # orange
    esac
    
    local payload=$(cat <<EOF
{
    "attachments": [{
        "color": "$color",
        "title": "PayHero Pipeline - $MODULE_NAME/$ENVIRONMENT",
        "text": "$message",
        "fields": [
            {"title": "Project", "value": "$PROJECT_NAME", "short": true},
            {"title": "Module", "value": "$MODULE_NAME", "short": true},
            {"title": "Environment", "value": "$ENVIRONMENT", "short": true},
            {"title": "Image Tag", "value": "$IMAGE_TAG", "short": true},
            {"title": "Triggered By", "value": "${USER:-CI}", "short": true}
        ],
        "footer": "PayHero CI/CD",
        "ts": $(date +%s)
    }]
}
EOF
)
    
    curl -X POST -H 'Content-type: application/json' \
        --data "$payload" \
        "$SLACK_WEBHOOK_URL" 2>/dev/null || true
}

# --- Cleanup function ---
cleanup() {
    local exit_code=$?
    
    if [ $exit_code -ne 0 ]; then
        PIPELINE_STATUS="failed"
        log_error "Pipeline failed at step: $FAILED_STEP"
        send_notification "Pipeline failed at step: $FAILED_STEP" "error"
    fi
    
    # Calculate duration
    local end_time=$(date +%s)
    local duration=$((end_time - PIPELINE_START_TIME))
    local minutes=$((duration / 60))
    local seconds=$((duration % 60))
    
    log_info "Pipeline duration: ${minutes}m ${seconds}s"
    
    # Cleanup old Docker images if enabled
    if [ "$CLEANUP_IMAGES" = "true" ] && [ "$PIPELINE_STATUS" = "success" ]; then
        log_info "Cleaning up old Docker images..."
        docker image prune -f --filter "until=24h" 2>/dev/null || true
    fi
}

trap cleanup EXIT

# --- Pre-flight checks ---
run_preflight_checks() {
    log_phase "Pre-flight Checks"
    FAILED_STEP="preflight"
    
    log_step "Checking prerequisites..."
    
    # Check required tools
    local required_tools=("docker" "aws" "git" "jq" "npm")
    for tool in "${required_tools[@]}"; do
        if ! command -v "$tool" &> /dev/null; then
            log_error "$tool is not installed"
            exit 1
        fi
    done
    
    # Check AWS credentials
    if ! aws sts get-caller-identity &> /dev/null; then
        log_error "AWS credentials not configured"
        exit 1
    fi
    
    # Check git status
    if [ -d "$PROJECT_ROOT/.git" ]; then
        if ! git diff --quiet HEAD 2>/dev/null; then
            log_warn "There are uncommitted changes in the repository"
            read -p "Continue with uncommitted changes? (y/N) " -n 1 -r
            echo
            if [[ ! $REPLY =~ ^[Yy]$ ]]; then
                exit 1
            fi
        fi
    fi
    
    log_info "Pre-flight checks passed."
}

# --- Run tests ---
run_tests() {
    if [ "$RUN_TESTS" != "true" ]; then
        log_info "Skipping tests (RUN_TESTS=false)"
        return
    fi
    
    log_phase "Running Tests"
    FAILED_STEP="tests"
    
    # Check if PHPUnit is available
    if [ -f "$PROJECT_ROOT/vendor/bin/phpunit" ]; then
        log_step "Running PHPUnit tests..."
        (cd "$PROJECT_ROOT" && ./vendor/bin/phpunit --no-coverage) || {
            log_error "PHPUnit tests failed"
            exit 1
        }
    else
        log_warn "PHPUnit not found, skipping PHP tests"
    fi
    
    # Check if Jest/npm tests are available
    if [ -f "$PROJECT_ROOT/package.json" ] && grep -q "test" "$PROJECT_ROOT/package.json"; then
        log_step "Running JavaScript tests..."
        (cd "$PROJECT_ROOT" && npm test) || {
            log_error "JavaScript tests failed"
            exit 1
        }
    fi
    
    log_info "All tests passed."
}

# --- Build phase ---
run_build() {
    log_phase "Build Phase"
    FAILED_STEP="build"
    BUILD_STATUS="running"
    
    send_notification "Starting build for image tag: $IMAGE_TAG" "info"
    
    # Run the build script
    "$SCRIPT_DIR/build.sh" "$IMAGE_TAG" || {
        BUILD_STATUS="failed"
        log_error "Build failed"
        exit 1
    }
    
    BUILD_STATUS="success"
    log_info "Build completed successfully."
}

# --- Push phase ---
run_push() {
    log_phase "Push Phase"
    FAILED_STEP="push"
    PUSH_STATUS="running"
    
    send_notification "Pushing images to ECR..." "info"
    
    # Run the push script
    "$SCRIPT_DIR/push.sh" "$IMAGE_TAG" || {
        PUSH_STATUS="failed"
        log_error "Push failed"
        exit 1
    }
    
    PUSH_STATUS="success"
    log_info "Push completed successfully."
}

# --- Database migrations ---
run_migrations() {
    if [ "$RUN_MIGRATIONS" != "true" ]; then
        log_info "Skipping database migrations (RUN_MIGRATIONS=false)"
        return
    fi
    
    log_phase "Database Migrations"
    FAILED_STEP="migrations"
    
    log_warn "Running database migrations in production!"
    read -p "Are you sure you want to run migrations? (y/N) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        log_info "Migrations skipped."
        return
    fi
    
    # Run migrations using the dedicated script
    if [ -f "$SCRIPT_DIR/run-migrations.sh" ]; then
        "$SCRIPT_DIR/run-migrations.sh" || {
            log_error "Migrations failed"
            exit 1
        }
    else
        log_warn "Migration script not found, skipping migrations"
    fi
    
    log_info "Migrations completed successfully."
}

# --- Deploy phase ---
run_deploy() {
    log_phase "Deploy Phase"
    FAILED_STEP="deploy"
    DEPLOY_STATUS="running"
    
    send_notification "Starting deployment..." "info"
    
    # Run the deploy script
    "$SCRIPT_DIR/deploy.sh" "$IMAGE_TAG" || {
        DEPLOY_STATUS="failed"
        log_error "Deployment failed"
        send_notification "Deployment failed! Check logs for details." "error"
        exit 1
    }
    
    DEPLOY_STATUS="success"
    log_info "Deployment completed successfully."
}

# --- Post-deployment tasks ---
run_post_deployment() {
    log_phase "Post-Deployment Tasks"
    FAILED_STEP="post-deployment"
    
    # Clear application caches
    log_step "Clearing application caches..."
    
    # Get running task ARN
    TASK_ARN=$(aws ecs list-tasks \
        --cluster "${PROJECT_NAME}-${ENVIRONMENT}-cluster" \
        --service-name "${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-service" \
        --region "$AWS_REGION" \
        --desired-status RUNNING \
        --query 'taskArns[0]' \
        --output text 2>/dev/null || echo "")
    
    if [ -n "$TASK_ARN" ] && [ "$TASK_ARN" != "None" ]; then
        log_info "Clearing Laravel caches on running task..."
        
        # Execute cache clear commands
        aws ecs execute-command \
            --cluster "${PROJECT_NAME}-${ENVIRONMENT}-cluster" \
            --task "$TASK_ARN" \
            --container app \
            --command "php artisan cache:clear" \
            --interactive \
            --region "$AWS_REGION" 2>/dev/null || log_warn "Failed to clear cache"
    else
        log_warn "No running tasks found, skipping cache clear"
    fi
    
    # Tag the deployment in git
    if [ -d "$PROJECT_ROOT/.git" ]; then
        log_step "Creating deployment tag..."
        TAG_NAME="deploy-${ENVIRONMENT}-${IMAGE_TAG}"
        git tag -a "$TAG_NAME" -m "Deployed to $ENVIRONMENT at $(date)" 2>/dev/null || true
        log_info "Created git tag: $TAG_NAME"
    fi
    
    log_info "Post-deployment tasks completed."
}

# --- Pipeline summary ---
show_pipeline_summary() {
    log_phase "Pipeline Summary"
    
    echo "================================================"
    echo "Pipeline Status: ${PIPELINE_STATUS^^}"
    echo ""
    echo "Steps:"
    echo "  ✓ Pre-flight checks"
    [ "$RUN_TESTS" = "true" ] && echo "  ✓ Tests"
    echo "  ${BUILD_STATUS} Build"
    echo "  ${PUSH_STATUS} Push"
    [ "$RUN_MIGRATIONS" = "true" ] && echo "  ✓ Migrations"
    echo "  ${DEPLOY_STATUS} Deploy"
    echo ""
    echo "Deployment Details:"
    echo "  Project: $PROJECT_NAME"
    echo "  Module: $MODULE_NAME"
    echo "  Environment: $ENVIRONMENT"
    echo "  Image Tag: $IMAGE_TAG"
    echo "  AWS Region: $AWS_REGION"
    echo ""
    
    # Get application URL
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --names "${PROJECT_NAME}-${ENVIRONMENT}-alb" \
        --region "$AWS_REGION" \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null || echo "Not found")
    
    echo "Application URL: http://$ALB_DNS"
    echo "================================================"
}

# --- Main execution ---
main() {
    log_info "Starting PayHero CI/CD Pipeline"
    log_info "Environment: $ENVIRONMENT"
    log_info "Image Tag: $IMAGE_TAG"
    
    send_notification "Pipeline started for image tag: $IMAGE_TAG" "info"
    
    # Run pipeline steps
    run_preflight_checks
    run_tests
    run_build
    run_push
    run_migrations
    run_deploy
    run_post_deployment
    
    PIPELINE_STATUS="success"
    FAILED_STEP=""
    
    show_pipeline_summary
    
    send_notification "Pipeline completed successfully! Application deployed with tag: $IMAGE_TAG" "success"
    
    log_info "Pipeline completed successfully!"
}

# Show help if requested
if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    echo "Usage: $0 [IMAGE_TAG]"
    echo ""
    echo "Run the complete PayHero CI/CD pipeline"
    echo ""
    echo "Arguments:"
    echo "  IMAGE_TAG    Tag for Docker images (default: branch-commit-timestamp)"
    echo ""
    echo "Environment variables:"
    echo "  AWS_REGION           AWS region (default: us-east-1)"
    echo "  AWS_ACCOUNT_ID       AWS account ID (default: 983877353757)"
    echo "  PROJECT_NAME         Project name (default: velana)"
    echo "  ENVIRONMENT          Environment name (default: production-minimal)"
    echo "  RUN_TESTS            Run tests before build (default: true)"
    echo "  RUN_MIGRATIONS       Run database migrations (default: false)"
    echo "  CLEANUP_IMAGES       Cleanup old Docker images (default: true)"
    echo "  SEND_NOTIFICATIONS   Send Slack notifications (default: false)"
    echo "  SLACK_WEBHOOK_URL    Slack webhook URL for notifications"
    echo ""
    echo "Pipeline steps:"
    echo "  1. Pre-flight checks"
    echo "  2. Run tests (optional)"
    echo "  3. Build Docker images"
    echo "  4. Push images to ECR"
    echo "  5. Run database migrations (optional)"
    echo "  6. Deploy to ECS"
    echo "  7. Post-deployment tasks"
    exit 0
fi

# Run main function
main