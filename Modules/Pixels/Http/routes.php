<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'pixels', 'namespace' => 'Modules\Pixels\Http\Controllers'], function() {

    Route::post('/data-source',[
        'as' => 'pixels.index',
        'uses' => 'PixelsController@index'
    ]);
    
    Route::post('/editarpixel', [
        'uses' => 'PixelsController@update',
        'as' => 'pixels.update',
    ]);

    Route::get('/deletarpixel/{id}', [
        'uses' => 'PixelsController@delete',
        'as' => 'pixels.delete',
    ]);

    Route::post('/cadastrarpixel', [
        'uses' => 'PixelsController@store',
        'as' => 'pixels.store',
    ]);

    Route::post('/detalhe',[
        'as' => 'pixels.details',
        'uses' => 'PixelsController@details'
    ]);

    Route::get('/getformaddpixel',[
        'as' => 'pixels.create',
        'uses' => 'PixelsController@create'
    ]);

    Route::post('/getformeditarpixel',[
        'as' => 'pixels.edit',
        'uses' => 'PixelsController@edit'
    ]);

});


Route::group(['middleware' => 'auth:api', 'prefix' => 'api/projetos/{id_projeto}/pixels', 'namespace' => 'Modules\Pixels\Http\Controllers'], function(){

    Route::get('/', [
        'uses' => 'PixelsApiController@index',
    ]);

    Route::post('/', [
        'uses' => 'PixelsApiController@store',
    ]);

    Route::put('/', [
        'uses' => 'PixelsApiController@update',
    ]);

    Route::delete('/{id_pixel}', [
        'uses' => 'PixelsApiController@destroy',
    ]);

    Route::get('/{id_pixel}', [
        'uses' => 'PixelsApiController@show',
    ]);

});
