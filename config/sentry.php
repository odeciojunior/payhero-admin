<?php

if (env('APP_ENV', 'local') == 'production' || env('APP_ENV', 'local') == 'homolog') {
    $sentry_dsn = env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')); // production
} else {
    $sentry_dsn = null; // local
}

return [

    'dsn' => $sentry_dsn,

    'traces_sample_rate' => 0.5,

    'breadcrumbs' => [

        // Capture bindings on SQL queries logged in breadcrumbs
        'sql_bindings' => true,

    ],

];
