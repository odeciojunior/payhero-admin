<?php

use Illuminate\Support\Facades\Route;
//role:account_owner|admin|finantial
Route::group(
    [
        'middleware' => ['web', 'auth', 'permission:dashboard'],
    ],
    function() {
        Route::resource('/dashboard', 'DashboardController')->only('index');
    }
);

