<?php

use Illuminate\Support\Facades\Route;


Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin','permission:finances'],
    ],
    function () {
        /**
         *  Old routes before ASAAS
         */
        Route::get('/finances/getbalances', 'FinancesApiController@getBalances')
            ->name('api.finances.balances');

        Route::post('/finances/export', 'FinancesApiController@export')
            ->name('api.finances.export')
            ->middleware('permission:finances_manage');
        Route::get('/finances/get-statement-resumes', 'FinancesApiController@getStatementResume')->name('finances.statement-resumes');


        /**
         * News routes after getnet
         */
        Route::get('/old_finances/getbalances', 'OldFinancesApiController@getBalances')
            ->name('api.finances.balances');

        Route::post('/old_finances/export', 'OldFinancesApiController@export')
            ->name('api.finances.export');
    }
);
