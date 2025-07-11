<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Whitelabel Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file contains settings for the whitelabel functionality
    | of the application.
    |
    */

    'enabled' => env('WHITELABEL_ENABLED', false),
    
    'default_brand' => [
        'name' => env('WHITELABEL_BRAND_NAME', 'PayHero'),
        'logo' => env('WHITELABEL_LOGO', null),
        'primary_color' => env('WHITELABEL_PRIMARY_COLOR', '#000000'),
        'secondary_color' => env('WHITELABEL_SECONDARY_COLOR', '#ffffff'),
    ],
];