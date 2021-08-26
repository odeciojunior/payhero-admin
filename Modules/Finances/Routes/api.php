<?php

use Illuminate\Support\Facades\Route;


Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin'],
    ],
    function () {
        /**
         *  Old routes before ASAAS
         */
        Route::get('/finances/getbalances', 'FinancesApiController@getBalances')
            ->name('api.finances.balances')
            ->middleware('role:account_owner|admin');

        Route::post('/finances/export', 'FinancesApiController@export')
            ->name('api.finances.export')
            ->middleware('role:account_owner|admin');

        /**
         * News routes after getnet
         */
        Route::get('/old_finances/getbalances', 'OldFinancesApiController@getBalances')
            ->name('api.finances.balances')
            ->middleware('role:account_owner|admin');

        Route::post('/old_finances/export', 'OldFinancesApiController@export')
            ->name('api.finances.export')
            ->middleware('role:account_owner|admin');
    }
);
