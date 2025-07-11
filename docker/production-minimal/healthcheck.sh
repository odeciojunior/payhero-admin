#!/bin/sh
# Simple PHP-FPM health check

# Check if PHP-FPM is listening on port 9000
if nc -z localhost 9000; then
    echo "PHP-FPM is running on port 9000"
    exit 0
else
    echo "PHP-FPM is not running on port 9001"
    exit 1
fi