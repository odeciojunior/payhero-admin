#!/bin/bash
set -e

echo "Testing module loading in Docker container..."

# Build the test image if needed
if [[ "$(docker images -q payhero-admin-app:latest 2> /dev/null)" == "" ]]; then
    echo "Building Docker image first..."
    cd /home/hero/projects/payhero/admin/docker/production-minimal
    docker build -t payhero-admin-app:latest -f Dockerfile.app ../../
fi

echo ""
echo "=== Testing Composer Autoload ==="
docker run --rm payhero-admin-app:latest composer dump-autoload --no-dev --optimize

echo ""
echo "=== Checking if Modules namespace is registered ==="
docker run --rm payhero-admin-app:latest php -r "
    require 'vendor/autoload.php';
    \$loader = require 'vendor/autoload.php';
    \$prefixes = \$loader->getPrefixesPsr4();
    if (isset(\$prefixes['Modules\\\\'])) {
        echo 'SUCCESS: Modules namespace is registered' . PHP_EOL;
        echo 'Path: ' . implode(', ', \$prefixes['Modules\\\\']) . PHP_EOL;
    } else {
        echo 'ERROR: Modules namespace is NOT registered' . PHP_EOL;
        echo 'Registered namespaces:' . PHP_EOL;
        foreach (\$prefixes as \$prefix => \$paths) {
            echo '  - ' . \$prefix . PHP_EOL;
        }
    }
"

echo ""
echo "=== Testing Module Discovery ==="
docker run --rm payhero-admin-app:latest php artisan module:list

echo ""
echo "=== Testing ActiveCampaign Module ==="
docker run --rm payhero-admin-app:latest php -r "
    require 'vendor/autoload.php';
    if (class_exists('Modules\\\\ActiveCampaign\\\\Providers\\\\ActiveCampaignServiceProvider')) {
        echo 'SUCCESS: ActiveCampaignServiceProvider class exists' . PHP_EOL;
    } else {
        echo 'ERROR: ActiveCampaignServiceProvider class NOT found' . PHP_EOL;
    }
"

echo ""
echo "=== Checking Module Files ==="
docker run --rm payhero-admin-app:latest ls -la Modules/ActiveCampaign/Providers/