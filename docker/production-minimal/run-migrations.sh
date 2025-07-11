#!/bin/bash

# PayHero Production-Minimal Database Migration Script
# Project: Velana
# Environment: production-minimal

set -euo pipefail

# Configuration
AWS_REGION=${AWS_REGION:-us-east-1}
CLUSTER_NAME="payhero-production-minimal-cluster"
TASK_DEFINITION="payhero-production-minimal-migrations"
SUBNET_ID=""  # Will be fetched dynamically
SECURITY_GROUP_ID=""  # Will be fetched dynamically
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

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Get VPC configuration
get_vpc_config() {
    log_info "Getting VPC configuration..."
    
    # Get the VPC ID from the cluster
    VPC_ID=$(aws ecs describe-clusters \
        --clusters "$CLUSTER_NAME" \
        --region "$AWS_REGION" \
        --query 'clusters[0].configuration.executeCommandConfiguration.subnets[0]' \
        --output text 2>/dev/null || echo "")
    
    if [ -z "$VPC_ID" ]; then
        # Try to get VPC from service
        SERVICE_INFO=$(aws ecs describe-services \
            --cluster "$CLUSTER_NAME" \
            --services "payhero-$ENVIRONMENT-service" \
            --region "$AWS_REGION" \
            --query 'services[0].networkConfiguration.awsvpcConfiguration' \
            --output json 2>/dev/null || echo "{}")
        
        SUBNET_ID=$(echo "$SERVICE_INFO" | jq -r '.subnets[0] // empty')
        SECURITY_GROUP_ID=$(echo "$SERVICE_INFO" | jq -r '.securityGroups[0] // empty')
    fi
    
    if [ -z "$SUBNET_ID" ] || [ -z "$SECURITY_GROUP_ID" ]; then
        log_error "Could not determine VPC configuration"
        exit 1
    fi
    
    log_info "Using subnet: $SUBNET_ID"
    log_info "Using security group: $SECURITY_GROUP_ID"
}

# Create migration task definition
create_migration_task_def() {
    log_info "Creating migration task definition..."
    
    # Get the current app task definition as base
    BASE_TASK_DEF=$(aws ecs describe-task-definition \
        --task-definition "payhero-$ENVIRONMENT-app" \
        --region "$AWS_REGION" \
        --query 'taskDefinition' \
        --output json)
    
    # Modify for migrations
    MIGRATION_TASK_DEF=$(echo "$BASE_TASK_DEF" | jq '
        .family = "payhero-'$ENVIRONMENT'-migrations" |
        .containerDefinitions[0].command = ["php", "artisan", "migrate", "--force"] |
        .containerDefinitions = [.containerDefinitions[0]] |
        del(.taskDefinitionArn) |
        del(.revision) |
        del(.status) |
        del(.requiresAttributes) |
        del(.compatibilities) |
        del(.registeredAt) |
        del(.registeredBy)
    ')
    
    # Register the task definition
    TASK_DEF_ARN=$(aws ecs register-task-definition \
        --cli-input-json "$MIGRATION_TASK_DEF" \
        --region "$AWS_REGION" \
        --query 'taskDefinition.taskDefinitionArn' \
        --output text)
    
    log_info "Migration task definition created: $TASK_DEF_ARN"
}

# Run migration task
run_migration() {
    log_info "Running database migrations..."
    
    # Start the migration task
    TASK_OUTPUT=$(aws ecs run-task \
        --cluster "$CLUSTER_NAME" \
        --task-definition "$TASK_DEFINITION" \
        --launch-type FARGATE \
        --network-configuration "awsvpcConfiguration={subnets=[$SUBNET_ID],securityGroups=[$SECURITY_GROUP_ID],assignPublicIp=DISABLED}" \
        --region "$AWS_REGION" \
        --output json)
    
    TASK_ARN=$(echo "$TASK_OUTPUT" | jq -r '.tasks[0].taskArn')
    
    if [ -z "$TASK_ARN" ] || [ "$TASK_ARN" = "null" ]; then
        log_error "Failed to start migration task"
        exit 1
    fi
    
    log_info "Migration task started: $TASK_ARN"
    
    # Wait for task completion
    log_info "Waiting for migrations to complete..."
    
    while true; do
        TASK_STATUS=$(aws ecs describe-tasks \
            --cluster "$CLUSTER_NAME" \
            --tasks "$TASK_ARN" \
            --region "$AWS_REGION" \
            --query 'tasks[0].lastStatus' \
            --output text)
        
        case "$TASK_STATUS" in
            RUNNING)
                echo -n "."
                sleep 5
                ;;
            STOPPED)
                echo
                break
                ;;
            *)
                echo
                log_warn "Unknown task status: $TASK_STATUS"
                sleep 5
                ;;
        esac
    done
    
    # Check exit code
    EXIT_CODE=$(aws ecs describe-tasks \
        --cluster "$CLUSTER_NAME" \
        --tasks "$TASK_ARN" \
        --region "$AWS_REGION" \
        --query 'tasks[0].containers[0].exitCode' \
        --output text)
    
    if [ "$EXIT_CODE" = "0" ]; then
        log_info "Migrations completed successfully!"
    else
        log_error "Migrations failed with exit code: $EXIT_CODE"
        
        # Try to get logs
        log_error "Checking logs..."
        LOG_GROUP="/ecs/payhero-$ENVIRONMENT"
        LOG_STREAM_PREFIX="app"
        
        aws logs tail "$LOG_GROUP" \
            --log-stream-name-prefix "$LOG_STREAM_PREFIX" \
            --region "$AWS_REGION" \
            --since 5m | tail -50
        
        exit 1
    fi
}

# Run seeders (optional)
run_seeders() {
    log_info "Running database seeders..."
    
    # Create seeder task definition
    SEEDER_TASK_DEF=$(aws ecs describe-task-definition \
        --task-definition "payhero-$ENVIRONMENT-app" \
        --region "$AWS_REGION" \
        --query 'taskDefinition' \
        --output json | jq '
        .family = "payhero-'$ENVIRONMENT'-seeders" |
        .containerDefinitions[0].command = ["php", "artisan", "db:seed", "--force"] |
        .containerDefinitions = [.containerDefinitions[0]] |
        del(.taskDefinitionArn) |
        del(.revision) |
        del(.status) |
        del(.requiresAttributes) |
        del(.compatibilities) |
        del(.registeredAt) |
        del(.registeredBy)
    ')
    
    # Register and run seeder task
    SEEDER_TASK_DEF_ARN=$(aws ecs register-task-definition \
        --cli-input-json "$SEEDER_TASK_DEF" \
        --region "$AWS_REGION" \
        --query 'taskDefinition.taskDefinitionArn' \
        --output text)
    
    SEEDER_TASK=$(aws ecs run-task \
        --cluster "$CLUSTER_NAME" \
        --task-definition "$SEEDER_TASK_DEF_ARN" \
        --launch-type FARGATE \
        --network-configuration "awsvpcConfiguration={subnets=[$SUBNET_ID],securityGroups=[$SECURITY_GROUP_ID],assignPublicIp=DISABLED}" \
        --region "$AWS_REGION" \
        --query 'tasks[0].taskArn' \
        --output text)
    
    log_info "Seeder task started: $SEEDER_TASK"
    
    # Wait for completion
    aws ecs wait tasks-stopped \
        --cluster "$CLUSTER_NAME" \
        --tasks "$SEEDER_TASK" \
        --region "$AWS_REGION"
    
    # Check exit code
    SEEDER_EXIT_CODE=$(aws ecs describe-tasks \
        --cluster "$CLUSTER_NAME" \
        --tasks "$SEEDER_TASK" \
        --region "$AWS_REGION" \
        --query 'tasks[0].containers[0].exitCode' \
        --output text)
    
    if [ "$SEEDER_EXIT_CODE" = "0" ]; then
        log_info "Seeders completed successfully!"
    else
        log_error "Seeders failed with exit code: $SEEDER_EXIT_CODE"
    fi
}

# Main execution
main() {
    log_info "PayHero Production-Minimal Migration Runner"
    log_info "=========================================="
    log_info "Project: $PROJECT_NAME"
    log_info "Environment: $ENVIRONMENT"
    
    # Parse arguments
    RUN_SEEDERS=false
    while [[ $# -gt 0 ]]; do
        case $1 in
            --seed)
                RUN_SEEDERS=true
                shift
                ;;
            --help)
                echo "Usage: $0 [--seed]"
                echo "  --seed    Run database seeders after migrations"
                exit 0
                ;;
            *)
                log_error "Unknown option: $1"
                exit 1
                ;;
        esac
    done
    
    # Execute migrations
    get_vpc_config
    create_migration_task_def
    run_migration
    
    # Optionally run seeders
    if [ "$RUN_SEEDERS" = true ]; then
        run_seeders
    fi
    
    log_info "Migration process completed!"
}

# Run main function
main "$@"