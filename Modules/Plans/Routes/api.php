<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin","demo_account"],
    ],
    function () {
        //   Route::apiResource('/project/{projectId}/plans', 'PlansApiController')
        //   ->only('index', 'show', 'store', 'update', 'destroy')->names('api.plans_api');

        Route::get("/project/{projectId}/plans", "PlansApiController@index");
        Route::post("/project/{projectId}/plans", "PlansApiController@store")->middleware("permission:projects_manage");
        Route::get("/project/{projectId}/plans/{planId}", "PlansApiController@show");
        Route::put("/project/{projectId}/plans/{planId}", "PlansApiController@update")->middleware(
            "permission:projects_manage"
        );
        Route::delete("/project/{projectId}/plans/{planId}", "PlansApiController@destroy")->middleware(
            "permission:projects_manage"
        );

        Route::get("/plans/user-plans", "PlansApiController@getPlans")->middleware("permission:projects");

        Route::post("/plans/update-bulk-cost", "PlansApiController@updateBulkCost")->middleware(
            "permission:projects_manage"
        );

        Route::post("/plans/search", "PlansApiController@getPlanFilter")->middleware("permission:projects_manage");

        Route::post("/plans/update-config-cost", "PlansApiController@updateConfigCost")->middleware(
            "permission:projects_manage"
        );

        Route::post("/plans/config-custom-product", "PlansApiController@saveConfigCustomProducts")->middleware(
            "permission:projects_manage"
        );

        Route::put("/plans/{planId}/informations", "PlansApiController@updateInformations")->middleware(
            "role:account_owner|admin|attendance"
        );

        Route::put("/plans/{planId}/products", "PlansApiController@updateProducts")->middleware(
            "role:account_owner|admin|attendance"
        );
    }
);
