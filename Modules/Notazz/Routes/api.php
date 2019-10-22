<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function() {

    Route::apiResource('apps/notazz', 'NotazzApiController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');

    Route::get('apps/notazz/invoice/{id}', 'NotazzApiController@getInvoice');

    Route::apiResource('apps/notazz/report', 'NotazzReportApiController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');

    Route::get('apps/notazz/export/{id}/', 'NotazzReportApiController@invoicesExport');

});
