<form id="form-register-plan" method="post" action="/plans" enctype="multipart/form-data">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <h4 class='mt-0'> Dados gerais </h4>
                <div class="row mt-2">
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="name">Nome</label>
                        <input name="name" type="text" class="form-control" id="name" placeholder="Nome" required>
                    </div>

                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="price">Preço</label>
                        <input name="price" type="text" class="form-control" id="price" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="description">Descrição</label>
                        <input name="description" type="text" class="form-control" id="description" placeholder="Descrição">
                    </div>
                </div>

                {{--<div class="row">--}}
                    {{--<div class="form-group col-xl-12">--}}
                        {{--<label for="status">Status</label>--}}
                        {{--<select name="status" type="text" class="form-control" id="status" required>--}}
                            {{--<option value="1">Ativo</option>--}}
                            {{--<option value="0">Inativo</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--  <div class="row">
                    <div id="div_id_plano_transportadora_cadastrar" class="form-group col-xl-6 col-lg-6" style="display:none">
                        <label for="id_plano_transportadora_cadastrar">Id da transportadora</label>
                        <input name="id_plano_transportadora" type="text" class="form-control" id="id_plano_transportadora_cadastrar" placeholder="id da transportadora">
                    </div>
                </div>  --}}

                <div id="products">
                    <div id="produtos_div_1" class="row">
                        <div class='col-xl-8'>
                            <div class="form-group">
                                <label>Produtos do plano:</label>
                                <select id="product_1" name="products[]" class="form-control">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='col-xl-3'>
                            <div class="form-group">
                                <label>Quantidade:</label>
                                <input class="form-control products_amount" type="text" name="product_amounts[]" placeholder="quantidade" data-mask="0#" value="1">
                            </div>
                        </div>
                        <div class='col-xl-1 mt-30'>
                            <button class='btn btn-sm btn-outline btn-danger btnDelete'><i class='icon wb-trash' aria-hidden='true'></i></button></button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <button type="button" id="add_product_plan" class="btn btn-primary">Adicionar produto</button>
                    </div>
                </div>
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
