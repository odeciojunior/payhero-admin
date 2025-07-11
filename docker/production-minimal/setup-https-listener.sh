#!/bin/bash

if [ $# -eq 0 ]; then
    echo "Usage: $0 <certificate-arn>"
    exit 1
fi

CERTIFICATE_ARN=$1
ALB_NAME="velana-production-minimal-alb"
REGION="us-east-1"

# Get ALB ARN
ALB_ARN=$(aws elbv2 describe-load-balancers \
    --names "$ALB_NAME" \
    --region "$REGION" \
    --query 'LoadBalancers[0].LoadBalancerArn' \
    --output text)

# Get target group ARN
TARGET_GROUP_ARN=$(aws elbv2 describe-target-groups \
    --load-balancer-arn "$ALB_ARN" \
    --region "$REGION" \
    --query 'TargetGroups[0].TargetGroupArn' \
    --output text)

# Create HTTPS listener
aws elbv2 create-listener \
    --load-balancer-arn "$ALB_ARN" \
    --protocol HTTPS \
    --port 443 \
    --certificates CertificateArn="$CERTIFICATE_ARN" \
    --default-actions Type=forward,TargetGroupArn="$TARGET_GROUP_ARN" \
    --region "$REGION"

echo "HTTPS listener created successfully!"

# Optional: Set up HTTP to HTTPS redirect
echo ""
read -p "Do you want to redirect HTTP to HTTPS? (y/n): " redirect
if [ "$redirect" = "y" ]; then
    HTTP_LISTENER=$(aws elbv2 describe-listeners \
        --load-balancer-arn "$ALB_ARN" \
        --region "$REGION" \
        --query 'Listeners[?Port==`80`].ListenerArn' \
        --output text)
    
    aws elbv2 modify-listener \
        --listener-arn "$HTTP_LISTENER" \
        --default-actions Type=redirect,RedirectConfig='{Protocol=HTTPS,Port=443,Host="#{host}",Path="/#{path}",Query="#{query}",StatusCode=HTTP_301}' \
        --region "$REGION"
    
    echo "HTTP to HTTPS redirect configured!"
fi
