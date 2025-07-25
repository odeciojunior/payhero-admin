<?php

use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => ['auth:api', 'scopes:admin', 'demo_account']], function() {
    Route::get('/tracking/resume', 'TrackingsApiController@resume');
    Route::get('/tracking/blockedbalance', 'TrackingsApiController@getBlockedBalance');
    Route::post('/tracking/notify/{trackingId}', 'TrackingsApiController@notifyClient')->middleware('permission:trackings_manage');
    Route::post('/tracking/export', 'TrackingsApiController@export');
    Route::post('/tracking/import', 'TrackingsApiController@import');

    //Route::apiResource('tracking', 'TrackingsApiController')->only( 'index', 'show', 'store')->names('api.trackings_api');
    Route::get("/tracking", "TrackingsApiController@index");
    Route::get("/tracking/{id}", "TrackingsApiController@show");
    Route::post("/tracking", "TrackingsApiController@store")->middleware("permission:trackings_manage");
});

Route::get("/tracking/detail/{trackingCode}", "TrackingsApiController@detail");

//Route::middleware('auth:api')->get('/trackings', function (Request $request) {
//    return $request->user();
//});
