<?php

Route::group(['middleware' => 'web', 'prefix' => 'autenticacao', 'namespace' => 'Modules\Autenticacao\Http\Controllers'], function()
{
    Route::get('/', 'AutenticacaoController@login');
});
