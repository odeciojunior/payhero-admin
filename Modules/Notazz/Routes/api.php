<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['web', 'auth']], function() {

    Route::apiResource('apps/notazz', 'NotazzController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');

    //Route::get('/apps/getnotazzintegrations', 'NotazzController@getIntegrations');
});
