<?php

Route::group(['middleware' => ['web','auth'], 'prefix' => 'pixels', 'namespace' => 'Modules\Pixels\Http\Controllers'], function() {

    Route::get('/', [
        'uses' => 'PixelsController@index',
        'as' => 'pixels',
    ]);

    Route::get('/cadastro', [
        'uses' => 'PixelsController@cadastro',
        'as' => 'pixels.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'PixelsController@editarPixel',
        'as' => 'pixels.editar',
    ]);

    Route::post('/editarpixel', [
        'uses' => 'PixelsController@updatePixel',
        'as' => 'pixels.update',
    ]);

    Route::get('/deletarpixel/{id}', [
        'uses' => 'PixelsController@deletarPixel',
        'as' => 'pixels.deletar',
    ]);

    Route::post('/cadastrarpixel', [
        'uses' => 'PixelsController@cadastrarPixel',
        'as' => 'pixels.cadastrarpixel',
    ]);

    Route::post('/data-source',[
        'as' => 'pixels.dadospixels',
        'uses' => 'PixelsController@dadosPixels'
    ]);

    Route::post('/detalhe',[
        'as' => 'pixels.detalhes',
        'uses' => 'PixelsController@getDetalhesPixel'
    ]);

    Route::get('/getformaddpixel',[
        'as' => 'pixels.detalhes',
        'uses' => 'PixelsController@getFormAddPixel'
    ]);

    Route::post('/getformeditarpixel',[
        'as' => 'pixels.getformeditarpixel',
        'uses' => 'PixelsController@getFormEditarPixel'
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
