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

Route::group(["middleware" => ["auth:api", "scopes:admin", "permission:apps","demo_account"]], function () {
    Route::get("apps/activecampaign", "ActiveCampaignApiController@index");
    Route::get("apps/activecampaign/{id}", "ActiveCampaignApiController@show");
    Route::get("apps/activecampaign/{id}/edit", "ActiveCampaignApiController@edit");
    Route::apiResource("apps/activecampaign", "ActiveCampaignApiController")
        ->only("create", "store", "update", "destroy")
        ->names("api.activecampaign_api")
        ->middleware("permission:apps_manage");

    Route::get("apps/activecampaignevent/create", "ActiveCampaignEventApiController@create");

    Route::get("apps/activecampaignevent", "ActiveCampaignEventApiController@index");
    Route::get("apps/activecampaignevent/{id}", "ActiveCampaignEventApiController@show");
    Route::get("apps/activecampaignevent/{id}/edit", "ActiveCampaignEventApiController@edit");

    Route::apiResource("apps/activecampaignevent", "ActiveCampaignEventApiController")
        ->only("store", "update", "destroy")
        ->names("api.activecampaignevent_api")
        ->middleware("permission:apps_manage");
});
