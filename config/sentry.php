<?php

if (env('APP_ENV', 'homolog') == 'production') {
    $sentry_dsn = env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')); // production
} else {
    $sentry_dsn = ''; // local
}

return [

    'dsn' => $sentry_dsn,

    // capture release as git sha
    // 'release' => trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD')),

    'breadcrumbs' => [

        // Capture bindings on SQL queries logged in breadcrumbs
        'sql_bindings' => true,

    ],

];
