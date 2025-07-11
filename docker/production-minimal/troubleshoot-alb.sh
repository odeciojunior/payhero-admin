#!/bin/bash

# AWS Troubleshooting Script for ALB and ECS
echo "=== AWS Infrastructure Troubleshooting ==="
echo "Starting at: $(date)"
echo ""

# Set variables
ALB_NAME="velana-production-minimal-alb"
CLUSTER_NAME="velana-production-minimal-cluster"
SERVICE_NAME="velana-production-minimal-service"
REGION="us-east-1"

# 1. Check ALB and Target Health
echo "1. Checking ALB and Target Health..."
echo "=================================="

# Get ALB ARN
ALB_ARN=$(aws elbv2 describe-load-balancers \
    --names "$ALB_NAME" \
    --region "$REGION" \
    --query 'LoadBalancers[0].LoadBalancerArn' \
    --output text 2>/dev/null)

if [ "$ALB_ARN" != "None" ] && [ -n "$ALB_ARN" ]; then
    echo "✓ ALB Found: $ALB_ARN"
    
    # Get ALB DNS
    ALB_DNS=$(aws elbv2 describe-load-balancers \
        --load-balancer-arns "$ALB_ARN" \
        --region "$REGION" \
        --query 'LoadBalancers[0].DNSName' \
        --output text)
    echo "  DNS Name: $ALB_DNS"
    
    # Get ALB State
    ALB_STATE=$(aws elbv2 describe-load-balancers \
        --load-balancer-arns "$ALB_ARN" \
        --region "$REGION" \
        --query 'LoadBalancers[0].State.Code' \
        --output text)
    echo "  State: $ALB_STATE"
    
    # Get Target Groups
    echo ""
    echo "  Target Groups:"
    TARGET_GROUP_ARNS=$(aws elbv2 describe-target-groups \
        --load-balancer-arn "$ALB_ARN" \
        --region "$REGION" \
        --query 'TargetGroups[*].TargetGroupArn' \
        --output text)
    
    for TG_ARN in $TARGET_GROUP_ARNS; do
        TG_NAME=$(aws elbv2 describe-target-groups \
            --target-group-arns "$TG_ARN" \
            --region "$REGION" \
            --query 'TargetGroups[0].TargetGroupName' \
            --output text)
        echo "  - Target Group: $TG_NAME"
        
        # Check target health
        echo "    Target Health:"
        aws elbv2 describe-target-health \
            --target-group-arn "$TG_ARN" \
            --region "$REGION" \
            --query 'TargetHealthDescriptions[*].[Target.Id,TargetHealth.State,TargetHealth.Reason]' \
            --output table
    done
else
    echo "✗ ALB not found: $ALB_NAME"
fi

echo ""
echo "2. Checking ECS Service and Tasks..."
echo "===================================="

# Check if cluster exists
CLUSTER_STATUS=$(aws ecs describe-clusters \
    --clusters "$CLUSTER_NAME" \
    --region "$REGION" \
    --query 'clusters[0].status' \
    --output text 2>/dev/null)

if [ "$CLUSTER_STATUS" = "ACTIVE" ]; then
    echo "✓ ECS Cluster is ACTIVE: $CLUSTER_NAME"
    
    # Check service
    SERVICE_STATUS=$(aws ecs describe-services \
        --cluster "$CLUSTER_NAME" \
        --services "$SERVICE_NAME" \
        --region "$REGION" \
        --query 'services[0].status' \
        --output text 2>/dev/null)
    
    if [ "$SERVICE_STATUS" = "ACTIVE" ]; then
        echo "✓ ECS Service is ACTIVE: $SERVICE_NAME"
        
        # Get service details
        aws ecs describe-services \
            --cluster "$CLUSTER_NAME" \
            --services "$SERVICE_NAME" \
            --region "$REGION" \
            --query 'services[0].[desiredCount,runningCount,pendingCount]' \
            --output text | while read desired running pending; do
                echo "  Desired: $desired, Running: $running, Pending: $pending"
            done
        
        # List tasks
        echo ""
        echo "  Running Tasks:"
        TASK_ARNS=$(aws ecs list-tasks \
            --cluster "$CLUSTER_NAME" \
            --service-name "$SERVICE_NAME" \
            --desired-status RUNNING \
            --region "$REGION" \
            --query 'taskArns' \
            --output text)
        
        if [ -n "$TASK_ARNS" ] && [ "$TASK_ARNS" != "None" ]; then
            for TASK_ARN in $TASK_ARNS; do
                TASK_ID=$(echo "$TASK_ARN" | rev | cut -d'/' -f1 | rev)
                echo "  - Task: $TASK_ID"
                
                # Get task details
                aws ecs describe-tasks \
                    --cluster "$CLUSTER_NAME" \
                    --tasks "$TASK_ARN" \
                    --region "$REGION" \
                    --query 'tasks[0].[lastStatus,healthStatus,stopCode,stoppedReason]' \
                    --output text | while read status health stopCode stopReason; do
                        echo "    Status: $status, Health: $health"
                        if [ "$stopCode" != "None" ]; then
                            echo "    Stop Code: $stopCode"
                            echo "    Stop Reason: $stopReason"
                        fi
                    done
            done
        else
            echo "  ✗ No running tasks found"
        fi
        
        # Check recent stopped tasks
        echo ""
        echo "  Recently Stopped Tasks (if any):"
        STOPPED_TASKS=$(aws ecs list-tasks \
            --cluster "$CLUSTER_NAME" \
            --service-name "$SERVICE_NAME" \
            --desired-status STOPPED \
            --region "$REGION" \
            --query 'taskArns' \
            --output text)
        
        if [ -n "$STOPPED_TASKS" ] && [ "$STOPPED_TASKS" != "None" ]; then
            # Get only the most recent 3 stopped tasks
            echo "$STOPPED_TASKS" | tr '\t' '\n' | head -3 | while read TASK_ARN; do
                if [ -n "$TASK_ARN" ]; then
                    TASK_ID=$(echo "$TASK_ARN" | rev | cut -d'/' -f1 | rev)
                    aws ecs describe-tasks \
                        --cluster "$CLUSTER_NAME" \
                        --tasks "$TASK_ARN" \
                        --region "$REGION" \
                        --query 'tasks[0].[taskArn,stopCode,stoppedReason]' \
                        --output text | while read arn stopCode stopReason; do
                            echo "  - Task: $TASK_ID"
                            echo "    Stop Code: $stopCode"
                            echo "    Stop Reason: $stopReason"
                        done
                fi
            done
        else
            echo "  No recently stopped tasks"
        fi
    else
        echo "✗ ECS Service not found or not active: $SERVICE_NAME"
    fi
else
    echo "✗ ECS Cluster not found or not active: $CLUSTER_NAME"
fi

echo ""
echo "3. Checking Security Groups..."
echo "==============================="

# Get ALB security groups
if [ -n "$ALB_ARN" ] && [ "$ALB_ARN" != "None" ]; then
    ALB_SG_IDS=$(aws elbv2 describe-load-balancers \
        --load-balancer-arns "$ALB_ARN" \
        --region "$REGION" \
        --query 'LoadBalancers[0].SecurityGroups' \
        --output text)
    
    echo "ALB Security Groups:"
    for SG_ID in $ALB_SG_IDS; do
        echo "  Security Group: $SG_ID"
        echo "  Inbound Rules:"
        aws ec2 describe-security-groups \
            --group-ids "$SG_ID" \
            --region "$REGION" \
            --query 'SecurityGroups[0].IpPermissions[*].[IpProtocol,FromPort,ToPort,IpRanges[0].CidrIp]' \
            --output table
    done
fi

# Get ECS task security groups from service
if [ "$SERVICE_STATUS" = "ACTIVE" ]; then
    echo ""
    echo "ECS Task Security Groups:"
    TASK_SG_IDS=$(aws ecs describe-services \
        --cluster "$CLUSTER_NAME" \
        --services "$SERVICE_NAME" \
        --region "$REGION" \
        --query 'services[0].networkConfiguration.awsvpcConfiguration.securityGroups' \
        --output text 2>/dev/null)
    
    if [ -n "$TASK_SG_IDS" ] && [ "$TASK_SG_IDS" != "None" ]; then
        for SG_ID in $TASK_SG_IDS; do
            echo "  Security Group: $SG_ID"
            echo "  Inbound Rules:"
            aws ec2 describe-security-groups \
                --group-ids "$SG_ID" \
                --region "$REGION" \
                --query 'SecurityGroups[0].IpPermissions[*].[IpProtocol,FromPort,ToPort,IpRanges[0].CidrIp]' \
                --output table 2>/dev/null || echo "    Unable to describe security group"
        done
    fi
fi

echo ""
echo "4. DNS Resolution Test..."
echo "========================="

if [ -n "$ALB_DNS" ]; then
    echo "Testing DNS resolution for: $ALB_DNS"
    nslookup "$ALB_DNS" | grep -A 2 "Name:" || echo "DNS resolution failed"
    
    echo ""
    echo "Testing connectivity (curl with 5s timeout):"
    curl -I -m 5 "http://$ALB_DNS" 2>&1 || echo "Connection failed"
fi

echo ""
echo "5. CloudWatch Logs (Recent Errors)..."
echo "====================================="

# Check for recent ECS task logs
LOG_GROUP="/ecs/velana-production-minimal"
echo "Checking log group: $LOG_GROUP"

# Get recent error logs
aws logs filter-log-events \
    --log-group-name "$LOG_GROUP" \
    --start-time $(($(date +%s) - 300))000 \
    --filter-pattern "ERROR" \
    --max-items 10 \
    --region "$REGION" \
    --query 'events[*].[timestamp,message]' \
    --output table 2>/dev/null || echo "No recent errors found or log group doesn't exist"

echo ""
echo "=== Troubleshooting Complete ==="
echo "Finished at: $(date)"

# Summary
echo ""
echo "SUMMARY:"
echo "--------"
if [ "$ALB_STATE" = "active" ]; then
    echo "✓ ALB is active"
else
    echo "✗ ALB is not active"
fi

if [ "$SERVICE_STATUS" = "ACTIVE" ]; then
    echo "✓ ECS Service is active"
else
    echo "✗ ECS Service is not active"
fi

# Provide recommendations
echo ""
echo "RECOMMENDATIONS:"
echo "----------------"
if [ "$ALB_STATE" != "active" ]; then
    echo "- Check ALB configuration and listeners"
fi

if [ "$SERVICE_STATUS" != "ACTIVE" ]; then
    echo "- Check ECS service configuration"
    echo "- Review task definition and container settings"
fi

echo "- Verify security groups allow traffic between ALB and ECS tasks"
echo "- Check that ECS tasks are in the same VPC/subnets as configured in ALB"
echo "- Review CloudWatch logs for container startup errors"