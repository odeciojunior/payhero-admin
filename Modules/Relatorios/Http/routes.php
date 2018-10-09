<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'relatorios', 'namespace' => 'Modules\Relatorios\Http\Controllers'], function()
{
    Route::get('/vendas',[
        'as' => 'relatorios.vendas',
        'uses' => 'RelatoriosController@vendas'
    ]);

    Route::post('/vendas/data-source',[
        'as' => 'relatorios.dadosvendas',
        'uses' => 'RelatoriosController@dadosVendas'
    ]);

    Route::post('/venda/detalhe',[
        'as' => 'relatorios.vendadetalhe',
        'uses' => 'RelatoriosController@getDetalhesVenda'
    ]);

});


