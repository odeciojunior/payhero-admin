<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api', 'scopes:admin','demo_account'],
    ],
    function () {
        /**
         * Routes after getnet
         */
        Route::get('/transfers', 'TransfersApiController@index');

        Route::get('/transfers/account-statement-data', 'TransfersApiController@accountStatementData');
        Route::post('/transfers/account-statement-data/export', 'TransfersApiController@accountStatementDataExport');

        /**
         * Old routes before getnet
         */
        Route::get('/old_transfers', 'OldTransfersApiController@index');
    }
);
