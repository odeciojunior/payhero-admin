<?php

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['web', 'auth'],
        'namespace'  => 'Modules\Finances\Http\Controllers',
    ],
    function() {
        // rotas autenticadas

        Route::get('/', 'FinancesController@index')->name('finances');
        Route::get('/testFinances', 'FinancesTestController@index')->name('financesTeste');

        Route::get('/getbalances/{company_id}', 'FinancesController@getBalances')->name('finances.balances');
    }

);



