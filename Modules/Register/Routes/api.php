<?php

Route::group(
    [
        'middleware' => ['web'],
        'prefix' => 'register'
    ],
    function() {
        Route::post('/', 'RegisterApiController@store');
        Route::get('/welcome', 'RegisterApiController@welcomeEmail');
    }
);
