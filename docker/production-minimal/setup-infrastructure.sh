#!/bin/bash

# PayHero Production-Minimal Infrastructure Setup for Velana
# This script helps verify and create necessary AWS resources

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

# Check if resource exists
check_resource() {
    local resource_type=$1
    local resource_name=$2
    local check_command=$3
    
    if eval "$check_command" &> /dev/null; then
        log_info "✓ $resource_type '$resource_name' exists"
        return 0
    else
        log_warn "✗ $resource_type '$resource_name' not found"
        return 1
    fi
}

# Check ECR repositories
check_ecr_repos() {
    log_step "Checking ECR Repositories"
    
    local repos=("velana-production-minimal-app" "velana-production-minimal-nginx")
    local missing_repos=()
    
    for repo in "${repos[@]}"; do
        if ! check_resource "ECR Repository" "$repo" "aws ecr describe-repositories --repository-names $repo --region $AWS_REGION"; then
            missing_repos+=("$repo")
        fi
    done
    
    if [ ${#missing_repos[@]} -gt 0 ]; then
        log_warn "Missing ECR repositories: ${missing_repos[*]}"
        read -p "Do you want to create missing ECR repositories? (y/N) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            for repo in "${missing_repos[@]}"; do
                log_info "Creating ECR repository: $repo"
                aws ecr create-repository \
                    --repository-name "$repo" \
                    --region "$AWS_REGION" \
                    --tags Key=Project,Value=$PROJECT_NAME Key=Environment,Value=$ENVIRONMENT \
                    --image-scanning-configuration scanOnPush=true
            done
        fi
    fi
}

# Check ECS cluster
check_ecs_cluster() {
    log_step "Checking ECS Cluster"
    
    local cluster_name="velana-production-minimal-cluster"
    
    if ! check_resource "ECS Cluster" "$cluster_name" "aws ecs describe-clusters --clusters $cluster_name --region $AWS_REGION --query 'clusters[0].status' | grep -q ACTIVE"; then
        read -p "Do you want to create the ECS cluster? (y/N) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            log_info "Creating ECS cluster: $cluster_name"
            aws ecs create-cluster \
                --cluster-name "$cluster_name" \
                --region "$AWS_REGION" \
                --capacity-providers FARGATE FARGATE_SPOT \
                --default-capacity-provider-strategy capacityProvider=FARGATE_SPOT,weight=80,base=0 capacityProvider=FARGATE,weight=20,base=1 \
                --tags key=Project,value=$PROJECT_NAME key=Environment,value=$ENVIRONMENT
        fi
    fi
}

# Check IAM roles
check_iam_roles() {
    log_step "Checking IAM Roles"
    
    local roles=(
        "velana-production-minimal-execution-role"
        "velana-production-minimal-task-role"
    )
    
    for role in "${roles[@]}"; do
        check_resource "IAM Role" "$role" "aws iam get-role --role-name $role" || {
            log_warn "You need to create IAM role: $role"
            log_warn "This typically requires specific permissions and trust policies"
        }
    done
}

# Check CloudWatch log group
check_cloudwatch_logs() {
    log_step "Checking CloudWatch Log Groups"
    
    local log_group="/ecs/velana-production-minimal"
    
    if ! check_resource "CloudWatch Log Group" "$log_group" "aws logs describe-log-groups --log-group-name-prefix $log_group --region $AWS_REGION --query 'logGroups[0].logGroupName' | grep -q $log_group"; then
        read -p "Do you want to create the CloudWatch log group? (y/N) " -n 1 -r
        echo
        if [[ $REPLY =~ ^[Yy]$ ]]; then
            log_info "Creating log group: $log_group"
            aws logs create-log-group \
                --log-group-name "$log_group" \
                --region "$AWS_REGION" \
                --tags Project=$PROJECT_NAME Environment=$ENVIRONMENT
            
            # Set retention
            aws logs put-retention-policy \
                --log-group-name "$log_group" \
                --retention-in-days 3 \
                --region "$AWS_REGION"
        fi
    fi
}

# Check Systems Manager parameters
check_ssm_parameters() {
    log_step "Checking Systems Manager Parameters"
    
    local params=(
        "APP_NAME"
        "APP_KEY"
        "APP_URL"
        "DB_HOST"
        "DB_DATABASE"
        "DB_USERNAME"
        "DB_PASSWORD"
        "REDIS_HOST"
        "MAIL_MAILER"
        "MAIL_HOST"
        "MAIL_PORT"
        "MAIL_USERNAME"
        "MAIL_PASSWORD"
        "MAIL_ENCRYPTION"
        "MAIL_FROM_ADDRESS"
    )
    
    local missing_params=()
    
    for param in "${params[@]}"; do
        local param_path="/velana/production-minimal/$param"
        if ! check_resource "SSM Parameter" "$param_path" "aws ssm get-parameter --name $param_path --region $AWS_REGION"; then
            missing_params+=("$param")
        fi
    done
    
    if [ ${#missing_params[@]} -gt 0 ]; then
        log_warn "Missing SSM parameters: ${missing_params[*]}"
        log_info "You need to create these parameters in AWS Systems Manager Parameter Store"
        log_info "Example command:"
        echo "aws ssm put-parameter --name \"/velana/production-minimal/PARAM_NAME\" --value \"value\" --type SecureString --region $AWS_REGION"
    fi
}

# Check CodeBuild project
check_codebuild() {
    log_step "Checking CodeBuild Project"
    
    local project_name="velana-production-minimal-build"
    
    if ! check_resource "CodeBuild Project" "$project_name" "aws codebuild batch-get-projects --names $project_name --region $AWS_REGION --query 'projects[0].name'"; then
        log_warn "CodeBuild project not found: $project_name"
        log_info "You need to create this in the AWS Console or using Terraform/CloudFormation"
        log_info "Use the buildspec.yml file in this directory"
    fi
}

# Show summary
show_summary() {
    log_step "Infrastructure Setup Summary"
    
    echo "Project: $PROJECT_NAME"
    echo "Environment: $ENVIRONMENT"
    echo "Account: $AWS_ACCOUNT_ID"
    echo "Region: $AWS_REGION"
    echo
    echo "Please ensure all resources are properly configured before running deployment."
    echo
    echo "Next steps:"
    echo "1. Create any missing resources mentioned above"
    echo "2. Configure SSM parameters with your actual values"
    echo "3. Run ./deploy-velana.sh to deploy the application"
}

# Main function
main() {
    log_info "PayHero Production-Minimal Infrastructure Check"
    log_info "=============================================="
    
    # Check AWS credentials
    CURRENT_ACCOUNT=$(aws sts get-caller-identity --query Account --output text 2>/dev/null || echo "")
    if [ "$CURRENT_ACCOUNT" != "$AWS_ACCOUNT_ID" ]; then
        log_error "AWS account mismatch. Expected: $AWS_ACCOUNT_ID, Current: $CURRENT_ACCOUNT"
        exit 1
    fi
    
    # Run checks
    check_ecr_repos
    check_ecs_cluster
    check_iam_roles
    check_cloudwatch_logs
    check_ssm_parameters
    check_codebuild
    
    show_summary
}

# Run main function
main "$@"