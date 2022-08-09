<div id="form-update-plan"
     style="display:none">
    <div class="container-fluid px-0">
        <div class="nav-tabs-horizontal">
            <div class="nav nav-tabs nav-tabs-line text-center"
                 id="nav-tab"
                 role="tablist">
                <a class="nav-item nav-link active"
                   id="nav-geral-tab"
                   data-toggle="tab"
                   href="#nav-geral"
                   role="tab"
                   style="width:50%;"><strong>Dados gerais</strong>
                </a>
                <a class="nav-item nav-link"
                   id="nav-custom-tab"
                   data-toggle="tab"
                   href="#nav-custom"
                   role="tab"
                   style="width:50%;"><strong>Personalizações</strong>
                </a>
            </div>
        </div>
        <div class="tab-content p-15"
             id="nav-tabContent">
            <div class="tab-pane fade show active"
                 id="nav-geral"
                 role="tabpanel"
                 aria-labelledby="nav-geral-tab">
                <div class="card container">
                    <form id="form-update-plan-tab-1"
                          method="PUT"
                          action="/plans"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        {{-- <input type="hidden" value="{{Hashids::encode($plan->id)}}" name="id"> --}}
                        <input type="hidden"
                               value=""
                               name="id"
                               id='plan_id'>
                        <div class="panel"
                             data-plugin="matchHeight">
                            <h4 class='mt-0'> Dados gerais </h4>
                            <div style="width:100%">
                                <div class="row mt-2">
                                    <div class="form-group col-md-6 col-lg-6">
                                        <label for="name">Nome</label>
                                        <input value=""
                                               name="name"
                                               type="text"
                                               class="form-control"
                                               id="plan-name_edit"
                                               placeholder="Nome"
                                               maxlength='50'
                                               required>
                                    </div>
                                    <div class="form-group col-md-6 col-lg-6">
                                        <label for="price">Preço</label>
                                        <input value=""
                                               name="price"
                                               type="text"
                                               class="form-control"
                                               id="plan-price_edit"
                                               placeholder="Preço"
                                               required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="description">Descrição</label>
                                        <input value=""
                                               name="description"
                                               type="text"
                                               class="form-control"
                                               id="plan-description_edit"
                                               maxlength='50'
                                               placeholder="Descrição">
                                    </div>
                                </div>
                                <hr class='display-lg-none display-xlg-none'>
                                <div id="products"
                                     class='products_row_edit'>
                                    {{-- carregado no js --}}
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <button type="button"
                                                id="add_product_plan"
                                                class="btn btn-primary col-12 add_product_plan_edit">Adicionar
                                            produto</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div class="tab-pane fade"
                 id="nav-custom"
                 role="tabpanel"
                 aria-labelledby="nav-custom-tab">
                <form id="form-update-plan-tab-2"
                      method="POST"
                      action="/plans/config-custom-product"
                      enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div id="custom_products"
                             class='col-md-12 products_row_custom'>
                            {{-- carregado no js --}}
                        </div>
                        <div id="custom_products_checkbox"
                             class="card container mb-0"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
