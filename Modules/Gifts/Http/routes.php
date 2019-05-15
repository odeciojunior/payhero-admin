<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'brindes', 'namespace' => 'Modules\Gifts\Http\Controllers'], function()
{

    Route::post('/data-source',[
        'as' => 'brindes.index',
        'uses' => 'GiftsController@index'
    ]);

    Route::post('/editarbrinde', [
        'uses' => 'GiftsController@update',
        'as' => 'brindes.update',
    ]);

    Route::post('/deletarbrinde', [
        'uses' => 'GiftsController@delete',
        'as' => 'brindes.delete',
    ]);

    Route::post('/cadastrarbrinde', [
        'uses' => 'GiftsController@create',
        'as' => 'brindes.create',
    ]);

    Route::post('/detalhe',[
        'as' => 'brindes.details',
        'uses' => 'GiftsController@details'
    ]);

    Route::get('/getformaddbrinde',[
        'as' => 'brindes.create',
        'uses' => 'GiftsController@create'
    ]);

    Route::post('/getformeditarbrinde',[
        'as' => 'brindes.edit',
        'uses' => 'GiftsController@edit'
    ]);

});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/brindes', 'namespace' => 'Modules\Gifts\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'GiftsApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'GiftsApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'GiftsApiController@update',
    ]);

    Route::delete('/{id_brinde}', [
        'uses' => 'GiftsApiController@destroy',
    ]);

    Route::get('/{id_brinde}', [
        'uses' => 'GiftsApiController@show',
    ]);

});