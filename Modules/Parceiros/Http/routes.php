<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'parceiros', 'namespace' => 'Modules\Parceiros\Http\Controllers'], function() {

    Route::get('/', 'ParceirosController@index');

    Route::post('/data-source',[
        'as' => 'parceiros.dados',
        'uses' => 'ParceirosController@dadosParceiros'
    ]);

    Route::get('/getformaddparceiro',[
        'as' => 'parceiros.getformaddparceiro',
        'uses' => 'ParceirosController@getFormAddParceiro'
    ]);

    Route::post('/getformeditarparceiro',[
        'as' => 'parceiros.getformaddparceiro',
        'uses' => 'ParceirosController@getFormEditarParceiro'
    ]);

    Route::post('/cadastrarparceiro',[
        'as' => 'parceiros.cadastrarparceiro',
        'uses' => 'ParceirosController@cadastrarParceiro'
    ]);

    Route::post('/detalhesparceiro',[
        'as' => 'parceiros.detalhesparceiro',
        'uses' => 'ParceirosController@detalhesParceiro'
    ]);

    Route::post('/editarparceiro',[
        'as' => 'parceiros.editarparceiro',
        'uses' => 'ParceirosController@editarParceiro'
    ]);

    Route::post('/removerparceiro',[
        'as' => 'parceiros.removerparceiro',
        'uses' => 'ParceirosController@removerParceiro'
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/parceiros', 'namespace' => 'Modules\Parceiros\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'ParceirosApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'ParceirosApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'ParceirosApiController@update',
    ]);

    Route::delete('/{id_parceiro}', [
        'uses' => 'ParceirosApiController@destroy',
    ]);

    Route::get('/{id_parceiro}', [
        'uses' => 'ParceirosApiController@show',
    ]);

});
