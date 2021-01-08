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
Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function () {
        Route::apiResource('/projectreviewsconfig', 'ProjectReviewsConfigApiController')
            ->only('index', 'store', 'destroy', 'update', 'show', 'edit')
            ->middleware('role:account_owner|admin');

//        Route::post('/projectreviewsconfig/previewupsell', 'ProjectReviewsConfigApiController@preview')
//            ->middleware('role:account_owner|admin');
    }
);
