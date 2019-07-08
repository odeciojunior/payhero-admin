<form id="form-update-plan" method="PUT" action="/plans" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" value="{{Hashids::encode($plan->id)}}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <h4 class='mt-0'> Dados gerais </h4>
            <div style="width:100%">
                <div class="row mt-2">
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="name">Nome</label>
                        <input value="{!! $plan->name != '' ? $plan->name : '' !!}" name="name" type="text" class="form-control" id="plan-name" placeholder="Nome" required>
                    </div>
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="price">Preço</label>
                        <input value="{!! $plan->price != '' ? $plan->price : '' !!}" name="price" type="text" class="form-control" id="plan-price" placeholder="Preço" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="description">Descrição</label>
                        <input value="{!! $plan->description != '' ? $plan->description : '' !!}" name="description" type="text" class="form-control" id="plan-description" placeholder="Descrição">
                    </div>
                </div>
                {{--<div class="row">--}}
                {{--<div class="form-group col-12">--}}
                {{--<label for="status">Status</label>--}}
                {{--<select name="status" type="text" class="form-control" id="status" required>--}}
                {{--<option value="1" {!! ($plan->status == '1') ? 'selected' : '' !!}>Ativo</option>--}}
                {{--<option value="0" {!! ($plan->status == '0') ? 'selected' : '' !!}>Inativo</option>--}}
                {{--</select>--}}
                {{--</div>--}}
                {{--</div>--}}
                <div id="products">
                    @if(count($productPlans) > 0)
                        @foreach($productPlans as $key => $productPlan)
                            <div id="produtos_div_1" class="row">
                                <div class="form-group col-xl-8">
                                    <label>Produtos do plano:</label>
                                    {{--<select id="product_{{ $key + 1 }}" name="product_{{ $key + 1 }}" class="form-control">--}}
                                    <select id="product_1" name="products[]" class="form-control">
                                        @foreach($products as $product)
                                            <option value="{{ $product['id'] }}" {!! ($product['id'] == $productPlan['product']) ? 'selected' : '' !!}>{{ $product['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xl-3">
                                    <label>Quantidade:</label>
                                    {{--<input value="{!! $productPlan['amount'] != '' ? $productPlan['amount'] : '' !!}" id="product_amount_1" class="form-control products_amount" type="text" data-mask='0#' name="product_amount_{{ $key + 1 }}" placeholder="quantidade">--}}
                                    <input value="{!! $productPlan['amount'] != '' ? $productPlan['amount'] : '' !!}" id="product_amount_1" class="form-control products_amount" type="text" data-mask='0#' name="product_amounts[]" placeholder="quantidade">
                                </div>
                                <div class='col-xl-1 mt-30'>
                                    <button class='btn btn-sm btn-outline btn-danger btnDelete'><i class='icon wb-trash' aria-hidden='true'></i></button></button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div id="produtos_div_1" class="row">
                            <div class="form-group col-xl-8">
                                <label>Produtos do plano:</label>
                                {{--<select id="product_1" name="product_1" class="form-control">--}}
                                <select id="product_1" name="products[]" class="form-control">
                                    @foreach($products as $product)
                                        <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-3">
                                <label>Quantidade:</label>
                                {{--<input class="form-control products_amount" id='product_amount_1' type="text" name="product_amount_1" data-mask='0#' placeholder="quantidade">--}}
                                <input class="form-control products_amount" id='product_amount_1' type="text" name="product_amounts[]" data-mask='0#' placeholder="quantidade">
                            </div>
                            <div class='col-xl-1 mt-30'>
                                <button class='btn btn-sm btn-outline btn-danger btnDelete'><i class='icon wb-trash' aria-hidden='true'></i></button></button>
                            </div>
                        </div>
                    @endif
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
