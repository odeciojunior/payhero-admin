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
                <div id="products">
                    <div id="products_div_1" class="row">
                        <div class='col-md-6'>
                            <div class="form-group">
                                <label>Produtos do plano:</label>
                                <select id="product_1" name="products[]" class="form-control plan_product">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class='col-md-4'>
                            <div class="form-group">
                                <label>Quantidade:</label>
                                <input class="form-control products_amount" type="text" name="product_amounts[]" placeholder="quantidade" data-mask="0#" value="1">
                            </div>
                        </div>
                        <div class='col-md-2 mt-30'>
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

