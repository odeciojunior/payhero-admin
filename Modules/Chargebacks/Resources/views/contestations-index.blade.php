@extends('layouts.master')

@section('content')
@push('css')
<link rel="stylesheet" href="{{ mix('build/layouts/chargebacks/contestations-index.min.css') }}">
<style>
    .select2-selection--single {
        border: 1px solid #dddddd !important;
        border-radius: .215rem !important;
        height: 43px !important;
    }

    .select2-selection__rendered {
        color: #707070 !important;
        font-size: 16px !important;
        font-family: 'Inter', sans-serif;
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
<div class="page" style="margin-bottom: 0 !important;">

    @include('layouts.company-select', ['version' => 'mobile'])

    <div style="display: none" class="page-header container pt-35" id="page_header">
        <div class="row align-items-center justify-content-between" style="min-height:50px">
            <div class="col-md-6">
                <h1 class="page-title">Contestações</h1>
            </div>
        </div>
    </div>
    <div id="project-not-empty" style="display:none">
        <div class="page-content container">
            <form id='filter_form' action='{{ route('contestations.getchargebacks') }}' method='GET'>
                @csrf
                <div id="filter-contestations" class="card">
                    <div class="row align-items-baseline">
                        <div class="col-sm-12 col-md-3 mt-10">
                            <label for="transaction">Transação</label>
                            <input name="transaction" id="transaction" class="input-pad" placeholder="Transação">
                        </div>

                        <div class="col-sm-12 col-md-3 mt-10">
                            <label for="is_expired">Expiração</label>
                            <br>
                            <select name='is_expired' id="is_expired" class="sirius-select">
                                <option value="0" selected>Ambos</option>
                                <option value="1">Expirado</option>
                                <option value="2">Não expirado</option>
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-3 mt-10">
                            <label for="date_type">Data</label>
                            <select name='date_type' id="date_type" class="sirius-select">
                                <option value="expiration_date">Data da expiração</option>
                                <option value="transaction_date">Data da compra</option>
                                <option value="adjustment_date">Data da contestação</option>
                            </select>
                        </div>

                        <div class="col-sm-12 col-md-3 mt-10">
                            <div class="form-group form-icons">
                                <label for="date_type">&nbsp;</label>
                                <input name='date_range' id="date_range" class="input-pad pr-30" readonly>
                                <i style="right: 24px;top: 31px;" class="filter-badge daterange"></i>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="bt_collapse">
                        <div class="row">
                            <div class="col-sm-12 col-md-3 mt-10">
                                <label for="project">Lojas</label><br>
                                <select name='project' id="project" class="sirius-select">
                                    <option value="">Todas lojas</option>
                                </select>
                            </div>
                            {{-- <div class="col-sm-12 col-md-3 mt-10">
                                    <label for="is_contested">Concluído</label>
                                    <br>
                                    <select name='is_contested' id="is_contested" class="sirius-select">
                                        <option value="0">Ambos</option>
                                        <option value="1">Concluído</option>
                                        <option value="2">Não concluído</option>
                                    </select>
                                </div> --}}
                            <div class="col-sm-12 col-md-3 mt-10">
                                <label for="contestation_situation">Situação</label>
                                <select name='contestation_situation' id="contestation_situation" class="sirius-select">
                                    <option value="">Todos status</option>
                                    <option value="1">Em Andamento</option>
                                    <option value="2">Perdida</option>
                                    <option value="3">Ganha</option>
                                </select>
                            </div>

                            <div class="col-sm-12 col-md-3 mt-10">
                                <label for='customer'>Cliente</label>
                                <input id="customer" name="customer" class="input-pad" placeholder="Nome do cliente">
                            </div>
                        </div>
                        {{-- <div class="row no-gutters justify-content-between justify-content-sm-end mt-20">
                                <div class="mr-10">
                                    <label for="sale_approve" class=''>
                                        Vendas aprovadas
                                    </label>
                                </div>

                                <div>
                                    <label class="switch m-0">
                                        <input type="checkbox" id='sale_approve' name="sale_approve" >
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div> --}}
                    </div>

                    <div class="row">
                        <div class="col-6 col-xl-3 mt-20 offset-xl-6 pr-0">
                            <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center" data-toggle="collapse" data-target="#bt_collapse" aria-expanded="false" aria-controls="bt_collapse">
                                <img id="icon-filtro" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} " />
                                <span id="text-filtro" class="text-break">Filtros avançados</span>
                            </div>
                        </div>
                        <div class="col-6 col-xl-3 mt-20">
                            <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                <img style="height: 12px; margin-right: 4px" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/check-all.svg') }} " />
                                <span class="text-break">Aplicar filtros</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="fixhalf"></div>

            <div class="row no-gutters mt-10">

                <div class="col-md-3 pl-0 py-15 pr-15">
                    <div class="card shadow" style='display:block;'>
                        <div class="card-body">
                            <h5 class="font-size-16 text-muted">N° de contestações</h5>
                            <h4 class="total-number">
                                <span class="font-size-30 bold" style="color:#5A5A5A" id="total-contestation"></span>
                                <span id="total-contestation-tax" class="text-muted"></span>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 pl-0 py-15 pr-15">
                    <div class="card shadow" style='display:block;'>
                        <div class="card-body">
                            <h5 class="font-size-16 text-muted">Resultou em chargeback</h5>
                            <h4 class="total-number">
                                <span class="font-size-30 bold" style="color:#5A5A5A" id="total-chargeback-tax-val"></span>
                                <span id="total-chargeback-tax" class="text-muted"></span>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 pl-0 py-15 pr-15">
                    <div class="card shadow" style='display:block;'>
                        <div class="card-body">
                            <h5 class="font-size-16 text-muted">Total</h5>
                            <h4 class="total-number">
                                <span class="text-muted">R$ </span>
                                <span class="font-size-30 bold" style="color:#5A5A5A" id="total-contestation-value"></span>
                            </h4>
                        </div>
                        <div class="s-border-right green"></div>
                    </div>
                </div>

                <div class="col-md-3"></div>
            </div>

            <div class="alert alert-light alert-dismissible fade show text-primary border border-primary alert-contestation" role="alert" style="border-radius: 12px">
                <img src="{{ mix('build/layouts/chargebacks/svg/info-contestation.svg') }}" alt="Informação sobre contestação">
                <span class="alert-text">
                    <span class="bold">Contestações</span>
                    são ocorrências enviadas pelas operadoras de crédito após contestação de alguma compra pelo titular
                    do cartão.
                </span>
                <button type="button" class="close text-primary" data-dismiss="alert" aria-label="Close" style="opacity: 1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="fixhalf"></div>

            <div class="card shadow">
                <div class="page-invoice-table table-responsive">
                    <table id="chargebacks-table" class="table-vendas table table-striped unify mb-0" style="width:100%;">
                        <thead>
                            <tr class="">
                                <td class="">Transação</td>
                                <td class="" style="text-align:left">Empresa</td>
                                <td class="">Compra</td>
                                <td class=" text-center" style="min-width: 100px;">Status</td>
                                <td class="">Prazo</td>
                                <td class="">Motivo</td>
                                {{-- <td class="">Valor</td> --}}
                                <td class=""></td>
                            </tr>
                        </thead>
                        <tbody id="chargebacks-table-data" img-empty="{!! mix('build/global/img/contestacoes.svg') !!}">
                            {{-- js carrega... --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="pagination-container" class="row no-gutters justify-content-center justify-content-md-end mb-60">
                <ul id="pagination" class="pagination-style mt-10">
                    {{-- js carrega... --}}
                </ul>
            </div>
            @include('chargebacks::contestations-files')
            @include('sales::details')

        </div>
        {{-- Quando não tem loja cadastrado --}}
        @include('projects::empty')
        {{-- FIM loja nao existem lojas --}}
    </div>
</div>

<!-- @push('scripts')
        <script src="{{ mix('build/layouts/chargebacks/contestations-index.min.js') }}"></script>
                                <script src="{{ mix('build/layouts/sales/details.min.js') }}"></script>
    @endpush -->

@push('scriptsView')
<script src="{{ mix('build/layouts/chargebacks/contestations-index.min.js') }}"></script>
@endpush
@endsection
