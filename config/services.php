<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    "mailgun" => [
        "domain" => env("MAILGUN_DOMAIN"),
        "secret" => env("MAILGUN_SECRET"),
    ],

    "ses" => [
        "key" => env("SES_KEY"),
        "secret" => env("SES_SECRET"),
        "region" => env("SES_REGION", "us-east-1"),
    ],

    "sparkpost" => [
        "secret" => env("SPARKPOST_SECRET"),
    ],

    "stripe" => [
        "model" => Modules\Core\Entities\User::class,
        "key" => env("STRIPE_KEY"),
        "secret" => env("STRIPE_SECRET"),
    ],

    "zenvia" => [
        "from" => "Azcend",
        "pretend" => false,
        "conta" => "healthlab.corp",
        "senha" => "hLQNVb7VQk",
    ],

    "shopify" => [
        "client_id" => env("SHOPIFY_KEY"),
        "client_secret" => env("SHOPIFY_SECRET"),
        "redirect" => env("SHOPIFY_REDIRECT"),
    ],

    'short_io' => [
        'domain' => env('SHORT_IO_DOMAIN', 'https://api.short.io'),
        'api_key' => env('SHORT_IO_API_KEY'),
    ],
];
