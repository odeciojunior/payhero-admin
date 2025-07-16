#\!/bin/bash
set -e

echo "Updating task definition with more memory..."

# Get current task definition
aws ecs describe-task-definition --task-definition velana-production-minimal-admin-task:5 > current-task-def.json

# Extract the task definition part
jq '.taskDefinition' current-task-def.json > task-def-only.json

# Update memory and CPU
jq '.memory = "4096"  < /dev/null |  .cpu = "2048" | .containerDefinitions[0].memory = 3072 | .containerDefinitions[1].memory = 1024' task-def-only.json > updated-task-def.json

# Remove fields that can't be in register request
jq 'del(.taskDefinitionArn, .revision, .status, .requiresAttributes, .compatibilities, .registeredAt, .registeredBy)' updated-task-def.json > register-task-def.json

# Register new task definition
aws ecs register-task-definition --cli-input-json file://register-task-def.json

echo "Task definition updated. Now updating service..."

# Update service with new task definition
aws ecs update-service \
  --cluster velana-production-minimal-cluster \
  --service velana-production-minimal-admin-service \
  --task-definition velana-production-minimal-admin-task \
  --force-new-deployment

echo "Service update initiated."
