<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::apiResource('companies', 'CompaniesApiController')->names('api.companies');
    Route::post('/companies/uploaddocuments', 'CompaniesController@uploadDocuments')
         ->name('api.companies.uploaddocuments');
});

