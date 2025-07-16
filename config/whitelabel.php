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
    
    // Default client when none is detected
    'default_client' => 'payhero',
    
    // Client detection configuration
    'client_detection' => [
        'method' => env('WHITELABEL_DETECTION_METHOD', 'env'), // Options: env, domain, subdomain, parameter, session
        'parameter_name' => 'client', // URL parameter name for parameter-based detection
    ],
    
    // Legacy default brand configuration (kept for backward compatibility)
    'default_brand' => [
        'name' => env('WHITELABEL_BRAND_NAME', 'PayHero'),
        'logo' => env('WHITELABEL_LOGO', null),
        'primary_color' => env('WHITELABEL_PRIMARY_COLOR', '#000000'),
        'secondary_color' => env('WHITELABEL_SECONDARY_COLOR', '#ffffff'),
    ],
    
    // Client configurations
    'clients' => [
        'payhero' => [
            'name' => 'PayHero',
            'app_name' => 'PayHero Admin',
            'domains' => ['payhero.io', 'app.payhero.io'],
            'colors' => [
                'primary' => '#FF5733',
                'primary-light' => '#FF8A65',
                'primary-dark' => '#D84315',
                'primary-contrast' => '#FFFFFF',
                'secondary' => '#6C757D',
                'secondary-light' => '#ADB5BD',
                'secondary-dark' => '#495057',
                'secondary-contrast' => '#FFFFFF',
                'accent' => '#17A2B8',
                'accent-light' => '#6EC6FF',
                'accent-dark' => '#0277BD',
                'accent-contrast' => '#FFFFFF',
                'success' => '#28A745',
                'success-light' => '#D4EDDA',
                'success-dark' => '#155724',
                'danger' => '#DC3545',
                'danger-light' => '#F8D7DA',
                'danger-dark' => '#721C24',
                'warning' => '#FFC107',
                'warning-light' => '#FFF3CD',
                'warning-dark' => '#856404',
                'info' => '#17A2B8',
                'info-light' => '#D1ECF1',
                'info-dark' => '#0C5460',
                'text' => '#212529',
                'text-muted' => '#6C757D',
                'border' => '#DEE2E6',
                'body-bg' => '#FFFFFF',
                'card-bg' => '#FFFFFF',
                'input-bg' => '#FFFFFF',
                'input-color' => '#495057',
                'input-disabled' => '#E9ECEF',
                'sidebar-bg' => '#343A40',
                'header-bg' => '#FFFFFF',
                'menu-bg' => '#FF5733',
                'link' => '#FF5733',
                'link-hover' => '#D84315',
                'shadow' => 'rgba(0, 0, 0, 0.125)',
                'focus-ring' => 'rgba(255, 87, 51, 0.25)',
                'selection-bg' => '#FF5733',
                'tooltip-bg' => '#000000',
                'backdrop' => 'rgba(0, 0, 0, 0.5)',
                'gray-100' => '#F8F9FA',
                'gray-200' => '#E9ECEF',
                'gray-300' => '#DEE2E6',
                'gray-400' => '#CED4DA',
                'gray-500' => '#ADB5BD',
                'gray-600' => '#6C757D',
                'gray-700' => '#495057',
                'gray-800' => '#343A40',
                'gray-900' => '#212529',
            ],
            'fonts' => [
                'primary' => [
                    'family' => '"Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                    'weights' => [
                        'regular' => 400,
                        'medium' => 500,
                        'semibold' => 600,
                        'bold' => 700,
                    ]
                ],
                'secondary' => [
                    'family' => '"Roboto Mono", monospace',
                    'weights' => [
                        'regular' => 400,
                        'bold' => 700,
                    ]
                ]
            ],
            'typography' => [
                'base-size' => '16px',
                'scale-ratio' => '1.2',
                'line-height' => [
                    'base' => '1.5',
                    'tight' => '1.25',
                    'loose' => '1.75',
                ],
                'letter-spacing' => [
                    'normal' => '0',
                    'wide' => '0.025em',
                    'wider' => '0.05em',
                ],
            ],
            'logo' => [
                'main' => '/images/payhero-logo.png',
                'icon' => '/images/payhero-icon.png',
                'login' => '/images/payhero-login-logo.png',
            ],
            'favicon' => '/images/payhero-favicon.ico',
            'footer_text' => 'PayHero Technology © ' . date('Y'),
        ],
        'velana' => [
            'name' => 'Velana',
            'app_name' => 'Velana Admin',
            'domains' => ['velana.io', 'app.velana.io'],
            'colors' => [
                'primary' => '#3baa1a',
                'primary-light' => '#66BB6A',
                'primary-dark' => '#2E7D32',
                'primary-contrast' => '#FFFFFF',
                'secondary' => '#F5F5F5',
                'secondary-light' => '#FAFAFA',
                'secondary-dark' => '#EEEEEE',
                'secondary-contrast' => '#212529',
                'accent' => '#4CAF50',
                'accent-light' => '#81C784',
                'accent-dark' => '#388E3C',
                'accent-contrast' => '#FFFFFF',
                'success' => '#4CAF50',
                'success-light' => '#E8F5E8',
                'success-dark' => '#2E7D32',
                'danger' => '#F44336',
                'danger-light' => '#FFEBEE',
                'danger-dark' => '#C62828',
                'warning' => '#FF9800',
                'warning-light' => '#FFF3E0',
                'warning-dark' => '#F57C00',
                'info' => '#2196F3',
                'info-light' => '#E3F2FD',
                'info-dark' => '#1565C0',
                'text' => '#212529',
                'text-muted' => '#757575',
                'border' => '#E0E0E0',
                'body-bg' => '#FAFAFA',
                'card-bg' => '#FFFFFF',
                'input-bg' => '#FFFFFF',
                'input-color' => '#424242',
                'input-disabled' => '#F5F5F5',
                'sidebar-bg' => '#2E7D32',
                'header-bg' => '#FFFFFF',
                'menu-bg' => '#3baa1a',
                'link' => '#3baa1a',
                'link-hover' => '#2E7D32',
                'shadow' => 'rgba(0, 0, 0, 0.1)',
                'focus-ring' => 'rgba(59, 170, 26, 0.25)',
                'selection-bg' => '#3baa1a',
                'tooltip-bg' => '#424242',
                'backdrop' => 'rgba(0, 0, 0, 0.5)',
                'gray-100' => '#F5F5F5',
                'gray-200' => '#EEEEEE',
                'gray-300' => '#E0E0E0',
                'gray-400' => '#BDBDBD',
                'gray-500' => '#9E9E9E',
                'gray-600' => '#757575',
                'gray-700' => '#616161',
                'gray-800' => '#424242',
                'gray-900' => '#212121',
            ],
            'fonts' => [
                'primary' => [
                    'family' => '"Poppins", sans-serif',
                    'weights' => [
                        'regular' => 400,
                        'medium' => 500,
                        'semibold' => 600,
                        'bold' => 700,
                    ]
                ]
            ],
            'typography' => [
                'base-size' => '15px',
                'scale-ratio' => '1.15',
                'line-height' => [
                    'base' => '1.6',
                    'tight' => '1.3',
                    'loose' => '1.8',
                ],
            ],
            'logo' => [
                'main' => '/images/clients/velana/logo.svg',
                'icon' => '/images/clients/velana/icon.png',
                'login' => '/images/clients/velana/login-logo.png',
            ],
            'favicon' => '/images/clients/velana/favicon.ico',
            'footer_text' => 'Velana © ' . date('Y'),
        ],
    ],
    
    // Legacy brands configuration (kept for backward compatibility)
    'brands' => [
        'payhero' => [
            'name' => 'PayHero',
            'logo' => '/images/payhero-logo.png',
            'primary_color' => '#FF5733',
            'secondary_color' => '#FFFFFF',
        ],
        'velana' => [
            'name' => 'Velana',
            'logo' => '/images/clients/logo.png',
            'primary_color' => '#3baa1aff',
            'secondary_color' => '#F5F5F5',
        ],
    ],
];