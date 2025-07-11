#!/bin/bash

# Create ECS Service for Velana Production-Minimal

set -euo pipefail

# Configuration
AWS_REGION=${AWS_REGION:-us-east-1}
AWS_ACCOUNT_ID="983877353757"
CLUSTER_NAME="velana-production-minimal-cluster"
SERVICE_NAME="velana-production-minimal-service"
TASK_DEFINITION="velana-production-minimal-app"
PROJECT_NAME="velana"
ENVIRONMENT="production-minimal"

# Known resources
VPC_ID="vpc-007f6d595b20510fd"
SUBNETS="subnet-03e95877b9a467278,subnet-011f2742d24b4209a"
SECURITY_GROUP="sg-052f51faa21ed7598"  # App security group

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Helper functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

# Check if ALB exists
check_alb() {
    log_info "Checking for ALB..."
    
    ALB_ARN=$(aws elbv2 describe-load-balancers \
        --names "velana-production-minimal-alb" \
        --region $AWS_REGION \
        --query 'LoadBalancers[0].LoadBalancerArn' \
        --output text 2>/dev/null || echo "")
    
    if [ -n "$ALB_ARN" ] && [ "$ALB_ARN" != "None" ]; then
        log_info "Found ALB: $ALB_ARN"
        
        # Get target group
        TARGET_GROUP_ARN=$(aws elbv2 describe-target-groups \
            --load-balancer-arn "$ALB_ARN" \
            --region $AWS_REGION \
            --query 'TargetGroups[0].TargetGroupArn' \
            --output text 2>/dev/null || echo "")
        
        if [ -n "$TARGET_GROUP_ARN" ] && [ "$TARGET_GROUP_ARN" != "None" ]; then
            log_info "Found Target Group: $TARGET_GROUP_ARN"
            return 0
        fi
    fi
    
    log_warn "No ALB found. Service will be created without load balancer."
    TARGET_GROUP_ARN=""
    return 1
}

# Create ECS service
create_service() {
    log_info "Creating ECS service..."
    
    # Check if ALB exists
    if check_alb; then
        # With load balancer
        aws ecs create-service \
            --cluster "$CLUSTER_NAME" \
            --service-name "$SERVICE_NAME" \
            --task-definition "$TASK_DEFINITION" \
            --desired-count 1 \
            --capacity-provider-strategy "capacityProvider=FARGATE_SPOT,weight=80,base=0" "capacityProvider=FARGATE,weight=20,base=1" \
            --network-configuration "awsvpcConfiguration={subnets=[$SUBNETS],securityGroups=[$SECURITY_GROUP],assignPublicIp=DISABLED}" \
            --load-balancers "targetGroupArn=$TARGET_GROUP_ARN,containerName=nginx,containerPort=80" \
            --health-check-grace-period-seconds 60 \
            --tags "key=Project,value=$PROJECT_NAME" "key=Environment,value=$ENVIRONMENT" \
            --region $AWS_REGION
    else
        # Without load balancer, use public IP
        aws ecs create-service \
            --cluster "$CLUSTER_NAME" \
            --service-name "$SERVICE_NAME" \
            --task-definition "$TASK_DEFINITION" \
            --desired-count 1 \
            --capacity-provider-strategy "capacityProvider=FARGATE_SPOT,weight=80,base=0" "capacityProvider=FARGATE,weight=20,base=1" \
            --network-configuration "awsvpcConfiguration={subnets=[$SUBNETS],securityGroups=[$SECURITY_GROUP],assignPublicIp=ENABLED}" \
            --tags "key=Project,value=$PROJECT_NAME" "key=Environment,value=$ENVIRONMENT" \
            --region $AWS_REGION
    fi
    
    log_info "ECS service created successfully!"
}

# Main execution
main() {
    log_info "Creating ECS Service for Velana Production-Minimal"
    log_info "Project: $PROJECT_NAME"
    log_info "Environment: $ENVIRONMENT"
    log_info "Cluster: $CLUSTER_NAME"
    log_info "Service: $SERVICE_NAME"
    log_info "VPC: $VPC_ID"
    log_info "Subnets: $SUBNETS"
    log_info "Security Group: $SECURITY_GROUP"
    
    # Create the service
    create_service
    
    log_info "Service creation complete!"
    log_info "You can now run ./deploy-ecs-task.sh to deploy your application"
}

# Run main function
main "$@"