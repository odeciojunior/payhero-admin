@extends('layouts.master')

@section('content')
@push('css')
<link rel="stylesheet" href="{{ mix('build/layouts/products/edit.min.css') }}">
@endpush

<!-- Page -->
<div class="page">

    @include('layouts.company-select',['version'=>'mobile'])

    <div style="display: none;" class="page-header container">
        <h1 class="page-title my-10" style="min-height: 28px">
            <a href="/products">
                <span class="o-arrow-right-1 font-size-30 ml-2" aria-hidden="true"></span>
                Editar produto digital
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
                                <small class="text-center text-muted mt-15">Sugerimos PNG ou JPG com 650px x 650px
                                    (1:1).</small>
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
                                <small class="text-center text-muted mt-15">Este é o arquivo que será enviado ao cliente
                                    após a confirmação da venda.</small>
                                <div class="d-flex flex-row align-items-center mt-10 px-2 py-1 rounded" style="width: fit-content; background: #F4F6FB;">
                                    <img src="{{ mix('build/global/img/icon-info-plans-c.svg') }}" width="9" height="9" />
                                    <span class="font-size-10 ml-2" style="color: #636363">Essa opção é exclusiva para produtos digitais.</span>
                                </div>
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

            <div class="form-buttons" style="display: block; text-align: right">
                <div style="float: left">
                    <a class="btn btn-lg btn-excluir delete-product" data-toggle="modal" data-target="#modal-delete" href="#">
                        <svg width="20" height="23" viewBox="0 0 20 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4.00002C12 2.89545 11.1046 2.00002 10 2.00002C8.89543 2.00002 8 2.89545 8 4.00002H6.66667C6.66667 2.15907 8.15905 0.666687 10 0.666687C11.8409 0.666687 13.3333 2.15907 13.3333 4.00002H19.3333C19.7015 4.00002 20 4.2985 20 4.66669C20 5.03488 19.7015 5.33335 19.3333 5.33335H18.5947L16.8666 20.3057C16.7113 21.6513 15.572 22.6667 14.2175 22.6667H5.7825C4.428 22.6667 3.28867 21.6513 3.13341 20.3057L1.404 5.33335H0.666667C0.339387 5.33335 0.0671889 5.09752 0.0107409 4.78652L0 4.66669C0 4.2985 0.298477 4.00002 0.666667 4.00002H12ZM17.2507 5.33335H2.748L4.45796 20.1529C4.53558 20.8256 5.10525 21.3334 5.7825 21.3334H14.2175C14.8948 21.3334 15.4644 20.8256 15.542 20.1529L17.2507 5.33335ZM8 8.66669C8.32728 8.66669 8.59948 8.87304 8.65593 9.14517L8.66667 9.25002V17.4167C8.66667 17.7389 8.36819 18 8 18C7.67272 18 7.40052 17.7937 7.34407 17.5215L7.33333 17.4167V9.25002C7.33333 8.92785 7.63181 8.66669 8 8.66669ZM12 8.66669C12.3273 8.66669 12.5995 8.87304 12.6559 9.14517L12.6667 9.25002V17.4167C12.6667 17.7389 12.3682 18 12 18C11.6727 18 11.4005 17.7937 11.3441 17.5215L11.3333 17.4167V9.25002C11.3333 8.92785 11.6318 8.66669 12 8.66669Z" fill="#212121" />
                        </svg>

                        Excluir produto
                    </a>
                </div>

                <div style="float: right">
                    <a class="btn btn-lg btn-cancelar" href="/products">Cancelar</a>
                    <button type="submit" class="btn btn-primary btn-lg ml-15"><img style="height: 12px; margin-right: 4px" src="{{ mix('build/global/img/svg/check-all.svg') }}">Salvar</button>
                </div>
                <div style="clear:both"></div>
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
                    {{-- <h4 class="modal-title font-size-20 text-center">Encontramos dados que precisam ser atualizados!</h4> --}}
                    <i class="material-icons gradient text-center" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    <span class="py-1 text-center">
                        Impossível alterar pois já existe um plano cadastrado com esse produto, você precisa excluir o
                        plano para depois alterá-lo para Digital.
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
<script src="{{ mix('build/layouts/products/edit.min.js') }}"></script>
@endpush
@endsection
