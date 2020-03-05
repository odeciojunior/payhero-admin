<?php

/*
 * This file is part of Laravel Hashids.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'salt'   => '7d124336feb18d280c0f32d7fc5e57a1',  // gerado aleatoriamente na internet (igual ao do checkout)
            'length' => '15',
        ],

        'sale_id' => [
            'salt'   => '7d124336feb18d280c0f32d7fc5e57a1',
            'length' => '8',
        ],

        'pusher_connection' => [
            'salt'   => '7d124336feb18d280c0f32d7fc5e57a1',
            'length' => '50',
        ],

        'whatsapp2_token' => [
            'salt'   => '7d124336feb18d280c0f32d7fc5e57a1',
            'length' => '40',
        ],

        'affiliate' => [
            'salt'   => '7d124336feb18d280c0f32d7fc5e57a1',
            'length' => '18',
        ],
    ],

];
