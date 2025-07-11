<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return [
    "Modules\\Invites\\Database\\Seeders\\InvitesDatabaseSeeder" =>
        $baseDir . "/Database/Seeders/InvitesDatabaseSeeder.php",
    "Modules\\Invites\\Http\\Controllers\\InvitesApiController" =>
        $baseDir . "/Http/Controllers/InvitesApiController.php",
    "Modules\\Invites\\Http\\Controllers\\InvitesController" => $baseDir . "/Http/Controllers/InvitesController.php",
    "Modules\\Invites\\Http\\Requests\\SendInvitationRequest" => $baseDir . "/Http/Requests/SendInvitationRequest.php",
    "Modules\\Invites\\Providers\\InvitesServiceProvider" => $baseDir . "/Providers/InvitesServiceProvider.php",
    "Modules\\Invites\\Providers\\RouteServiceProvider" => $baseDir . "/Providers/RouteServiceProvider.php",
    "Modules\\Invites\\Transformers\\InviteResource" => $baseDir . "/Transformers/InviteResource.php",
    "Modules\\Invites\\Transformers\\InvitesResource" => $baseDir . "/Transformers/InvitesResource.php",
];
