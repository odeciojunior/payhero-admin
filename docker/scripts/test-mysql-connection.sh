#!/bin/bash

# Test MySQL Connection Script
# This script tests the MySQL connection and database setup

echo "Testing MySQL connection..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 5

# Test connection using docker exec
docker exec payhero_mysql mysql -upayhero -psecret -e "SELECT 'Connection successful!' as status;" payhero

# Check if database exists
echo -e "\nChecking if database exists..."
docker exec payhero_mysql mysql -upayhero -psecret -e "SHOW DATABASES LIKE 'payhero';" payhero

# Show user privileges
echo -e "\nChecking user privileges..."
docker exec payhero_mysql mysql -upayhero -psecret -e "SHOW GRANTS FOR 'payhero'@'%';" payhero

echo -e "\nDatabase setup test complete!"