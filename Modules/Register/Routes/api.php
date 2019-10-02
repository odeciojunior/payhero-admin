<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['api'],
        'prefix'     => 'register',
    ],
    function() {
        Route::post('/', 'RegisterApiController@store');
    }
);
Route::group(
    [
        'middleware' => ['auth:api'],
        'prefix'     => 'register',
    ],
    function() {
        Route::get('/welcome', 'RegisterApiController@welcomeEmail');
    }
);
