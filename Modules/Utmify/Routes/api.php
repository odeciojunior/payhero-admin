<?php

Route::group(["middleware" => ["auth:api", "permission:apps", "demo_account"]], function () {
    Route::apiResource("apps/utmify", "UtmifyApiController")
        ->only("index", "show", "create", "store", "update", "destroy")
        ->middleware("permission:apps_manage");
});
