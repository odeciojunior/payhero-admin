<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth', 'role:account_owner|admin'],
    ],
    function() {
        Route::resource('/dashboard', 'DashboardController')->only('index');
    }
);
