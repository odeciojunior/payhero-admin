<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => '', 'namespace' => 'Modules\Pixels\Http\Controllers'], function() {

    Route::Resource('/pixels', 'PixelsController')
         ->only('edit', 'create', 'index', 'show', 'update', 'destroy', 'store');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/pixels', 'namespace' => 'Modules\Pixels\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'PixelsApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'PixelsApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'PixelsApiController@update',
    ]);

    Route::delete('/{id_pixel}', [
        'uses' => 'PixelsApiController@destroy',
    ]);

    Route::get('/{id_pixel}', [
        'uses' => 'PixelsApiController@show',
    ]);
});
