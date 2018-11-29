<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'ferramentas', 'namespace' => 'Modules\Ferramentas\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'FerramentasController@index',
        'as' => 'ferramentas'
    ]);
});
