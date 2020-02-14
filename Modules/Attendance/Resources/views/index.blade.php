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
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label>Status:</label>
                                    <select id='' class='form-control'>
                                        <option value="">Selecione...</option>
                                        <option value="1">Em aberto</option>
                                        <option value="2">Em mediação</option>
                                        <option value="3">Resolvidos</option>
                                    </select>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label>Cliente:</label>
                                    <input id='' class='form-control' type='text' placeholder='Nome do cliente'>
                                </div>
                            </div>
                            <div class='col-md-3'>
                                <div class='form-group'>
                                    <label>Código do chamado:</label>
                                    <input id='' class='form-control' type='text' placeholder='Código do chamado'>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mt-25">
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
                                <span id="today_money" class="text-money">2</span>
                            </div>
                        </div>
                        {{--                        <div class="card-bottom orange"></div>--}}
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white card-left red">
                        <div class="card-header bg-white p-20 pb-0">
                            <div>
                                <span class="card-desc">Chamados em mediação</span>
                            </div>
                            <div class='mt-10 mx-10'>
                                <span id="today_money" class="text-money">1</span>
                            </div>
                        </div>
                        {{--                        <div class="card-bottom orangered"></div>--}}
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white card-left green">
                        <div class="card-header bg-white p-20 pb-0">
                            <div>
                                <span class="card-desc">Chamados resolvidos</span>
                            </div>
                            <div class='mt-10 mx-10'>
                                <span id="today_money" class="text-money">0</span>
                            </div>
                        </div>
                        {{--                        <div class="card-bottom green"></div>--}}
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white card-left purple">
                        <div class="card-header bg-white p-20 pb-0">
                            <div>
                                <span class="card-desc">Total</span>
                            </div>
                            <div class='mt-10 mx-10'>
                                <span id="today_money" class="text-money">3</span>
                            </div>
                        </div>
                        {{--                        <div class="card-bottom orangered"></div>--}}
                    </div>
                </div>
            </div>
            <div id='tickets' class='row'>
                <div class='col-12 col-lg-12'>
                    <div class="card card-shadow bg-white card-left orange">
                        <div class="card-header bg-white p-20 pb-0">
                            <i class="material-icons mr-1">chat_bubble_outline</i>
                            <span id='' class='font-size-18 font-weight-bold'>Título do chamado</span>
                            <div class='float-right'>
                                <div class='dropdown'>
                                    <i class="material-icons" id="dropdownMenuButton" title='Opções' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style='cursor:pointer'>more_vert</i>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item details" href="http://dev.cloudfox.com.br/attendance/490019">Detalhes</a>
                                        <a class="dropdown-item solve" href="#">Resolver</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body font-size-12 bg-white">
                            <div class='row'>
                                <div class='col-6 col-lg-2'>
                                    <div>
                                        <span>Empresa</span>
                                    </div>
                                    <span class='font-weight-bold'>Health Lab</span>
                                </div>
                                <div class='col-6 col-lg-2'>
                                    <div>
                                        <span>ID</span>
                                    </div>
                                    <span class='font-weight-bold'>#490019</span>
                                </div>
                                <div class='col-6 col-lg-2'>
                                    <div>
                                        <span>Cliente</span>
                                    </div>
                                    <span class='font-weight-bold'>Matheus Silva</span>
                                </div>
                                <div class='col-6 col-lg-2'>
                                    <div>
                                        <span>Motivo</span>
                                    </div>
                                    <span class='font-weight-bold'>Reclamação</span>
                                </div>
                                <div class='col-6 col-lg-2'>
                                    <div>
                                        <span>Aberto em</span>
                                    </div>
                                    <span class='font-weight-bold'>17/09/2019</span>
                                </div>
                                <div class='col-6 col-lg-2'>
                                    <div>
                                        <span>Última resposta</span>
                                    </div>
                                    <span class='font-weight-bold'>17/09/2019 08:23</span>
                                </div>
                                <div class='col-6 col-lg-2 mt-10'>
                                    <span id='status' class='font-size-12 orange-gradient mt-20'>Em aberto</span>
                                </div>
                            </div>
                        </div>
                        {{--                        <div class="card-bottom orange"></div>--}}
                    </div>
                </div>
            </div>
        </div>
        <ul id="pagination-tickets" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
            {{-- js carrega... --}}
        </ul>
    </div>
    @push('scripts')
        <script src='{{asset('/modules/tickets/js/index.js')}}'></script>
    @endpush

@endsection
