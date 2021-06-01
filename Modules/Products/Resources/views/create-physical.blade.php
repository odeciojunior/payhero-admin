@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!!  asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
        <link rel="stylesheet" href="{{ asset('/modules/products/css/create.css?v=02') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title" style="color: #707070;">Novo produto físico</h1>
            <p class="desc mt-10 text-muted"> Preencha os dados sobre seu produto atentamente. </p>
        </div>
        <div class="page-content container">
            <form id='my-form-add-product'>
                <div class="panel pt-10 pb-20" data-plugin="matchHeight" style="border-radius: 16px">
                    <h4 class="px-40">1. Informações básicas</h4>
                    <hr class="my-20">
                    <div class="px-40 row justify-content-between align-items-baseline">
                        <div class="form-group col-12 col-md-4">
                            <div class="d-flex flex-column" id="div_img" style="position: relative">
                                <div class="d-flex flex-column" id="div_digital_product_upload">
                                    <label for="product_photo">Imagem do produto</label>
                                    <input type="file" id="product_photo" name="product_photo" data-height="651" data-max-width="651">
                                    <small class="text-center text-muted mt-15">Sugerimos PNG ou JPG com 650px x 650px (1:1).</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">Nome do produto</label>
                                    <input name="name" type="text" class="input-pad" id="name" placeholder="Digite o nome">
                                </div>
                                <div class="form-group col-12">
                                    <label for="description">Descrição</label>
                                    <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição apresentada no checkout"></textarea>
                                </div>                            
                            </div>
                        </div>                        
                    </div>
                    <hr class="my-20">
                    <h4 class="px-40"> 2. Logística </h4>
                    <hr class="my-20">
                    <div class="px-40 row justify-content-between">
                        <div class="col-12 col-md-4 text-center">
                            <img id="caixinha-img"
                                 src="{{ asset('modules/global/img/svg/caixinha.svg') }}"
                                 class="img-fluid"
                                 alt="novo produto fisico">
                        </div>

                        <div class="row col-12 col-md-8">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="height">Altura</label>
                                <div class="form-group input-group">
                                    <input name="height" type="text" class="form-control" id="height" placeholder="Ex: 150" data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="width">Largura</label>
                                <div class="form-group input-group">
                                    <input name="width" type="text" class="form-control" id="width" placeholder="Ex: 135" data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="length">Comprimento</label>
                                <div class="form-group input-group">
                                    <input name="length" type="text" class="form-control" id="length" placeholder="Ex: 150" value="{{--{!! $product->width !!}--}}" data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text">CM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="weight">Peso</label>
                                <div class="form-group input-group">
                                    <input name="weight" type="text" class="form-control" id="weight" placeholder="Ex: 950" data-mask="0#">
                                    <div class="input-group-append">
                                        <span class="input-group-text">G</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-12 mt-0">
                                <small class="text-muted">
                                    Clique <a href="http://www2.correios.com.br/sistemas/precosprazos/Formato.cfm" target="_blank">aqui</a> para consultar as regras de dimensões dos Correios.
                                </small>
                                <br>
                                <small class="text-muted">
                                    Informações utilizadas para calcular o valor do frete PAC e SEDEX, se não utilizar esses fretes ignore essas informações
                                </small>
                            </div>
                        </div>                        
                    </div>
                </div>

                <div class="row pr-15 form-buttons">
                    <a type="button" class="btn btn-cancelar" href="/products">Cancelar</a>
                    <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Tudo certo!</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('modules/products/js/create-physical.js?v=08') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
    @endpush

@endsection
