<?php

Route::group(['middleware' => 'web', 'prefix' => 'login', 'namespace' => 'Modules\Autenticacao\Http\Controllers'], function()
{
    Route::get('/', 'AutenticacaoController@login');
});
