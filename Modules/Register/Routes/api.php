<?php

use Illuminate\Support\Facades\Route;

// Route::group(
//     [
//         'middleware' => ['web'],
//         'prefix'     => 'register',
//     ],
//     function() {
//         Route::post('/', 'RegisterApiController@store');
//     }
// );

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
        'prefix'     => 'register',
    ],
    function() {
        Route::get('/welcome', 'RegisterApiController@welcomeEmail');
    }
);
