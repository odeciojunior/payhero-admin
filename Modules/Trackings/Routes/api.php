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
    Route::apiResource('tracking', 'TrackingsApiController')->only('index', 'store','destroy')->names('api.trackings');
});

//Route::middleware('auth:api')->get('/trackings', function (Request $request) {
//    return $request->user();
//});
