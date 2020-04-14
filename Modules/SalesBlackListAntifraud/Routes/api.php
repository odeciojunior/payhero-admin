<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('blacklistantifraud', 'SalesBlackListAntifraudApiController')
     ->only('index', 'show')
     ->middleware(['auth:api', 'setUserAsLogged']);
