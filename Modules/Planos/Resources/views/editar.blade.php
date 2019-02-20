<div style="text-align: center">
    <h3> Editar plano </h3>
</div>

<form id="editar_plano" method="post" action="/planos/editarplano" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="{!! $plano->id !!}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <h4> Dados gerais </h4>
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="nome">Nome</label>
                        <input value="{!! $plano->nome != '' ? $plano->nome : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Nome" required>
                    </div>

                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="preco">Preço</label>
                        <input value="{!! $plano->preco != '' ? $plano->preco : '' !!}" name="preco" type="text" class="form-control dinheiro" id="preco" placeholder="Preço" required>
                    </div>

                </div>

                <div class="row">

                    <div class="form-group col-xl-12">
                        <label for="descricao">Descrição</label>
                        <input value="{!! $plano->descricao != '' ? $plano->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-12">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status" required>
                            <option value="1" {!! ($plano->status == '1') ? 'selected' : '' !!}>Ativo</option>
                            <option value="0" {!! ($plano->status == '0') ? 'selected' : '' !!}>Inativo</option>
                        </select>
                    </div>
                </div>

                {{--  <div id="div_id_plano_transportadora_editar" class="form-group col-xl-6 col-lg-6" style="{!! (!$plano->frete || $plano->transportadora == 2 ) ? 'display:none' : '' !!}">
                    <label for="id_plano_transportadora">Id da transportadora</label>
                    <input value="{!! $plano->id_plano_transportadora != '' ? $plano->id_plano_transportadora : '' !!}" name="id_plano_transportadora" type="text" class="form-control" id="id_plano_transportadora" placeholder="id da transportadora">
                </div>  --}}

                <h4> Foto do plano </h4>

                <div class="row"> 
                    <div class="form-group col-12">
                        <label for="selecionar_foto_plano_editar">Foto do plano</label><br>
                        <input type="button" id="selecionar_foto_plano_editar" class="btn btn-default" value="Alterar foto do plano">
                        <input name="foto_plano_editar" type="file" class="form-control" id="foto_plano_editar" accept="image/*" style="display:none">
                        <div  style="margin: 20px 0 0 30px;">
                            <img id="previewimage_plano_editar" src="{!! $foto !!}" alt="Selecione a foto do plano" style="max-height: 250px; max-width: 350px;"/>
                        </div> 
                        <input type="hidden" name="foto_plano_editar_x1"/>
                        <input type="hidden" name="foto_plano_editar_y1"/>
                        <input type="hidden" name="foto_plano_editar_w"/>
                        <input type="hidden" name="foto_plano_editar_h"/>
                    </div>
                </div>

                <h4> Produtos </h4>
                <div id="produtos">
                    @if(count($produtos_planos) > 0)
                        @foreach($produtos_planos as $key => $produto_plano)
                            <div id="produtos_div_{{ $key + 1 }}" class="row">
                                <div class="form-group col-xl-10">
                                    <select id="produto_{{ $key + 1 }}" name="produto_{{ $key + 1 }}" class="form-control">
                                        <option value="">Selecione</option>
                                        @foreach($produtos as $produto)
                                            <option value="{{ $produto['id'] }}"  {!! ($produto['id'] == $produto_plano['produto']) ? 'selected' : '' !!}>{{ $produto['nome'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xl-2">
                                    <input value="{!! $produto_plano['quantidade_produto'] != '' ? $produto_plano['quantidade_produto'] : '' !!}" class="form-control qtd-produtos" type="text" name="produto_qtd_{{ $key + 1 }}" placeholder="quantidade">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div id="produtos_div_1" class="row">
                            <div class="form-group col-xl-10">
                                <select id="produto_1" name="produto_1" class="form-control">
                                    <option value="" selected>Selecione</option>
                                    @foreach($produtos as $produto)
                                        <option value="{{ $produto['id'] }}">{{ $produto['nome'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-2">
                                <input class="form-control qtd-produtos" type="text" name="produto_qtd_1" placeholder="quantidade">
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_produto_plano" class="btn btn-primary">Adicionar produto</button>
                    </div>
                </div>

                @if(count($planoBrindes) > 0)
                    <h4> Brindes </h4>
                    <div id="brindes">
                        @if(count($planoBrindes) > 0)
                            @foreach($planoBrindes as $key => $planoBrinde)
                                <div id="brindes_div_{{ $key + 1 }}" class="row">
                                    <div class="form-group col-xl-12">
                                        <select id="brinde_{{ $key + 1 }}" name="brinde_{{ $key + 1 }}" class="form-control">
                                            <option value="">Selecione</option>
                                            @foreach($brindes as $brinde)
                                                <option value="{{ $brinde['id'] }}"  {!! ($brinde['id'] == $planoBrinde['brinde']) ? 'selected' : '' !!}>{{ $brinde['descricao'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div id="brindes_div_1" class="row">
                                <div class="form-group col-xl-12">
                                    <select id="brinde_1" name="brinde_1" class="form-control">
                                        <option value="" selected>Selecione</option>
                                        @foreach($brindes as $brinde)
                                            <option value="{{ $brinde['id'] }}">{{ $brinde['descricao'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="form-group col-xl-12">
                            <button type="button" id="add_brinde" class="btn btn-primary">Adicionar brinde</button>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</form>
