<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['web', 'auth'],
    ],
    function() {
        // rotas autenticadas
        Route::get('/', 'FinancesController@index')->name('finances')->middleware('role:account_owner|admin');
    }

);
