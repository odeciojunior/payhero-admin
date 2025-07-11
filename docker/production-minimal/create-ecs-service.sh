#!/bin/bash

# Create ECS Service for PayHero Production-Minimal
# Project: Velana

set -euo pipefail

# Configuration
AWS_REGION=${AWS_REGION:-us-east-1}
AWS_ACCOUNT_ID="983877353757"
CLUSTER_NAME="velana-production-minimal-cluster"
SERVICE_NAME="velana-production-minimal-service"
TASK_DEFINITION="velana-production-minimal-app"
PROJECT_NAME="velana"
ENVIRONMENT="production-minimal"

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

# Get VPC and networking information
get_vpc_info() {
    log_info "Getting VPC information..."
    
    # Get default VPC
    VPC_ID=$(aws ec2 describe-vpcs \
        --filters "Name=is-default,Values=true" \
        --region $AWS_REGION \
        --query 'Vpcs[0].VpcId' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$VPC_ID" ] || [ "$VPC_ID" = "None" ]; then
        # Get any VPC
        VPC_ID=$(aws ec2 describe-vpcs \
            --region $AWS_REGION \
            --query 'Vpcs[0].VpcId' \
            --output text)
    fi
    
    log_info "Using VPC: $VPC_ID"
    
    # Get private subnets
    PRIVATE_SUBNETS=$(aws ec2 describe-subnets \
        --filters "Name=vpc-id,Values=$VPC_ID" \
        --region $AWS_REGION \
        --query 'Subnets[?MapPublicIpOnLaunch==`false`].SubnetId' \
        --output json | jq -r '.[:2] | join(",")')
    
    if [ -z "$PRIVATE_SUBNETS" ] || [ "$PRIVATE_SUBNETS" = "" ]; then
        # If no private subnets, use any subnets
        PRIVATE_SUBNETS=$(aws ec2 describe-subnets \
            --filters "Name=vpc-id,Values=$VPC_ID" \
            --region $AWS_REGION \
            --query 'Subnets[:2].SubnetId' \
            --output json | jq -r 'join(",")')
    fi
    
    log_info "Using subnets: $PRIVATE_SUBNETS"
}

# Get or create security group
get_security_group() {
    log_info "Getting security group..."
    
    # Check if security group exists
    SECURITY_GROUP=$(aws ec2 describe-security-groups \
        --filters "Name=group-name,Values=velana-production-minimal-ecs" \
        --region $AWS_REGION \
        --query 'SecurityGroups[0].GroupId' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$SECURITY_GROUP" ] || [ "$SECURITY_GROUP" = "None" ]; then
        log_info "Creating security group..."
        SECURITY_GROUP=$(aws ec2 create-security-group \
            --group-name "velana-production-minimal-ecs" \
            --description "Security group for Velana production-minimal ECS tasks" \
            --vpc-id "$VPC_ID" \
            --region $AWS_REGION \
            --tag-specifications "ResourceType=security-group,Tags=[{Key=Project,Value=$PROJECT_NAME},{Key=Environment,Value=$ENVIRONMENT}]" \
            --query 'GroupId' \
            --output text)
        
        # Add rules
        log_info "Adding security group rules..."
        
        # Allow HTTP from ALB
        aws ec2 authorize-security-group-ingress \
            --group-id "$SECURITY_GROUP" \
            --protocol tcp \
            --port 80 \
            --source-group "$SECURITY_GROUP" \
            --region $AWS_REGION 2>/dev/null || true
        
        # Allow app port from within security group
        aws ec2 authorize-security-group-ingress \
            --group-id "$SECURITY_GROUP" \
            --protocol tcp \
            --port 9000 \
            --source-group "$SECURITY_GROUP" \
            --region $AWS_REGION 2>/dev/null || true
        
        # Allow all outbound
        aws ec2 authorize-security-group-egress \
            --group-id "$SECURITY_GROUP" \
            --protocol all \
            --cidr 0.0.0.0/0 \
            --region $AWS_REGION 2>/dev/null || true
    fi
    
    log_info "Using security group: $SECURITY_GROUP"
}

# Get or create target group
get_target_group() {
    log_info "Getting target group..."
    
    # Check if ALB exists
    ALB_ARN=$(aws elbv2 describe-load-balancers \
        --names "velana-production-minimal-alb" \
        --region $AWS_REGION \
        --query 'LoadBalancers[0].LoadBalancerArn' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$ALB_ARN" ] || [ "$ALB_ARN" = "None" ]; then
        log_warn "No ALB found. Service will be created without load balancer."
        TARGET_GROUP_ARN=""
    else
        # Get target group
        TARGET_GROUP_ARN=$(aws elbv2 describe-target-groups \
            --load-balancer-arn "$ALB_ARN" \
            --region $AWS_REGION \
            --query 'TargetGroups[0].TargetGroupArn' \
            --output text 2>/dev/null || echo "")
        
        if [ -n "$TARGET_GROUP_ARN" ] && [ "$TARGET_GROUP_ARN" != "None" ]; then
            log_info "Using target group: $TARGET_GROUP_ARN"
        else
            log_warn "No target group found for ALB"
            TARGET_GROUP_ARN=""
        fi
    fi
}

# Create ECS service
create_service() {
    log_info "Creating ECS service..."
    
    # Build network configuration
    NETWORK_CONFIG="awsvpcConfiguration={subnets=[$PRIVATE_SUBNETS],securityGroups=[$SECURITY_GROUP],assignPublicIp=DISABLED}"
    
    # Build service command
    SERVICE_CMD="aws ecs create-service \
        --cluster $CLUSTER_NAME \
        --service-name $SERVICE_NAME \
        --task-definition $TASK_DEFINITION \
        --desired-count 1 \
        --launch-type FARGATE \
        --network-configuration '$NETWORK_CONFIG' \
        --region $AWS_REGION"
    
    # Add load balancer if available
    if [ -n "$TARGET_GROUP_ARN" ] && [ "$TARGET_GROUP_ARN" != "" ]; then
        SERVICE_CMD="$SERVICE_CMD --load-balancers targetGroupArn=$TARGET_GROUP_ARN,containerName=nginx,containerPort=80"
    fi
    
    # Add capacity provider strategy for cost optimization
    SERVICE_CMD="$SERVICE_CMD --capacity-provider-strategy capacityProvider=FARGATE_SPOT,weight=80,base=0 capacityProvider=FARGATE,weight=20,base=1"
    
    # Add tags
    SERVICE_CMD="$SERVICE_CMD --tags key=Project,value=$PROJECT_NAME key=Environment,value=$ENVIRONMENT"
    
    # Execute command
    eval $SERVICE_CMD
    
    log_info "ECS service created successfully!"
}

# Main execution
main() {
    log_info "Creating ECS Service for Velana Production-Minimal"
    log_info "Project: $PROJECT_NAME"
    log_info "Environment: $ENVIRONMENT"
    
    # Get networking information
    get_vpc_info
    get_security_group
    get_target_group
    
    # Create the service
    create_service
    
    log_info "Service creation complete!"
    log_info "You can now run ./deploy-ecs.sh to deploy your application"
}

# Run main function
main "$@"