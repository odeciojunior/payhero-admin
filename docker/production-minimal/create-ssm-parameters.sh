#!/bin/bash

# Create missing SSM parameters for Velana production-minimal environment

set -euo pipefail

# Configuration
AWS_REGION="us-east-1"
SSM_PREFIX="/velana/production-minimal"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}Creating missing SSM parameters for Velana production-minimal...${NC}"

# APP_NAME
aws ssm put-parameter \
    --name "${SSM_PREFIX}/APP_NAME" \
    --value "Velana" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter APP_NAME already exists or failed to create"

# APP_URL
aws ssm put-parameter \
    --name "${SSM_PREFIX}/APP_URL" \
    --value "https://velana.payhero.com" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter APP_URL already exists or failed to create"

# Mail configuration parameters
aws ssm put-parameter \
    --name "${SSM_PREFIX}/MAIL_MAILER" \
    --value "smtp" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter MAIL_MAILER already exists or failed to create"

aws ssm put-parameter \
    --name "${SSM_PREFIX}/MAIL_HOST" \
    --value "smtp.sendgrid.net" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter MAIL_HOST already exists or failed to create"

aws ssm put-parameter \
    --name "${SSM_PREFIX}/MAIL_PORT" \
    --value "587" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter MAIL_PORT already exists or failed to create"

aws ssm put-parameter \
    --name "${SSM_PREFIX}/MAIL_USERNAME" \
    --value "apikey" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter MAIL_USERNAME already exists or failed to create"

# MAIL_PASSWORD - this should be replaced with actual SendGrid API key
aws ssm put-parameter \
    --name "${SSM_PREFIX}/MAIL_PASSWORD" \
    --value "REPLACE_WITH_SENDGRID_API_KEY" \
    --type "SecureString" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter MAIL_PASSWORD already exists or failed to create"

aws ssm put-parameter \
    --name "${SSM_PREFIX}/MAIL_ENCRYPTION" \
    --value "tls" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter MAIL_ENCRYPTION already exists or failed to create"

aws ssm put-parameter \
    --name "${SSM_PREFIX}/MAIL_FROM_ADDRESS" \
    --value "noreply@velana.payhero.com" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter MAIL_FROM_ADDRESS already exists or failed to create"

# REDIS_PASSWORD - empty for AWS ElastiCache
aws ssm put-parameter \
    --name "${SSM_PREFIX}/REDIS_PASSWORD" \
    --value "" \
    --type "String" \
    --region "$AWS_REGION" \
    --tags Key=Project,Value=velana Key=Environment,Value=production-minimal \
    2>/dev/null || echo "Parameter REDIS_PASSWORD already exists or failed to create"

echo -e "${GREEN}SSM parameters created successfully!${NC}"
echo -e "${YELLOW}NOTE: Please update the MAIL_PASSWORD parameter with your actual SendGrid API key${NC}"
echo -e "${YELLOW}You can do this by running:${NC}"
echo "aws ssm put-parameter --name \"${SSM_PREFIX}/MAIL_PASSWORD\" --value \"YOUR_ACTUAL_API_KEY\" --type SecureString --region $AWS_REGION --overwrite"