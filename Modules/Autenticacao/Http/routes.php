<?php

Route::group(['middleware' => 'web', 'prefix' => 'api/login', 'namespace' => 'Modules\Autenticacao\Http\Controllers'], function()
{
    Route::post('/', 'AutenticacaoController@login');
});
