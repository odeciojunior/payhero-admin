<?php

Route::group(
    [
        'middleware' => ['web', 'auth', 'setUserAsLogged']
    ],
    function () {

        Route::Resource('/projects', 'ProjectsController')
            ->only('index', 'create', 'show')->middleware('role:account_owner|admin');
    }
);
