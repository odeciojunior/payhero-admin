<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['api', 'auth:api'],
    ],
    function() {
        // rotas autenticadas
        Route::get('/getbalances', 'FinancesApiController@getBalances')->name('api.finances.balances');
    }
);
