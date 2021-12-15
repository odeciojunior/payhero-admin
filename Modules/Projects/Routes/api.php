<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function() {
        Route::get('/projects/user-projects', 'ProjectsApiController@getProjects')
             ->middleware('role:account_owner|admin|attendance');

        Route::apiResource('/projects', 'ProjectsApiController')
             ->only('index', 'create', 'store', 'edit', 'destroy', 'update', 'show')
             ->middleware('role:account_owner|admin|attendance');

        Route::post('/projects/updateorder', 'ProjectsApiController@updateOrder')
             ->middleware('role:account_owner|admin');

        Route::post('/projects/updateconfig', 'ProjectsApiController@updateConfig')
             ->middleware('role:account_owner|admin');
    }
);
