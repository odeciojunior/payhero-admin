@extends("layouts.master")
@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/digitalmanager/css/index.css') }}">
    <style>
        .o-arrow-right-1 {
            font-size: 30px;
        }

        .o-arrow-right-1::before {
            transform: rotate(180deg);
        }
        .gray:hover{
            color:#a1a1a1 !important;
        }
     </style>
@endpush
@section('content')
    <div class='page'>
        <div style="display: none !important;" class="page-header container">
            <div class="row justify-content-between">
                <div class="col-lg-8">
                    <h1 class="page-title my-10" style="min-height: 28px">
                        <a class="gray" href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2 gray" aria-hidden="true"></span>
                            Integrações Smart Funnel
                        </a>
                    </h1>
                </div>
                <div class="col text-right" id="integration-actions" style="display:none">
                    <a id='btn-add-integration' class="btn btn-floating btn-primary"
                        style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-add-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        @include('projects::empty')
        <div class='page-content container' id='project-integrated'>
            <div class='col-md-12'>
                <div class="row" id="content">
                </div>
            </div>
            <!-- Modal add integração -->
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_integracao"
                    aria-hidden="true"
                    aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg d-flex justify-content-center">
                    <div class="modal-content w-450" id="conteudo_modal_add">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="font-weight: 700;"></h4>
                        </div>
                        <div class="pt-10 pr-20 pl-20 modal_integracao_body">
                            @include('smartfunnel::create')
                            @include('smartfunnel::edit')
                        </div>
                        <div class="modal-footer" style="margin-top: 15px">
                            <button id="bt_integration" type="button" class="btn btn-success"
                                    data-dismiss="modal"></button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
            <div id="modal-project" class="modal fade modal-3d-flip-vertical " role="dialog" tabindex="-1">
                <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
                    <div id="conteudo_modal_add" class="modal-content p-10">
                        <div class="header-modal simple-border-bottom">
                            <h2 id="modal-project-title" class="modal-title"></h2>
                        </div>
                        <div id="modal_project_body" class="modal-body simple-border-bottom"
                                style='padding-bottom:1%;padding-top:1%;'>
                        </div>
                        <div id='modal-withdraw-footer' class="modal-footer">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="no-integration-found" class='justify-content-center' style="display:none !important;
                                                                height: 100%; 
                                                                width: 100%; 
                                                                position: absolute;
                                                                display: -webkit-flex;
                                                                display: flex;
                                                                -webkit-align-items: center;
                                                                align-items: center;
                                                                -webkit-justify-content: center;
                                                                justify-content: center;
                                                                padding-bottom:116px">
            <div class="content-error text-center">
                <img src="{!! asset('modules/global/img/aplicativos.svg') !!}" width="250px">
                <h1 class="big gray"><strong>Nenhuma integração encontrada!</strong></h1>
                <p class="desc gray">Integre seus projetos com Smart Funnel de forma totalmente automatizada!</p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="/modules/smartfunnel/js/index.js?v=s0"></script>
    @endpush
@endsection
