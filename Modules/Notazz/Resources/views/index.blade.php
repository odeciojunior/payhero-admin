@extends("layouts.master")
@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/notazz/css/index.css?v=' .  versionsFile()) }}">
    <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=03') !!}">
    <style>
        .gray:hover{
            color:#a1a1a1 !important;
        }
    </style>
@endpush
@section('content')
    <div class='page'>
        <div class="page-header container" style="display:none !important;">
            <div class="row justify-content-between">
                <div class="col-lg-8">
                    <h1 class="page-title my-10" style="min-height: 28px">
                        <a href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2" aria-hidden="true"></span>
                            Integrações com Notazz
                        </a>
                    </h1>
                </div>
                <div class="col text-right">
                    <a data-toggle="modal" id='btn-add-integration' class="btn btn-floating btn-primary" style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-add-1" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class='page-content container' id='project-integrated'>
            <div class="row" id="content">
                {{-- js load dynamically --}}
            </div>

            {{-- Modal add-edit integration --}}
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_integracao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-lg d-flex justify-content-center">
                    <div class="modal-content w-450" id="conteudo_modal_add">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 class="modal-title" style="font-weight: 700;"></h4>
                        </div>
                        <div class="pt-10 pr-20 pl-20 modal_integracao_body">
                            @include('notazz::create')
                            @include('notazz::edit')
                        </div>
                        <div class="modal-footer" style="margin-top: 15px">
                            <button id="bt_integration" type="button" class="btn btn-success" data-dismiss="modal"></button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
            {{-- End Modal  --}}
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
                    <p class="desc gray">Integre suas lojas com Notazz de forma totalmente automatizada!</p>
                </div>
        </div>
    </div>
    <!-- Delete -->
    <div id="modal-delete-integration" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" role="dialog" tabindex="-1">
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
                    <button type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                        <b>Cancelar</b>
                    </button>
                    <button project="" type="button" data-dismiss="modal"  class="col-4 btn border-0 btn-delete btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" style="width: 20%;">
                        <b class="mr-2">Excluir </b>
                        <span class="o-bin-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{asset('modules/notazz/js/index.js?v='.uniqid()) }}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    @endpush
@endsection
