<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['web', 'auth'],
    ],
    function() {
        // rotas autenticadas
        Route::get('/', 'FinancesController@index')->name('finances')->middleware('permission:finances');
        Route::get('/{gatewayId}', 'FinancesController@show')->name('finances.show')->middleware('permission:finances');
        Route::get('/download/{filename}', 'FinancesController@download')->middleware('permission:finances_manage');
    }
);

Route::group(
    [
        'prefix'     => 'old-finances',
        'middleware' => ['web', 'auth'],
    ],
    function() {
        // rotas autenticadas
        Route::get('/', 'FinancesController@oldIndex')->name('old-finances')->middleware('permission:finances');
    }
);

