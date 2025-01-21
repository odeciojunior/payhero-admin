<?php

declare(strict_types=1);

return [
    'gateways' => [
        'monetix' => [
            'production_url' => getenv('MONETIX_PRODUCTION_URL'),
            'production_token' => getenv('MONETIX_PRODUCTION_TOKEN'),
            'sandbox_url' => getenv('MONETIX_SANDBOX_URL'),
            'sandbox_token' => getenv('MONETIX_SANDBOX_TOKEN'),
        ],
    ],
];
