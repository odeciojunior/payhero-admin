<?php

Route::group(['middleware' => 'web', 'prefix' => 'aplicativos', 'namespace' => 'Modules\Aplicativos\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'AplicativosController@index',
        'as' => 'aplicativos'
    ]);
});
