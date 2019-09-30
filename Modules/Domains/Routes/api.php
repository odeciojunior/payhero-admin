<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],

    ],
    function() {
        Route::apiResource('project/{projectId}/domains', 'DomainsApiController')
             ->names('api.domains');

        Route::apiResource('project/{projectId}/domain/{domainId}/records', 'DomainRecordsApiController')
             ->names('api.domainrecords');

        Route::get('project/{projectId}/domain/{domainId}/recheck', 'DomainsApiController@recheckOnly')
             ->name('api.domain.recheck');

        // Route::get('project/{projectId}/domain/{domainId}', 'DomainsApiController@show')
        //      ->name('api.domain.show');
    }
);
