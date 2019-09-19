<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth'],
        'as' => 'hotzapp.'
    ],
    function() {
        Route::resource('/apps/hotzapp', 'HotZappController')->only('index');
    }
);
