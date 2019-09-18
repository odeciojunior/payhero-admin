<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => '', 'namespace' => 'Modules\Projects\Http\Controllers'], function() {

    Route::get('/projects/user-projects', 'ProjectsController@getProjects');

    Route::Resource('/projects', 'ProjectsController')
         ->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');

});
