<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "demo_account"],
    ],
    function () {
        Route::get("/projects/user-projects", "ProjectsApiController@getProjects")->middleware(
            "permission:projects|apps"
        );

        Route::get("/projects", "ProjectsApiController@index");

        Route::get("/projects/create", "ProjectsApiController@create");
        Route::get("/projects/{id}", "ProjectsApiController@show");
        Route::get("/projects/{id}/edit", "ProjectsApiController@edit");

        Route::apiResource("/projects", "ProjectsApiController")
            ->only("store", "destroy")
            ->middleware("permission:projects_manage");

        // Nova Edicao de projeto com novo metodo
        Route::put("/projects/{id}/settings", "ProjectsApiController@updateSettings");

        Route::post("/projects/updateorder", "ProjectsApiController@updateOrder")->middleware(
            "permission:projects_manage"
        );

        Route::post("/projects/updateconfig", "ProjectsApiController@updateConfig")->middleware(
            "permission:projects_manage"
        );

        Route::get("/projects/{id}/companie", "ProjectsApiController@getCompanieByProject")->middleware(
            "permission:projects_manage"
        );
    }
);
