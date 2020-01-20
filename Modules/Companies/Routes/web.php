<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['web', 'auth', 'setUserAsLogged']], function() {
    Route::resource('/companies', 'CompaniesController')->names('companies')->middleware('role:account_owner|admin');
    Route::post('/companies/getcompanyform', 'CompaniesController@getCreateForm')->name('companies.getcompanyform')->middleware('role:account_owner|admin');
//    Route::get('/companies/user-companies', 'CompaniesApiController@getCompanies');
});
