<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['web', 'auth'],
    ],
    function() {
        // rotas autenticadas
        Route::get('/', 'FinancesController@index')->name('finances')->middleware('role:account_owner|admin|finantial');
        Route::get('/download/{filename}', 'FinancesController@download')->middleware('role:account_owner|admin|finantial');
    }
);

Route::group(
    [
        'prefix'     => 'old-finances',
        'middleware' => ['web', 'auth'],
    ],
    function() {
        // rotas autenticadas
        Route::get('/', 'FinancesController@oldIndex')->name('old-finances')->middleware('role:account_owner|admin|finantial');
    }
);

