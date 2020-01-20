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


Route::group(['middleware' => ['web', 'auth', 'setUserAsLogged']], function() {

    Route::Resource('apps/activecampaign', 'ActiveCampaignController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');

    Route::Resource('apps/activecampaignevent', 'ActiveCampaignEventController')
         ->only('index', 'create', 'store', 'edit', 'update', 'show', 'destroy');
});
