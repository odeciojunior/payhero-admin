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

Route::middleware('auth:api')->get('/reportana', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:api','permission:apps']], function() {

    Route::get('apps/reportana', 'ReportanaApiController@index');
    Route::get('apps/reportana/{id}', 'ReportanaApiController@show');
    Route::get('apps/reportana/{id}/edit', 'ReportanaApiController@edit');

    Route::apiResource('apps/reportana', 'ReportanaApiController')
         ->only('create', 'store', 'update', 'destroy')->middleware('permission:apps_manage');

});