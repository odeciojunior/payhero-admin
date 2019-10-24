<form id="form-register-plan" method="post" action="/plans" enctype="multipart/form-data" style="display:none">
    @csrf
    <div class="container-fluid">
        <div class="" data-plugin="matchHeight">
            <div style="width:100%">
                <h4 class='mt-0'> Dados gerais </h4>
                <div class="row mt-2">
                    <div class="form-group col-md-6 col-lg-6">
                        <label for="name">Nome</label>
                        <input name="name" type="text" class="form-control" id="name" placeholder="Nome" maxlength='50' required>
                    </div>
                    <div class="form-group col-md-6 col-lg-6">
                        <label for="price">Preço</label>
                        <input name="price" type="text" class="form-control" id="price" placeholder="99,99" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="description">Descrição</label>
                        <input name="description" type="text" class="form-control" id="description" maxlength='50' placeholder="Descrição">
                    </div>
                </div>
                <hr class='display-lg-none display-xlg-none'>
                <div id="products" class='card p-10 card-products'>
                    <div id="products_div_1" class="row products_div_1 mb-4">
                        <div class='col-sm-12 col-md-12 col-lg-12'>
                            <div class="form-group">
                                <label>Produtos do plano:</label>
                                <select id="product_1" name="products[]" class="form-control plan_product">
                                    {{--select no js--}}
                                </select>
                            </div>
                        </div>
                        <div class='col-sm-4 col-md-3 col-lg-3'>
                            <div class="form-group">
                                <label>Quantidade:</label>
                                <input class="form-control products_amount_create" type="text" name="product_amounts[]" id="products_amount" placeholder="quantidade" data-mask="0#" value="1">
                            </div>
                        </div>
                        <div class="form-group col-sm-4 col-md-3 col-lg-3">
                            <label>Custo (<b>Un</b>):</label>
                            <input value="" id="product_cost_1" class="form-control products_cost" type="text" data-mask='0#' name="product_cost[]" placeholder="Unitario">
                        </div>
                        <div class="form-group col-sm-4 col-md-3 col-lg-3">
                            <label>Custo Total:</label>
                            <input value="" id="product_total_1" class="form-control products_total" type="text" data-mask='0#' name="product_total[]" placeholder="Total" readonly>
                        </div>
                        <div class="form-group col-sm-4 col-md-3 col-lg-3">
                            <label>Moeda:</label>
                            <select id="select_currency" class='form-control' name='currency[]'>
                                <option>BRL</option>
                                <option>USD</option>
                            </select>
                        </div>
                        {{--                        <div class="switch-holder col-sm-4 col-md-3 col-lg3">--}}
                        {{--                            <select class='form-control' name='status[]'>--}}
                        {{--                                <option>Ativo</option>--}}
                        {{--                            </select>--}}
                        {{--                            <label for="token" class='mb-10'>Dólar:</label>--}}
                        {{--                            <br>--}}
                        {{--                            <label class="switch">--}}
                        {{--                                <input type="checkbox" id="status" name="status" class='check shipping-status' value='0'>--}}
                        {{--                                <span class="slider round"></span>--}}
                        {{--                                <input type='hidden' name='status[]' id='status-input'>--}}
                        {{--                            </label>--}}
                        {{--                        </div>--}}
                        <div class='col-sm-12 offset-md-4 col-md-4 offset-lg-4 col-lg-4'>
                            {{--                            <label class="display-xsm-none">Remover:</label>--}}
                            <button class='btn btn-outline btn-danger btnDelete form-control'>
                                <b>Remover </b><i class='icon wb-trash' aria-hidden='true'></i>
                            </button>
                        </div>
                        <hr class='mb-30 display-lg-none display-xlg-none'>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <button type="button" id="add_product_plan" class="btn btn-primary col-12">Adicionar produto</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

