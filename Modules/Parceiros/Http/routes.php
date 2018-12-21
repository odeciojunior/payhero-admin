<?php

Route::group(['middleware' => 'web', 'prefix' => 'parceiros', 'namespace' => 'Modules\Parceiros\Http\Controllers'], function() {

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

});

