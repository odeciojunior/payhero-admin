<?php

Route::group(['middleware' => 'web', 'prefix' => 'login', 'namespace' => 'Modules\Autenticacao\Http\Controllers'], function()
{
    Route::post('/', 'AutenticacaoController@login');
});
