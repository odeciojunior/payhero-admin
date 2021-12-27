@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href='{{asset('/modules/sales/css/index.css?v=' . uniqid())}}'>
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=10') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=10') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
        <style>
            .select2-selection--single {
                border: 1px solid #dddddd !important;
                border-radius: .215rem !important;
                height: 43px !important;
            }

            .select2-selection__rendered {
                color: #707070 !important;
                font-size: 16px !important;
                font-family: 'Muli', sans-serif;
                line-height: 43px !important;
                padding-left: 14px !important;
                padding-right: 38px !important;
            }

            .select2-selection__arrow {
                height: 43px !important;
                right: 10px !important;
            }

            .select2-selection__arrow b {
                border-color: #8f9ca2 transparent transparent transparent !important;
            }

            .select2-container--open .select2-selection__arrow b {
                border-color: transparent transparent #8f9ca2 transparent !important;
            }

            .badge {
                color: white;
                padding: 5px 15px !important;
                border-radius: 16px;
                font-weight: 700;
            }

            .badge.badge-success {
                background-color: #5EE2A1;
            }

            #check-status-text-icon {
                background-color: #5EE2A1;
                color: white;
                font-size: 12px;
                padding: 5px;
                border-radius: 50px;
                margin-top: -12px;
            }

        </style>
    @endpush

    <!-- Page -->
    <div class="page mb-0">
        <div style="display: none" class="page-header container" id="page_header">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6">
                    <h1 class="page-title">Contestações</h1>
                </div>
            </div>
        </div>
        <div id="project-not-empty" style="display:none">
            <div class="page-content container">
                <div class="fixhalf"></div>
                <form id='filter_form' action='{{ route('contestations.getchargebacks') }}' method='GET'>
                    @csrf
                    <div id="" class="card shadow p-20">
                        <div class="row align-items-baseline mb-md-15">

                            <div class="col-sm-12 col-md">
                                <label for="transaction">Transação</label>
                                <input name="transaction" id="transaction" class="input-pad" placeholder="Transação">
                            </div>

                            <div class="col-sm-12 col-md">
                                <label for="is_expired">Expiração</label>
                                <br>
                                <select name='is_expired' id="is_expired" class="form-control input-pad">
                                    <option value="0">Ambos</option>
                                    <option value="1">Expirado</option>
                                    <option value="2" selected>Não expirado</option>
                                </select>
                            </div>


                            <div class="col-sm-12 col-md">
                                <label for="date_type">Data</label>
                                <select name='date_type' id="date_type" class="form-control input-pad">
                                    <option value="expiration_date">Data da expiração</option>
                                    <option value="transaction_date">Data da compra</option>
                                    <option value="adjustment_date">Data da contestação</option>
                                </select>
                            </div>

                            <div class="col-sm-12 col-md">
                                <div class="form-group form-icons">
                                    <label for="date_type">&nbsp;</label>
                                    <i style="right: 24px;top: 41px;"
                                       class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-20"></i>
                                    <input name='date_range' id="date_range" class="input-pad pr-30"
                                           placeholder="Clique para editar..." readonly style="">
                                </div>
                            </div>
                        </div>
                        <div class="collapse" id="bt_collapse">

                            <div class="row">
                                <div class="col-sm-12 col-md">
                                    <label for="project">Projeto</label><br>
                                    <select name='project' id="project" class="form-control input-pad">
                                        <option value="">Todos projetos</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md">
                                    <label for="is_contested">Concluído</label>
                                    <br>
                                    <select name='is_contested' id="is_contested" class="form-control input-pad">
                                        <option value="0">Ambos</option>
                                        <option value="1">Concluído</option>
                                        <option value="2">Não concluído</option>
                                    </select>
                                </div>

                                <div class="col-sm-12 col-md">
                                    <label for="status">Status</label>
                                    <select name='sale_status' id="status" class="form-control input-pad">
                                        <option value="0">Todos status</option>
                                        <option value="1">Aprovado</option>
                                        <option value="2">Aguardando pagamento</option>
                                        <option value="3">Recusado</option>
                                        <option value="4">ChargeBack</option>
                                        {{--                                <option value="6">Em análise</option>--}}
                                        <option value="7">Estornado</option>
                                        <option value="5">Cancelada</option>
                                        <option value="10">BlackList</option>
                                        <option value="20">Revisão Antifraude</option>
                                        <option value="21">Cancelada antifraude</option>
                                        <option value="chargeback_recovered">Chargeback recuperado</option>
                                        <option value="99">Erro Sistema</option>
                                        <option value='24'>Em Disputa</option>
                                        <option value='6'>Em Processo</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md">
                                    <label for='customer'>Cliente</label>
                                    <select id="customer" name="customer" class="form-control select-pad"
                                            style='width:100%;height:100%' data-plugin="select2">
                                        <option value="">Selecione</option>
                                    </select>
                                </div>

                            </div>
                        </div>

                        <div class="row" style="height: 30px">
                            <div class="col-6 col-xl-3 mt-20 offset-xl-6 pr-0">
                                <div
                                    class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center"
                                    data-toggle="collapse"
                                    data-target="#bt_collapse"
                                    aria-expanded="false"
                                    aria-controls="bt_collapse">
                                    <img id="icon-filtro"
                                         class="hidden-xs-down"
                                         src=" {{ asset('/modules/global/img/svg/filter-2-line.svg') }} "/>
                                    <span id="text-filtro">Filtros avançados</span>
                                </div>
                            </div>
                            <div class="col-6 col-xl-3 mt-20">
                                <div id="bt_filtro"
                                     class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                    <img style="height: 12px; margin-right: 4px"
                                         class="hidden-xs-down"
                                         src=" {{ asset('/modules/global/img/svg/check-all.svg') }} "/>
                                    Aplicar filtros
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="fixhalf"></div>

                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <div class="card shadow" style='display:block;'>
                            <div class="card-body ">
                                <h5 class="font-size-14 gray-600 ">N° de contestações</h5>
                                <h4 class="total-number"><span class="font-size-30 bold "
                                                               id="total-contestation"></span><span
                                        id="total-contestation-tax" style="color:#959595"></span></h4>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow" style='display:block;'>
                            <div class="card-body">
                                <h5 class="font-size-14 gray-600">Resultantes em chargeback</h5>
                                <h4 class="total-number"><span class="font-size-30 bold "
                                                               id="total-chargeback-tax-val"></span>
                                    <span id="total-chargeback-tax" style="color:#959595"></span></h4>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow" style='display:block;'>
                            <div class="card-body">
                                <h5 class="font-size-14 gray-600">Total</h5>
                                <h4 class="total-number" style=""><span style="color:#959595">R$ </span><span class="font-size-30 bold"
                                                                           id="total-contestation-value"></span></h4>
                            </div>
                            <div class="s-border-right yellow"></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div style="display:block;">
                            <div>
                                <h5 class="font-size-14 gray-600"> O que são contestações? </h5>
                                <p>
                                    São ocorrências enviadas pelas operadoras de crédito
                                    após contestações do titular do cartão.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card shadow" style="min-height: 300px">
                    <div class="page-invoice-table table-responsive">
                        <table id="chargebacks-table" class="table-vendas table table-striped unify mb-0"
                               style="width:100%;">
                            <thead>
                            <tr class="">
                                <td class="table-title">Transação</td>
                                <td class="table-title" style="min-width: 200px; text-align:left">Empresa</td>
                                <td class="table-title">Compra</td>
                                <td class="table-title" style="min-width: 150px;">Status</td>
                                <td class="table-title" style="min-width: 170px;">Prazo para recurso</td>
                                <td class="table-title">Motivo</td>
                                {{--                            <td class="table-title">Valor</td>--}}
                                <td class="table-title" style="min-width: 100px;"></td>
                            </tr>
                            </thead>
                            <tbody id="chargebacks-table-data" img-empty="{!! asset('modules/global/img/contestacoes.svg')!!}">
                            {{-- js carrega... --}}
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="row justify-content-center justify-content-md-end">
                    <ul id="pagination" class="pl-5 pr-md-15 mb-25">
                        {{-- js carrega... --}}
                    </ul>
                </div>
                @include('chargebacks::contestations-files')
                @include('sales::details')

            </div>
            {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
            {{-- FIM projeto nao existem projetos--}}
        </div>
        @push('scripts')
            <script src="{{ asset('/modules/chargebacks/js/contestations-index.js?v='. random_int(100, 10000)) }}"></script>
            <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
            <script src="{{ asset('modules/global/js/daterangepicker.min.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    @endpush

@endsection

