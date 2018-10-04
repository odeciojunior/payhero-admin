<?php

Route::group(['middleware' => 'web', 'prefix' => 'logs', 'namespace' => 'Modules\Logs\Http\Controllers'], function()
{
    Route::get('/',[
        'as' => 'logs',
        'uses' => 'LogsController@logs'
    ]);

    Route::post('/data-source',[
        'as' => 'logs.dadoslogs',
        'uses' => 'LogsController@dadosLogs'
    ]);

});
