<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['web', 'auth'],
    ],
    function() {
        // rotas autenticadas
        Route::get('/getbalances', 'FinancesApiController@getBalances')->name('api.finances.balances');
    }
);
