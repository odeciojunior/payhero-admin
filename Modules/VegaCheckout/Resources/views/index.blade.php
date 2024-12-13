@extends('layouts.master')

@push('css')
    <style>
        .page-title > a,
        .page-title > a > span {
            color: #707070;
        }

        .o-arrow-right-1 {
            font-size: 30px;
        }

        .o-arrow-right-1::before {
            transform: rotate(180deg);
        }

        .gray:hover {
            color: #a1a1a1 !important;
        }
    </style>
@endpush

@section('content')
    <div class='page'>

        @include('layouts.company-select',['version'=>'mobile'])

        <div style="display: none !important;" class="page-header container">
            <div class="row jusitfy-content-between">
                <div class="col-lg-8">
                    <h1 class="page-title my-10"
                        style="min-height: 28px">
                        <a href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2"
                                  aria-hidden="true"></span>
                            Integrações vegacheckout
                        </a>
                    </h1>
                </div>
                <div class="col text-right"
                     id="integration-actions"
                     style="display:none">
                    <a id='btn-add-integration'
                       class="btn btn-floating btn-primary"
                       style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-add-1"
                           aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class='page-content container'
             id='project-integrated'>
            <div class='col-md-12'>
                <div class="row"
                     id="content">
                </div>
            </div>
        </div>

        @include('projects::empty')

        <div id="no-integration-found"
             class='justify-content-center'
             style="display:none !important;
                    height: 100%;
                    width: 100%;
                    position: absolute;
                    display: -webkit-flex;
                    display: flex;
                    -webkit-align-items: center;
                    align-items: center;
                    -webkit-justify-content: center;
                    justify-content: center;">
            <div class="content-error text-center">
                <img src="{!! mix('build/global/img/aplicativos.svg') !!}"
                     width="250px">
                <h1 class="big gray"><strong>Nenhuma integração encontrada!</strong></h1>
            </div>
        </div>
    </div>

    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
         id="modal_add_integracao"
         aria-hidden="true"
         aria-labelledby="exampleModalTitle"
         role="dialog"
         tabindex="-1">
        <div class="modal-dialog modal-lg d-flex justify-content-center">
            <div class="modal-content w-450"
                 id="conteudo_modal_add">
                <div class="modal-header">
                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title"
                        style="font-weight: 700;"></h4>
                </div>
                <div class="pr-20 pl-20 modal_integracao_body">
                    @include('vegacheckout::create')
                    @include('vegacheckout::show')
                </div>
                <div class="modal-footer"
                     style="margin-top: 15px">
                    <button id="bt_integration"
                            type="button"
                            class="btn btn-success"
                            data-dismiss="modal"></button>
                    <button type="button"
                            class="btn btn-primary"
                            data-dismiss="modal">Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-project"
         class="modal fade modal-3d-flip-vertical "
         role="dialog"
         tabindex="-1">
        <div id="modal_add_size"
             class="modal-dialog modal-dialog-centered modal-simple ">
            <div id="conteudo_modal_add"
                 class="modal-content p-10">
                <div class="header-modal simple-border-bottom">
                    <h2 id="modal-project-title"
                        class="modal-title"></h2>
                </div>
                <div id="modal_project_body"
                     class="modal-body simple-border-bottom"
                     style='padding-bottom:1%;padding-top:1%;'>
                </div>
                <div id='modal-withdraw-footer'
                     class="modal-footer">
                </div>
            </div>
        </div>
    </div>

    <div id="modal-delete-integration" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true"
         role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button type="button"
                            class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                            data-dismiss="modal" style="width: 20%;">
                        <b>Cancelar</b>
                    </button>
                    <button id_code="" type="button" data-dismiss="modal"
                            class="col-4 btn border-0 btn-delete btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                            style="width: 20%;">
                        <b class="mr-2">Excluir </b>
                        <span class="o-bin-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="{{ mix('build/layouts/vegacheckout/index.min.js') }}"></script>
@endpush
