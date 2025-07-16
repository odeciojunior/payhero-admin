<?php

use Illuminate\Support\Facades\Route;

Route::group(["middleware" => ["auth:api", "scopes:admin", "permission:invitations","demo_account"]], function () {
    Route::get("invitations", "InvitesApiController@index");
    Route::apiResource("invitations", "InvitesApiController")
        ->only("store", "destroy")
        ->names("api.invites_api")
        ->middleware("permission:invitations_manage");

    Route::get("/invitations/getinvitationdata", "InvitesApiController@getInvitationData")->name(
        "api.getinvitationdata"
    );
    Route::post("/invitations/resendinvitation", "InvitesApiController@resendInvitation")
        ->name("api.resendinvitation")
        ->middleware("permission:invitations_manage");
});
