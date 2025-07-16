#!/bin/bash

# Script to build and deploy the fixed admin application image
# This script addresses route conflicts and missing directories

set -e

# Configuration
ECR_REGISTRY="983877353757.dkr.ecr.us-east-1.amazonaws.com"
REPOSITORY_NAME="velana-production-minimal-admin-app"
NEW_VERSION="v1.0.4"
REGION="us-east-1"

echo "🚀 Building fixed admin application image..."
echo "Registry: $ECR_REGISTRY"
echo "Repository: $REPOSITORY_NAME"
echo "Version: $NEW_VERSION"
echo "Region: $REGION"

# Authenticate with ECR
echo "🔑 Authenticating with ECR..."
aws ecr get-login-password --region $REGION | docker login --username AWS --password-stdin $ECR_REGISTRY

# Build the fixed image
echo "🔨 Building Docker image..."
docker build -f Dockerfile.fixed -t $ECR_REGISTRY/$REPOSITORY_NAME:$NEW_VERSION .

# Tag as latest
docker tag $ECR_REGISTRY/$REPOSITORY_NAME:$NEW_VERSION $ECR_REGISTRY/$REPOSITORY_NAME:latest

# Push to ECR
echo "📤 Pushing image to ECR..."
docker push $ECR_REGISTRY/$REPOSITORY_NAME:$NEW_VERSION
docker push $ECR_REGISTRY/$REPOSITORY_NAME:latest

echo "✅ Successfully built and pushed fixed image: $ECR_REGISTRY/$REPOSITORY_NAME:$NEW_VERSION"
echo ""
echo "🔧 Fixes applied:"
echo "  ✓ Route conflict resolved: activecampaign.create -> apps.activecampaign.create"
echo "  ✓ Missing directory created: /var/www/resources/views/modules/postback"
echo "  ✓ Proper permissions set"
echo ""
echo "📋 Next steps:"
echo "  1. Update ECS task definition to use version $NEW_VERSION"
echo "  2. Deploy the updated service"