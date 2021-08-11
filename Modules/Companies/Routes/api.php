<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/companies/uploaddocuments', 'CompaniesApiController@uploadDocuments')
        ->name('api.companies.uploaddocuments')->middleware('role:account_owner|admin');

    Route::get('/companies/usercompanies', 'CompaniesApiController@getCompanies')->name('api.companies.getcompanies')
        ->middleware('role:account_owner|admin');

    Route::post('/companies/opendocument', 'CompaniesApiController@openDocument');

    Route::get('/companies/verify', 'CompaniesApiController@verify');

    Route::post('/companies/verifycnpj', 'CompaniesApiController@verifyCnpj');

    Route::post('/companies/consultcnpj', 'CompaniesApiController@consultCnpj');

    Route::get('/companies/checkbraspagcompany', 'CompaniesApiController@checkBraspagCompany');

    Route::get('/companies/check-statement-available', 'CompaniesApiController@checkStatementAvailable');

    Route::post('/companies/{companiId}/getdocuments', 'CompaniesApiController@getDocuments');

    //role:account_owner|admin|attendance|finantial
    Route::apiResource('companies', 'CompaniesApiController')->names('api.companies')
        ->middleware('permission:sales|finances|report_pending|apps|invitations');

    Route::post('/companies/updateorder', 'CompaniesApiController@updateOrder');
    Route::post('/companies/{company_id}/updatetax', 'CompaniesApiController@updateTax');
});

