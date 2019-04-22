<?php

Route::group(['middleware' => 'web', 'prefix' => 'apiteste', 'namespace' => 'Modules\ApiTeste\Http\Controllers'], function()
{
    Route::get('/', 'ApiTesteController@index');
});
