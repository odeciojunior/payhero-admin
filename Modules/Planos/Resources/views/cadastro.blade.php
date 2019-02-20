<div style="text-align: center">
    <h3> Cadastrar plano </h3>
</div>

<form id="cadastrar_plano" method="post" action="/planos/cadastrarplano" enctype="multipart/form-data">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <h4> Dados gerais </h4>
                <div class="row">
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="nome">Nome</label>
                        <input name="nome" type="text" class="form-control" id="nome_plano" placeholder="Nome" required>
                    </div>

                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="preco">Preço</label>
                        <input name="preco" type="text" class="form-control dinheiro" id="preco_plano" placeholder="Preço" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="descricao">Descrição</label>
                        <input name="descricao" type="text" class="form-control" id="descricao_plano" placeholder="Descrição">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status_plano" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>

                {{--  <div class="row">
                    <div id="div_id_plano_transportadora_cadastrar" class="form-group col-xl-6 col-lg-6" style="display:none">
                        <label for="id_plano_transportadora_cadastrar">Id da transportadora</label>
                        <input name="id_plano_transportadora" type="text" class="form-control" id="id_plano_transportadora_cadastrar" placeholder="id da transportadora">
                    </div>
                </div>  --}}

                <h4> Foto do plano </h4> 

                <div class="row">
                    <div class="form-group col-12">
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
                            <input class="form-control qtd-produtos" type="text" name="produto_qtd_1" placeholder="quantidade" value="1">
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_produtoplano" class="btn btn-primary">Adicionar produto</button>
                    </div>
                </div>

                @if(count($brindes) > 0)
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
                @endif
            </div>
        </div>
    </div>
</form>


                    {{--  <div class="form-group col-xl-6">
                        <label for="hotzapp_dados">Integração com HotZapp</label>
                        <select name="hotzapp_dados" id="hotzapp_dados" class="form-control">
                            <option value="">Sem integração</option>
                            @foreach($dados_hotzapp as $hotzapp_dados)
                                <option value="{{ $hotzapp_dados['id'] }}">{{ $hotzapp_dados['descricao'] }}</option>
                            @endforeach
                        </select>
                    </div>  --}}
