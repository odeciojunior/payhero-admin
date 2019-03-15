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

    Route::post('/getformeditarsms',[
        'as' => 'sms.getformeditarsms',
        'uses' => 'SmsController@getFormEditarSms'
    ]);

    Route::post('/detalhescompra',[
        'as' => 'sms.detalhescompra',
        'uses' => 'SmsController@detalhesCompra'
    ]);

    Route::post('/dadosmensagens',[
        'as' => 'sms.dadosmensagens',
        'uses' => 'SmsController@dadosMensagens'
    ]);

    Route::post('/enviarsmsmanual',[
        'as' => 'sms.enviarsmsmanual',
        'uses' => 'SmsController@enviarSmsManual'
    ]);

});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'ferramentas', 'namespace' => 'Modules\Sms\Http\Controllers'], function()
{
    Route::get('/sms', [
        'uses' => 'SmsController@index',
        'as' => 'ferramentas.sms',
    ]);
    
});

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'atendimento', 'namespace' => 'Modules\Sms\Http\Controllers'], function()
{
    Route::get('/sms', [
        'uses' => 'SmsController@smsAtendimento',
        'as' => 'atendimento.sms',
    ]);

});

Route::group(['middleware' => 'auth:api', 'prefix' => 'api/atendimento/sms', 'namespace' => 'Modules\Sms\Http\Controllers'], function()
{
    Route::get('/', [
        'uses' => 'SmsApiController@index',
    ]);

});


