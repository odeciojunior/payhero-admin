#!/bin/bash
# MySQL Health Check Script
# More robust health check that waits for MySQL to be fully initialized

set -e

MYSQL_HOST="${MYSQL_HOST:-localhost}"
MYSQL_PORT="${MYSQL_PORT:-3306}"
MYSQL_USER="${MYSQL_USER:-root}"
MYSQL_PASSWORD="${MYSQL_PASSWORD:-secret}"

# First check if mysqld is running
if ! pgrep -x mysqld > /dev/null; then
    echo "MySQL daemon not running"
    exit 1
fi

# Try to connect and run a simple query
if mysql -h"${MYSQL_HOST}" -P"${MYSQL_PORT}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -e "SELECT 1" > /dev/null 2>&1; then
    # Check if we can access the mysql database (indicates full initialization)
    if mysql -h"${MYSQL_HOST}" -P"${MYSQL_PORT}" -u"${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -e "SELECT 1 FROM mysql.user LIMIT 1" > /dev/null 2>&1; then
        echo "MySQL is healthy"
        exit 0
    else
        echo "MySQL is running but not fully initialized"
        exit 1
    fi
else
    echo "Cannot connect to MySQL"
    exit 1
fi