<form id="form-register-plan" method="post" action="/plans" enctype="multipart/form-data">
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
                <div id="products">
                    <div id="products_div_1" class="row">
                        <div class='col-sm-8 col-md-7 col-lg-7'>
                            <div class="form-group">
                                <label>Produtos do plano:</label>
                                <select id="product_1" name="products[]" class="form-control plan_product">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='col-sm-4 col-md-3 col-lg-3'>
                            <div class="form-group">
                                <label>Quantidade:</label>
                                <input class="form-control products_amount" type="text" name="product_amounts[]" placeholder="quantidade" data-mask="0#" value="1">
                            </div>
                        </div>
                        <div class='col-sm-12 col-md-2 col-lg-2'>
                            <label class="display-xsm-none">Remover:</label>
                            <button class='btn btn-outline btn-danger btnDelete form-control'>
                                <i class='icon wb-trash' aria-hidden='true'></i></button>
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

