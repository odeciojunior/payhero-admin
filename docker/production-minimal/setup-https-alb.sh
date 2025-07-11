#!/bin/bash

# Script to set up HTTPS for ALB
echo "=== Setting up HTTPS for ALB ==="
echo "Starting at: $(date)"

# Variables
ALB_NAME="velana-production-minimal-alb"
REGION="us-east-1"
DOMAIN_NAME="${DOMAIN_NAME:-velana.payhero.com}"  # Change this to your domain

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    if [ "$1" = "success" ]; then
        echo -e "${GREEN}✓ $2${NC}"
    elif [ "$1" = "error" ]; then
        echo -e "${RED}✗ $2${NC}"
    elif [ "$1" = "info" ]; then
        echo -e "${YELLOW}ℹ $2${NC}"
    fi
}

# Get ALB ARN
echo "1. Getting ALB information..."
ALB_ARN=$(aws elbv2 describe-load-balancers \
    --names "$ALB_NAME" \
    --region "$REGION" \
    --query 'LoadBalancers[0].LoadBalancerArn' \
    --output text)

if [ "$ALB_ARN" = "None" ] || [ -z "$ALB_ARN" ]; then
    print_status "error" "ALB not found: $ALB_NAME"
    exit 1
fi

print_status "success" "Found ALB: $ALB_ARN"

# Get VPC ID and Security Group
VPC_ID=$(aws elbv2 describe-load-balancers \
    --load-balancer-arns "$ALB_ARN" \
    --region "$REGION" \
    --query 'LoadBalancers[0].VpcId' \
    --output text)

ALB_SG_ID=$(aws elbv2 describe-load-balancers \
    --load-balancer-arns "$ALB_ARN" \
    --region "$REGION" \
    --query 'LoadBalancers[0].SecurityGroups[0]' \
    --output text)

print_status "info" "VPC ID: $VPC_ID"
print_status "info" "Security Group: $ALB_SG_ID"

# 2. Update Security Group to allow HTTPS
echo ""
echo "2. Updating Security Group to allow HTTPS..."

# Check if HTTPS rule already exists
HTTPS_EXISTS=$(aws ec2 describe-security-groups \
    --group-ids "$ALB_SG_ID" \
    --region "$REGION" \
    --query 'SecurityGroups[0].IpPermissions[?FromPort==`443`]' \
    --output text)

if [ -z "$HTTPS_EXISTS" ]; then
    aws ec2 authorize-security-group-ingress \
        --group-id "$ALB_SG_ID" \
        --protocol tcp \
        --port 443 \
        --cidr 0.0.0.0/0 \
        --region "$REGION" 2>/dev/null && \
        print_status "success" "Added HTTPS (443) rule to security group" || \
        print_status "error" "Failed to add HTTPS rule (may already exist)"
else
    print_status "info" "HTTPS rule already exists in security group"
fi

# 3. Request or Import SSL Certificate
echo ""
echo "3. Managing SSL Certificate..."
echo "Choose an option:"
echo "  1) Request a new certificate from ACM (requires domain validation)"
echo "  2) Use an existing ACM certificate"
echo "  3) Skip certificate (will provide instructions)"
read -p "Enter your choice (1-3): " cert_choice

CERTIFICATE_ARN=""

case $cert_choice in
    1)
        # Request new certificate
        print_status "info" "Requesting new certificate for domain: $DOMAIN_NAME"
        
        # Check if certificate already exists
        EXISTING_CERT=$(aws acm list-certificates \
            --region "$REGION" \
            --query "CertificateSummaryList[?DomainName=='$DOMAIN_NAME'].CertificateArn" \
            --output text)
        
        if [ -n "$EXISTING_CERT" ] && [ "$EXISTING_CERT" != "None" ]; then
            print_status "info" "Certificate already exists: $EXISTING_CERT"
            CERTIFICATE_ARN=$EXISTING_CERT
        else
            # Request new certificate
            CERTIFICATE_ARN=$(aws acm request-certificate \
                --domain-name "$DOMAIN_NAME" \
                --validation-method DNS \
                --subject-alternative-names "*.$DOMAIN_NAME" \
                --region "$REGION" \
                --query 'CertificateArn' \
                --output text)
            
            if [ -n "$CERTIFICATE_ARN" ]; then
                print_status "success" "Certificate requested: $CERTIFICATE_ARN"
                print_status "info" "You must validate the certificate before using it!"
                print_status "info" "Check your email or use DNS validation in ACM console"
            else
                print_status "error" "Failed to request certificate"
            fi
        fi
        ;;
    2)
        # List existing certificates
        echo ""
        echo "Existing certificates:"
        aws acm list-certificates \
            --region "$REGION" \
            --query 'CertificateSummaryList[*].[CertificateArn,DomainName,Status]' \
            --output table
        
        echo ""
        read -p "Enter the Certificate ARN to use: " CERTIFICATE_ARN
        ;;
    3)
        print_status "info" "Skipping certificate configuration"
        print_status "info" "To add HTTPS later:"
        print_status "info" "1. Request or import a certificate in ACM"
        print_status "info" "2. Run this script again and choose option 2"
        ;;
esac

# 4. Configure HTTPS Listener (if certificate is available)
if [ -n "$CERTIFICATE_ARN" ] && [ "$CERTIFICATE_ARN" != "None" ]; then
    echo ""
    echo "4. Configuring HTTPS Listener..."
    
    # Get the target group ARN
    TARGET_GROUP_ARN=$(aws elbv2 describe-target-groups \
        --load-balancer-arn "$ALB_ARN" \
        --region "$REGION" \
        --query 'TargetGroups[0].TargetGroupArn' \
        --output text)
    
    if [ -z "$TARGET_GROUP_ARN" ] || [ "$TARGET_GROUP_ARN" = "None" ]; then
        print_status "error" "No target group found for ALB"
        exit 1
    fi
    
    # Check if HTTPS listener already exists
    HTTPS_LISTENER=$(aws elbv2 describe-listeners \
        --load-balancer-arn "$ALB_ARN" \
        --region "$REGION" \
        --query 'Listeners[?Port==`443`].ListenerArn' \
        --output text)
    
    if [ -z "$HTTPS_LISTENER" ] || [ "$HTTPS_LISTENER" = "None" ]; then
        # Create HTTPS listener
        LISTENER_ARN=$(aws elbv2 create-listener \
            --load-balancer-arn "$ALB_ARN" \
            --protocol HTTPS \
            --port 443 \
            --certificates CertificateArn="$CERTIFICATE_ARN" \
            --default-actions Type=forward,TargetGroupArn="$TARGET_GROUP_ARN" \
            --region "$REGION" \
            --query 'Listeners[0].ListenerArn' \
            --output text)
        
        if [ -n "$LISTENER_ARN" ]; then
            print_status "success" "Created HTTPS listener: $LISTENER_ARN"
        else
            print_status "error" "Failed to create HTTPS listener"
        fi
    else
        print_status "info" "HTTPS listener already exists"
        
        # Update certificate if needed
        read -p "Do you want to update the certificate on the existing listener? (y/n): " update_cert
        if [ "$update_cert" = "y" ]; then
            aws elbv2 modify-listener \
                --listener-arn "$HTTPS_LISTENER" \
                --certificates CertificateArn="$CERTIFICATE_ARN" \
                --region "$REGION" && \
                print_status "success" "Updated certificate on HTTPS listener" || \
                print_status "error" "Failed to update certificate"
        fi
    fi
    
    # 5. Configure HTTP to HTTPS redirect
    echo ""
    echo "5. Configuring HTTP to HTTPS redirect..."
    
    # Get HTTP listener
    HTTP_LISTENER=$(aws elbv2 describe-listeners \
        --load-balancer-arn "$ALB_ARN" \
        --region "$REGION" \
        --query 'Listeners[?Port==`80`].ListenerArn' \
        --output text)
    
    if [ -n "$HTTP_LISTENER" ] && [ "$HTTP_LISTENER" != "None" ]; then
        read -p "Do you want to redirect HTTP traffic to HTTPS? (y/n): " setup_redirect
        if [ "$setup_redirect" = "y" ]; then
            aws elbv2 modify-listener \
                --listener-arn "$HTTP_LISTENER" \
                --default-actions Type=redirect,RedirectConfig='{Protocol=HTTPS,Port=443,Host="#{host}",Path="/#{path}",Query="#{query}",StatusCode=HTTP_301}' \
                --region "$REGION" && \
                print_status "success" "Configured HTTP to HTTPS redirect" || \
                print_status "error" "Failed to configure redirect"
        fi
    fi
fi

# 6. Display Summary
echo ""
echo "=== Configuration Summary ==="
echo ""

# Get ALB DNS
ALB_DNS=$(aws elbv2 describe-load-balancers \
    --load-balancer-arns "$ALB_ARN" \
    --region "$REGION" \
    --query 'LoadBalancers[0].DNSName' \
    --output text)

print_status "info" "ALB DNS: $ALB_DNS"

if [ -n "$CERTIFICATE_ARN" ] && [ "$CERTIFICATE_ARN" != "None" ]; then
    print_status "info" "Certificate ARN: $CERTIFICATE_ARN"
    
    # Check certificate status
    CERT_STATUS=$(aws acm describe-certificate \
        --certificate-arn "$CERTIFICATE_ARN" \
        --region "$REGION" \
        --query 'Certificate.Status' \
        --output text 2>/dev/null)
    
    if [ "$CERT_STATUS" = "ISSUED" ]; then
        print_status "success" "Certificate Status: ISSUED (Ready to use)"
    else
        print_status "error" "Certificate Status: $CERT_STATUS (Not ready)"
        print_status "info" "Please validate the certificate in ACM console"
    fi
fi

echo ""
echo "=== Next Steps ==="
echo ""
echo "1. If using a custom domain:"
echo "   - Create a CNAME record pointing $DOMAIN_NAME to $ALB_DNS"
echo "   - Or create an A record using Route 53 alias to the ALB"
echo ""
echo "2. If certificate is pending validation:"
echo "   - Check ACM console for validation instructions"
echo "   - Complete DNS or email validation"
echo ""
echo "3. Test HTTPS access:"
echo "   - https://$ALB_DNS (will show certificate warning)"
echo "   - https://$DOMAIN_NAME (after DNS configuration)"
echo ""
echo "=== HTTPS Setup Complete ==="
echo "Finished at: $(date)"