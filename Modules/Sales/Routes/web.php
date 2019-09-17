<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function() {
        Route::resource('/sales', 'SalesController')->only('index');
    }
);
