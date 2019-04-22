<div class="row">
        <div class="col-3">
            <img class="card-img-top img-fluid w-full" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
        </div>
        <div class="col-9">
            <h3>{!! $projeto['nome'] !!}</h3>
            <h4>{!! $projeto['descricao'] !!}</h4>
            <hr>
            <div class="nav-tabs-horizontal" data-plugin="tabs">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_info_geral"
                        aria-controls="tab_info_geral" role="tab">Informações básicas</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_afiliados"
                        aria-controls="tab_afiliados" role="tab">Afiliados</a></li>
                </ul>
                <div class="tab-content pt-20">
                    <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                        <b>Página: </b>-<br>
                        <b>Formato: </b>Físico<br>
                        <b>Comissão: </b>{!! $projeto['porcentagem_afiliados'] !!}%<br>
                    </div>
                    <div class="tab-pane" id="tab_afiliados" role="tabpanel">
                        @if(count($afiliados) == 0)
                            <b> Nenhum afiliado </b>
                        @endif
                        @foreach($afiliados as $afiliado)
                            <b>Afiliado: </b> {!! $afiliado['nome'] !!}<br>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    