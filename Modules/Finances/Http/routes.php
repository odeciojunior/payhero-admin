<?php

Route::group(
    [
        'prefix'     => 'finances',
        'middleware' => ['web','auth'],
        'namespace' => 'Modules\Finances\Http\Controllers'
    ],
    function() {
        // rotas autenticadas

        Route::get('/', 'WithdrawalController@index')->name('finances');

    }
);

