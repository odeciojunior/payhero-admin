<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function () {
        Route::get('/projects/user-projects', 'ProjectsApiController@getProjects')
            ->middleware('permission:projects|apps');

        //role:account_owner|admin|attendance|finantial
        Route::get('/projects', 'ProjectsApiController@index');
        Route::get('/projects/create', 'ProjectsApiController@create');
        Route::get('/projects/{id}', 'ProjectsApiController@show');
        Route::get('/projects/{id}/edit', 'ProjectsApiController@edit');

        Route::apiResource('/projects', 'ProjectsApiController')
            ->only('store', 'destroy', 'update')->middleware('permission:projects_manage');

        Route::post('/projects/updateorder', 'ProjectsApiController@updateOrder')
            ->middleware('permission:projects_manage');

        Route::post('/projects/updateconfig', 'ProjectsApiController@updateConfig')
            ->middleware('permission:projects_manage');
    }
);
