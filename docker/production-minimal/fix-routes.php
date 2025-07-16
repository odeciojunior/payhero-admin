<?php
/**
 * Script to fix route conflicts in ActiveCampaign module
 * This script resolves the duplicate route name 'activecampaign.create'
 */

$routePath = '/var/www/Modules/ActiveCampaign/Routes/web.php';

if (file_exists($routePath)) {
    echo "Found ActiveCampaign routes file at: $routePath\n";
    
    $content = file_get_contents($routePath);
    $originalContent = $content;
    
    // Fix the conflicting route name
    $content = str_replace(
        "->name('activecampaign.create')", 
        "->name('apps.activecampaign.create')", 
        $content
    );
    
    if ($content !== $originalContent) {
        file_put_contents($routePath, $content);
        echo "✓ Route conflict fixed: activecampaign.create -> apps.activecampaign.create\n";
    } else {
        echo "ⓘ No route conflicts found or already fixed\n";
    }
} else {
    echo "⚠ ActiveCampaign routes file not found at: $routePath\n";
}

echo "Route fix script completed.\n";