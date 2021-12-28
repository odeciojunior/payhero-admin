@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!!  asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div style="display:none !important;" class="page-header container">
            <h1 class="page-title">Editar produto</h1>
            <p class="desc mt-10"> Preencha os dados sobre seu produto atentamente. </p>
            <div class="page-header-actions">
                <a class="d-none d-lg-block btn btn-primary float-right" href="{{ route('products.index') }}">
                    Meus produtos
                </a>
            </div>
        </div>
        <div class="page-content container">
            <form id='my-form'>
                @method('PUT')
                <div class="card shadow pt-15">
                    <nav>
                        <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-basic-tab" data-toggle="tab" href="#nav-basic" role="tab" aria-controls="nav-basic" aria-selected="true">1. Informações Básicas</a>
                            <a class="nav-item nav-link" id="nav-logistic-tab" data-toggle="tab" href="#nav-logistic" role="tab" aria-controls="nav-logistic" aria-selected="false">2. Logística</a>
                        </div>
                    </nav>
                    <div class="p-15">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active p-30" id="nav-basic" role="tabpanel" aria-labelledby="nav-basic-tab">
                                <div class="row justify-content-between align-items-baseline">
                                    <div class="col-lg-10">
                                        <h3> 1. Informações Básicas </h3>
                                        <p> Preencha atentamente as informações sobre seu produto </p>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class=" flex-column">
                                            <input name="product_photo" type="file" class="form-control" id="photo" style="display:none">
                                            <label for="name">Selecione uma imagem</label>
                                            <img id="previewimage" alt="Selecione a foto do produto" accept="image/*" src='' style="height: 300px; width: 300px;">
                                            <input type="hidden" name="photo_x1" value=''>
                                            <input type="hidden" name="photo_y1" value=''>
                                            <input type="hidden" name="photo_w" id="photo_w" value=''>
                                            <input type="hidden" name="photo_h" id="photo_h" value=''>
                                            <p class="info mt-5" style="font-size:10px;">
                                                <i class="icon wb-info-circle" aria-hidden="true"></i> A imagem escolhida deve estar no formato (JPG, JPEG, ou PNG).
                                                <br> Dimensões ideais: 300 x 300 pixels.
                                            </p>
                                        </div>
                                        <div class="" id="div_digital_product_upload" style="visibility: hidden; width:300px;">
                                            <label for="digital_product_url">Produto digital</label>
                                            <input type="file" id="digital_product_url" name="digital_product_url" data-height="300" data-max-width="300">
                                            <p class="info mt-5" style="font-size:10px;">
                                                <i class="icon wb-info-circle" aria-hidden="true"></i> Produto digital que será enviado para o cliente.
                                                <br>.
                                            </p>
                                            <button class="btn btn-primary btn-sm btn-view-product-url d-flex align-items-center" link='' style="display:none;" title='Visualizar produto digital'>
                                                <span style="-webkit-text-stroke: 0.8px #FFF" class="o-eye-1 white font-size-20 mr-2"></span> Visualizar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row">
                                            <div class="form-group col-lg-12">
                                                <label for="name">Nome</label>
                                                <input name="name" type="text" class="input-pad" id="name" value="" placeholder="O nome do seu produto">
                                            </div>
                                            <div class="form-group col-lg-12">
                                                <label for="description">Descrição</label>
                                                <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição apresentada no checkout"></textarea>
                                                <p class="mb-0"> Máximo 30 caracteres. </p>
                                            </div>
                                            <div class="form-group col-lg-12" id="sku" style="display: none">
                                                <label>SKU</label>
                                                <input type="text" class="input-pad gray mb-2" readonly/>
                                                <p> Editável somente no Shopify. </p>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="name">Tipo</label>
                                                <div class="d-flex justify-content-start">
                                                    <div class="radio-custom radio-primary pr-20">
                                                        <input type="radio" id="physical" name="format" value="1">
                                                        <label for="physical">Físico</label>
                                                    </div>
                                                    <div class="radio-custom radio-primary ">
                                                        <input type="radio" id="digital" name="format" value="2">
                                                        <label for="digital">Digital</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='col-lg-6'></div>
                                            {{--                                            <div class="form-group col-lg-6">--}}
                                            {{--                                                <label for="category">Categoria</label>--}}
                                            {{--                                                <select name="category" class="form-control select-pad" id='select-categories'>--}}
                                            {{--                                                    <option value="">Selecione</option>--}}
                                            {{--                                                </select>--}}
                                            {{--                                            </div>--}}

                                            <div class='form-group col-lg-6 div-expiration-time' style='display:none;'>
                                                <label for="url_expiration_time">Tempo de expiração da url (em horas)</label>
                                                <div class="d-flex input-group">
                                                    <input type="text" min="0" class="form-control" name="url_expiration_time" id="url_expiration_time" placeholder="Tempo de expiração da url em horas" maxlength='5' data-mask="0#">
                                                </div>
                                            </div>
                                            {{--                                            <div class="form-group col-lg-4">--}}
                                            {{--                                                <label for="price">Preço--}}
                                            {{--                                                    <span class="ml-5 sm-text text-muted" style="font-size: 0.8em; font-weight: normal;"> Opcional </span>--}}
                                            {{--                                                </label>--}}
                                            {{--                                                <input name="price" type="text" class="input-pad money" placeholder="Digite o preço" id='price' value="" autocomplete="off">--}}
                                            {{--                                            </div>--}}
                                            <div id="div_next_step" class="form-group col-lg-12 justify-content-between mt-10">
                                                <button id="next_step" type="button" class="mr-5 btn btn-success">Prosseguir<i style="-webkit-text-stroke: 1.45px #FFF;" class="o-arrow-right-1 font-size-16 ml-2" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                            <div id="div_save_digital_product" class="form-group col-lg-12 text-right" style="display:none">
                                                <button id="save_digital_product" type="submit" class="btn btn-success">Salvar</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='col-lg-12'>
                                        <a style='display:none;' class="btn btn-primary delete-product white float-right d-flex align-items-center" productname='' product="" data-toggle="modal" data-target="#modal-delete">
                                            <i class="o-bin-1 align-middle mr-5 white" aria-hidden="true"></i> Excluir produto
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade p-30" id="nav-logistic" role="tabpanel" aria-labelledby="nav-logistic-tab">
                                <div class="row mt50">
                                    <div class="col-lg-12">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <h3> 2. Logística </h3>
                                                <p class="pt-10"> Preencha atentamente as informações sobre seu produto </p>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="height">Altura (cm)</label>
                                                <input name="height" type="text" class="input-pad" id="height" placeholder="Ex: 80cm" value="{{--{!! $product->height !!}--}}" data-mask="0#">
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="width">Largura (cm)</label>
                                                <input name="width" type="text" class="input-pad" id="width" placeholder="Ex: 135cm" value="{{--{!! $product->width !!}--}}" data-mask="0#">
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="width">Comprimento (cm)</label>
                                                <input name="length" type="text" class="input-pad" id="length" placeholder="Ex: 150cm" value="{{--{!! $product->width !!}--}}" data-mask="0#">
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <label for="weight">Peso (g)</label>
                                                <input name="weight" type="text" class="input-pad" id="weight" placeholder="Ex: 950g" value="{{--{!! $product->weight !!}--}}" data-mask="0#">
                                            </div>
                                            <div class="form-group col-lg-6 mt-0">
                                                <p class="info pt-5 mb-5" style="font-size: 10px;">
                                                    <i class="icon wb-info-circle" aria-hidden="true"></i> Clique
                                                    <a href="http://www2.correios.com.br/sistemas/precosprazos/Formato.cfm" target="_blank">aqui</a>
                                                    para consultar as regras de dimensões dos Correios.
                                                </p>
                                                <p class="info pt-5" style="font-size: 10px;">
                                                    <i class="icon wb-info-circle" aria-hidden="true"></i> Informações utilizadas para calcular o valor do frete PAC e SEDEX, se não utilizar esses fretes ignore essas informações
                                                </p>
                                            </div>
                                            <div class="form-group col-lg-12 text-right btnSave">
                                                <button type="submit" id='btn-save' class="btn btn-success">Atualizar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <!-- Modal padrão para excluir -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog  modal-dialog-centered  modal-simple">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <a class="pointer close" role="button" data-dismiss="modal" aria-label="Close" id="close-modal-delete">
                                <i class="material-icons md-16">close</i>
                            </a>
                        </div>
                        <div id="modal_excluir_body" class="modal-body text-center p-20">
                            <div class=" justify-content-center">
                                <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                            </div>
                            <h3 class="black"> Você tem certeza? </h3>
                            <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-center">
                            <button id="bt_cancelar" type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                                <b>Cancelar</b>
                            </button>
                            <button id="bt_excluir" type="button" class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                                <b class="mr-2">Excluir </b>
                                <span class="o-bin-1"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Produto digital -->
            <div id="modal-plan-already-created" class="modal fade" role="dialog" data-backdrop="static">
                <div class="modal-dialog p-2">
                    <!-- Modal content-->
                    <div class="modal-content p-4">
{{--                        <h4 class="modal-title font-size-20 text-center">Encontramos dados que precisam ser atualizados!</h4>--}}
                        <i class="material-icons gradient text-center" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                        <span class="py-1 text-center">
                        Impossível alterar pois já existe um plano cadastrado com esse produto, você precisa excluir o plano para depois alterá-lo para Digital.
                    </span>
                        <div class="modal-body p-2 text-center">
                            <a class='btn btn-close-modal-plan mt-10 btn-success text-white' data-dismiss="modal">
                                <b>Fechar</b></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('modules/products/js/products.js?v=05') }}"></script>
        <script src="{{asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
        <script src="{{asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
    @endpush

@endsection
