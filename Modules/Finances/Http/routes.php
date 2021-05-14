<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['web', 'auth'],
        'namespace'  => 'Modules\Finances\Http\Controllers',
    ],
    function() {
        // rotas autenticadas
        Route::get('/', 'FinancesController@index')->name('finances');
        Route::get('/getbalances', 'FinancesController@getBalances')->name('finances.balances');
    }

);



