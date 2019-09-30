<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function () {
        Route::get('/projects/user-projects', 'ProjectsApiController@getProjects');

        Route::apiResource('/projects', 'ProjectsApiController')
            ->only('index', 'create', 'store', 'edit', 'destroy', 'update', 'show');
    }
);
