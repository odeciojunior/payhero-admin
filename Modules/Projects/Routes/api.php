<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function () {
        Route::get('/projects/user-projects', 'ProjectsApiController@getProjects');

        Route::apiResource('/projects', 'ProjectsApiController')
            ->only('index', 'create', 'store', 'edit', 'destroy', 'update', 'show');
    }
);
