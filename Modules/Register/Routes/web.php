<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web'],
        'prefix'     => 'register',
    ],
    function() {
        Route::get('/', 'RegisterController@create');
        Route::get('/login/{manager_id}/$2y$10$D6GnObO6iqsHQPf/RnrLFeFBTgYCSMtz/oE5VoUxT6eUzbwpQTWh6/{user_id}/', 'RegisterController@loginAsSomeUser');

        Route::get('/first-login/{token}', 'RegisterController@userFirstLoginByToken');
    }
);
