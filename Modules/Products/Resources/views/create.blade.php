@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!!  asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
    @endpush

    <!-- Page -->
    <div class="page" style="display:none !important;">
        <div style="display: none !important;" class="page-header container">
            <h1 class="page-title">Cadastrar novo produto</h1>
            <p class="desc mt-10"> Preencha os dados sobre seu produto atentamente. </p>
            <div class="page-header-actions">
                <a class="d-none d-lg-block btn btn-primary float-right" href="{{ route('products.index') }}">
                    Meus produtos
                </a>
            </div>
        </div>
        <div class="page-content container">
            <form id='my-form-add-product'>
                <div class="panel pt-30 p-30" data-plugin="matchHeight">
                    <nav>
                        <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-basic-tab" data-toggle="tab" href="#nav-basic" role="tab" aria-controls="nav-basic" aria-selected="true">1. Informações Básicas</a>
                            <a class="nav-item nav-link" id="nav-logistic-tab" data-toggle="tab" href="#nav-logistic" role="tab" aria-controls="nav-logistic" aria-selected="false">2. Logística</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active p-30" id="nav-basic" role="tabpanel" aria-labelledby="nav-basic-tab">
                            <div class="row justify-content-between align-items-baseline">
                                <div class="col-lg-12">
                                    <h3> 1. Informações Básicas </h3>
                                    <p class="pt-10"> Preencha atentamente as informações sobre seu produto </p>
                                </div>
                                <div class="col-lg-4">
                                    <div class="d-flex flex-column" id="div_img" style="position: relative">
                                        <input name="product_photo" type="file" class="form-control" id="photo" style="display:none !important;">
                                        <label for="name">Selecione uma imagem</label>
                                        <img id="previewimage" alt="Selecione a foto do produto" accept="image/*" src="{{ mix('modules/global/img/produto.svg') }}" style="max-height: 300px; max-width: 300px;">
                                        <input type="hidden" name="photo_x1" value=''>
                                        <input type="hidden" name="photo_y1" value=''>
                                        <input type="hidden" name="photo_w" id="photo_w" value=''>
                                        <input type="hidden" name="photo_h" id="photo_h" value=''>
                                        <p class="info mt-5" style="font-size:10px;">
                                            <i class="icon wb-info-circle" aria-hidden="true"></i> A imagem escolhida deve estar no formato JPG, JPEG, ou PNG.
                                            <br> Dimensões ideais: 300 x 300 pixels.
                                        </p>
                                    </div>
                                    <div class="" id="div_digital_product_upload" style="visibility:hidden; width:300px;">
                                        <label for="digital_product_url">Produto digital</label>
                                        <input type="file" id="digital_product_url" name="digital_product_url" data-height="300" data-max-width="300">
                                        <p class="info mt-5" style="font-size:10px;">
                                            <i class="icon wb-info-circle" aria-hidden="true"></i> Produto digital que será enviado para o cliente.
                                            <br>.
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <label for="name">Nome</label>
                                            <input name="name" type="text" class="input-pad" id="name" value='' placeholder="O nome do seu produto">
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <label for="description">Descrição</label>
                                            <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição apresentada no checkout"></textarea>
                                            <p> Máximo 30 caracteres. </p>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="name">Tipo</label>
                                            <div class="d-flex justify-content-start">
                                                <div class="radio-custom radio-primary pr-20">
                                                    <input type="radio" id="physical" name="format" value="1" checked>
                                                    <label for="physical">Físico</label>
                                                </div>
                                                <div class="radio-custom radio-primary d-flex">
                                                    <input type="radio" id="digital" name="format" value="2">
                                                    <label for="digital">Digital</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-lg-6'></div>
{{--                                        <div class="form-group col-lg-6">--}}
{{--                                            <label for="category">Categoria</label>--}}
{{--                                            <select name="category" class="form-control select-pad" id='select-categories'>--}}
{{--                                                <option value="">Selecione</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}

                                        <div class='form-group col-lg-6 div-expiration-time' style='display:none;'>
                                            <label for="url_expiration_time">Tempo de expiração da url (em horas)</label>
                                            <div class="d-flex input-group">
                                                <input type="text" min="0" class="form-control" name="url_expiration_time" id="url_expiration_time" placeholder="Tempo de expiração da url em horas" maxlength='5' data-mask="0#" >
                                            </div>
                                        </div>
{{--                                        <div class="form-group col-lg-4">--}}
{{--                                            <label for="price">Preço</label>--}}
{{--                                            <input name="price" type="text" class="input-pad money" value='' placeholder="Digite o preço" autocomplete="off">--}}
{{--                                        </div>--}}
                                        <div id="div_next_step" class="form-group col-lg-12 text-right d-flex justify-content-end">
                                            <button id="next_step" type="button" class="btn btn-success d-flex align-items-center">Prosseguir<i style="-webkit-text-stroke: 1.45px #FFF;" class="o-arrow-right-1 font-size-16 ml-2" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div id="div_save_digital_product" class="form-group col-lg-12 text-right" style="display:none">
                                            <button id="save_digital_product" type="submit" class="btn btn-success">Salvar</button>
                                        </div>
                                    </div>
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
                                            <label for="width">Altura (cm)</label>
                                            <input name="width" type="text" class="input-pad" id="width" value='' placeholder="Ex: 150cm" data-mask="0#">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="height">Largura (cm)</label>
                                            <input name="height" type="text" class="input-pad" id="height" value='' placeholder="Ex: 135cm" data-mask="0#">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="width">Comprimento (cm)</label>
                                            <input name="length" type="text" class="input-pad" id="length" placeholder="Ex: 150cm" value="{{--{!! $product->width !!}--}}" data-mask="0#">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="weight">Peso (g)</label>
                                            <input name="weight" type="text" class="input-pad" id="weight" value='' placeholder="Ex: 950g" data-mask="0#">
                                        </div>
                                        <div class="form-group col-lg-6 mt-0">
                                            <p class="info pt-5 mb-5" style="font-size: 10px;">
                                                <i class="icon wb-info-circle" aria-hidden="true"></i>
                                                Clique <a href="http://www2.correios.com.br/sistemas/precosprazos/Formato.cfm" target="_blank">aqui</a> para consultar as regras de dimensões dos Correios.
                                            </p>
                                            <p class="info pt-5" style="font-size: 10px;">
                                                <i class="icon wb-info-circle" aria-hidden="true"></i>
                                                Informações utilizadas para calcular o valor do frete PAC e SEDEX, se não utilizar esses fretes ignore essas informações
                                            </p>
                                        </div>
                                        <div class="form-group col-lg-12 text-right">
                                            <button type="submit" class="btn btn-success btnSave">Salvar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ mix('modules/products/js/create.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
    @endpush

@endsection
