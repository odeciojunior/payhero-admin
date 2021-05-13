@extends("layouts.master")

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=4545') }}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=02') }}">
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
    <div class="page">
        <div style="display: none" class="page-header container">
            <button id="store-integrate" type="button" class="btn btn-floating btn-primary" style="position: relative; float: right" {{--data-target='#modal' data-toggle='modal'--}}>
                <i class="o-add-1" aria-hidden="true"></i>
            </button>
            <h1 class="page-title my-10" style="min-height: 28px">
                <a class="gray" href="/apps">
                    <span class="o-arrow-right-1 font-size-30 ml-2 gray" aria-hidden="true"></span>
                    Integrações
                </a>
            </h1>
            <p id='text-info' style="margin-top: 12px;">Crie chaves de acesso para que apps de terceiros se conectem a CloudFox</p>
            <br>
            <div class="card shadow p-20 mb-0" id='card-integration-data' style='display:none;'>
                <div class="row justify-content-center">
                    <div class="col-md-3 col-sm-12">
                        <h6 class="text-center orange-gradient">
                            <i class="material-icons align-middle mr-1 orange-gradient"> add_to_queue </i> Integrações cadastradas
                        </h6>
                        <h4 id='integrations_stored' class="number text-center orange-gradient"></h4>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <h6 class="text-center green-gradient">
                            <i class="material-icons align-middle green-gradient mr-1"> airplay </i> Integrações ativas
                        </h6>
                        <h4 id='integrations_active' class="number text-center green-gradient"></i>
                        </h4>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <h6 class="text-center orange-gradient">
                            <i class="material-icons align-middle orange-gradient"> cloud_download </i> Posts recebidos
                        </h6>
                        <h4 id='posts_received' class="number text-center orange-gradient" style='color:green'></i>
                        </h4>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <h6 class="text-center green-gradient">
                            <i class="material-icons align-middle green-gradient"> cloud_upload </i> Posts enviados
                        </h6>
                        <h4 id='posts_sent' class="number text-center green-gradient"></i>
                        </h4>
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
            <div class="card shadow" id='card-table-integrate' data-plugin="matchHeight" style='display:none;'>
                <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">
                    <table class="table table-striped unify">
                        <thead>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Token</th>
                            <th></th>
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
            <div class="modal fade modal-3d-flip-vertical" id="modal-integrate" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div id='mainModalBody' class="modal-dialog modal-simple">
                    <div id="modal-create-integration" class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h4 id="modal-reverse-title" class="modal-title" style="width: 100%; text-align:center">Nova Integração</h4>
                        </div>
                        <div id="modal-reverse-body" class="modal-body">
                            <div id="body-modal">
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
                                            <select name="token_type_enum" class="select-enum-list">
                                                <option value="2">Acesso Pessoal</option>
                                                <option value="3">Integração Externa</option>
                                                <option value="4">Checkout API</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row companies-container d-none">
                                    <div class="col-sm-12 col-md">
                                        <label for="empresa">Empresa</label>
                                        <select name="company_id" id="companies" class="form-control select-pad">
                                            <option value="">Todas empresas</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 35px">
                                    <div class="form-group col-12 text-right">
                                        <button id="btn-save-integration" type="button" class="btn btn-primary">Gerar Token</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal padrão para excluir -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-integration" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-refresh-integration" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
        <script src="{{asset('modules/integrations/js/index.js?v=s0') }}"></script>
    @endpush

@endsection

