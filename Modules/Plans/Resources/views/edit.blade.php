<form id="edit_plan" method="post" action="/plans" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="{{Hashids::encode($plan->id)}}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <h4 class='mt-0'> Dados gerais </h4>
            <div style="width:100%">
                <div class="row mt-2">
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="name">Nome</label>
                        <input value="{!! $plan->name != '' ? $plan->name : '' !!}" name="name" type="text" class="form-control" id="name" placeholder="Nome" required>
                    </div>
                    <div class="form-group col-xl-6 col-lg-6">
                        <label for="price">Preço</label>
                        <input value="{!! $plan->price != '' ? $plan->price : '' !!}" name="price" type="text" class="form-control price" id="price" placeholder="Preço" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="description">Descrição</label>
                        <input value="{!! $plan->description != '' ? $plan->description : '' !!}" name="description" type="text" class="form-control" id="description" placeholder="Descrição">
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
                            <div id="produtos_div_{{ $key + 1 }}" class="row">
                                <div class="form-group col-xl-10">
                                    <label>Produtos do plano:</label>
                                    <select id="product_{{ $key + 1 }}" name="product_{{ $key + 1 }}" class="form-control">
                                        <option value="">Selecione</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product['id'] }}" {!! ($product['id'] == $productPlan['product']) ? 'selected' : '' !!}>{{ $product['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-xl-2">
                                    <label>Quantidade:</label>
                                    <input value="{!! $productPlan['amount'] != '' ? $productPlan['amount'] : '' !!}" class="form-control products_amount" type="text" data-mask='0#' name="products_amount_{{ $key + 1 }}" placeholder="quantidade">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div id="produtos_div_1" class="row">
                            <div class="form-group col-xl-10">
                                <label>Produtos do plano:</label>
                                <select id="product_1" name="product_1" class="form-control">
                                    <option value="" selected>Selecione</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product['id'] }}">{{ $product['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-2">
                                <label>Quantidade:</label>
                                <input class="form-control products_amount" type="text" name="products_amount" data-mask='0#' placeholder="quantidade">
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
