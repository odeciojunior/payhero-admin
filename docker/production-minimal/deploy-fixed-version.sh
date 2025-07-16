#!/bin/bash

# Script to deploy the fixed admin application (v1.0.4)
# This script updates the ECS service with the corrected task definition

set -e

# Configuration
CLUSTER_NAME="velana-production-minimal-cluster"
SERVICE_NAME="velana-production-minimal-admin-task-service-v2"
TASK_DEFINITION_FILE="task-definition-admin-v1.0.4.json"
REGION="us-east-1"

echo "🚀 Deploying fixed admin application..."
echo "Cluster: $CLUSTER_NAME"
echo "Service: $SERVICE_NAME"
echo "Task Definition: $TASK_DEFINITION_FILE"
echo "Region: $REGION"

# Register the new task definition
echo "📋 Registering new task definition..."
TASK_DEFINITION_ARN=$(aws ecs register-task-definition \
    --region $REGION \
    --cli-input-json file://$TASK_DEFINITION_FILE \
    --query 'taskDefinition.taskDefinitionArn' \
    --output text)

echo "✅ Task definition registered: $TASK_DEFINITION_ARN"

# Update the service
echo "🔄 Updating ECS service..."
aws ecs update-service \
    --region $REGION \
    --cluster $CLUSTER_NAME \
    --service $SERVICE_NAME \
    --task-definition $TASK_DEFINITION_ARN \
    --force-new-deployment

echo "⏳ Waiting for deployment to complete..."
aws ecs wait services-stable \
    --region $REGION \
    --cluster $CLUSTER_NAME \
    --services $SERVICE_NAME

echo "✅ Deployment completed successfully!"
echo ""
echo "🔧 Applied fixes:"
echo "  ✓ Route conflict resolved: activecampaign.create -> apps.activecampaign.create"
echo "  ✓ Missing directory created: /var/www/resources/views/modules/postback"
echo "  ✓ Image version updated: v1.0.4"
echo ""
echo "📊 Service status:"
aws ecs describe-services \
    --region $REGION \
    --cluster $CLUSTER_NAME \
    --services $SERVICE_NAME \
    --query 'services[0].{Status:status,RunningCount:runningCount,DesiredCount:desiredCount,TaskDefinition:taskDefinition}' \
    --output table