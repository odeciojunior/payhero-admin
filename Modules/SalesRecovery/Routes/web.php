<?php

use Illuminate\Support\Facades\Route;

// role:account_owner|admin|attendance
Route::group(
    [
        'middleware' => ['web', 'auth', 'permission:sales_recovery'],
    ],
    function() {
        Route::resource('recovery', 'SalesRecoveryController')->only('index')
             ->names('recovery');
    }
);
