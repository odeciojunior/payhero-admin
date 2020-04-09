<?php

use Illuminate\Support\Facades\Route;

Route::group([
                 'middleware' => ['auth:api', 'role:account_owner|admin|attendance', 'setUserAsLogged'],
             ],
    function() {
        Route::apiResource('salesblacklistantifraud', 'SalesBlackListAntifraudApiController')->only('index', 'show')
             ->names('api.salesblacklistantifraud');
    }
);
