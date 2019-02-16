<div class="nav-tabs-horizontal" data-plugin="tabs">
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_links"
            aria-controls="tab_links" role="tab">URLs</a></li>
        <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_pixels"
            aria-controls="tab_pixels" role="tab">Pixels</a></li>
    </ul>
    <div class="tab-content pt-20">
        <div class="tab-pane active" id="tab_links" role="tabpanel">
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
                    {{--  <b>Seu lucro: </b>R$ {!! $plano['lucro'] !!}<br>  --}}
                    <b>Url: </b>{!! $plano['url'] !!}<br>
                    {{--  <b>Vendas: </b> 0<br>  --}}
                </div>
            </div>
            <hr>
            @endforeach
        </div>
        <div class="tab-pane" id="tab_pixels" role="tabpanel">
            <div class="row" style="margin-bottom: 30px">
                <div class="col-9">
                </div>
                <div class="col-3">
                    <button id="bt_adicionar_pixel" class="btn btn-primary" style="color: white">
                        <i class='icon wb-plugin' aria-hidden='true'></i>
                        Adicionar pixel
                    </button>
                </div>
            </div>
            @if(count($pixels) < 1)
                <div class="alert alert-success">
                    <p>Nenhum pixel configurado</p>
                </div>
            @endif
            <form id='form_adicionar_pixel' style="display:none">
                @csrf
                <input type="hidden" name="campanha" value="{!! $id_campanha !!}">
                <div class="row">
                    <div class="form-group col-6">
                        <label for="nome">Descrição</label>
                        <input name="nome" type="text" class="form-control" id="nome" placeholder="Descrição">
                    </div>

                    <div class="form-group col-6">
                        <label for="cod_pixel">Código</label>
                        <input name="cod_pixel" type="text" class="form-control" id="cod_pixel" placeholder="Código">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        <label for="plataforma">Plataforma</label>
                        <select name="plataforma" type="text" class="form-control" id="plataforma">
                            <option value="facebook">Facebook</option>
                            <option value="google">Google</option>
                            <option value="taboola">Taboola</option>
                            <option value="outbrain">Outbrain</option>
                        </select>
                    </div>

                    <div class="form-group col-6">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status_pixel">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <button id="cadastrar_pixel" type="button" class="btn btn-success">Salvar</button>
                </div>
                <hr style="margin-bottom: 30px">
            </form>
            <table id="tabela_vendas_afiliado" class="table table-hover table-bordered table-stripped w-full">
                <thead>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Plataforma</th>
                    <th>Status</th>
                    <th style="width:160px">Excluir</th>
                </thead>
                <tbody>
                    @foreach($pixels as $pixel)
                        <tr>
                            <td>{!! $pixel['nome'] !!}</td>
                            <td>{!! $pixel['cod_pixel'] !!}</td>
                            <td>{!! $pixel['plataforma'] !!}</td>
                            <td>{!! ($pixel['status']) ? 'Ativo' : 'Desativado' !!}</td>
                            <td>
                                <a class='btn btn-outline btn-danger excluir_pixel' data-placement='top' data-toggle='tooltip' title='Excluir' pixel='{!! $pixel['id'] !!}'>
                                    <i class='icon wb-trash' aria-hidden='true'></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
