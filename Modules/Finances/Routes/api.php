<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["auth:api", "scopes:admin", "permission:finances","demo_account"],
    ],
    function () {
        /**
         *  Old routes before ASAAS
         */
        Route::get("/finances/getbalances", "FinancesApiController@getBalances")->name("api.finances.balances");

        Route::post("/finances/export", "FinancesApiController@export")
            ->name("api.finances.export")
            ->middleware("permission:finances_manage");

        Route::get("/finances/acquirers/{companyId?}", "FinancesApiController@getAcquirers")->name(
            "api.finances.acquirers"
        );

        /**
         * News routes after getnet
         */
        Route::get("/old_finances/getbalances", "OldFinancesApiController@getBalances")->name("api.old_finances.balances");

        Route::post("/old_finances/export", "OldFinancesApiController@export")->name("api.old_finances.export");

        Route::get("/finances/get-statement-resumes/", "FinancesApiController@getStatementResume")->name(
            "finances.statement-resumes"
        );
    }
);
