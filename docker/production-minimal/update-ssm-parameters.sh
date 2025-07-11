#!/bin/bash

# Script to update SSM parameters for ElastiCache without authentication

echo "Updating SSM parameters for ElastiCache without authentication..."

# Remove Redis password parameters that shouldn't exist for ElastiCache
aws ssm delete-parameter --name "/velana/production-minimal/REDIS_PASSWORD" 2>/dev/null || true
aws ssm delete-parameter --name "/velana/production-minimal/REDIS_SESSION_PASSWORD" 2>/dev/null || true
aws ssm delete-parameter --name "/velana/production-minimal/REDIS_HORIZON_PASSWORD" 2>/dev/null || true
aws ssm delete-parameter --name "/velana/production-minimal/REDIS_CACHE_PASSWORD" 2>/dev/null || true
aws ssm delete-parameter --name "/velana/production-minimal/REDIS_STATEMENT_PASSWORD" 2>/dev/null || true

echo "Redis password parameters removed from SSM Parameter Store"
echo "ElastiCache is now configured without authentication"