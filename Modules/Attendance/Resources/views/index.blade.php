@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=1') }}">
    @endpush
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Atendimento</h1>
        </div>
        <div class="page-content container">
            <div class='row'>
                <div class='col-12 col-lg-12'>
                    <div class="card card-shadow p-20">
                        <div class='row'>
                            <div class='col-12 col-md-3 col-lg-3'>
                                <div class='form-group'>
                                    <label>Status:</label>
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
                                    <label>Cliente:</label>
                                    <input id='customer-filter' class='form-control' type='text' placeholder='Nome do cliente'>
                                </div>
                            </div>
                            <div class='col-12 col-md-3 col-lg-3'>
                                <div class='form-group'>
                                    <label>Código do chamado:</label>
                                    <input id='ticker-code-filter' class='form-control' type='text' placeholder='Código do chamado'>
                                </div>
                            </div>
                            <div class='col-12 col-md-3 col-lg-3'>
                                <div class='form-group'>
                                    <label>Data:</label>
                                    <input name='date_range' id="date_range" class="form-control" placeholder="Clique para editar..." readonly>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 col-lg-3 ">
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
                    <div class="card card-shadow bg-white card-left red">
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
                    <div class="card card-shadow bg-white p-5">
                        <div class='alert alert-info font-size-14 text-center mt-10'>Nenhum chamado encontrado</div>
                    </div>
                </div>
            </div>
            <ul id="pagination-tickets" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                {{-- js carrega... --}}
            </ul>
        </div>
    </div>
    @push('scripts')
        <script src='{{asset('/modules/tickets/js/index.js')}}'></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
    @endpush

@endsection
