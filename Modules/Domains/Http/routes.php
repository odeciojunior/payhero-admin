<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Domains\Http\Controllers'], function() {
    Route::resource("/domains", "DomainsController")
         ->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy')
         ->names('domain');

    Route::post("/domains/deleterecord", "DomainsController@destroyRecord")
         ->name('domain.destroy.record');

    Route::post("/domains/recheck", "DomainsController@recheckDomain")
         ->name('domain.recheck');

    Route::get('/domains/getDomainData/{domainId}', "DomainsController@getDomainData")
        ->name('domain.getDomainData');
});

