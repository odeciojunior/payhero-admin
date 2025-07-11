#!/bin/bash

# PayHero Production-Minimal Cost Monitoring Script
# Project: Velana
# Environment: production-minimal

set -euo pipefail

# Configuration
AWS_REGION=${AWS_REGION:-us-east-1}
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

format_cost() {
    printf "%.2f" "$1"
}

# Get current month dates
get_date_range() {
    START_DATE=$(date -u +%Y-%m-01)
    END_DATE=$(date -u +%Y-%m-%d)
    
    log_info "Cost analysis period: $START_DATE to $END_DATE"
}

# Get cost by service
get_service_costs() {
    log_info "Fetching AWS service costs..."
    
    # Get cost and usage data
    COSTS=$(aws ce get-cost-and-usage \
        --time-period Start="$START_DATE",End="$END_DATE" \
        --granularity MONTHLY \
        --metrics "UnblendedCost" \
        --group-by Type=DIMENSION,Key=SERVICE \
        --filter '{
            "Tags": {
                "Key": "Project",
                "Values": ["'$PROJECT_NAME'"]
            }
        }' \
        --region "$AWS_REGION" \
        --output json 2>/dev/null || echo "{}")
    
    if [ "$COSTS" = "{}" ]; then
        log_warn "No cost data found for project tags. Showing all costs."
        
        # Get all costs without filter
        COSTS=$(aws ce get-cost-and-usage \
            --time-period Start="$START_DATE",End="$END_DATE" \
            --granularity MONTHLY \
            --metrics "UnblendedCost" \
            --group-by Type=DIMENSION,Key=SERVICE \
            --region "$AWS_REGION" \
            --output json)
    fi
    
    # Display costs by service
    echo
    echo "Service Costs (Month-to-Date):"
    echo "=============================="
    
    TOTAL_COST=0
    
    # Expected costs for production-minimal
    declare -A EXPECTED_COSTS=(
        ["Amazon Elastic Container Service"]="5.00"
        ["Amazon Relational Database Service"]="15.50"
        ["Amazon ElastiCache"]="11.00"
        ["Elastic Load Balancing"]="22.50"
        ["Amazon Virtual Private Cloud"]="45.00"
        ["Amazon CloudWatch"]="2.00"
        ["Amazon Elastic Compute Cloud - Other"]="8.50"
    )
    
    # Process actual costs
    echo "$COSTS" | jq -r '.ResultsByTime[0].Groups[] | select(.Metrics.UnblendedCost.Amount != "0") | "\(.Keys[0])|\(.Metrics.UnblendedCost.Amount)"' | while IFS='|' read -r service amount; do
        formatted_amount=$(format_cost "$amount")
        TOTAL_COST=$(echo "$TOTAL_COST + $amount" | bc)
        
        # Check if this is an expected service
        if [[ -v "EXPECTED_COSTS[$service]" ]]; then
            expected="${EXPECTED_COSTS[$service]}"
            variance=$(echo "scale=2; $amount - $expected" | bc)
            
            if (( $(echo "$variance > 5" | bc -l) )); then
                log_warn "$(printf "%-45s $%8s (Expected: $%s, +$%s)" "$service" "$formatted_amount" "$expected" "$variance")"
            elif (( $(echo "$variance < -5" | bc -l) )); then
                log_info "$(printf "%-45s $%8s (Expected: $%s, $%s)" "$service" "$formatted_amount" "$expected" "$variance")"
            else
                echo "$(printf "%-45s $%8s" "$service" "$formatted_amount")"
            fi
        else
            echo "$(printf "%-45s $%8s" "$service" "$formatted_amount")"
        fi
    done
    
    echo "=============================="
    echo "$(printf "%-45s $%8s" "TOTAL" "$(format_cost $TOTAL_COST)")"
    
    # Check against target
    TARGET_COST=109.50
    if (( $(echo "$TOTAL_COST > $TARGET_COST" | bc -l) )); then
        log_warn "Total cost exceeds target of \$$TARGET_COST"
    else
        log_info "Total cost is within target of \$$TARGET_COST"
    fi
}

# Get resource inventory
get_resource_inventory() {
    log_info "Checking resource inventory..."
    echo
    echo "Active Resources:"
    echo "================="
    
    # ECS Resources
    echo "ECS:"
    TASK_COUNT=$(aws ecs list-tasks \
        --cluster "payhero-$ENVIRONMENT-cluster" \
        --region "$AWS_REGION" \
        --query 'length(taskArns)' \
        --output text 2>/dev/null || echo "0")
    echo "  - Running tasks: $TASK_COUNT"
    
    # RDS Instances
    echo "RDS:"
    RDS_INSTANCES=$(aws rds describe-db-instances \
        --region "$AWS_REGION" \
        --query 'DBInstances[?contains(DBInstanceIdentifier, `production-minimal`)].[DBInstanceIdentifier, DBInstanceClass, AllocatedStorage]' \
        --output text)
    if [ -n "$RDS_INSTANCES" ]; then
        echo "$RDS_INSTANCES" | while read -r instance class storage; do
            echo "  - $instance: $class, ${storage}GB"
        done
    else
        echo "  - No RDS instances found"
    fi
    
    # ElastiCache Clusters
    echo "ElastiCache:"
    REDIS_CLUSTERS=$(aws elasticache describe-cache-clusters \
        --region "$AWS_REGION" \
        --query 'CacheClusters[?contains(CacheClusterId, `production-minimal`)].[CacheClusterId, CacheNodeType, NumCacheNodes]' \
        --output text)
    if [ -n "$REDIS_CLUSTERS" ]; then
        echo "$REDIS_CLUSTERS" | while read -r cluster type nodes; do
            echo "  - $cluster: $type, $nodes nodes"
        done
    else
        echo "  - No ElastiCache clusters found"
    fi
    
    # Load Balancers
    echo "Load Balancers:"
    ALB_COUNT=$(aws elbv2 describe-load-balancers \
        --names "payhero-$ENVIRONMENT-alb" \
        --region "$AWS_REGION" \
        --query 'length(LoadBalancers)' \
        --output text 2>/dev/null || echo "0")
    echo "  - Application Load Balancers: $ALB_COUNT"
    
    # NAT Gateways
    echo "NAT Gateways:"
    NAT_COUNT=$(aws ec2 describe-nat-gateways \
        --filter "Name=state,Values=available" "Name=tag:Environment,Values=$ENVIRONMENT" \
        --region "$AWS_REGION" \
        --query 'length(NatGateways)' \
        --output text 2>/dev/null || echo "0")
    echo "  - Active NAT Gateways: $NAT_COUNT"
}

# Cost optimization recommendations
show_recommendations() {
    log_info "Cost Optimization Recommendations..."
    echo
    echo "Current Configuration vs Recommendations:"
    echo "========================================"
    
    # Check Fargate Spot usage
    SPOT_USAGE=$(aws ecs describe-services \
        --cluster "payhero-$ENVIRONMENT-cluster" \
        --services "payhero-$ENVIRONMENT-service" \
        --region "$AWS_REGION" \
        --query 'services[0].capacityProviderStrategy[?capacityProvider==`FARGATE_SPOT`].weight' \
        --output text 2>/dev/null || echo "0")
    
    if [ "$SPOT_USAGE" -lt 80 ]; then
        log_warn "Consider increasing Fargate Spot usage to 80% (current: $SPOT_USAGE%)"
    else
        log_info "✓ Fargate Spot usage is optimized ($SPOT_USAGE%)"
    fi
    
    # Check RDS instance type
    RDS_CLASS=$(aws rds describe-db-instances \
        --db-instance-identifier "payhero-$ENVIRONMENT-db" \
        --region "$AWS_REGION" \
        --query 'DBInstances[0].DBInstanceClass' \
        --output text 2>/dev/null || echo "")
    
    if [[ "$RDS_CLASS" == *"t4g"* ]]; then
        log_info "✓ RDS using ARM-based instance (cost-optimized)"
    else
        log_warn "Consider switching RDS to t4g.micro for cost savings"
    fi
    
    # Check for unnecessary resources
    echo
    echo "Potential Cost Savings:"
    echo "======================"
    
    # Check for unused EBS volumes
    UNUSED_VOLUMES=$(aws ec2 describe-volumes \
        --filters "Name=status,Values=available" \
        --region "$AWS_REGION" \
        --query 'length(Volumes)' \
        --output text)
    
    if [ "$UNUSED_VOLUMES" -gt 0 ]; then
        log_warn "Found $UNUSED_VOLUMES unused EBS volumes - consider deletion"
    fi
    
    # Check for old snapshots
    OLD_SNAPSHOTS=$(aws ec2 describe-snapshots \
        --owner-ids self \
        --filters "Name=status,Values=completed" \
        --query "Snapshots[?StartTime<='$(date -u -d '30 days ago' +%Y-%m-%d)'] | length(@)" \
        --region "$AWS_REGION" \
        --output text)
    
    if [ "$OLD_SNAPSHOTS" -gt 0 ]; then
        log_warn "Found $OLD_SNAPSHOTS snapshots older than 30 days - consider cleanup"
    fi
}

# Generate cost report
generate_report() {
    log_info "Generating cost report..."
    
    REPORT_FILE="/tmp/payhero-cost-report-$(date +%Y%m%d).txt"
    
    {
        echo "PayHero Production-Minimal Cost Report"
        echo "====================================="
        echo "Generated: $(date)"
        echo "Project: $PROJECT_NAME"
        echo "Environment: $ENVIRONMENT"
        echo "Period: $START_DATE to $END_DATE"
        echo
        echo "Target Monthly Cost: \$109.50"
        echo
    } > "$REPORT_FILE"
    
    # Append cost data
    {
        echo "Service Breakdown:"
        echo "$COSTS" | jq -r '.ResultsByTime[0].Groups[] | select(.Metrics.UnblendedCost.Amount != "0") | "  \(.Keys[0]): $\(.Metrics.UnblendedCost.Amount)"'
    } >> "$REPORT_FILE"
    
    log_info "Report saved to: $REPORT_FILE"
}

# Main execution
main() {
    log_info "PayHero Production-Minimal Cost Monitor"
    log_info "======================================"
    
    get_date_range
    get_service_costs
    echo
    get_resource_inventory
    echo
    show_recommendations
    
    if [ "${1:-}" = "--report" ]; then
        generate_report
    fi
}

# Run main function
main "$@"