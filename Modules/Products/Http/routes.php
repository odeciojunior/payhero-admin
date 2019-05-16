<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'produtos', 'namespace' => 'Modules\Products\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'ProductsController@index',
        'as' => 'products',
    ]);

    Route::get('/cadastro', [
        'uses' => 'ProductsController@create',
        'as' => 'products.create',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'ProductsController@edit',
        'as' => 'products.edit',
    ]);

    Route::post('/editarproduto', [
        'uses' => 'ProductsController@update',
        'as' => 'products.update',
    ]);

    Route::get('/deletarproduto/{id}', [
        'uses' => 'ProductsController@delete',
        'as' => 'products.delete',
    ]);

    Route::post('/cadastrarproduto', [
        'uses' => 'ProductsController@store',
        'as' => 'products.store',
    ]);

    Route::post('/detalhe',[
        'as' => 'products.details',
        'uses' => 'ProductsController@details'
    ]);
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
