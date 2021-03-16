@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css?v=05') }}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=4545') }}">
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

            .badge.badge-success  {
                background-color: #5EE2A1;
            }
        </style>
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6">
                    <h1 class="page-title">Contestações</h1>
                </div>
            </div>
        </div>
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
                            <label for="project">Projeto</label>
                            <select name="project" id="project" class="form-control select-pad"
                                    style='width:100%;' data-plugin="select2">
                                <option value="">Todos projetos</option>
                            </select>
                        </div>

                        <div class="col-sm-12 col-md">
                            <label for='customer'>Cliente</label>
                            <select id="customer" name="customer" class="form-control select-pad"
                                    style='width:100%;height:100%' data-plugin="select2">
                                <option value="">Selecione</option>
                            </select>
                        </div>

                        <div class="col-sm-12 col-md">
                            <label for="customer_document">CPF do Cliente</label>
                            <input name='customer_document' id="customer_document" class="input-pad" data-mask='0#'
                                   placeholder="CPF do Cliente">
                        </div>

                    </div>
                    <div class="row collapse" id="bt_collapse">
                        <div class="d-flex flex-wrap">

                            <div class="col-sm-12 col-md">
                                <label for="status">Status</label>
                                <select name='sale_status' id="status" class="form-control select-pad">
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
                                <label for="date_type">Data</label>
                                <select name='date_type' id="date_type" class="form-control select-pad">
                                    <option value="transaction_date">Data da compra</option>
                                    <option value="adjustment_date">Data da contestação</option>
                                    <option value="expiration_date">Data da expiração</option>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md">
                                <input name='date_range' id="date_range" class="select-pad"
                                       placeholder="Clique para editar..." readonly style="margin-top:30px">
                            </div>

                        </div>


                    </div>
                    <div class="row" style="height: 30px">
                        <div class="col-sm-6 col-xl-3 text-right mt-20 offset-xl-6">
                            <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center"
                                 data-toggle="collapse"
                                 data-target="#bt_collapse"
                                 aria-expanded="false"
                                 aria-controls="bt_collapse">
                                <img id="icon-filtro" src=" {{ asset('/modules/global/img/svg/filter-2-line.svg') }} "/>
                                <span id="text-filtro">Filtros avançados</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3 text-right mt-20">
                            <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} "/>
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
                        <div class="card-body">
                            <h5 class="gray font-size-16"> Total de
                                contestações </h5>
                            <h4 id="total-contestation" class="number"></h4>
                        </div>
                        <div class="s-border-right green"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow" style='display:block;'>
                        <div class="card-body">
                            <h5 class="gray font-size-16"> Valor total
                                de Contestação </h5>
                            <h4 id="total-contestation-value" class="number"></h4>
                        </div>
                        <div class="s-border-right green"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow" style='display:block;'>
                        <div class="card-body">
                            <h5 class="gray font-size-16"> Taxa total de
                                Contestação</h5>
                            <h4 id="total-contestation-tax" class="number"></h4>
                        </div>
                        <div class="s-border-right green"></div>
                    </div>
                </div>

                <div class="col-md-3 d-sm-none d-md-block">
                    <div class="card shadow" style='display:block;'>
                        <div class="card-body">
                            <h5 class="gray font-size-16"> Taxa total de
                                chargeback</h5>
                            <h4 id="total-chargeback-tax" class="number"></h4>
                        </div>
                        <div class="s-border-right green"></div>
                    </div>
                </div>

            </div>

            <div class="card shadow" style="min-height: 300px">
                <div class="page-invoice-table table-responsive">
                    <table id="chargebacks-table" class="table-vendas table table-striped unify" style="width:100%;">
                        <thead>
                        <tr>
                            <td class="table-title">Transação</td>
                            <td class="table-title">Empresa</td>
                            <td class="table-title">Cliente</td>
                            <td class="table-title">Status</td>
                            <td class="table-title">Contestação</td>
                            <td class="table-title">Motivo</td>
                            <td class="table-title">Valor</td>
                            <td class="table-title"></td>
                        </tr>
                        </thead>
                        <tbody id="chargebacks-table-data">
                        {{-- js carrega... --}}
                        </tbody>
                    </table>
                </div>

                <div class="modal fade modal-3d-flip-vertical" id="observation-modal" aria-hidden='true'
                     aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-simple">
                        <div class="modal-content p-20 " style="width: 500px;">
                            <div class="header-modal">
                                <div class="row justify-content-between align-items-center" style="width: 100%;">
                                    <div class="col-lg-2"> &nbsp;</div>
                                    <div class="col-lg-8 text-center"><h4> Arquivos para contestação </h4></div>
                                    <div class="col-lg-2 text-right">
                                        <a role="button" data-dismiss="modal">
                                            <i class="material-icons pointer">close</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-user-observation-body">
                                <div class="form-group">
                                    <label for="observation">Escolha uma categoria</label>
                                    <select name="" class="form-control" id="">
                                        <option value="">Nota fiscal</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="pdf">Enviar os arquivos</label>
                                    <input type="file" id="file_contestation" name="file_contestation" class="form-control" multiple />
                                </div>
                                <div class="row mt-40 mb-0">
                                    <div class="col-12 text-right">
                                        <button id="update-contestation-observation" contestation="" type="button" class="btn btn-success">Enviar</button>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <hr>
                            <div class="col-lg-12 text-center"><h4> Últimos arquivos enviados </h4></div>
                            <ul>
                                <li>xxxxxxx</li>
                            </ul>

                        </div>


                    </div>
                </div>


                <div class="modal fade modal-3d-flip-vertical" id="pdf-modal" aria-hidden='true'
                     aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-simple">
                        <div class="modal-content p-20 " style="width: 500px;">
                            <div class="header-modal">
                                <div class="row justify-content-between align-items-center" style="width: 100%;">
                                    <div class="col-lg-2"> &nbsp;</div>
                                    <div class="col-lg-8 text-center"><h4> Enviar contestação para GETNET </h4></div>
                                    <div class="col-lg-2 text-right">
                                        <a role="button" data-dismiss="modal">
                                            <i class="material-icons pointer">close</i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-user-pdf-body">
                                <div class="form-group">
                                    <label for="pdf"></label>
                                    <input type="file" id="file_contestation" name="file_contestation" class="form-control" />
                                </div>
                                <div class="row mt-40 mb-0">
                                    <div class="col-12 text-right">
                                        <button id="update-contestation-pdf" contestation="" type="button" class="btn btn-success">Enviar contestação</button>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal detalhes da venda-->
                <div class="modal fade example-modal-lg" id="modal_detalhes" aria-hidden="true"
                     aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple modal-sidebar modal-lg">
                        <div id="modal-details" class="modal-content p-20 " style="width: 550px;">
                            <div class="header-modal">
                                <div class="row justify-content-between align-items-center" style="width: 100%;">
                                    <div class="col-lg-2"> &nbsp;</div>
                                    <div class="col-lg-8 text-center"><h4 id="modal_titulo"> Detalhes da venda </h4>
                                    </div>
                                    <div class="col-lg-2 text-right">
                                        <a role="button" data-dismiss="modal">
                                            <i class="material-icons pointer">close</i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <!-- End Modal detalhes da venda -->
            </div>
            <ul id="pagination" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                {{-- js carrega... --}}
            </ul>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/modules/chargebacks/js/contestations-index.js?v='. random_int(100, 10000)) }}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src="{{ asset('modules/global/js/daterangepicker.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    @endpush

@endsection

