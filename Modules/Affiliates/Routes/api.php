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
        "middleware" => ["auth:api",'demo_account'],
    ],
    function () {
        Route::get("/affiliates/getaffiliates", "AffiliatesApiController@getAffiliates")->middleware(
            "permission:affiliates"
        );

        Route::get("/affiliates/getaffiliaterequests", "AffiliatesApiController@getAffiliateRequests")->middleware(
            "permission:affiliates"
        );

        Route::post(
            "/affiliates/evaluateaffiliaterequest",
            "AffiliatesApiController@evaluateAffiliateRequest"
        )->middleware("permission:affiliates_manage");

        Route::post(
            "/affiliates/updateconfigaffiliate/{affiliateId}",
            "AffiliatesApiController@updateConfigAffiliate"
        )->middleware("permission:affiliates_manage");

        Route::get("/affiliates", "AffiliatesApiController@index");
        Route::get("/affiliates/{id}", "AffiliatesApiController@show");
        Route::get("/affiliates/{id}/edit", "AffiliatesApiController@edit");

        Route::apiResource("/affiliates", "AffiliatesApiController")
            ->only("store", "update", "destroy")
            ->names("api.affiliates_api")
            ->middleware("permission:affiliates_manage");

        Route::get("/affiliatelinks", "AffiliateLinksApiController@index");
        Route::get("/affiliatelinks/{id}", "AffiliateLinksApiController@show");
        Route::get("/affiliatelinks/{id}/edit", "AffiliateLinksApiController@edit");

        Route::apiResource("/affiliatelinks", "AffiliateLinksApiController")
            ->only("store", "update", "destroy")
            ->names("api.affiliatelinks_api")
            ->middleware("permission:affiliates_manage");
    }
);
