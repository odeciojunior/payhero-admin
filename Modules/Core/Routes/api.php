<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'middleware' => ['auth:api','demo_account'],
    ],
    function() {

        Route::get('/core/verifydocuments', 'CoreApiController@verifyDocuments');

        Route::get('/core/usercompanies', 'CoreApiController@getCompanies')
        //->name('api.companies.getcompanies')
        ->middleware('role:account_owner|admin');

        Route::get('/core/companies', 'CoreApiController@companies')
        //->names('api.companies')
        ->middleware('permission:sales|finances|report_pending|apps|invitations');

        Route::post('/core/company-default','CoreApiController@updateCompanyDefault');
    }
);
