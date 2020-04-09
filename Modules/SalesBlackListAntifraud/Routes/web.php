<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['web', 'auth', 'role:account_owner|admin|attendance', 'setUserAsLogged'],
    ],
    function() {
        Route::resource('salesblacklistantifraud', 'SalesBlackListAntifraudController')->only('index')
             ->names('salesblacklistantifraud');
    }
);

