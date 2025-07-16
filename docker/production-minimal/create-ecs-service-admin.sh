#!/bin/bash
# Create ECS Service for PayHero Admin Module
# This script creates the ECS service specifically for the admin module

set -euo pipefail

# --- Configuration ---
AWS_REGION="${AWS_REGION:-us-east-1}"
AWS_ACCOUNT_ID="${AWS_ACCOUNT_ID:-983877353757}"
PROJECT_NAME="${PROJECT_NAME:-velana}"
MODULE_NAME="${MODULE_NAME:-admin}"
ENVIRONMENT="${ENVIRONMENT:-production-minimal}"

# Service configuration
CLUSTER_NAME="${PROJECT_NAME}-${ENVIRONMENT}-cluster"
SERVICE_NAME="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-service"
TASK_FAMILY="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-task"
DESIRED_COUNT="${DESIRED_COUNT:-1}"

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
    
    log_info "Prerequisites check passed."
}

verify_infrastructure() {
    log_step "Verifying infrastructure..."
    
    # Check cluster exists
    if ! aws ecs describe-clusters \
        --clusters "$CLUSTER_NAME" \
        --region "$AWS_REGION" \
        --query "clusters[?status=='ACTIVE'].clusterName" \
        --output text | grep -q "$CLUSTER_NAME"; then
        log_error "ECS cluster '$CLUSTER_NAME' not found or not active"
        log_info "Please ensure the infrastructure is set up properly"
        exit 1
    fi
    
    # Check if service already exists
    SERVICE_EXISTS=$(aws ecs describe-services \
        --cluster "$CLUSTER_NAME" \
        --services "$SERVICE_NAME" \
        --region "$AWS_REGION" \
        --query 'services[0].status' \
        --output text 2>/dev/null || echo "")
    
    if [ -n "$SERVICE_EXISTS" ] && [ "$SERVICE_EXISTS" != "None" ]; then
        log_warn "Service '$SERVICE_NAME' already exists with status: $SERVICE_EXISTS"
        read -p "Do you want to update the existing service? (y/N) " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 0
        fi
    fi
    
    log_info "Infrastructure verified."
}

get_network_configuration() {
    log_step "Getting network configuration..."
    
    # Get VPC ID
    VPC_ID=$(aws ec2 describe-vpcs \
        --filters "Name=tag:Project,Values=$PROJECT_NAME" "Name=tag:Environment,Values=$ENVIRONMENT" \
        --region "$AWS_REGION" \
        --query 'Vpcs[0].VpcId' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$VPC_ID" ] || [ "$VPC_ID" = "None" ]; then
        log_warn "Could not find VPC by tags, looking for default VPC..."
        VPC_ID=$(aws ec2 describe-vpcs \
            --filters "Name=isDefault,Values=true" \
            --region "$AWS_REGION" \
            --query 'Vpcs[0].VpcId' \
            --output text)
    fi
    
    log_info "Using VPC: $VPC_ID"
    
    # Get private subnets
    PRIVATE_SUBNETS=$(aws ec2 describe-subnets \
        --filters "Name=vpc-id,Values=$VPC_ID" "Name=tag:Type,Values=private" \
        --region "$AWS_REGION" \
        --query 'Subnets[*].SubnetId' \
        --output json 2>/dev/null || echo "[]")
    
    if [ "$PRIVATE_SUBNETS" = "[]" ]; then
        log_warn "No private subnets found, using all subnets..."
        PRIVATE_SUBNETS=$(aws ec2 describe-subnets \
            --filters "Name=vpc-id,Values=$VPC_ID" \
            --region "$AWS_REGION" \
            --query 'Subnets[*].SubnetId' \
            --output json)
    fi
    
    SUBNET_IDS=$(echo "$PRIVATE_SUBNETS" | jq -r '.[]' | paste -sd "," -)
    log_info "Using subnets: $SUBNET_IDS"
    
    # Get or create security group
    SECURITY_GROUP_NAME="${PROJECT_NAME}-${ENVIRONMENT}-${MODULE_NAME}-ecs-sg"
    SECURITY_GROUP_ID=$(aws ec2 describe-security-groups \
        --filters "Name=group-name,Values=$SECURITY_GROUP_NAME" "Name=vpc-id,Values=$VPC_ID" \
        --region "$AWS_REGION" \
        --query 'SecurityGroups[0].GroupId' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$SECURITY_GROUP_ID" ] || [ "$SECURITY_GROUP_ID" = "None" ]; then
        log_info "Creating security group..."
        SECURITY_GROUP_ID=$(aws ec2 create-security-group \
            --group-name "$SECURITY_GROUP_NAME" \
            --description "Security group for $PROJECT_NAME $MODULE_NAME ECS tasks" \
            --vpc-id "$VPC_ID" \
            --region "$AWS_REGION" \
            --query 'GroupId' \
            --output text)
        
        # Add rules
        aws ec2 authorize-security-group-ingress \
            --group-id "$SECURITY_GROUP_ID" \
            --protocol tcp \
            --port 80 \
            --source-group "$SECURITY_GROUP_ID" \
            --region "$AWS_REGION" || true
        
        aws ec2 authorize-security-group-ingress \
            --group-id "$SECURITY_GROUP_ID" \
            --protocol tcp \
            --port 9000 \
            --source-group "$SECURITY_GROUP_ID" \
            --region "$AWS_REGION" || true
    fi
    
    log_info "Using security group: $SECURITY_GROUP_ID"
}

get_load_balancer_config() {
    log_step "Getting load balancer configuration..."
    
    # Get ALB
    ALB_NAME="${PROJECT_NAME}-${ENVIRONMENT}-alb"
    ALB_ARN=$(aws elbv2 describe-load-balancers \
        --names "$ALB_NAME" \
        --region "$AWS_REGION" \
        --query 'LoadBalancers[0].LoadBalancerArn' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$ALB_ARN" ] || [ "$ALB_ARN" = "None" ]; then
        log_error "Load balancer '$ALB_NAME' not found"
        log_info "Please create the load balancer first"
        exit 1
    fi
    
    # Get or create target group for admin module
    TARGET_GROUP_NAME="${PROJECT_NAME}-ProdMini-${MODULE_NAME}-tg"
    TARGET_GROUP_ARN=$(aws elbv2 describe-target-groups \
        --names "$TARGET_GROUP_NAME" \
        --region "$AWS_REGION" \
        --query 'TargetGroups[0].TargetGroupArn' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$TARGET_GROUP_ARN" ] || [ "$TARGET_GROUP_ARN" = "None" ]; then
        log_info "Creating target group for admin module..."
        TARGET_GROUP_ARN=$(aws elbv2 create-target-group \
            --name "$TARGET_GROUP_NAME" \
            --protocol HTTP \
            --port 80 \
            --vpc-id "$VPC_ID" \
            --target-type ip \
            --health-check-enabled \
            --health-check-path /health \
            --health-check-interval-seconds 30 \
            --health-check-timeout-seconds 5 \
            --healthy-threshold-count 2 \
            --unhealthy-threshold-count 3 \
            --matcher HttpCode=200 \
            --region "$AWS_REGION" \
            --tags Key=Project,Value="$PROJECT_NAME" Key=Module,Value="$MODULE_NAME" Key=Environment,Value="$ENVIRONMENT" \
            --query 'TargetGroups[0].TargetGroupArn' \
            --output text)
        
        # Add listener rule for admin module
        LISTENER_ARN=$(aws elbv2 describe-listeners \
            --load-balancer-arn "$ALB_ARN" \
            --region "$AWS_REGION" \
            --query 'Listeners[?Port==`80`].ListenerArn' \
            --output text)
        
        if [ -n "$LISTENER_ARN" ] && [ "$LISTENER_ARN" != "None" ]; then
            # Get the next available priority
            PRIORITY=$(aws elbv2 describe-rules \
                --listener-arn "$LISTENER_ARN" \
                --region "$AWS_REGION" \
                --query 'Rules[?Priority!=`default`].Priority' \
                --output text | tr '\t' '\n' | sort -n | tail -1)
            
            NEXT_PRIORITY=$((${PRIORITY:-0} + 10))
            
            log_info "Creating listener rule with priority $NEXT_PRIORITY..."
            aws elbv2 create-rule \
                --listener-arn "$LISTENER_ARN" \
                --priority "$NEXT_PRIORITY" \
                --conditions Field=path-pattern,Values="/admin*" \
                --actions Type=forward,TargetGroupArn="$TARGET_GROUP_ARN" \
                --region "$AWS_REGION" || log_warn "Failed to create listener rule"
        fi
    fi
    
    log_info "Using target group: $TARGET_GROUP_ARN"
}

create_or_update_service() {
    log_step "Creating/updating ECS service..."
    
    # Create the service configuration
    SERVICE_CONFIG=$(cat <<EOF
{
    "cluster": "$CLUSTER_NAME",
    "serviceName": "$SERVICE_NAME",
    "taskDefinition": "$TASK_FAMILY",
    "desiredCount": $DESIRED_COUNT,
    "launchType": "FARGATE",
    "platformVersion": "LATEST",
    "networkConfiguration": {
        "awsvpcConfiguration": {
            "subnets": [$(echo "$SUBNET_IDS" | sed 's/,/","/g' | sed 's/^/"/;s/$/"/')],
            "securityGroups": ["$SECURITY_GROUP_ID"],
            "assignPublicIp": "DISABLED"
        }
    },
    "loadBalancers": [
        {
            "targetGroupArn": "$TARGET_GROUP_ARN",
            "containerName": "admin-nginx",
            "containerPort": 80
        }
    ],
    "healthCheckGracePeriodSeconds": 120,
    "deploymentConfiguration": {
        "maximumPercent": 200,
        "minimumHealthyPercent": 100,
        "deploymentCircuitBreaker": {
            "enable": true,
            "rollback": true
        }
    },
    "enableExecuteCommand": true,
    "propagateTags": "SERVICE",
    "tags": [
        {"key": "Project", "value": "$PROJECT_NAME"},
        {"key": "Module", "value": "$MODULE_NAME"},
        {"key": "Environment", "value": "$ENVIRONMENT"},
        {"key": "ManagedBy", "value": "terraform"}
    ]
}
EOF
)
    
    # Check if service exists
    if [ -n "$SERVICE_EXISTS" ] && [ "$SERVICE_EXISTS" != "None" ]; then
        log_info "Updating existing service..."
        aws ecs update-service \
            --cluster "$CLUSTER_NAME" \
            --service "$SERVICE_NAME" \
            --task-definition "$TASK_FAMILY" \
            --desired-count "$DESIRED_COUNT" \
            --network-configuration "$(echo "$SERVICE_CONFIG" | jq -c '.networkConfiguration')" \
            --health-check-grace-period-seconds 120 \
            --enable-execute-command \
            --force-new-deployment \
            --region "$AWS_REGION" || {
                log_error "Failed to update service"
                exit 1
            }
    else
        log_info "Creating new service..."
        aws ecs create-service \
            --cli-input-json "$SERVICE_CONFIG" \
            --region "$AWS_REGION" || {
                log_error "Failed to create service"
                exit 1
            }
    fi
    
    log_info "Service created/updated successfully."
}

wait_for_service_stability() {
    log_step "Waiting for service to stabilize..."
    
    local max_attempts=30
    local attempt=0
    
    while [ $attempt -lt $max_attempts ]; do
        SERVICE_STATUS=$(aws ecs describe-services \
            --cluster "$CLUSTER_NAME" \
            --services "$SERVICE_NAME" \
            --region "$AWS_REGION" \
            --query 'services[0]')
        
        RUNNING_COUNT=$(echo "$SERVICE_STATUS" | jq -r '.runningCount // 0')
        DESIRED_COUNT=$(echo "$SERVICE_STATUS" | jq -r '.desiredCount // 0')
        PENDING_COUNT=$(echo "$SERVICE_STATUS" | jq -r '.pendingCount // 0')
        
        log_info "Service status: Running $RUNNING_COUNT/$DESIRED_COUNT (Pending: $PENDING_COUNT)"
        
        if [ "$RUNNING_COUNT" -eq "$DESIRED_COUNT" ] && [ "$PENDING_COUNT" -eq 0 ]; then
            log_info "Service is stable!"
            break
        fi
        
        sleep 10
        ((attempt++))
    done
    
    if [ $attempt -ge $max_attempts ]; then
        log_warn "Service stabilization timed out, but continuing..."
    fi
}

show_service_info() {
    log_step "Service Information"
    echo "================================================"
    echo "Project: $PROJECT_NAME"
    echo "Module: $MODULE_NAME"
    echo "Environment: $ENVIRONMENT"
    echo "Cluster: $CLUSTER_NAME"
    echo "Service: $SERVICE_NAME"
    echo "Task Family: $TASK_FAMILY"
    echo ""
    
    # Get ALB DNS
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --names "${PROJECT_NAME}-${ENVIRONMENT}-alb" \
        --region "$AWS_REGION" \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null || echo "Not found")
    
    echo "Admin Module URL: http://$ALB_DNS/admin"
    echo "Health Check URL: http://$ALB_DNS/admin/health"
    echo "================================================"
}

# --- Main execution ---
main() {
    log_info "Creating ECS service for PayHero Admin module..."
    
    check_prerequisites
    verify_infrastructure
    get_network_configuration
    get_load_balancer_config
    create_or_update_service
    wait_for_service_stability
    show_service_info
    
    log_info "Admin module service setup completed!"
    log_info ""
    log_info "Next steps:"
    log_info "  1. Build images: ./build.sh"
    log_info "  2. Push to ECR: ./push.sh"
    log_info "  3. Deploy: ./deploy.sh"
}

# Show help if requested
if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    echo "Usage: $0"
    echo ""
    echo "Create ECS service for PayHero Admin module"
    echo ""
    echo "Environment variables:"
    echo "  AWS_REGION          AWS region (default: us-east-1)"
    echo "  AWS_ACCOUNT_ID      AWS account ID (default: 983877353757)"
    echo "  PROJECT_NAME        Project name (default: velana)"
    echo "  MODULE_NAME         Module name (default: admin)"
    echo "  ENVIRONMENT         Environment name (default: production-minimal)"
    echo "  DESIRED_COUNT       Desired task count (default: 1)"
    exit 0
fi

# Run main function
main