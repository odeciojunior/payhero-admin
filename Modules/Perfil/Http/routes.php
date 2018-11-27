<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'perfil', 'namespace' => 'Modules\Perfil\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'PerfilController@index',
        'as'   => 'perfil'
    ]);

    Route::post('/update', [
        'uses' => 'PerfilController@update',
        'as'   => 'perfil.update'
    ]);

});
