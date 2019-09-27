<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function () {
        Route::get('/projects/user-projects', 'ProjectsController@getProjects');

        Route::Resource('/projects', 'ProjectsController')
            ->only('index', 'create', 'store', 'show', 'update', 'destroy');
    }
);
