<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::resource('/companies', 'CompaniesController')->only('index', 'create', 'store', 'edit', 'update', 'destroy')
         ->names('companies');
    Route::post('/companies/uploaddocuments', 'CompaniesController@uploadDocuments')->name('companies.uploaddocuments');
    Route::post('/companies/getcompanyform', 'CompaniesController@getCreateForm')
         ->name('companies.getcompanyform');
});
