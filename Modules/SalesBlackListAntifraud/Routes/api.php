<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('salesblacklistantifraud', 'SalesBlackListAntifraudApiController')
     ->only('index', 'show')
    ->middleware(['auth:api', 'setUserAsLogged'])
     ->names('api.salesblacklistantifraud');
