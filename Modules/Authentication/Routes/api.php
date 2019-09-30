<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => 'api',
    ],
    function() {
        Route::post('/login', 'AuthenticationApiController@login');
        Route::post('/logout', 'AuthenticationApiController@logout');
    }
);
