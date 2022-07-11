<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::prefix('activecampaign')->group(function() {
//     Route::get('/', 'ActiveCampaignController@index');
// });


Route::group(['middleware' => ['web', 'auth','permission:apps']], function () {

     Route::get('apps/activecampaign', 'ActiveCampaignController@index');
     Route::get('apps/activecampaign/{id}', 'ActiveCampaignController@show');
     Route::get('apps/activecampaign/{id}/edit', 'ActiveCampaignController@edit');
     Route::Resource('apps/activecampaign', 'ActiveCampaignController')
     ->only('create', 'store', 'update', 'destroy')->middleware('permission:apps_manage');

     Route::get('apps/activecampaignevent', 'ActiveCampaignEventController@index');
     Route::get('apps/activecampaignevent/{id}', 'ActiveCampaignEventController@show');
     Route::get('apps/activecampaignevent/{id}/edit', 'ActiveCampaignEventController@edit');

     Route::Resource('apps/activecampaignevent', 'ActiveCampaignEventController')
     ->only('create', 'store', 'update', 'destroy')->middleware('permission:apps_manage');
});
