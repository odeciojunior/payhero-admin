<?php

Route::group(['middleware' => ['web', 'auth', 'scopes:admin'], 'prefix' => '', 'namespace' => 'Modules\Partners\Http\Controllers'], function() {
    Route::Resource('/partners', 'PartnersController')
         ->only('index', 'create', 'store', 'show', 'edit', 'update', 'destroy');

    /* Route::post('/data-source',[
         'as' => 'parceiros.index',
         'uses' => 'PartnersController@index'
     ]);*/

    /* Route::get('/getformaddparceiro', [
         'as'   => 'parceiros.create',
         'uses' => 'PartnersController@create',
     ]);*/
    /*Route::post('/getformeditarparceiro', [
        'as'   => 'parceiros.edit',
        'uses' => 'PartnersController@edit',
    ]);

   /* Route::post('/cadastrarparceiro', [
        'as'   => 'parceiros.store',
        'uses' => 'PartnersController@store',
    ]);

    Route::post('/detalhesparceiro', [
        'as'   => 'parceiros.details',
        'uses' => 'PartnersController@details',
    ]);

    Route::post('/editarparceiro', [
        'as'   => 'parceiros.edit',
        'uses' => 'PartnersController@edit',
    ]);

    Route::post('/removerparceiro', [
        'as'   => 'parceiros.delete',
        'uses' => 'PartnersController@delete',
    ]);*/
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/parceiros', 'namespace' => 'Modules\Partners\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'PartnersApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'PartnersApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'PartnersApiController@update',
    ]);

    Route::delete('/{id_parceiro}', [
        'uses' => 'PartnersApiController@destroy',
    ]);

    Route::get('/{id_parceiro}', [
        'uses' => 'PartnersApiController@show',
    ]);
});
