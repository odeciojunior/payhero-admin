<?php

use Illuminate\Support\Facades\Route;

/**
 * Private Routes
 */
Route::group(
    [
        'middleware' => ['web', 'auth'],
    ],
    function() {
        Route::resource('recovery', 'SalesRecoveryController')->only('index')
             ->names('recovery');
    }
);
