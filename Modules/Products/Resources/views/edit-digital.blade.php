@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!!  asset('/modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
        <link rel="stylesheet" href="{{ asset('/modules/products/css/edit.css?v=01') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container">
            <h1 class="page-title">Editar produto digital</h1>
            <p class="desc mt-10"> Preencha os dados sobre seu produto atentamente. </p>
        </div>
        <div class="page-content container">
            <form id='my-form'>
                @method('PUT')
                <div class="panel pt-30 p-30" data-plugin="matchHeight">
                    <div class="row justify-content-between align-items-baseline">
                        <div class="col-4">
                            <div class="d-flex flex-column" id="div_img" style="position: relative">
                                <div class="d-flex flex-column" id="div_digital_product_upload">
                                    <label for="digital_product_url">Imagem do produto</label>
                                    <input type="file" id="digital_product_url" name="digital_product_url" data-height="300" data-max-width="300">
                                    <small class="text-center text-muted mt-15">Sugerimos PNG ou JPEG com 650px x 650px (1:1).</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="name">Nome do produto</label>
                                    <input name="name" type="text" class="input-pad" id="name" placeholder="Digite o produto">
                                </div>
                                <div class="form-group col-12">
                                    <label for="description">Descrição</label>
                                    <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Descrição apresentada no checkout"></textarea>
                                    <small class="text-muted"> Máximo 30 caracteres. </small>
                                </div>

                                <div class='form-group col-lg-6 div-expiration-time' style='display:none;'>
                                    <label for="url_expiration_time">Tempo de expiração da url (em horas)</label>
                                    <div class="d-flex input-group">
                                        <input type="text" min="0" class="form-control" name="url_expiration_time" id="url_expiration_time" placeholder="Tempo de expiração da url em horas" maxlength='5' data-mask="0#" >
                                    </div>
                                </div>

                                <div class="form-group col-12">
                                    <label for="product">Arquivo do produto</label>
                                    <input type="file" id="digital_product" name="digital_product">
                                    <small class="text-center text-muted mt-15">Este é o arquivo que será enviado ao cliente após a confirmação da venda.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row float-right mr-10">
                    <button type="button" class="btn btn-light btn-lg">Cancelar</button>
                    <button type="button" class="btn btn-danger delete-product btn-lg ml-15" data-toggle="modal" data-target="#modal-delete">Excluir produto</button>
                    <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Salvar</button>
                </div>
            </form>
            <!-- Modal padrão para excluir -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog  modal-dialog-centered  modal-simple">
                    <div class="modal-content">
                        <div class="modal-header text-center">
                            <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="close-modal-delete">
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
        <script src="{{ asset('modules/products/js/products.js?v=05') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
        <script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
    @endpush

@endsection
