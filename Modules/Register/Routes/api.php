<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['api'],
        'prefix'     => 'register',
    ],
    function() {
        Route::post('/', 'RegisterApiController@store');
        Route::get('/welcome', 'RegisterApiController@welcomeEmail');
    }
);
