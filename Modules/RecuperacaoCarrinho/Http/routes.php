<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'recuperacaocarrinho', 'namespace' => 'Modules\RecuperacaoCarrinho\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'RecuperacaoCarrinhoController@index',
        'as' => 'vendas.recuperacaocarrinho' 
    ]);

    Route::post('/data-source',[
        'as' => 'vendas.dadoscarrinhosabandonados',
        'uses' => 'RecuperacaoCarrinhoController@dadosCarrinhosAbandonados'
    ]);

    Route::post('/opcoes',[
        'as' => 'carrinhosabandonados.opcoes',
        'uses' => 'RecuperacaoCarrinhoController@opcoes'
    ]);

});
