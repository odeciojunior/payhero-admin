<?php

Route::group(['middleware' => 'web', 'prefix' => 'transferencias', 'namespace' => 'Modules\Transferencias\Http\Controllers'], function()
{
    Route::get('/', 'TransferenciasController@index');
});
