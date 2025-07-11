#!/bin/bash

# PayHero Production-Minimal Deployment Script for Velana
# Main deployment orchestrator

set -euo pipefail

# Configuration
export PROJECT_NAME="velana"
export ENVIRONMENT="production-minimal"
export AWS_ACCOUNT_ID="983877353757"
export AWS_REGION="us-east-1"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
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

log_step() {
    echo -e "\n${BLUE}===> $1${NC}\n"
}

# Show deployment banner
show_banner() {
    echo
    echo "======================================"
    echo " PayHero Production-Minimal Deployment"
    echo "======================================"
    echo " Project: $PROJECT_NAME"
    echo " Environment: $ENVIRONMENT"
    echo " AWS Account: $AWS_ACCOUNT_ID"
    echo " Region: $AWS_REGION"
    echo "======================================"
    echo
}

# Check prerequisites
check_prerequisites() {
    log_step "Checking Prerequisites"
    
    # Check AWS CLI
    if ! command -v aws &> /dev/null; then
        log_error "AWS CLI not found. Please install AWS CLI v2."
        exit 1
    fi
    
    # Check AWS credentials
    CURRENT_ACCOUNT=$(aws sts get-caller-identity --query Account --output text 2>/dev/null || echo "")
    if [ -z "$CURRENT_ACCOUNT" ]; then
        log_error "AWS credentials not configured."
        exit 1
    fi
    
    if [ "$CURRENT_ACCOUNT" != "$AWS_ACCOUNT_ID" ]; then
        log_error "AWS account mismatch. Expected: $AWS_ACCOUNT_ID, Current: $CURRENT_ACCOUNT"
        exit 1
    fi
    
    log_info "✓ AWS CLI configured"
    log_info "✓ AWS Account: $CURRENT_ACCOUNT"
    
    # Check Docker
    if ! command -v docker &> /dev/null; then
        log_error "Docker not found. Please install Docker."
        exit 1
    fi
    
    log_info "✓ Docker installed"
    
    # Check jq
    if ! command -v jq &> /dev/null; then
        log_error "jq not found. Please install jq."
        exit 1
    fi
    
    log_info "✓ jq installed"
}

# Build and push Docker images
build_and_push() {
    log_step "Building and Pushing Docker Images"
    
    # Use existing build script or CodeBuild
    if [ "${USE_CODEBUILD:-true}" = "true" ]; then
        log_info "Using AWS CodeBuild for building images..."
        ./trigger-codebuild.sh --branch main --wait || {
            log_error "CodeBuild failed"
            exit 1
        }
    else
        log_info "Building images locally..."
        # Use local build script
        ./build-local.sh || {
            log_error "Local build failed"
            exit 1
        }
    fi
}

# Deploy to ECS
deploy_to_ecs() {
    log_step "Deploying to ECS"
    
    # Deploy using the ECS deployment script
    ./deploy-ecs.sh latest || {
        log_error "ECS deployment failed"
        exit 1
    }
}

# Run database migrations
run_migrations() {
    log_step "Running Database Migrations"
    
    read -p "Do you want to run database migrations? (y/N) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        ./run-migrations.sh || {
            log_warn "Migration failed, but continuing..."
        }
    else
        log_info "Skipping migrations"
    fi
}

# Run health checks
verify_deployment() {
    log_step "Verifying Deployment"
    
    # Wait a bit for services to stabilize
    log_info "Waiting 30 seconds for services to stabilize..."
    sleep 30
    
    # Run health check
    ./health-check.sh || {
        log_warn "Some health checks failed"
    }
}

# Show deployment summary
show_summary() {
    log_step "Deployment Summary"
    
    # Get ALB URL
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --names $PROJECT-production-minimal-alb \
        --region $AWS_REGION \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null || echo "Not found")
    
    echo "Deployment completed!"
    echo
    echo "Application URL: http://$ALB_DNS"
    echo "CloudWatch Logs: https://console.aws.amazon.com/cloudwatch/home?region=$AWS_REGION#logsV2:log-groups/log-group/%2Fecs%2F$PROJECT-production-minimal"
    echo "ECS Console: https://console.aws.amazon.com/ecs/v2/clusters/$PROJECT-production-minimal-cluster?region=$AWS_REGION"
    echo
    echo "Default credentials:"
    echo "  Email: hero@payhero.app"
    echo "  Password: ******"
    echo
    echo "Next steps:"
    echo "  1. Update Route53 or your DNS to point to the ALB"
    echo "  2. Configure SSL certificate in ACM"
    echo "  3. Update ALB listener to use HTTPS"
    echo "  4. Monitor costs with: ./monitor-costs.sh"
}

# Main deployment flow
main() {
    show_banner
    
    # Parse command line arguments
    SKIP_BUILD=false
    SKIP_DEPLOY=false
    SKIP_HEALTH=false
    
    while [[ $# -gt 0 ]]; do
        case $1 in
            --skip-build)
                SKIP_BUILD=true
                shift
                ;;
            --skip-deploy)
                SKIP_DEPLOY=true
                shift
                ;;
            --skip-health)
                SKIP_HEALTH=true
                shift
                ;;
            --use-local-build)
                USE_CODEBUILD=false
                shift
                ;;
            --help)
                echo "Usage: $0 [OPTIONS]"
                echo ""
                echo "Options:"
                echo "  --skip-build      Skip building Docker images"
                echo "  --skip-deploy     Skip ECS deployment"
                echo "  --skip-health     Skip health checks"
                echo "  --use-local-build Use local Docker build instead of CodeBuild"
                echo "  --help            Show this help message"
                exit 0
                ;;
            *)
                log_error "Unknown option: $1"
                exit 1
                ;;
        esac
    done
    
    # Execute deployment steps
    check_prerequisites
    
    if [ "$SKIP_BUILD" = false ]; then
        build_and_push
    else
        log_info "Skipping build step"
    fi
    
    if [ "$SKIP_DEPLOY" = false ]; then
        deploy_to_ecs
        run_migrations
    else
        log_info "Skipping deployment step"
    fi
    
    if [ "$SKIP_HEALTH" = false ]; then
        verify_deployment
    else
        log_info "Skipping health checks"
    fi
    
    show_summary
    
    log_info "Deployment process completed!"
}

# Run main function
main "$@"