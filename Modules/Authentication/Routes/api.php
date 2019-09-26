<?php

Route::group(
    [
        'middleware' => 'web'
    ],
    function() {

        Route::post('/login', 'AuthenticationApiController@login');

        Route::post('/logout', 'AuthenticationApiController@logout');
    }
);
