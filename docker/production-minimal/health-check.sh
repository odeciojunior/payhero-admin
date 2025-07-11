#!/bin/bash

# PayHero Production-Minimal Health Check Script
# Project: Velana
# Environment: production-minimal

set -euo pipefail

# Configuration
AWS_REGION=${AWS_REGION:-us-east-1}
CLUSTER_NAME="payhero-production-minimal-cluster"
SERVICE_NAME="payhero-production-minimal-service"
ALB_NAME="payhero-production-minimal-alb"
PROJECT_NAME="velana"
ENVIRONMENT="production-minimal"

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

log_success() {
    echo -e "${GREEN}[✓]${NC} $1"
}

log_fail() {
    echo -e "${RED}[✗]${NC} $1"
}

# Check ECS Service Health
check_ecs_service() {
    log_info "Checking ECS Service Health..."
    
    # Get service details
    SERVICE_INFO=$(aws ecs describe-services \
        --cluster "$CLUSTER_NAME" \
        --services "$SERVICE_NAME" \
        --region "$AWS_REGION" \
        --query 'services[0]' \
        --output json 2>/dev/null || echo "{}")
    
    if [ "$SERVICE_INFO" = "{}" ]; then
        log_fail "ECS Service not found"
        return 1
    fi
    
    # Check service status
    SERVICE_STATUS=$(echo "$SERVICE_INFO" | jq -r '.status')
    DESIRED_COUNT=$(echo "$SERVICE_INFO" | jq -r '.desiredCount')
    RUNNING_COUNT=$(echo "$SERVICE_INFO" | jq -r '.runningCount')
    PENDING_COUNT=$(echo "$SERVICE_INFO" | jq -r '.pendingCount')
    
    if [ "$SERVICE_STATUS" = "ACTIVE" ]; then
        log_success "Service Status: $SERVICE_STATUS"
    else
        log_fail "Service Status: $SERVICE_STATUS"
    fi
    
    if [ "$RUNNING_COUNT" -eq "$DESIRED_COUNT" ]; then
        log_success "Task Count: $RUNNING_COUNT/$DESIRED_COUNT running"
    else
        log_warn "Task Count: $RUNNING_COUNT/$DESIRED_COUNT running, $PENDING_COUNT pending"
    fi
    
    # Check deployment status
    DEPLOYMENTS=$(echo "$SERVICE_INFO" | jq -r '.deployments | length')
    if [ "$DEPLOYMENTS" -eq 1 ]; then
        log_success "Deployments: Stable (1 active deployment)"
    else
        log_warn "Deployments: $DEPLOYMENTS active deployments"
    fi
    
    # Check for recent events
    RECENT_EVENTS=$(echo "$SERVICE_INFO" | jq -r '.events[:3] | .[] | .message')
    if [ -n "$RECENT_EVENTS" ]; then
        log_info "Recent Events:"
        echo "$RECENT_EVENTS" | while read -r event; do
            echo "  - $event"
        done
    fi
}

# Check Task Health
check_tasks() {
    log_info "Checking ECS Tasks..."
    
    # Get running tasks
    TASK_ARNS=$(aws ecs list-tasks \
        --cluster "$CLUSTER_NAME" \
        --service-name "$SERVICE_NAME" \
        --desired-status RUNNING \
        --region "$AWS_REGION" \
        --query 'taskArns' \
        --output json)
    
    if [ "$TASK_ARNS" = "[]" ]; then
        log_fail "No running tasks found"
        return 1
    fi
    
    # Get task details
    TASKS=$(aws ecs describe-tasks \
        --cluster "$CLUSTER_NAME" \
        --tasks $(echo "$TASK_ARNS" | jq -r '.[]') \
        --region "$AWS_REGION" \
        --query 'tasks' \
        --output json)
    
    # Check each task
    echo "$TASKS" | jq -r '.[] | "\(.taskArn | split("/") | .[-1]) \(.lastStatus) \(.healthStatus // "N/A")"' | while read -r task_id status health; do
        if [ "$status" = "RUNNING" ] && [ "$health" = "HEALTHY" ]; then
            log_success "Task $task_id: $status ($health)"
        elif [ "$status" = "RUNNING" ]; then
            log_warn "Task $task_id: $status (Health: $health)"
        else
            log_fail "Task $task_id: $status"
        fi
    done
    
    # Check container statuses
    log_info "Container Statuses:"
    echo "$TASKS" | jq -r '.[] | .containers[] | "  - \(.name): \(.lastStatus) (Exit Code: \(.exitCode // "N/A"))"'
}

# Check ALB Health
check_alb() {
    log_info "Checking Application Load Balancer..."
    
    # Get ALB details
    ALB_INFO=$(aws elbv2 describe-load-balancers \
        --names "$ALB_NAME" \
        --region "$AWS_REGION" \
        --query 'LoadBalancers[0]' \
        --output json 2>/dev/null || echo "{}")
    
    if [ "$ALB_INFO" = "{}" ]; then
        log_fail "ALB not found"
        return 1
    fi
    
    ALB_STATE=$(echo "$ALB_INFO" | jq -r '.State.Code')
    ALB_DNS=$(echo "$ALB_INFO" | jq -r '.DNSName')
    
    if [ "$ALB_STATE" = "active" ]; then
        log_success "ALB State: $ALB_STATE"
        log_info "ALB DNS: $ALB_DNS"
    else
        log_fail "ALB State: $ALB_STATE"
    fi
    
    # Check target health
    TARGET_GROUP_ARNS=$(aws elbv2 describe-target-groups \
        --load-balancer-arn $(echo "$ALB_INFO" | jq -r '.LoadBalancerArn') \
        --region "$AWS_REGION" \
        --query 'TargetGroups[].TargetGroupArn' \
        --output json)
    
    echo "$TARGET_GROUP_ARNS" | jq -r '.[]' | while read -r tg_arn; do
        TARGET_HEALTH=$(aws elbv2 describe-target-health \
            --target-group-arn "$tg_arn" \
            --region "$AWS_REGION" \
            --query 'TargetHealthDescriptions' \
            --output json)
        
        HEALTHY_COUNT=$(echo "$TARGET_HEALTH" | jq '[.[] | select(.TargetHealth.State == "healthy")] | length')
        TOTAL_COUNT=$(echo "$TARGET_HEALTH" | jq '. | length')
        
        if [ "$HEALTHY_COUNT" -eq "$TOTAL_COUNT" ] && [ "$TOTAL_COUNT" -gt 0 ]; then
            log_success "Target Group: $HEALTHY_COUNT/$TOTAL_COUNT healthy"
        else
            log_warn "Target Group: $HEALTHY_COUNT/$TOTAL_COUNT healthy"
        fi
    done
}

# Check Application Endpoints
check_endpoints() {
    log_info "Checking Application Endpoints..."
    
    # Get ALB DNS
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --names "$ALB_NAME" \
        --region "$AWS_REGION" \
        --query 'LoadBalancers[0].DNSName' \
        --output text 2>/dev/null)
    
    if [ -z "$ALB_DNS" ] || [ "$ALB_DNS" = "None" ]; then
        log_warn "Could not get ALB DNS, skipping endpoint checks"
        return
    fi
    
    # Test endpoints
    local endpoints=(
        "/health:Application Health"
        "/health.php:PHP Health"
        "/nginx-health:Nginx Health"
    )
    
    for endpoint_info in "${endpoints[@]}"; do
        IFS=':' read -r endpoint description <<< "$endpoint_info"
        
        HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://$ALB_DNS$endpoint" || echo "000")
        
        if [ "$HTTP_CODE" = "200" ]; then
            log_success "$description: OK (HTTP $HTTP_CODE)"
        elif [ "$HTTP_CODE" = "000" ]; then
            log_fail "$description: Connection failed"
        else
            log_fail "$description: Failed (HTTP $HTTP_CODE)"
        fi
    done
    
    # Test application home page
    HTTP_CODE=$(curl -s -o /dev/null -w "%{http_code}" "http://$ALB_DNS/" || echo "000")
    if [ "$HTTP_CODE" = "200" ] || [ "$HTTP_CODE" = "302" ]; then
        log_success "Application Home: OK (HTTP $HTTP_CODE)"
    else
        log_warn "Application Home: HTTP $HTTP_CODE"
    fi
}

# Check CloudWatch Metrics
check_metrics() {
    log_info "Checking CloudWatch Metrics (last hour)..."
    
    # Calculate time range
    END_TIME=$(date -u +%Y-%m-%dT%H:%M:%S)
    START_TIME=$(date -u -d '1 hour ago' +%Y-%m-%dT%H:%M:%S)
    
    # Check ECS CPU utilization
    CPU_STATS=$(aws cloudwatch get-metric-statistics \
        --namespace AWS/ECS \
        --metric-name CPUUtilization \
        --dimensions Name=ServiceName,Value="$SERVICE_NAME" Name=ClusterName,Value="$CLUSTER_NAME" \
        --start-time "$START_TIME" \
        --end-time "$END_TIME" \
        --period 300 \
        --statistics Average,Maximum \
        --region "$AWS_REGION" \
        --output json)
    
    if [ -n "$CPU_STATS" ]; then
        AVG_CPU=$(echo "$CPU_STATS" | jq -r '.Datapoints | if length > 0 then [.[] | .Average] | add/length | round else 0 end')
        MAX_CPU=$(echo "$CPU_STATS" | jq -r '.Datapoints | if length > 0 then [.[] | .Maximum] | max | round else 0 end')
        
        if [ "$AVG_CPU" -lt 70 ]; then
            log_success "CPU Utilization: Avg: ${AVG_CPU}%, Max: ${MAX_CPU}%"
        else
            log_warn "CPU Utilization: Avg: ${AVG_CPU}%, Max: ${MAX_CPU}%"
        fi
    fi
    
    # Check ECS memory utilization
    MEM_STATS=$(aws cloudwatch get-metric-statistics \
        --namespace AWS/ECS \
        --metric-name MemoryUtilization \
        --dimensions Name=ServiceName,Value="$SERVICE_NAME" Name=ClusterName,Value="$CLUSTER_NAME" \
        --start-time "$START_TIME" \
        --end-time "$END_TIME" \
        --period 300 \
        --statistics Average,Maximum \
        --region "$AWS_REGION" \
        --output json)
    
    if [ -n "$MEM_STATS" ]; then
        AVG_MEM=$(echo "$MEM_STATS" | jq -r '.Datapoints | if length > 0 then [.[] | .Average] | add/length | round else 0 end')
        MAX_MEM=$(echo "$MEM_STATS" | jq -r '.Datapoints | if length > 0 then [.[] | .Maximum] | max | round else 0 end')
        
        if [ "$AVG_MEM" -lt 80 ]; then
            log_success "Memory Utilization: Avg: ${AVG_MEM}%, Max: ${MAX_MEM}%"
        else
            log_warn "Memory Utilization: Avg: ${AVG_MEM}%, Max: ${MAX_MEM}%"
        fi
    fi
}

# Generate summary report
generate_summary() {
    log_info "Health Check Summary"
    echo "===================="
    echo "Project: $PROJECT_NAME"
    echo "Environment: $ENVIRONMENT"
    echo "Region: $AWS_REGION"
    echo "Timestamp: $(date)"
    echo "===================="
}

# Main health check flow
main() {
    log_info "PayHero Production-Minimal Health Check"
    log_info "======================================="
    
    # Run all health checks
    local overall_status=0
    
    check_ecs_service || overall_status=1
    echo
    
    check_tasks || overall_status=1
    echo
    
    check_alb || overall_status=1
    echo
    
    check_endpoints || overall_status=1
    echo
    
    check_metrics || overall_status=1
    echo
    
    generate_summary
    
    if [ $overall_status -eq 0 ]; then
        log_success "Overall Status: HEALTHY"
        exit 0
    else
        log_fail "Overall Status: ISSUES DETECTED"
        exit 1
    fi
}

# Check for verbose flag
if [ "${1:-}" = "--verbose" ] || [ "${1:-}" = "-v" ]; then
    set -x
fi

# Run main function
main