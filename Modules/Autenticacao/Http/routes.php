<?php

Route::group(['middleware' => 'web', 'prefix' => 'api', 'namespace' => 'Modules\Autenticacao\Http\Controllers'], function()
{
    Route::post('/login', 'AutenticacaoController@login');

    Route::post('/logout', 'AutenticacaoController@logout');

});
