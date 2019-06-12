<?php

Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Products\Http\Controllers'], function() {

    Route::resource('/products', 'ProductsController')->only('index', 'create', 'store', 'edit', 'update', 'destroy');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/produtos', 'namespace' => 'Modules\Products\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'ProductsApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'ProductsApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'ProductsApiController@update',
    ]);

    Route::delete('/{id_produto}', [
        'uses' => 'ProductsApiController@destroy',
    ]);

    Route::get('/{id_produto}', [
        'uses' => 'ProductsApiController@show',
    ]);
});
