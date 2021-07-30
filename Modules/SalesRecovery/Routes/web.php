<?php

use Illuminate\Support\Facades\Route;

/**
 * Private Routes
 */
Route::group(
    [
        'middleware' => ['web', 'auth', 'role:account_owner|admin|attendance|finantial'],
    ],
    function() {
        Route::resource('recovery', 'SalesRecoveryController')->only('index')
             ->names('recovery');
    }
);
