@extends('layouts.master')
@section('title', '- Relatório de Vendas')

@section('content')

    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/reports/projections.min.css') }}">
    @endpush

    <div class="page">

        @include('layouts.company-select',['version'=>'mobile'])

        <div style="display: none" class="page-header container">
            <div class="row">
                <div class="col-8">
                    <h1 class="page-title">Projeção financeira</h1>
                    <span type="hidden"
                          class="error-data"></span>
                </div>
                <div class="col-4 text-right">
                    <div class="justify-content-end align-items-center"
                         id="export-excel"
                         style="display: none">
                        <!-- <div class="p-2 align-items-center">
                            <span class="o-download-cloud-1 mr-2"></span>
                            <div class="btn-group"
                                 role="group"> -->
                                <!-- <button id="bt_get_xls"
                                        type="button"
                                        class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button> -->
                                <!-- <button id="bt_get_csv"
                                        type="button"
                                        class="btn btn-round btn-default btn-outline btn-pill">.CSV</button>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="reports-content"
             class="page-content container"
             style="display:none">
            <div class="row align-items-center justify-content-between">
                <div class="col-sm-6 col-m-3 col-lg-3">
                    <div class="">
                        <select id='select_companies'
                                class="form-control select-pad">
                            {{-- JS carrega.. --}}
                        </select>
                    </div>
                </div>
            </div>
            <div class="row justify-content-between mt-20">
                <div class="col-lg-12">
                    <div class="card shadow">
                        <div class="wrap">
                            <div class="row justify-content-between gutter_top">
                                <div class="col text-center">
                                    <h6 class="label-price relatorios">
                                        <i class="fas fa-dollar"></i>
                                        Projeção Total
                                    </h6>
                                    <h4 id='projection-total'
                                        class="number blue-800"
                                        style='color:blue'>0
                                    </h4>
                                </div>
                                <div class="col text-center">
                                    <h6 class="label-price relatorios"><i class="fas fa-2x fa-barcode"></i> Boletos </h6>
                                    <h4 id='projection-billet'
                                        class="number green"
                                        style='color:green'>0
                                    </h4>
                                </div>
                                <div class="col text-center">
                                    <h6 class="label-price relatorios"><i class="fas fa-credit-card"></i> Cartão </h6>
                                    <h4 id='projection-card'
                                        class="number orange"
                                        style='color:orange'>0
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 gutter_top display-xsm-none display-sm-none"
                     class="ct-chart"
                     id="ecommerceChartView">
                    <div class="card card-shadow">
                        <div class="card-header card-header-transparent py-20">

                            <ul class="nav nav-pills nav-pills-rounded chart-action"
                                style="display: none">
                                <li class="nav-item">
                                    <a class="active nav-link"
                                       data-toggle="tab"
                                       href="#scoreLineToDay">Day</a>
                                </li>
                            </ul>
                        </div>
                        <div class="widget-content tab-content bg-white p-20">
                            <div class="ct-chart tab-pane active"
                                 id="scoreLineToDay"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card shadow ">
                        <div class="card-header">
                            <h4>Saldo a liberar por dia</h4>
                        </div>
                        <div class=" card-body data-holder"
                             style="height: 400px; overflow-y: auto;">
                            <table class="table-transaction-itens table table-striped"
                                   style="width:100%;margin: auto;">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody id="body-table-transaction-itens">
                                    {{-- js carrega... --}}
                                </tbody>
                            </table>
                            <br />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('projects::empty')
    </div>
    </div>
@endsection

@push('scripts')
    <script type='text/javascript'
            src='{{ mix('build/layouts/reports/projections.min.js') }}'></script>
@endpush
