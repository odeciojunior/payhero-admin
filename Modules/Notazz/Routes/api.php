<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function() {

    Route::apiResource('apps/notazz', 'NotazzApiController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');

    //Route::get('/apps/getnotazzintegrations', 'NotazzController@getIntegrations');
});
