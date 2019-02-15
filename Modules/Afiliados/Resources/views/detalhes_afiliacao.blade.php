@extends("layouts.master")

@section('styles')

<link rel="stylesheet" href="{{ asset('css/style.css') }}">

@endsection

@section('content')

    <!-- Page -->
    <div class="page">

        <div class="page-header">
            <h1 class="page-title">Afiliação no projeto {{ $projeto['nome'] }}</h1>
        </div>

        <div class="page-content container-fluid">
            <div class="panel pt-10 p-10" data-plugin="matchHeight" style="min-height: 400px">

                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_info_geral"
                            aria-controls="tab_info_geral" role="tab">Informações básicas</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_vendas"
                            aria-controls="tab_vendas" role="tab">Vendas</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_links"
                            aria-controls="tab_links" role="tab">Links</a></li>
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_campanhas"
                            aria-controls="tab_campanhas" role="tab">Campanhas</a></li>    
                        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_configuracoes"
                            aria-controls="tab_configuracoes" role="tab">Configurações</a></li>
                    </ul>
                    <div class="tab-content pt-20">
                        <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                            <div class="row">
                                <div class="col-lg-3 col-xl-3">
                                    <img src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 200px; width: 200px"/>
                                </div>
                                <div class="col-lg-9 col-xl-9">
                                    <table class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <td><b>Produtor</b></td>
                                                <td>{!! $produtor !!}</td>
                                            </tr>
                                            <tr>
                                                <td><b>Formato</b></td> 
                                                <td>Físico</td>
                                            <tr>
                                                <td><b>Comissão</b></td>
                                                <td>{!! $projeto['porcentagem_afiliados'] !!}%</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_vendas" role="tabpanel">
                            <h1>Tabela de vendas</h1>
                        </div>
                        <div class="tab-pane" id="tab_links" role="tabpanel"> 
                            <div class="row">
                                <div class="col-3">
                                    <img class="card-img-top img-fluid" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 150px;">
                                </div>
                                <div class="col-9">
                                    <a>
                                        <h4 class="card-title">Página principal</h4>
                                    </a>
                                    <div class="row">
                                        <div class="col-7">
                                            <hr>
                                        </div>
                                    </div>
                                    <b>Url: </b>{!! $url_pagina !!}<br>
                                </div>
                            </div>
                            <hr>
                            @foreach($planos as $plano)
                                <div class="row">
                                    <div class="col-3">
                                        <img class="card-img-top img-fluid" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PLANO.$plano['foto'] !!}" alt="Imagem não encontrada" style="height: 150px;">
                                    </div>
                                    <div class="col-9">
                                        <a>
                                            <h4 class="card-title">{!! $plano['nome'] !!}</h4>
                                            <p class="card-text">{!! $plano['descricao'] !!}</p>
                                        </a>
                                        <div class="row">
                                            <div class="col-7">
                                                <hr>
                                            </div>
                                        </div>
                                        <b>Valor: </b>R$ {!! $plano['preco'] !!}<br>
                                        <b>Seu lucro: </b>R$ {!! $plano['lucro'] !!}<br>
                                        <b>Url: </b>{!! $plano['url'] !!}<br>
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        </div>
                        <div class="tab-pane" id="tab_campanhas" role="tabpanel">
                            <h1>Campanhas</h1>
                        </div>
                        <div class="tab-pane" id="tab_configuracoes" role="tabpanel" style="padding: 50px">
                            <div class="row">
                                <div class="col-10">
                                    <label for="select_empresas">Minha empresa responsável</label>
                                    <select id="select_empresas" class="form-control">
                                        @foreach($empresas as $empresa)
                                            <option value="{!! $empresa['id'] !!}" {!! $afiliado['empresa'] == $empresa['id'] ? 'selected' : '' !!} >{!! $empresa['nome_fantasia'] !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-5">
                                    <div style="margin-top:20px">
                                        <input id="afiliado" value="{!! $afiliado['id'] !!}" type="hidden">
                                        <button id="alterar_empresa" class="btn btn-success">Alterar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
