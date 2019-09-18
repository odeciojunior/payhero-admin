<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::apiResource('profile', 'ProfileApiController')
             ->only('index', 'show', 'edit', 'store', 'update', 'destroy', 'create')
             ->names('api.profile');

        Route::post('profile.uploaddocuments', 'ProfileApiController@uploaddocuments')->name('profile.uploaddocuments');
    }
);
