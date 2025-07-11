#!/bin/bash

# Create VPC Endpoints for SSM access in private subnets

set -euo pipefail

# Configuration
VPC_ID="vpc-007f6d595b20510fd"
REGION="us-east-1"
SUBNETS="subnet-03e95877b9a467278,subnet-011f2742d24b4209a"
SECURITY_GROUP="sg-052f51faa21ed7598"
PROJECT_NAME="velana"
ENVIRONMENT="production-minimal"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}Creating VPC Endpoints for SSM access...${NC}"

# Services that need endpoints
SERVICES=(
    "com.amazonaws.$REGION.ssm"
    "com.amazonaws.$REGION.ssmmessages"
    "com.amazonaws.$REGION.ec2messages"
)

for SERVICE in "${SERVICES[@]}"; do
    echo -e "${GREEN}Creating endpoint for $SERVICE...${NC}"
    
    aws ec2 create-vpc-endpoint \
        --vpc-id "$VPC_ID" \
        --service-name "$SERVICE" \
        --vpc-endpoint-type Interface \
        --subnet-ids subnet-03e95877b9a467278 subnet-0ad57a5eecec7877a \
        --security-group-ids "$SECURITY_GROUP" \
        --tag-specifications "ResourceType=vpc-endpoint,Tags=[{Key=Name,Value=$PROJECT_NAME-$ENVIRONMENT-${SERVICE##*.}},{Key=Project,Value=$PROJECT_NAME},{Key=Environment,Value=$ENVIRONMENT}]" \
        --region "$REGION" \
        --no-cli-pager || echo -e "${YELLOW}Endpoint for $SERVICE might already exist${NC}"
done

echo -e "${GREEN}VPC Endpoints created successfully!${NC}"
echo -e "${GREEN}Note: It may take a few minutes for the endpoints to become available.${NC}"