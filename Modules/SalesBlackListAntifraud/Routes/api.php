<?php

use Illuminate\Support\Facades\Route;

Route::apiResource('antifraud', 'SalesBlackListAntifraudApiController')
     ->only('index', 'show')
     ->middleware(['auth:api', 'setUserAsLogged']);
