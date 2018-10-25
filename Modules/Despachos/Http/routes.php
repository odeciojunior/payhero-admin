<?php

Route::group(['middleware' => 'web', 'prefix' => 'despachos', 'namespace' => 'Modules\Despachos\Http\Controllers'], function()
{
    Route::get('/',[
        'as' => 'despachos',
        'uses' => 'DespachosController@index'
    ]);

    Route::get('/atualizaentregas',[
        'as' => 'despachos.atualiza',
        'uses' => 'DespachosController@atualizaEntregas'
    ]);

    Route::post('/data-source',[
        'as' => 'despachos.dadosdespachos',
        'uses' => 'DespachosController@dadosDespachos'
    ]);

    Route::post('/detalhe',[
        'as' => 'despachos.detalhe',
        'uses' => 'DespachosController@getDetalhesDespacho'
    ]);

});


