@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=5') }}">
    @endpush
    <div class="page">
        <div style="display: none" class="page-header container">
            <h1 class="page-title">Atendimento</h1>
        </div>
        <div id="project-not-empty" style="display: none">
            <div class="page-content container">
                <div class='row'>
                    <div class='col-12 col-lg-12'>
                        <div class="card card-shadow p-20">
                            <div class='row'>
                                <div class='col-12 col-md-3 col-lg-3'>
                                    <div class='form-group'>
                                        <label>Status</label>
                                        <select id='status-filter' class='form-control'>
                                            <option value="">Selecione...</option>
                                            <option value="open">Em aberto</option>
                                            <option value="mediation">Em mediação</option>
                                            <option value="closed">Resolvido</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='col-12 col-md-3 col-lg-3'>
                                    <div class='form-group'>
                                        <label>Motivo</label>
                                        <select id='category-filter' class='form-control'>
                                            <option value="">Selecione...</option>
                                            <option value="complaint">Reclamação</option>
                                            <option value="doubt">Dúvida</option>
                                            <option value="suggestion">Sugestão</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='col-12 col-md-3 col-lg-3'>
                                    <div class='form-group'>
                                        <label>Cliente</label>
                                        <input id='customer-filter' class='form-control' type='text' placeholder='Nome do cliente'>
                                    </div>
                                </div>
                                <div class='col-12 col-md-3 col-lg-3'>
                                    <div class='form-group'>
                                        <label>CPF do Cliente</label>
                                        <input id='cpf-filter' class='form-control' type='text' placeholder='CPF do cliente'>
                                    </div>
                                </div>
                                <div class='col-12 col-md-3 col-lg-3'>
                                    <div class='form-group'>
                                        <label>Data</label>
                                        <input name='date_range' id="date_range" class="form-control bg-white" placeholder="Clique para editar..." readonly>
                                    </div>
                                </div>
                                <div class='col-12 col-md-3 col-lg-3'>
                                    <div class='form-group'>
                                        <label>Código do chamado</label>
                                        <input id='ticker-code-filter' class='form-control' type='text' placeholder='Código do chamado'>
                                    </div>
                                </div>
                                <div class='col-12 col-md-3 col-lg-3'>
                                    <div class='form-group'>
                                        <label>Respostas</label>
                                        <select id='answered' class='form-control'>
                                            <option value="">Todos</option>
                                            <option value="last-answer-admin">Última resposta minha</option>
                                            <option value="last-answer-customer">Última resposta do cliente</option>
                                            <option value="not-answered">Não respondidos</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 col-lg-3 mt-25">
                                    <button id="btn-filter" class="btn btn-primary w-full">
                                        <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class="col-6 col-lg-3">
                        <div class="card card-shadow bg-white card-left orange">
                            <div class="card-header bg-white p-20 pb-0">
                                <div>
                                    <span class="card-desc">Chamados em aberto</span>
                                </div>
                                <div class='mt-10 mx-10'>
                                    <span id="ticket-open" class="text-money ticket-number">2</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card card-shadow bg-white card-left">
                            <div class="card-header bg-white p-20 pb-0">
                                <div>
                                    <span class="card-desc">Chamados em mediação</span>
                                </div>
                                <div class='mt-10 mx-10'>
                                    <span id="ticket-mediation" class="text-money ticket-number">1</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card card-shadow bg-white card-left green">
                            <div class="card-header bg-white p-20 pb-0">
                                <div>
                                    <span class="card-desc">Chamados resolvidos</span>
                                </div>
                                <div class='mt-10 mx-10'>
                                    <span id="ticket-closed" class="text-money ticket-number">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card card-shadow bg-white card-left purple">
                            <div class="card-header bg-white p-20 pb-0">
                                <div>
                                    <span class="card-desc">Total</span>
                                </div>
                                <div class='mt-10 mx-10'>
                                    <span id="ticket-total" class="text-money ticket-number">3</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id='div-tickets' class='row' style='display:none;'>
                    {{-- js carrega... --}}
                </div>
                <div id='div-ticket-empty' class='row' style='display:none;'>
                    <div class='col-12 col-lg-12'>
                        <div class="card card-shadow bg-white card-left orange">
                            <div class='card-header bg-white font-size-16 text-center py-20 d-flex justify-content-center align-items-center' style="height: 135.35px">Nenhum chamado encontrado</div>
                        </div>
                    </div>
                </div>
                <ul id="pagination-tickets" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right;margin-bottom:100px;">
                    {{-- js carrega... --}}
                </ul>
            </div>
        </div>
        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>
    <!-- Modal detalhes do ticket -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-ticket" role="dialog" tabindex="-1" style='padding-right: 15px;'>
        <div class="modal-dialog modal-dialog-centered modal-simple modal-lg">
            <div class="modal-content p-10">
                <div class="modal-header simple-border-bottom mb-10">
                    <h4 class="modal-title" id="modal-title-ticket">Detalhes do Chamado </h4>
                    <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                        <i class="material-icons md-16">close</i>
                    </a>
                </div>
                <div id="modal-body-content" class="modal-body" style='min-height: 100px'>
                    <div class="card card-shadow card-top card-ticket-color">
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
                                        <textarea class='form-control user-message' placeholder='Digite sua resposta' rows='6'></textarea>
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
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
    @endpush

@endsection
