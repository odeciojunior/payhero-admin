{{--<form id="form-update-plan" method="PUT" action="/plans" enctype="multipart/form-data" style="display:none">--}}
{{--    @csrf--}}
{{--    @method('PUT')--}}
{{--    <input type="hidden" value="{{Hashids::encode($plan->id)}}" name="id">--}}
{{--    <input type="hidden" value="" name="id">--}}
{{--    <div class="container-fluid">--}}
{{--        <div class="panel" data-plugin="matchHeight">--}}
{{--            <h4 class='mt-0'> Dados gerais </h4>--}}
{{--            <div style="width:100%">--}}
{{--                <div class="row mt-2">--}}
{{--                    <div class="form-group col-md-6 col-lg-6">--}}
{{--                        <label for="name">Nome</label>--}}
{{--                        <input value="{!! $plan->name != '' ? $plan->name : '' !!}" name="name" type="text" class="form-control" id="plan-name" placeholder="Nome" maxlength='50' required>--}}
{{--                    </div>--}}
{{--                    <div class="form-group col-md-6 col-lg-6">--}}
{{--                        <label for="price">Preço</label>--}}
{{--                        <input value="{!! $plan->price != '' ? $plan->price : '' !!}" name="price" type="text" class="form-control" id="plan-price" placeholder="Preço" required>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <div class="form-group col-md-12">--}}
{{--                        <label for="description">Descrição</label>--}}
{{--                        <input value="{!! $plan->description != '' ? $plan->description : '' !!}" name="description" type="text" class="form-control" id="plan-description" maxlength='50' placeholder="Descrição">--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <hr class='display-lg-none display-xlg-none'>--}}
{{--                <div id="products">--}}
{{--                    @if(count($productPlans) > 0)--}}
{{--                        @foreach($productPlans as $key => $productPlan)--}}
{{--                            <div id="products_div_1" class="row">--}}
{{--                                <div class="form-group col-sm-8 col-md-7 col-lg-7">--}}
{{--                                    <label>Produtos do plano:</label>--}}
{{--                                    <select id="product_1" name="products[]" class="form-control">--}}
{{--                                        @foreach($products as $product)--}}
{{--                                            <option value="{{ $product['id'] }}" {{($product['id'] == $productPlan['product_id']) ? 'selected' : '' }}>{{ $product['name'] }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                                <div class="form-group col-sm-4 col-md-3 col-lg-3">--}}
{{--                                    <label>Quantidade:</label>--}}
{{--                                    <input value="{!! $productPlan['amount'] != '' ? $productPlan['amount'] : '' !!}" id="product_amount_1" class="form-control products_amount" type="text" data-mask='0#' name="product_amounts[]" placeholder="quantidade">--}}
{{--                                </div>--}}
{{--                                <div class='form-group col-sm-12 col-md-2 col-lg-2'>--}}
{{--                                    <label class="display-xsm-none">Remover:</label>--}}
{{--                                    <button class='btn btn-outline btn-danger btnDelete form-control'>--}}
{{--                                        <i class='icon wb-trash' aria-hidden='true'></i></button>--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                                <hr class='mb-30 display-lg-none display-xlg-none'>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    @else--}}
{{--                        <div id="products_div_1" class="row">--}}
{{--                            <div class="form-group col-sm-8 col-md-7 col-lg-7">--}}
{{--                                <label>Produtos do plano:</label>--}}
{{--                                <select id="product_1" name="products[]" class="form-control plan_product">--}}
{{--                                    @foreach($products as $product)--}}
{{--                                        <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <div class="form-group col-sm-4 col-md-3 col-lg-3">--}}
{{--                                <label>Quantidade:</label>--}}
{{--                                <input class="form-control products_amount" id='product_amount_1' type="text" name="product_amounts[]" data-mask='0#' placeholder="quantidade">--}}
{{--                            </div>--}}
{{--                            <div class='form-group col-sm-12 col-md-2 col-lg-2'>--}}
{{--                                <label class="display-xsm-none">Remover:</label>--}}
{{--                                <button class='btn btn-sm btn-outline btn-danger btnDelete form-control'>--}}
{{--                                    <i class='icon wb-trash' aria-hidden='true'></i>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                            <hr class='mb-30 display-lg-none display-xlg-none'>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                <div class="row">--}}
{{--                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">--}}
{{--                        <button type="button" id="add_product_plan" class="btn btn-primary col-12">Adicionar produto</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</form>--}}
