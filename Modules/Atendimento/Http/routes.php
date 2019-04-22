<?php

Route::group(['middleware' => 'web', 'prefix' => 'atendimento', 'namespace' => 'Modules\Atendimento\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'AtendimentoController@index',
        'as' => 'atendimento.index'
    ]);
    
});
