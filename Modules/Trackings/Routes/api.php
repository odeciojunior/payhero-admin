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
Route::group(['middleware' => ['auth:api']], function() {
    Route::get('/tracking/resume', 'TrackingsApiController@resume');
    Route::post('/tracking/notify/{trackingId}', 'TrackingsApiController@notifyClient');
    Route::apiResource('tracking', 'TrackingsApiController')->only( 'index', 'show', 'store')->names('api.trackings');
});

//Route::middleware('auth:api')->get('/trackings', function (Request $request) {
//    return $request->user();
//});
