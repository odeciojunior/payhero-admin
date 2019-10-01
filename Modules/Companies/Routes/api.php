<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function() {

    Route::apiResource('companies', 'CompaniesApiController')->names('api.companies');

    Route::post('/companies/uploaddocuments', 'CompaniesApiController@uploadDocuments')
         ->name('api.companies.uploaddocuments');

    Route::get('/companies/usercompanies', 'CompaniesApiController@getCompanies')->name('api.companies.getcompanies');
});

