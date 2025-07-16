#\!/bin/bash
set -e

echo "Creating IAM roles for ECS task..."

# Create trust policy for ECS tasks
cat > ecs-task-trust-policy.json << 'POLICY'
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Principal": {
        "Service": "ecs-tasks.amazonaws.com"
      },
      "Action": "sts:AssumeRole"
    }
  ]
}
POLICY

# Create task role
echo "Creating task role..."
aws iam create-role \
  --role-name velana-production-minimal-admin-task-role \
  --assume-role-policy-document file://ecs-task-trust-policy.json \
  --description "Task role for PayHero Admin ECS tasks"

# Create task role policy
cat > task-role-policy.json << 'POLICY'
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "ssm:GetParameter",
        "ssm:GetParameters",
        "ssm:GetParameterHistory",
        "ssm:GetParametersByPath"
      ],
      "Resource": [
        "arn:aws:ssm:us-east-1:983877353757:parameter/velana-production-minimal-admin/*"
      ]
    },
    {
      "Effect": "Allow",
      "Action": [
        "kms:Decrypt"
      ],
      "Resource": "*"
    },
    {
      "Effect": "Allow",
      "Action": [
        "logs:CreateLogGroup",
        "logs:CreateLogStream",
        "logs:PutLogEvents"
      ],
      "Resource": "*"
    }
  ]
}
POLICY

# Attach policy to task role
echo "Attaching policy to task role..."
aws iam put-role-policy \
  --role-name velana-production-minimal-admin-task-role \
  --policy-name velana-production-minimal-admin-task-policy \
  --policy-document file://task-role-policy.json

# Check if execution role exists
echo "Checking execution role..."
aws iam get-role --role-name velana-production-minimal-admin-execution-role 2>/dev/null || {
  echo "Creating execution role..."
  
  # Create execution role
  aws iam create-role \
    --role-name velana-production-minimal-admin-execution-role \
    --assume-role-policy-document file://ecs-task-trust-policy.json \
    --description "Execution role for PayHero Admin ECS tasks"
  
  # Attach managed policy for ECR access
  aws iam attach-role-policy \
    --role-name velana-production-minimal-admin-execution-role \
    --policy-arn arn:aws:iam::aws:policy/service-role/AmazonECSTaskExecutionRolePolicy
  
  # Create and attach policy for SSM parameter access
  cat > execution-role-policy.json << 'POLICY'
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "ssm:GetParameter",
        "ssm:GetParameters"
      ],
      "Resource": [
        "arn:aws:ssm:us-east-1:983877353757:parameter/velana-production-minimal-admin/*"
      ]
    },
    {
      "Effect": "Allow",
      "Action": [
        "kms:Decrypt"
      ],
      "Resource": "*"
    }
  ]
}
POLICY
  
  aws iam put-role-policy \
    --role-name velana-production-minimal-admin-execution-role \
    --policy-name velana-production-minimal-admin-execution-policy \
    --policy-document file://execution-role-policy.json
}

echo "IAM roles created successfully\!"

# Force update the service to retry
echo "Updating service to retry deployment..."
aws ecs update-service \
  --cluster velana-production-minimal-cluster \
  --service velana-production-minimal-admin-service \
  --force-new-deployment

echo "Deployment restarted with proper IAM roles."
