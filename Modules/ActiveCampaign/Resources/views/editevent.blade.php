<form id="form-update-plan" method="PUT" action="/plans" enctype="multipart/form-data" style="display:none">
    @csrf
    @method('PUT')
    {{--    <input type="hidden" value="{{Hashids::encode($plan->id)}}" name="id">--}}
    <input type="hidden" value="" name="id" id='plan_id'>
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <h4 class='mt-0'> Dados gerais </h4>
            <div style="width:100%">
                <div class="row mt-2">
                    <div class="form-group col-md-6 col-lg-6">
                        <label for="name">Nome</label>
                        <input value="" name="name" type="text" class="form-control" id="plan-name_edit" placeholder="Nome" maxlength='50' required>
                    </div>
                    <div class="form-group col-md-6 col-lg-6">
                        <label for="price">Preço</label>
                        <input value="" name="price" type="text" class="form-control" id="plan-price_edit" placeholder="Preço" required>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="description">Descrição</label>
                        <input value="" name="description" type="text" class="form-control" id="plan-description_edit" maxlength='50' placeholder="Descrição">
                    </div>
                </div>
                <hr class='display-lg-none display-xlg-none'>
                <div id="products" class='products_row_edit'>
                        {{-- carregado no js--}}
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                        <button type="button" id="add_product_plan" class="btn btn-primary col-12 add_product_plan_edit">Adicionar produto</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
