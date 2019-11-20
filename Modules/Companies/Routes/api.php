<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function() {

    Route::post('/companies/uploaddocuments', 'CompaniesApiController@uploadDocuments')
         ->name('api.companies.uploaddocuments')->middleware('role:account_owner|admin');

    Route::get('/companies/usercompanies', 'CompaniesApiController@getCompanies')->name('api.companies.getcompanies')->middleware('role:account_owner|admin');

    Route::post('/companies/opendocument', 'CompaniesApiController@openDocument');

    Route::apiResource('companies', 'CompaniesApiController')->names('api.companies')->middleware('role:account_owner|admin');
});

