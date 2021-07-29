@extends("layouts.master")

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=4546') }}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=03') }}">
        <style>
            .gray:hover{
                color:#a1a1a1 !important;
            }

            a o-arrow-right {
                color: inherit;
            }

            #card-integration-data .card .title {
                font-size: 16px;
                font-weight: 400;
            }
            #card-integration-data .card .number {
                margin-bottom: 0;
                font-size: 24px;
                font-weight: 700;
                color: #5D5D5D;
            }

            #content-script .input-group .input-group-text {
                font-size: 16px;
                font-weight: 700;
                border-color: #2E85EC;
                border-top-left-radius: 8px;
                border-bottom-left-radius: 8px; 
            }
            #content-script .input-group .form-control[readonly] {
                background-color: #ffffff;
                border-color: #ffffff;
                border-right: 1px solid #F4F4F4;
                font-size: 16px;
            }
            #content-script .input-group .btn {
                border-top-right-radius: 8px;
                border-bottom-right-radius: 8px; 
                padding-left: 8px;
                padding-right: 8px;
                color: #3D4456;
            }
            #content-script .input-group .btn:hover {
                color: inherit;
            }

            .table td {
                padding: 18px 25px !important;
            }
            .table thead > tr > td {
                padding: 25px !important;
            }
            
            .table td .description {
                font-size: 16px;
                font-weight: 700;
                color: #5D5D5D;
                margin: 0;
            }
            .table td small {
                font-size: 12px;
                font-weight: 400;
                color: #B9B9B9;
            }
            .table td .input-group .form-control:not(:last-child) {
                border-top-left-radius: 8px;
                border-bottom-left-radius: 8px; 
                font-size: 16px;
            }
            .table td .input-group .form-control {
                border-color: #CECECE;
            }
            .table td .input-group .btn {
                border: 1px solid #CECECE;
                border-top-right-radius: 8px;
                border-bottom-right-radius: 8px;
                padding-left: 8px;
                padding-right: 8px;
                color: #3D4456;
            }
            .table td .input-group .btn:hover {
                color: inherit;
            }
            td .badge {
                font-size: 14px;
                padding: 10px 24px;
            }
            td .badge.badge-personal {
                background-color: #9E00D6;
            }
            td .badge.badge-warning {
                background-color: #FF9900;
            }

            .table td .btn.pointer {
                padding-left: .5rem; 
                padding-right: .5rem;
            }

            .btn:not(:disabled):not(.disabled):active:focus {
                box-shadow: none !important;
            }
        </style>
    @endpush
    <div class="page">
        <div style="display: none" class="page-header container">
            <button id="store-integrate" type="button" class="btn btn-floating btn-primary" style="position: relative; float: right" {{--data-target='#modal' data-toggle='modal'--}}>
                <i class="o-add-1" aria-hidden="true"></i>
            </button>
            <h1 class="page-title my-10" style="min-height: 28px">
                <span class="">
                    <img src="{{ asset('modules/global/img/svg/api-sirius.png') }}" style="height: 40px; width: auto;" alt="API Sirius">
                </span>
                <span class="" style="line-height: 40px;">
                    API Sirius
                </span>
            </h1>
            <p id='text-info' style="margin-top: 12px;">Use nosso checkout API ou faça integrações externas.</p>
            <br>

            <div class="" id='card-integration-data' style='display:none;'>
                <div class="row justify-content-center">
                    <div class="col-md-3 col-sm-12">
                        <div class="card shadow p-20 mb-0">
                            <p class="title">Integrações cadastradas</p>
                            <p id='integrations_stored' class="number"></p>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12">
                        <div class="card shadow p-20 mb-0">
                            <p class="">Integrações ativas</p>
                            <p id='integrations_active' class="number"></p>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12">
                        <div class="card shadow p-20 mb-0">
                            <p class="">Posts recebidos</h3>
                            <p id='posts_received' class="number"></p>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-12">
                        <div class="card shadow p-20 mb-0">
                            <p class="">Posts enviados</p>
                            <p id='posts_sent' class="number"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container" id='page-integrates'>
            <div id="content-error" class='' style='display:none;'>
                <div class="content-modal-error text-center" style=''>
                    <img src="modules/global/img/empty.svg" width="250px"/>
                    <h4 class="big gray" style='width:100%'>Você ainda não cadastrou integrações!</h4>
                </div>
            </div>

            <div class="mb-30" id="content-script" style='display:none;'>
                <div class="row">
                    <div class="col-9">
                        <div class="input-group input-group-lg" id="script-antifraud" style="display: none">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary">
                                    <span class="icon-antifraud-1 mr-5"></span> Script do Antifraude para Checkout API
                                </span>
                            </div>
                            <input type="text" class="form-control" id="input-url-antifraud" readonly>
                            <div class="input-group-prepend">
                                <button class="btn btn-primary bg-white btnCopiarLinkAntifraud" type="button" data-toggle="tooltip" title="Copiar URL antifraud">
                                    <span class="icon-copy-2"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 text-right">
                        <a href="" class="font-weight-bold d-flex justify-content-end align-items-center" style="line-height: 46px;">
                            <span>Acesse a documentação da API</span>
                            <span class="o-arrow-right-1 ml-15" style="color: #2E85EC;" aria-hidden="true"></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow" id='card-table-integrate' data-plugin="matchHeight" style='display:none;'>
                <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">
                    <table class="table table-striped unify">
                        <thead>
                            <tr>
                                <td class="table-title">Descrição</td>
                                <td class="table-title text-center">Tipo</td>
                                <td class="table-title">Token</td>
                                <td class="table-title"></td>
                            </tr>
                        </thead>
                        <tbody id='table-body-integrates'>
                            {{-- js integrates carrega  --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <ul id="pagination-integrates" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
                {{-- js pagination carrega --}}
            </ul>

            <!-- Modal para criar integração -->
            <div class="modal fade modal-3d-flip-vertical modal-new-layout" id="modal-integrate" role="dialog" tabindex="-1">
                <div id='mainModalBody' class="modal-dialog modal-dialog-centered modal-simple">
                    <div id="modal-create-integration" class="modal-content">                        
                        <div class="modal-header simple-border-bottom mb-10">
                            <div class="row">
                                <div class="col-11">
                                    <h4 class="modal-title bold" id="modal-title-plan"><span class="ml-15">Nova Integração </span></h4>
                                </div>
                                <div class="col-1" align="right">
                                    <a id="modal-button-close" class="pointer close btn-close-add-plan" role="button" data-dismiss="modal" aria-label="Close">
                                        <i class="material-icons md-16">close</i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div id="modal-reverse-body" class="modal-body px-0 pb-0">
                            <div id="body-modal" class="container-fluid p-15">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="description">Descrição</label>
                                        <input name="description" type="text" class="form-control" id="description" placeholder="Descrição">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="token_type_enum">Tipo de Integração</label>
                                        <div id="enum-list">
                                            <select name="token_type_enum" class="form-control select-enum-list">
                                                <option value="2">Acesso Pessoal</option>
                                                <option value="3">Integração Externa</option>
                                                <option value="4">Checkout API</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row companies-container d-none">
                                    <div class="form-group col-sm-12 col-md">
                                        <label for="empresa">Empresa</label>
                                        <select name="company_id" id="companies" class="form-control">
                                            <option value="">Todas empresas</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row postback-container d-none">
                                    <div class="form-group col-sm-12 col-md">
                                        <label for="postback">Postback</label>
                                        <input name="postback" type="text" class="form-control" id="postback" placeholder="Postback">
                                        <small class="text-muted">Insira uma url válida para receber as notificações referentes a integração</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button id="btn-save-integration" type="button" class="btn btn-lg btn-primary">Gerar Token</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar integração -->
    <div class="modal fade modal-3d-flip-vertical modal-new-layout" id="modal-edit-integration" role="dialog" tabindex="-1">
        <div id='mainModalBody' class="modal-dialog modal-dialog-centered modal-simple">
            <div id="modal-create-integration" class="modal-content">                        
                <div class="modal-header simple-border-bottom mb-10">
                    <div class="row">
                        <div class="col-11">
                            <h4 class="modal-title bold" id="modal-title-plan"><span class="ml-15">Editar Integração </span></h4>
                        </div>
                        <div class="col-1" align="right">
                            <a id="modal-button-close" class="pointer close btn-close-add-plan" role="button" data-dismiss="modal" aria-label="Close">
                                <i class="material-icons md-16">close</i>
                            </a>
                        </div>
                    </div>
                </div>

                <div id="modal-reverse-body" class="modal-body px-0 pb-0">
                    <div id="body-modal" class="container-fluid p-15">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="description">Descrição</label>
                                <input name="description" type="text" class="form-control" id="description" placeholder="Descrição">
                            </div>
                        </div>
                        <div class="row postback-container" style="display: none;">
                            <div class="form-group col-sm-12 col-md">
                                <label for="postback">Postback</label>
                                <input name="postback" type="text" class="form-control" id="postback" placeholder="Postback">
                                <small class="text-muted">Insira uma url válida para receber as notificações referentes a integração</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="token_type_enum">
                    <button id="btn-edit-integration" type="button" class="btn btn-lg btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal padrão para excluir -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical modal-new-layout" id="modal-delete-integration" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id='btn-cancel-integration' type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                        <b>Cancelar</b>
                    </button>
                    <button id="btn-delete-integration" type="button" class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                        <b class="mr-2">Excluir </b>
                        <span class="o-bin-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para reenviar convite -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical modal-new-layout" id="modal-refresh-integration" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons" style="font-size: 80px;color:#16b248;"> loop </i>
                    </div>
                    <h4 class="black"> Você realmente deseja regerar o token? </h4>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id='btn-cancel' type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                    <button id="btn-refresh-integration" type="button" class="col-4 btn btn-success" style="width: 20%;" data-dismiss="modal">Regerar</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{asset('modules/integrations/js/index.js?v='.uniqid()) }}"></script>
    @endpush

@endsection

