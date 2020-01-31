<?php

use Illuminate\Http\Request;
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
Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {
        Route::apiResource('/affiliates', 'AffiliatesApiController')
             ->only('index', 'show', 'store', 'update', 'destroy')->names('api.affiliates')
             ->middleware('role:account_owner|admin');

        Route::get('/affiliates/getaffiliates/{projectId}', 'AffiliatesApiController@getAffiliates')
             ->middleware('role:account_owner|admin');

        Route::get('/affiliates/getaffiliaterequests/{projectId}', 'AffiliatesApiController@getAffiliateRequests')
             ->middleware('role:account_owner|admin');
    }
);
//Route::middleware('auth:api')->get('/affiliates', function (Request $request) {
//    return $request->user();
//});
