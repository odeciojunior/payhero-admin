<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'perfil', 'namespace' => 'Modules\Perfil\Http\Controllers'], function()
{
    Route::get('/', 'PerfilController@index');
});
