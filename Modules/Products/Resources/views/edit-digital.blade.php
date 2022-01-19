@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!!  asset('/modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
        <link rel="stylesheet" href="{{ asset('/modules/products/css/edit.css?v=123') }}">
    @endpush

    <!-- Page -->
    <div class="page p-50" style="margin-bottom: 0 !important;">
        <div style="display: none;" class="page-header container">
            <h1 class="page-title">
                <a href="/products">
                    <span class="o-arrow-right-1 font-size-30 mr-2" aria-hidden="true"></span>Editar produto digital
                </a>
            </h1>
            <p class="desc mt-10 text-muted"> Preencha os dados sobre seu produto atentamente. </p>
        </div>
        <div class="page-content container pb-20">
            <form id='my-form'>
                @method('PUT')
                <div class="panel px-40 p-20" style="border-radius: 16px">
                    <div class="row justify-content-between align-items-baseline">
                        <div class="col-12 col-lg-5 col-xl-4 d-flex flex-column align-items-center">
                            <div class="d-flex flex-column" id="div_img" style="position: relative">
                                <div class="d-flex flex-column" id="div_digital_product_upload">
                                    <label for="product_photo">Imagem do produto</label>
                                    <input type="file" id="product_photo" name="product_photo" data-height="651" data-max-width="651" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                                    <small class="text-center text-muted mt-15">Sugerimos PNG ou JPG com 650px x 650px (1:1).</small>
                                </div>
                            </div>
                            <div class="my-30">
                                <button class="btn btn-primary btn-lg btn-view-product-url" style="width: 150px">Visualizar</button>
                            </div>
                        </div>
                        <div class="col-12 col-lg-7 col-xl-8">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">Nome do produto</label>
                                    <input name="name" type="text" class="input-pad" id="name" placeholder="Digite o produto">
                                </div>
                                <div class="form-group col-12">
                                    <label for="description">Descrição</label>
                                    <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição apresentada no checkout"></textarea>
                                </div>

                                <div class="form-group col-12 col-md-7 input-file-container">  
                                    <label>Arquivo do produto</label>
                                    <input class="input-file" name="digital_product_url" id="digital_product_url" type="file" title="">
                                    <div class="form-group d-flex pt-10">
                                        <label tabindex="0" for="digital_product_url" class="input-file-trigger mb-0 px-15 py-5">Escolher arquivos</label>
                                        <span id="file_return" class="ml-20 pt-10"></span>
                                    </div>
                                    <small class="text-center text-muted mt-15">Este é o arquivo que será enviado ao cliente após a confirmação da venda.</small>
                                </div>

                                <div class="col-12 col-md-5 div-expiration-time">
                                    <label for="url_expiration_time">Expiração do link</label>
                                    <div class="form-group input-group">
                                        <input name="url_expiration_time" type="text" class="form-control" id="url_expiration_time" min="0" maxlength="3" data-mask="0#">
                                        <div class="input-group-append">
                                            <span class="input-group-text font-weight-bold">HORA(S)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-buttons">
                    <div>
                        <a class="btn btn-lg btn-excluir delete-product" data-toggle="modal" data-target="#modal-delete" href="#"><span class="o-bin-1 mr-2"></span>Excluir produto</a>
                    </div>

                    <div>
                        <a class="btn btn-lg btn-cancelar" href="/products">Cancelar</a>
                        <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Salvar</button>
                    </div>
                </div>
            </form>
            
            <!-- Modal padrão para excluir -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog  modal-dialog-centered  modal-simple">
                    <div class="modal-content">
                        <div id="modal_excluir_body" class="modal-body text-center p-20">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
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
        <script src="{{ asset('modules/products/js/products.js?v=15') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
    @endpush

@endsection
