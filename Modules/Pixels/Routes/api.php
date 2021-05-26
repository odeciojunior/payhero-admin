<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function () {
        Route::get("/projects/{projectId}/pixels/configs", 'PixelsApiController@getPixelConfigs');
        Route::post("/projects/{projectId}/pixels/saveconfigs", 'PixelsApiController@storePixelConfigs');
        Route::apiResource('/project/{projectId}/pixels', 'PixelsApiController');
    }
);
