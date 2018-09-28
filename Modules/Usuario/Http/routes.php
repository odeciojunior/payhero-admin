<?php

Route::group(['middleware' => 'web', 'prefix' => 'usuario', 'namespace' => 'Modules\Usuario\Http\Controllers'], function()
{
    Route::get('/', 'UsuarioController@index');
});
