<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Domains\Http\Controllers'], function()
{
    Route::resource("/domains", "DomainsController")
    ->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');

});

