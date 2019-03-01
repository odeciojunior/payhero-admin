<?php

Route::group(['middleware' => 'web', 'prefix' => 'notificacoes', 'namespace' => 'Modules\Notificacoes\Http\Controllers'], function()
{
    Route::get('/', 'NotificacoesController@index');

    Route::post('/markasread', 'NotificacoesController@markasread');
});
