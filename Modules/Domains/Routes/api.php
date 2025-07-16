<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin','demo_account'],

    ],
    function () {
        // Route::apiResource('project/{projectId}/domains', 'DomainsApiController')->names('api.domains_api');
        Route::get("project/{projectId}/domains", "DomainsApiController@index");
        Route::get("project/{projectId}/domains/{domainId}", "DomainsApiController@show");
        Route::get("project/{projectId}/domains/{domainId}/edit", "DomainsApiController@edit");
        Route::post("project/{projectId}/domains", "DomainsApiController@store")->middleware(
            "permission:projects_manage"
        );
        Route::delete("project/{projectId}/domains/{domainId}", "DomainsApiController@destroy")->middleware(
            "permission:projects_manage"
        );

        Route::get("project/{projectId}/domain/{domainId}/records", "DomainRecordsApiController@index");
        Route::apiResource("project/{projectId}/domain/{domainId}/records", "DomainRecordsApiController")
            ->except(["index"])
            ->names("api.domainrecords_api")
            ->middleware("permission:projects_manage");

        Route::get("project/{projectId}/domain/{domainId}/recheck", "DomainsApiController@recheckOnly")
            ->name("api.domain.recheck")
            ->middleware("permission:projects_manage");

        // Route::get('project/{projectId}/domain/{domainId}', 'DomainsApiController@show')
        //      ->name('api.domain.show');
    }
);
