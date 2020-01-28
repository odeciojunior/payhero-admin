<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth', 'setUserAsLogged'],

    ],
    function() {
        Route::resource('/domains', 'DomainsController');
    }
);

