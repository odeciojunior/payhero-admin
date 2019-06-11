<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'planos', 'namespace' => 'Modules\Plans\Http\Controllers'], function()
{

    Route::post('/editarplano', [
        'uses' => 'PlansController@update',
        'as' => 'planos.update',
    ]);

    Route::post('/deletarplano', [
        'uses' => 'PlansController@delete',
        'as' => 'planos.delete',
    ]);

    Route::post('/cadastrarplano', [
        'uses' => 'PlansController@store',
        'as' => 'planos.store',
    ]);

    Route::post('/data-source',[
        'as' => 'planos.index',
        'uses' => 'PlansController@index'
    ]);

    Route::post('/detalhe',[
        'as' => 'usuario.details',
        'uses' => 'PlansController@details'
    ]);

    Route::post('/getformaddplano',[
        'as' => 'usuario.create',
        'uses' => 'PlansController@create'
    ]);

    Route::post('/getformeditarplano',[
        'as' => 'usuario.edit',
        'uses' => 'PlansController@edit'
    ]);

});

/*
Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/planos', 'namespace' => 'Modules\Plans\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'PlansApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'PlansApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'PlansApiController@update',
    ]);

    Route::delete('/{id_plano}', [
        'uses' => 'PlansApiController@destroy',
    ]);

    Route::get('/{id_plano}', [
        'uses' => 'PlansApiController@show',
    ]);

});

Route::group([ 'prefix' => 'api/planos', 'namespace' => 'Modules\Plans\Http\Controllers'], function(){

    Route::get('/{cod_identificador}', [
        'uses' => 'PlansApiController@planoCheckout',
    ]);

});
*/
