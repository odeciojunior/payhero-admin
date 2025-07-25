<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Horizon Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where Horizon will be accessible from. If this
    | setting is null, Horizon will reside under the same domain as the
    | application. Otherwise, this value will serve as the subdomain.
    |
    */

    "domain" => null,

    /*
    |--------------------------------------------------------------------------
    | Horizon Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where Horizon will be accessible from. Feel free
    | to change this path to anything you like. Note that the URI will not
    | affect the paths of its internal API that aren't exposed to users.
    |
    */

    "path" => "horizon",

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Connection
    |--------------------------------------------------------------------------
    |
    | This is the name of the Redis connection where Horizon will store the
    | meta information required for it to function. It includes the list
    | of supervisors, failed jobs, job metrics, and other information.
    |
    */

    // 'use' => 'default',
    "use" => "redis-horizon",

    /*
    |--------------------------------------------------------------------------
    | Horizon Redis Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used when storing all Horizon data in Redis. You
    | may modify the prefix when you are running multiple installations
    | of Horizon on the same server so that they don't have problems.
    |
    */

    "prefix" => env("HORIZON_PREFIX", "horizon:"),

    /*
    |--------------------------------------------------------------------------
    | Horizon Route Middleware
    |--------------------------------------------------------------------------
    |
    | These middleware will get attached onto each Horizon route, giving you
    | the chance to add your own middleware to this list or change any of
    | the existing middleware. Or, you can simply stick with this list.
    |
    */

    "middleware" => ["web"],

    /*
    |--------------------------------------------------------------------------
    | Queue Wait Time Thresholds
    |--------------------------------------------------------------------------
    |
    | This option allows you to configure when the LongWaitDetected event
    | will be fired. Every connection / queue combination may have its
    | own, unique threshold (in seconds) before this event is fired.
    |
    */

    "waits" => [
        "redis:default" => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Trimming Times
    |--------------------------------------------------------------------------
    |
    | Here you can configure for how long (in minutes) you desire Horizon to
    | persist the recent and failed jobs. Typically, recent jobs are kept
    | for one hour while all failed jobs are stored for an entire week.
    |
    */

    "trim" => [
        "recent" => 60,
        "recent_failed" => 10080,
        "failed" => 10080,
        "monitored" => 10080,
    ],

    /*
    |--------------------------------------------------------------------------
    | Fast Termination
    |--------------------------------------------------------------------------
    |
    | When this option is enabled, Horizon's "terminate" command will not
    | wait on all of the workers to terminate unless the --wait option
    | is provided. Fast termination can shorten deployment delay by
    | allowing a new instance of Horizon to start while the last
    | instance will continue to terminate each of its workers.
    |
    */

    "fast_termination" => false,

    /*
    |--------------------------------------------------------------------------
    | Memory Limit (MB)
    |--------------------------------------------------------------------------
    |
    | This value describes the maximum amount of memory the Horizon worker
    | may consume before it is terminated and restarted. You should set
    | this value according to the resources available to your server.
    |
    */

    "memory_limit" => 256,

    /*
    |--------------------------------------------------------------------------
    | Queue Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may define the queue worker settings used by your application
    | in all environments. These supervisors and settings handle all your
    | queued jobs and will be provisioned by Horizon during deployment.
    |
    */

    "defaults" => [],

    "environments" => [
        "production" => [
            "supervisor-production" => [
                "connection" => "redis-horizon",
                "queue" => ["high", "default", "low"],
                "balance" => "false",
                "processes" => 40,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
            "supervisor-production-postback" => [
                "connection" => "redis-horizon",
                "queue" => "postback",
                "balance" => "false",
                "processes" => 2,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
            "supervisor-production-long-running" => [
                "connection" => "redis-horizon-long-running",
                "queue" => "long",
                "balance" => "false",
                "processes" => 10,
                "tries" => 3,
                "timeout" => 36000,
                "delay" => 1,
            ],
            "supervisor-production-shopify-tracking-postback" => [
                "connection" => "redis-horizon",
                "queue" => "postback-shopify-tracking",
                "balance" => "false",
                "processes" => env("APP_NAME") == "Azcend-cron" ? 3 : 0,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
        ],
        "homolog" => [
            "supervisor-homolog" => [
                "connection" => "redis-horizon",
                "queue" => ["high", "default", "low"],
                "balance" => "false",
                "processes" => 10,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
            "supervisor-homolog-postback" => [
                "connection" => "redis-horizon",
                "queue" => "postback",
                "balance" => "false",
                "processes" => 2,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
            "supervisor-homolog-long-running" => [
                "connection" => "redis-horizon-long-running",
                "queue" => "long",
                "balance" => "false",
                "processes" => 4,
                "tries" => 3,
                "timeout" => 36000,
                "delay" => 1,
            ],
            "supervisor-homolog-shopify-tracking-postback" => [
                "connection" => "redis-horizon",
                "queue" => "postback-shopify-tracking",
                "balance" => "false",
                "processes" => 2,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
        ],

        "local" => [
            "supervisor-local" => [
                "connection" => "redis-horizon",
                "queue" => ["high", "default", "low"],
                "balance" => "false",
                "processes" => 1,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
            "supervisor-local-postback" => [
                "connection" => "redis-horizon",
                "queue" => "postback",
                "balance" => "false",
                "processes" => 2,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
            "supervisor-local-long-running" => [
                "connection" => "redis-horizon-long-running",
                "queue" => "long",
                "balance" => "false",
                "processes" => 1,
                "tries" => 3,
                "timeout" => 36000,
                "delay" => 1,
            ],
            "supervisor-local-shopify-tracking-postback" => [
                "connection" => "redis-horizon",
                "queue" => "postback-shopify-tracking",
                "balance" => "false",
                "processes" => 2,
                "tries" => 3,
                "timeout" => 3600,
                "delay" => 1,
            ],
        ],
    ],
];
