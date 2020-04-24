<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['auth:api', 'scopes:admin', 'setUserAsLogged'],
    ],
    function() {
        // rotas autenticadas
        Route::get('/getbalances', 'FinancesApiController@getBalances')->name('api.finances.balances')->middleware('role:account_owner|admin');
        Route::post('/export', 'FinancesApiController@export')->name('api.finances.export')->middleware('role:account_owner|admin');
    }
);
