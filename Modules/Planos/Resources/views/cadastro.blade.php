<div style="text-align: center">
    <h4> Cadastrar plano </h4>
</div>

<form id="cadastrar_plano" method="post" action="/planos/cadastrarplano" enctype="multipart/form-data">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <h4> Dados gerais </h4>
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="nome">Nome</label>
                        <input name="nome" type="text" class="form-control" id="nome_plano" placeholder="Nome" required>
                    </div>

                    <div class="form-group col-xl-6">
                        <label for="preco">Preço</label>
                        <input name="preco" type="text" class="form-control dinheiro" id="preco_plano" placeholder="Preço" required>
                    </div>

                </div>

                <div class="row">

                    <div class="form-group col-xl-6">
                        <label for="descricao">Descrição</label>
                        <input name="descricao" type="text" class="form-control" id="descricao_plano" placeholder="Descrição">
                    </div>
                    <div class="form-group col-xl-6">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status_plano" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="disponivel">Status cupons</label>
                        <select name="disponivel" type="text" class="form-control" id="disponivel" required>
                            <option value="1">Disponível</option>
                            <option value="0">Indisponível</option>
                        </select>
                    </div>

                    <div class="form-group col-xl-6">
                        <label for="frete">Frete</label>
                        <select name="frete" type="text" class="form-control" id="frete_plano" required>
                            <option value="">Selecione</option>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="frete_fixo">Frete fixo</label>
                        <select name="frete_fixo" type="text" class="form-control" id="frete_fixo_plano" required>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    <div class="form-group col-xl-6">
                        <label for="valor_frete">Valor frete fixo</label>
                        <input name="valor_frete" type="text" class="form-control dinheiro" id="valor_frete" placeholder="valor fixo">
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="transportadora">Transportadora</label>
                        <select name="transportadora" type="text" class="form-control" id="transportadora_plano" required>
                            @foreach($transportadoras as $transportadora)
                                <option value="{{ $transportadora['id'] }}">{{ $transportadora['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-xl-6">
                        <label for="id_plano_transportadora">Id da transportadora</label>
                        <input name="id_plano_transportadora" type="text" class="form-control" id="id_plano_transportadora" placeholder="id da transportadora">
                    </div>
                </div>

                <div class="row">

                    {{--  <div class="form-group col-xl-6">
                        <label for="hotzapp_dados">Integração com HotZapp</label>
                        <select name="hotzapp_dados" id="hotzapp_dados" class="form-control">
                            <option value="">Sem integração</option>
                            @foreach($dados_hotzapp as $hotzapp_dados)
                                <option value="{{ $hotzapp_dados['id'] }}">{{ $hotzapp_dados['descricao'] }}</option>
                            @endforeach
                        </select>
                    </div>  --}}

                    <div class="form-group col-xl-6">
                        <label for="layout">Layout</label>
                        <select name="layout" type="text" class="form-control" id="layout" required>
                            <option value="">Layout padrão</option>
                            @foreach($layouts as $layout)
                                <option value="{{ $layout['id'] }}">{{ $layout['descricao'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{--  <input value="10" name="quantidade" type="hidden" class="form-control" id="quantidade" placeholder="Quantidade" required>  --}}

                </div>

                <div class="row">
                    <div class="form-group col-12"> 
                        <label for="selecionar_foto_plano_cadastrar">Foto do plano</label><br>
                        <input type="button" id="selecionar_foto_plano_cadastrar" class="btn btn-default" value="Selecionar foto do plano">
                        <input name="foto_plano_cadastrar" type="file" class="form-control" id="foto_plano_cadastrar" accept="image/*" style="display:none">
                        <div  style="margin: 20px 0 0 30px;">
                            <img id="preview_image_plano_cadastrar" alt="Selecione a foto do plano" style="max-height: 250px; max-width: 350px;"/>
                        </div>
                        <input type="hidden" name="foto_plano_cadastrar_x1"/>
                        <input type="hidden" name="foto_plano_cadastrar_y1"/>
                        <input type="hidden" name="foto_plano_cadastrar_w"/>
                        <input type="hidden" name="foto_plano_cadastrar_h"/>
                    </div>
                </div>

                <h4> Produtos do plano </h4>
                <div id="produtos">
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
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_produtoplano" class="btn btn-primary">Adicionar produto</button>
                    </div>
                </div>

                <h4> Pixels do plano </h4>
                <div id="pixels">
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
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_pixel" class="btn btn-primary">Adicionar pixel</button>
                    </div>
                </div>

                <h4> Brindes do plano</h4>
                <div id="brindes">
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
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_brinde" class="btn btn-primary">Adicionar brinde</button>
                    </div>
                </div>

                <h4> Cupons de desconto do plano</h4>
                <div id="cupons">
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
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_cupom" class="btn btn-primary">Adicionar cupom de desconto</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
