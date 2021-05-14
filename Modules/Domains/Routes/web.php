<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],

    ],
    function() {
        Route::resource('/domains', 'DomainsController');
    }
);

