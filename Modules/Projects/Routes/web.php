<?php

declare(strict_types=1);
//role:account_owner|admin
Route::group(
    [
        "middleware" => ["web", "auth", "permission:projects"],
    ],
    function () {
        Route::get("/projects/create", "ProjectsController@create")->middleware("permission:projects_manage");

        Route::Resource("/projects", "ProjectsController")->only("index", "show");

        Route::get("/projects/{projectId}/{affiliateId}", "ProjectsController@showAffiliate")->name("showaffiliate");
    }
);
