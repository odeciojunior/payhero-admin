<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["middleware" => "authApiV1"], function () {
    // SUBSELLERS
    Route::post("subsellers", "V1\SubsellersApiController@createSubsellers");
    Route::put("subsellers/{id}", "V1\SubsellersApiController@updateSubsellers");
    Route::put("subsellers/{id}/send-documents", "V1\SubsellersApiController@sendDocumentsSubsellers");
    Route::get("subsellers/{id}", "V1\SubsellersApiController@showSubsellers");
    Route::get("subsellers", "V1\SubsellersApiController@getSubsellers");

    // COMPANIES
    Route::post("companies", "V1\CompaniesApiController@createCompanies");
    Route::put("companies/{id}", "V1\CompaniesApiController@updateCompanies");
    Route::put("companies/{id}/send-documents", "V1\CompaniesApiController@sendDocumentsCompanies");
    Route::get("companies/{id}", "V1\CompaniesApiController@showCompanies");
    Route::get("companies", "V1\CompaniesApiController@getCompanies");

    // SALES
    Route::get("sales", "V1\SalesApiController@getSales");
    Route::post("sales/refund/{id}", "V1\SalesApiController@refundSales");

    // WITHDRAWALS
    Route::post("withdrawals", "V1\WithdrawalsApiController@storeWithdrawals");
});
