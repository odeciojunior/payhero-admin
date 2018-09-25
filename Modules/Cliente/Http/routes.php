<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'cliente', 'namespace' => 'Modules\Cliente\Http\Controllers'], function()
{
    Route::get('/', 'ClienteController@index');
    
});
