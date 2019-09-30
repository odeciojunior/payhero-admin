<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function () {

        Route::Resource('/projects', 'ProjectsController')
            ->only('index', 'create', 'show');
    }
);
