<?php

/*
Route::group(['middleware' => ['web', 'auth'], 'namespace' => 'Modules\Companies\Http\Controllers'], function() {

    Route::resource('/companies', 'CompaniesController');

    Route::get('/', [
        'uses' => 'CompaniesController@index',
        'as'   => 'companies',
    ]);

    Route::get('/cadastro', [
        'uses' => 'CompaniesController@create',
        'as'   => 'companies.create',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'CompaniesController@edit',
        'as'   => 'companies.edit',
    ]);

    Route::post('/editarempresa', [
        'uses' => 'CompaniesController@update',
        'as'   => 'companies.update',
    ]);

    Route::get('/deletarempresa/{id}', [
        'uses' => 'CompaniesController@delete',
        'as'   => 'companies.delete',
    ]);

    // Route::post('/cadastrarempresa', [
    //     'uses' => 'CompaniesController@store',
    //     'as' => 'companies.store',
    // ]);

    Route::post('/data-source', [
        'as'   => 'companies.getCompaniesData',
        'uses' => 'CompaniesController@getCompaniesData',
    ]);

    Route::post('/detalhe', [
        'as'   => 'usuario.details',
        'uses' => 'CompaniesController@details',
    ]);

    Route::get("/getformcadastrarempresa/{country}", [
        'uses' => 'CompaniesController@getCreateForm',
    ]);
});*/
/*
Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'api', 'namespace' => 'Modules\Companies\Http\Controllers'], function() {

    Route::resource('/companies', 'CompaniesApiController')->names('api.companies');
    /*
    Route::get('/companies/index', [
        'uses' => 'CompaniesApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'CompaniesApiController@create',
    ]);

    Route::put('/', [
        'uses' => 'CompaniesApiController@update',
    ]);

    Route::delete('/{id}', [
        'uses' => 'CompaniesApiController@delete',
    ]);

    Route::get('/{id}', [
        'uses' => 'CompaniesApiController@show',
    ]);

    Route::get('/getbancos', [
        'uses' => 'CompaniesApiController@getBancos',
    ]);
    */
