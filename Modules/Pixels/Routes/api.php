<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function () {
        Route::get("/projects/{projectId}/pixels/configs", 'PixelsApiController@getPixelConfigs')
            ->name('pixels.getconfig');
        Route::post("/projects/{projectId}/pixels/saveconfigs", 'PixelsApiController@storePixelConfigs')
            ->name('pixels.saveconfig');

        Route::apiResource('/project/{projectId}/pixels', 'PixelsApiController')
            ->only('index', 'show', 'edit', 'update', 'destroy', 'store')
            ->names("pixels");
    }
);
