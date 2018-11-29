<?php

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'sms', 'namespace' => 'Modules\Sms\Http\Controllers'], function()
{
    Route::get('/enviarmensagem', [
        'uses' => 'SmsController@enviarMensagem',
        'as' => 'sms.enviarmensagem',
    ]);

    Route::get('/cadastro', [
        'uses' => 'SmsController@cadastro',
        'as' => 'sms.cadastro',
    ]);

    Route::get('/editar/{id}', [
        'uses' => 'SmsController@editarSms',
        'as' => 'sms.editar',
    ]);

    Route::post('/editarsms', [
        'uses' => 'SmsController@updateSms',
        'as' => 'sms.update',
    ]);

    Route::get('/deletarsms/{id}', [
        'uses' => 'SmsController@deletarSms',
        'as' => 'sms.deletar',
    ]);

    Route::post('/cadastrarsms', [
        'uses' => 'SmsController@cadastrarSms',
        'as' => 'sms.cadastrarsms',
    ]);

    Route::post('/data-source',[
        'as' => 'sms.dadossms',
        'uses' => 'SmsController@dadosSms'
    ]);

    Route::post('/detalhe',[
        'as' => 'sms.detalhes',
        'uses' => 'SmsController@getDetalhesSms'
    ]);

    Route::post('/getformaddsms',[
        'as' => 'sms.detalhes',
        'uses' => 'SmsController@getFormAddSms'
    ]);

    Route::post('/getformeditarsms',[
        'as' => 'sms.getformeditarsms',
        'uses' => 'SmsController@getFormEditarSms'
    ]);


});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'ferramentas', 'namespace' => 'Modules\Sms\Http\Controllers'], function()
{

    Route::get('/sms', [
        'uses' => 'SmsController@index',
        'as' => 'ferramentas.sms',
    ]);

});