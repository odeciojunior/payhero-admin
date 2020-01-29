@extends("layouts.master")
@section('title', '- Relatório de Vendas')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist-plugin-tooltip.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/reports.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
    @endpush

    <div class="page">
        <div class="page-header container">
            <div class="row">
                <div class="col-8">
                    <h1 class="page-title">Projeção financeira</h1>
                    <span type="hidden" class="error-data"></span>
                </div>
                <div class="col-4 text-right">
                    <div class="justify-content-end align-items-center" id="export-excel" style="display:n">
                        <div class="p-2 align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-download" width="20" height="20" viewBox="0 0 24 24">
                                <path d="M8 20h3v-5h2v5h3l-4 4-4-4zm11.479-12.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                            </svg>
                            <div class="btn-group" role="group">
                                <button id="bt_get_xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                                <button id="bt_get_csv" type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="reports-content" class="page-content container" style="display:none">
            <div class="row align-items-center justify-content-between">
                <div class="col-sm-6 col-m-3 col-lg-3">
                    <div class="">
                        <select id='select_companies' class="form-control select-pad">
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
                                    <h4 id='projection-total' class="number red" style='color:red'>0
                                    </h4>
                                </div>
                                <div class="col text-center">
                                    <h6 class="label-price relatorios"><i class="fas fa-2x fa-barcode"></i> Boletos </h6>
                                    <h4 id='projection-billet' class="number green" style='color:green'>0
                                    </h4>
                                </div>
                                <div class="col text-center">
                                    <h6 class="label-price relatorios"><i class="fas fa-credit-card"></i> Cartão </h6>
                                    <h4 id='projection-card' class="number blue-800" style='color:blue'>0 
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 gutter_top display-xsm-none display-sm-none" class="ct-chart" id="ecommerceChartView">
                    <div class="card card-shadow">
                        <div class="card-header card-header-transparent py-20">

                            <ul class="nav nav-pills nav-pills-rounded chart-action" style="display: none">
                                <li class="nav-item">
                                    <a class="active nav-link" data-toggle="tab" href="#scoreLineToDay">Day</a>
                                </li>
                            </ul>
                        </div>
                        <div class="widget-content tab-content bg-white p-20">
                            <div class="ct-chart tab-pane active" id="scoreLineToDay"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card shadow ">
                        <div class="card-header">
                            <h4>Saldo a liberar por dia</h4>
                        </div>
                        <div class=" card-body data-holder">
                            <table class="table-transaction-itens table table-striped" style="width:100%;margin: auto; margin-top:15px">
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
                            <br/>
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
    {{-- <script type='text/javascript' src='{{asset('modules/reports/js/moment.min.js')}}'></script> --}}
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-tooltip.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-legend.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/projections.js?v=1')}}'></script>
@endpush
