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

