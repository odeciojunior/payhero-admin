<?php

Route::group(
    [
        'middleware' => ['web', 'auth']
    ],
    function () {
        Route::apiResource('/projects', 'ProjectsApiController')
            ->only('index', 'create', 'store', 'edit');
    }
);
