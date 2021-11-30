@extends('layouts.master')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/attendance/css/index.css?v=06') }}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=100?v=02') }}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/select2.min.css')  }}"/>
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
                width: 190px !important;
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
        </style>
    @endpush

    <div class="page mb-0">
        <div style="display: none" class="page-header container">
            <h1 class="page-title">Atendimento</h1>
        </div>
        <div id="project-not-empty" style="display: none">
            <div class="page-content container">
                <div class='row'>
                    <div class='col-12 col-lg-12'>
                        <div class="card p-20">
                            <div class='row'>
                                <div class='col-sm-12 col-md'>
                                    <div class='form-group'>
                                        <label>Status</label>
                                        <select id='status-filter' class='form-control select-pad'>
                                            <option value="">Selecione...</option>
                                            <option value="open">Em aberto</option>
                                            <option value="mediation">Em mediação</option>
                                            <option value="closed">Resolvido</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='col-sm-12 col-md'>
                                    <div class='form-group'>
                                        <label>Motivo</label>
                                        <select id='category-filter' class='form-control select-pad'>
                                            <option value="">Selecione...</option>
                                            <option value="complaint">Reclamação</option>
                                            <option value="doubt">Dúvida</option>
                                            <option value="suggestion">Sugestão</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='col-sm-12 col-md'>
                                    <div class="form-group form-icons">
                                        <label for="date_range">Data</label>
                                        <i style="right: 23px;bottom: 11px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-18"></i>
                                        <input name='date_range' id="date_range" class="form-control bg-white pr-30 input-pad"
                                               placeholder="Clique para editar..." readonly>
                                    </div>
                                </div>
                                <div class='col-sm-12 col-md'>
                                    <div class='form-group'>
                                        <label>Cliente</label>
                                        <input id='customer-filter' class='form-control input-pad' type='text' placeholder='Nome do cliente'>
                                    </div>
                                </div>
                                <div class='col-sm-12 col-md'>
                                    <div class='form-group'>
                                        <label>CPF do Cliente</label>
                                        <input id='cpf-filter' class='form-control input-pad' type='text' placeholder='CPF do cliente'>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse" id="bt_collapse">
                                <div class="row">
                                    <div class='col-sm-12 col-md'>
                                        <div class='form-group'>
                                            <label>Código do chamado</label>
                                            <input id='ticker-code-filter' class='form-control input-pad' type='text'
                                                   placeholder='Código do chamado'>
                                        </div>
                                    </div>
                                    <div class='col-sm-12 col-md'>
                                        <div class='form-group'>
                                            <label>Respostas</label>
                                            <select id='answered' class='form-control select-pad'>
                                                <option value="">Todos</option>
                                                <option value="last-answer-admin">Última resposta minha</option>
                                                <option value="last-answer-customer">Última resposta do cliente</option>
                                                <option value="not-answered">Não respondidos</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md">
                                        <label for="project">Projeto</label>
                                        <select id="project" class="form-control select-pad">
                                            <option value="">Todos projetos</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-12 col-md">
                                        <label for="plan">Plano</label>
                                        <select id="plan" class="form-control select-pad" style="width:100%;" data-plugin="select2">
                                            <option value="">Todos planos</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="height: 30px">
                                <div class="col-6 col-xl-3 mt-20 offset-xl-6 pr-0">
                                    <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center"
                                         data-toggle="collapse"
                                         data-target="#bt_collapse"
                                         aria-expanded="false"
                                         aria-controls="bt_collapse">
                                        <img id="icon-filtro" class="hidden-xs-down" style="margin-right: 4px" src=" {{ asset('/modules/global/img/svg/filter-2-line.svg') }} "/>
                                        <span id="text-filtro">Filtros avançados</span>
                                    </div>
                                </div>
                                <div class="col-6 col-xl-3 mt-20">
                                    <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                        <img style="height: 12px; margin-right: 4px" class="hidden-xs-down" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} "/>
                                        Aplicar filtros
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fixhalf"></div>
                <div class='container col-sm-12 mt-20 d-lg-block'>
                    <div class='row'>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Chamados em aberto </h6>
                                <h4 id="ticket-open" class="text-money ticket-number font-size-30 bold"></h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Chamados em mediação </h6>
                                <h4 id="ticket-mediation" class="text-money ticket-number font-size-30 bold"></h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Chamados resolvidos </h6>
                                <h4 id="ticket-closed" class="text-money ticket-number font-size-30 bold"></h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Total </h6>
                                <h4 id="ticket-total" class="text-money ticket-number font-size-30 bold"></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div id='div-tickets' class='row' style='display:none;'>
                    {{-- js carrega... --}}
                </div>
                <div id='div-ticket-empty' class='row' style='display:none;'>
                    <div class='col-12 col-lg-12'>
                        <div class="card bg-white">
                            <div class='card-header bg-white text-center py-25 d-flex justify-content-center align-items-center'
                            style="height: 257px">
                                <img style='width:124px;;margin-right:12px;' src="{!! asset('modules/global/img/suporte.svg') !!}">Nenhum chamado encontrado
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center justify-content-md-end">
                    <ul id="pagination-tickets" class="pl-5 pr-md-15"
                    style="margin-top:10px;position:relative;float:right;">
                        {{-- js carrega... --}}
                    </ul>
                </div>
            </div>
        </div>
        {{-- Quando não tem projeto cadastrado  --}}
        @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>
    <!-- Modal detalhes do ticket -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-ticket" role="dialog" tabindex="-1"
         style='padding-right: 15px;'>
        <div class="modal-dialog modal-dialog-centered modal-simple modal-lg">
            <div class="modal-content p-10">
                <div class="modal-header simple-border-bottom mb-10">
                    <h4 class="modal-title" id="modal-title-ticket">Detalhes do Chamado </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div id="modal-body-content" class="modal-body" style='min-height: 100px'>
                    <div class="card card-top card-ticket-color">
                        <div class="card-body bg-white p-40">
                            <div>
                                <h4 class='font-weight-bold ticket-subject'></h4>
                            </div>
                            <div>
                                <span class='font-size-14 ticket-description'></span>
                            </div>
                            <div>
                                <span class='font-size-14 customer-name'></span>
                            </div>
                            <div>
                                <span class='font-size-14 ticket-informations'></span>
                            </div>
                            <div class='row my-20 font-size-12'>
                                <div class='col-6 col-lg-3'>
                                    <span>Código da Transação</span>
                                    <br>
                                    <span id='sale_code' class='font-weight-bold sale-code'></span>
                                </div>
                                <div class='col-6 col-lg-3'>
                                    <span>Empresa</span>
                                    <br>
                                    <span class='font-weight-bold company-name'></span>
                                </div>
                                <div class='col-6 col-lg-3'>
                                    <span>Planos</span>
                                    <br>
                                    <span class='font-weight-bold ticket-products'></span>
                                </div>
                                <div class='col-6 col-lg-3'>
                                    <span>Valor total</span>
                                    <br>
                                    <span class='font-weight-bold total-value'></span>
                                </div>
                            </div>
                            <div class='my-20'>
                                <span class='font-size-16 mt-20 ticket-status'></span>
                                <hr class='mb-0 mt-10'>
                            </div>
                            <div style="display:none">
                                <span class="font-weight-bold d-block mb-10">Anexos:</span>
                                <div id='div-ticket-attachments'>
                                    {{-- js carrega... --}}
                                </div>
                                <hr class="mt-30">
                            </div>
                            <div id='div-ticket-comments'>
                                {{-- js carrega... --}}
                            </div>
                            <div class='text-right mt-10 div-buttons'>
                                <button id='btn-answer' class='btn btn-primary'>Responder</button>
                            </div>
                            <div class='row div-message' style='display:none;'>
                                <div class='col-lg-12'>
                                    <div class='form-group'>
                                        <label>Mensagem</label>
                                        <textarea class='form-control user-message' placeholder='Digite sua resposta'
                                                  rows='6'></textarea>
                                    </div>
                                </div>
                                <div class='col-lg-12 text-right'>
                                    <button id='btn-cancel' class='btn mr-20'>Cancelar</button>
                                    <button id='btn-send' class='btn btn-primary'>Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal detalhes do ticket -->
    @push('scripts')
        <script src='{{asset('/modules/tickets/js/index.js?v=' . random_int(100, 10000))}}'></script>
        <script src='{{asset('/modules/attendance/js/index.js?v=' . random_int(100, 10000))}}'></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
        <script src="{{ asset('modules/global/js/select2.min.js') }}"></script>
    @endpush

@endsection
