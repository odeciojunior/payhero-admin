<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api'],
    ],
    function() {

        Route::get('/core/verifydocuments', 'CoreApiController@verifyDocuments');

        Route::get('/core/usercompanies', 'CoreApiController@getCompanies')
        //->name('api.companies.getcompanies')
        ->middleware('role:account_owner|admin');

        Route::get('/core/companies', 'CoreApiController@companies')
        //->names('api.companies')
        ->middleware('permission:sales|finances|report_pending|apps|invitations');
    }
);

Route::group(
    [
        'middleware' => ['InternalApiAuth'],
    ],
    function() {
        Route::get('/core/get-company-balance/{company_id}', 'CoreApiController@getCompanyBalance');
    }
);

