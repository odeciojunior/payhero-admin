<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:api']], function() {
    //teste
    Route::apiResource('companies', 'CompaniesApiController')->names('api.companies');

    Route::post('/companies/uploaddocuments', 'CompaniesController@uploadDocuments')
         ->name('api.companies.uploaddocuments');

    Route::get('/companies/usercompanies', 'CompaniesApiController@getCompanies')->name('api.companies.getcompanies');
});

