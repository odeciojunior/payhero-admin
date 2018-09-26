<?php

Route::group(['middleware' => 'web', 'prefix' => 'relatorios', 'namespace' => 'Modules\Relatorios\Http\Controllers'], function()
{
    Route::get('/vendas',[
        'as' => 'relatorios.vendas',
        'uses' => 'RelatoriosController@vendas'
    ]);
});


