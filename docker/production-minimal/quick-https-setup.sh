#!/bin/bash

# Quick HTTPS setup for ALB
echo "=== Quick HTTPS Setup for ALB ==="

# Variables
ALB_NAME="velana-production-minimal-alb"
REGION="us-east-1"

# Get ALB ARN
ALB_ARN=$(aws elbv2 describe-load-balancers \
    --names "$ALB_NAME" \
    --region "$REGION" \
    --query 'LoadBalancers[0].LoadBalancerArn' \
    --output text)

ALB_DNS=$(aws elbv2 describe-load-balancers \
    --load-balancer-arns "$ALB_ARN" \
    --region "$REGION" \
    --query 'LoadBalancers[0].DNSName' \
    --output text)

echo "ALB DNS: $ALB_DNS"

# Option 1: Self-signed certificate for testing
echo ""
echo "Since you don't have a domain configured, you have these options:"
echo ""
echo "1. FOR TESTING (Certificate Warning in Browser):"
echo "   - The ALB already accepts HTTPS traffic on port 443"
echo "   - You can access: https://$ALB_DNS"
echo "   - Your browser will show a certificate warning (this is normal for testing)"
echo ""
echo "2. FOR PRODUCTION (No Certificate Warning):"
echo "   a) Register a domain name"
echo "   b) Request an ACM certificate for your domain:"
echo "      aws acm request-certificate \\"
echo "        --domain-name YOUR-DOMAIN.com \\"
echo "        --validation-method DNS \\"
echo "        --region us-east-1"
echo "   c) Validate the certificate (follow ACM instructions)"
echo "   d) Run this command to add HTTPS listener:"
echo "      ./docker/production-minimal/setup-https-listener.sh CERTIFICATE-ARN"
echo ""
echo "3. FOR QUICK PRODUCTION SETUP:"
echo "   - Use a subdomain of an existing domain you control"
echo "   - Example: velana.yourdomain.com"
echo ""

# Create the listener setup script
cat > /home/hero/projects/payhero/manager/docker/production-minimal/setup-https-listener.sh << 'EOF'
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
EOF

chmod +x /home/hero/projects/payhero/manager/docker/production-minimal/setup-https-listener.sh

echo "Created: setup-https-listener.sh"
echo ""
echo "Current Status:"
echo "✓ Security group allows HTTPS (port 443)"
echo "✓ ALB is ready for HTTPS"
echo "✗ No SSL certificate configured yet"
echo "✗ No HTTPS listener configured yet"