<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth']], function() {
    Route::resource('/companies', 'CompaniesController')->names('companies');
    Route::post('/companies/getcompanyform', 'CompaniesController@getCreateForm')->name('companies.getcompanyform');
//    Route::get('/companies/user-companies', 'CompaniesApiController@getCompanies');
});
