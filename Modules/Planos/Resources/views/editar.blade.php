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
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status" required>
                            <option value="1" {!! ($plano->status == '1') ? 'selected' : '' !!}>Ativo</option>
                            <option value="0" {!! ($plano->status == '0') ? 'selected' : '' !!}>Inativo</option>
                        </select>
                    </div>
                </div>

                <h4> Configurações do frete </h4>

                <div class="row">
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="frete_plano_editar">Possui frete</label>
                        <select name="frete" type="text" class="form-control" id="frete_plano_editar" required>
                            <option value="1" {!! ($plano->frete == '1') ? 'selected' : '' !!}>Sim</option>
                            <option value="0" {!! ($plano->frete == '0') ? 'selected' : '' !!}>Não</option>
                        </select>
                    </div>
                    <div id="div_frete_fixo_editar" class="form-group col-xl-6 col-lg-6" style="{!! !$plano->frete ? 'display:none' : '' !!}">
                        <label for="frete_fixo_plano_editar">Frete fixo</label>
                        <select name="frete_fixo" type="text" class="form-control" id="frete_fixo_plano_editar" required>
                            <option value="1" {!! ($plano->frete_fixo == '1') ? 'selected' : '' !!}>Sim</option>
                            <option value="0" {!! ($plano->frete_fixo == '0') ? 'selected' : '' !!}>Não</option>
                        </select>
                    </div>
                    <div id="div_valor_frete_fixo_editar" class="form-group col-xl-6 col-lg-6" style="{!! (!$plano->frete || !$plano->frete_fixo ) ? 'display:none' : '' !!}">
                        <label for="valor_frete_editar">Valor frete fixo</label>
                        <input value="{!! $plano->valor_frete != '' ? $plano->valor_frete : '' !!}" name="valor_frete" type="text" class="form-control dinheiro" id="valor_frete_editar" placeholder="valor fixo">
                    </div>
                </div>

                <div class="row">
                    <div id="div_transportadora_editar" class="form-group col-xl-6 col-lg-6" style="{!! !$plano->frete ? 'display:none' : '' !!}">
                        <label for="transportadora_plano_editar">Transportadora</label>
                        <select name="transportadora" type="text" class="form-control" id="transportadora_plano_editar" required>
                            @foreach($transportadoras as $transportadora)
                                <option value="{{ $transportadora['id'] }}" {!! ($plano->transportadora == $transportadora['id']) ? 'selected' : '' !!}>{{ $transportadora['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="div_responsavel_frete_editar" class="form-group col-xl-6 col-lg-6" style="{!! !$plano->frete ? 'display:none' : '' !!}">
                        <label for="responsavel_frete_editar">Responsável pelo frete</label>
                        <select name="responsavel_frete" type="text" class="form-control" id="responsavel_frete_editar">
                            <option value="proprietario" {!! $plano->responsavel_frete == 'proprietario' ? 'selected' : '' !!}>Proprietário</option>
                            <option value="parceiros" {!! $plano->responsavel_frete == 'parceiros' ? 'selected' : '' !!}>Proprietário + parceiros</option>
                        </select>
                    </div>
                    <div id="div_id_plano_transportadora_editar" class="form-group col-xl-6 col-lg-6" style="{!! (!$plano->frete || $plano->transportadora == 2 ) ? 'display:none' : '' !!}">
                        <label for="id_plano_transportadora">Id da transportadora</label>
                        <input value="{!! $plano->id_plano_transportadora != '' ? $plano->id_plano_transportadora : '' !!}" name="id_plano_transportadora" type="text" class="form-control" id="id_plano_transportadora" placeholder="id da transportadora">
                    </div>
                </div>

                <h4> Layout do checkout </h4>

                <div class="row">
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="layout">Layout</label>
                        <select name="layout" type="text" class="form-control" id="layout" required>
                            <option value="">Layout padrão</option>
                            @foreach($layouts as $layout)
                                <option value="{{ $layout['id'] }}" {!! ($plano->layout == $layout['id']) ? 'selected' : '' !!}>{{ $layout['descricao'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

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

                <h4> Pixels </h4>
                <div id="pixels">
                    @if(count($planoPixels) > 0)
                        @foreach($planoPixels as $key => $planoPixel)
                            <div id="pixels_div_{{ $key + 1 }}" class="row">
                                <div class="form-group col-xl-12">
                                    <select id="pixel_{{ $key + 1 }}" name="pixel_{{ $key + 1 }}" class="form-control">
                                        <option value="">Selecione</option>
                                        @foreach($pixels as $pixel)
                                            <option value="{{ $pixel['id'] }}"  {!! ($pixel['id'] == $planoPixel['pixel']) ? 'selected' : '' !!}>{{ $pixel['nome'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div id="pixels_div_1" class="row">
                            <div class="form-group col-xl-12">
                                <select id="pixel_1" name="pixel_1" class="form-control">
                                    <option value="" selected>Selecione</option>
                                    @foreach($pixels as $pixel)
                                        <option value="{{ $pixel['id'] }}">{{ $pixel['nome'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_pixel" class="btn btn-primary">Adicionar pixel</button>
                    </div>
                </div>

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

                <h4> Cupons de desconto </h4>
                <div id="cupons">
                    @if(count($planoCupons) > 0)
                        @foreach($planoCupons as $key => $planoCupom)
                            <div id="cupons_div_{{ $key + 1 }}" class="row">
                                <div class="form-group col-xl-12">
                                    <select id="cupom_{{ $key + 1 }}" name="cupom_{{ $key + 1 }}" class="form-control">
                                        <option value="">Selecione</option>
                                        @foreach($cupons as $cupom)
                                            <option value="{{ $cupom['id'] }}"  {!! ($cupom['id'] == $planoCupom['cupom']) ? 'selected' : '' !!}>{{ $cupom['nome'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div id="cupons_div_1" class="row">
                            <div class="form-group col-xl-12">
                                <select id="cupom_1" name="cupom_1" class="form-control">
                                    <option value="" selected>Selecione</option>
                                    @foreach($cupons as $cupom)
                                        <option value="{{ $cupom['id'] }}">{{ $cupom['nome'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_cupom" class="btn btn-primary">Adicionar cupom</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
