<?php

use Illuminate\Http\Request;

// role:attendance|account_owner|admin
Route::group(
    [
        "middleware" => ["auth:api", "permission:contestations","demo_account"],
        "prefix" => "contestations",
    ],
    function () {
        Route::get("/getcontestations", "ContestationsApiController@getContestations")->name(
            "contestations.getchargebacks"
        );
        Route::get("/gettotalvalues", "ContestationsApiController@getTotalValues")->name(
            "contestations.gettotalvalues"
        );
        Route::get(
            "/get-contestation-files/{salecontestation}",
            "ContestationsApiController@getContestationFiles"
        )->name("contestations.getContestationFiles");

        Route::post("/send-files", "ContestationsApiController@sendContestationFiles")
            ->name("contestations.sendContestationFiles")
            ->middleware("permission:contestations_manage");
        Route::get("/{contestationfile}/removefile", "ContestationsApiController@removeContestationFiles")
            ->name("contestations.removeContestationFiles")
            ->middleware("permission:contestations_manage");
        Route::post("/update-is-file-completed", "ContestationsApiController@updateIsFileCompleted")
            ->name("users.updateIsFileCompleted")
            ->middleware("permission:contestations_manage");

        Route::get('/projects-with-contestations', 'ContestationsApiController@getProjectsWithContestations');

        Route::get("/{contestation_id}/contestation", "ContestationsApiController@show")
            ->name("contestations.show")
            ->middleware("permission:contestations_manage");
    }
);
