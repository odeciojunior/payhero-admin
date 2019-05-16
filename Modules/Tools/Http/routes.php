<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'ferramentas', 'namespace' => 'Modules\Tools\Http\Controllers'], function()
{
    Route::get('/',[
        'uses' => 'ToolsController@index',
        'as' => 'tools'
    ]);
});
