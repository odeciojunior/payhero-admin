<?php

use Illuminate\Support\Facades\Route;

// role:account_owner|admin|attendance
Route::group(
    [
        'middleware' => ['web', 'auth', 'permission:recovery'],
    ],
    function() {
        Route::resource('recovery', 'SalesRecoveryController')->only('index')->names('recovery');
    }
);
