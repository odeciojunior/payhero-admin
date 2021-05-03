@extends("layouts.master")
@section('title', '- Relatório de Vendas')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist-plugin-tooltip.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/reports.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
    @endpush

    <div class="page mb-0">
        <div style="display: none" class="page-header container">
            <div class="row">
                <div class="col-12">
                    <h1 class="page-title">Relatório de Vendas</h1>
                    <span type="hidden" class="error-data"></span>
                </div>
            </div>
        </div>
        <div id="project-not-empty" style="display: none">
            <div id="reports-content" class="page-content container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-sm-6 col-m-3 col-lg-3">
                        <div class="">
                            <select id='select_projects' class="form-control select-pad">
                                {{-- JS carrega.. --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-m-3 col-lg-3">
                        <div class="row align-items-center">
                            <span class="o-agenda-1"></span>
                            <input id="date-filter" type="text" name="daterange" class="select-pad text-center font-size-14 ml-5" style="width: 85%" value="" readonly>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between mt-20">
                    <div class="col-lg-12">
                        <div class="card shadow">
                            <div class="wrap">
                                <div class="row justify-content-between gutter_top">
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Receita gerada </h6>
                                        <h4 id='revenue-generated' class="number green" style='color:green'>0</h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Aprovadas </h6>
                                        <h4 id='qtd-aproved' class="number green" style='color:green'>0<i
                                                class="fas fa-check"></i>
                                        </h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Pendentes </h6>
                                        <h4 id='qtd-pending' class="number blue-800" style='color:blue'>0<i
                                                class="fas fa-check"></i>
                                        </h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Canceladas </h6>
                                        <h4 id='qtd-canceled' class="number red" style='color:red'>0<i
                                                class="fas fa-check"></i>
                                        </h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Recusadas </h6>
                                        <h4 id='qtd-recusadas' class="number red" style='color:red'>0</h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Reembolsos </h6>
                                        <h4 id='qtd-reembolso' class="number purple" style='color:purple'>0</h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> ChargeBack </h6>
                                        <h4 id='qtd-chargeback' class="number purple" style='color:purple'>0</h4>
                                    </div>
                                    <div class="col">
                                        <h6 class="label-price relatorios"> Em disputa </h6>
                                        <h4 id='qtd-dispute' class="number purple" style='color:blue'>0</h4>
                                    </div>
                                    <!--div class="col-lg-12">
                                        <div class="grafico">
                                            <div class="text">
                                                <h1 class="text-muted op5"> Graph here </h1>
                                            </div>
                                        </div>
                                    </div-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 gutter_top display-xsm-none display-sm-none" class="ct-chart"
                         id="ecommerceChartView">
                        <div class="card card-shadow">
                            <div class="card-header card-header-transparent py-20">
                                <!--div class="btn-group dropdown"-->
                                <!--a href="#" class="text-body dropdown-toggle blue-grey-700" data-toggle="dropdown">PRODUCTS SALES</a-->
                                <!--div class="dropdown-menu animate" role="menu">
                                    <a class="dropdown-item" href="#" role="menuitem">Sales</a>
                                    <a class="dropdown-item" href="#" role="menuitem">Total sales</a>
                                    <a class="dropdown-item" href="#" role="menuitem">profit</a>
                                </div-->
                                <!--/div-->
                                <ul class="nav nav-pills nav-pills-rounded chart-action" style="display: none">
                                    <li class="nav-item">
                                        <a class="active nav-link" data-toggle="tab" href="#scoreLineToDay">Day</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#scoreLineToWeek">Week</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#scoreLineToMonth">Month</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="widget-content tab-content bg-white p-20">
                                <div class="ct-chart tab-pane active" id="scoreLineToDay"></div>
                                <div class="ct-chart tab-pane" id="scoreLineToWeek"></div>
                                <div class="ct-chart tab-pane" id="scoreLineToMonth"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 gutter_top">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <h4> Dispositivos </h4>
                            </div>
                            <div class="custom-table min-250">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12 ">
                                        <div class="data-holder b-bottom">
                                            <div class="row wrap justify-content-between align-items-center">
                                                <div class="col-lg-6 align-items-center">
                                                    <span class="o-imac-screen-1"></span> Desktop
                                                </div>
                                                <div class="col-lg-6">
                                                    <span class="money-td green" id='percent-desktop'>0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12 ">
                                        <div class="data-holder b-bottom">
                                            <div class="row wrap justify-content-between align-items-center">
                                                <div class="col-lg-6 align-items-center">
                                                    <span class="o-iphone-1"></span> Mobile
                                                </div>
                                                <div class="col-lg-6">
                                                    <span class="money-td green" id='percent-mobile'>0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 gutter_top">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <h4> Conversão </h4>
                            </div>
                            <div class="custom-table min-250">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12  ">
                                        <div class="data-holder b-bottom">
                                            <div class="row wrap justify-content-between align-items-center">
                                                <div class="col-lg-4">
                                                    <span class="o-bank-cards-1"></span> Cartão
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="" id='qtd-cartao-convert'>0</span>
                                                </div>
                                                <div class="col-lg-4" id='percent-credit-card-convert'>
                                                    0
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12  ">
                                        <div class="data-holder b-bottom">
                                            <div class="row wrap justify-content-between align-items-center">
                                                <div class="col-lg-4">
                                                    <span class="o-cash-dispenser-1"></span> Boleto
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="" id='qtd-boleto-convert'>0</span>
                                                </div>
                                                <div class="col-lg-4" id='percent-boleto-convert'>
                                                    0
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 gutter_top">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <h4> Meios de Pagamento </h4>
                            </div>
                            <div class="custom-table">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12  ">
                                        <div class="data-holder b-bottom">
                                            <div class="row wrap justify-content-between align-items-center">
                                                <div class="col-lg-4">
                                                    <span class="o-bank-cards-1"></span> Cartão
                                                </div>
                                                <div class="col-lg-3" id='percent-credit-card'>
                                                    0
                                                </div>
                                                <div class="col-lg-5">
                                                    <span class="money-td green" id='credit-card-value'></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12  ">
                                        <div class="data-holder b-bottom">
                                            <div class="row wrap justify-content-between align-items-center">
                                                <div class="col-lg-4">
                                                    <span class="o-cash-dispenser-1"></span> Boleto
                                                </div>
                                                <div class="col-lg-3" id='percent-values-boleto'>
                                                    0
                                                </div>
                                                <div class="col-lg-5">
                                                    <span class="money-td green" id='boleto-value'></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-lg-8'>
                        <div class="card shadow ">
                            <div class="card-header s-card-header">
                                <h4> Mais Vendidos </h4>
                            </div>
                            <div style=' max-height: 150px; overflow-y: auto; height: 150px;'>
                                <div style="padding: 0 20px;" class=" card-body data-holder">
                                    <table class="table-vendas-itens table table-striped"
                                           style="width:100%;margin: auto; margin-top:15px">
                                        <tbody id="origins-table-itens">
                                            {{-- js carrega... --}}
                                        </tbody>
                                    </table>
                                    <br/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-lg-4'>
                        <div class='card shadow'>
                            <div class='card-header s-card-header'>
                                <h4>Ticket Médio</h4>
                            </div>
                            <div style='height: 150px; '>
                                <div class='card-body custom-table min-250'>
                                    <div class='row'>
                                        <div class='col-lg-12 text-center'>
                                            <div class='data-holder text-center'>
                                                <div class='row wrap justify-content-between text-center'>
                                                    <div class='col-lg-12 text-center'>
                                                        <span class='money-td green h3' id='ticket-medio'>0</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 mt-10">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <div class="row">
                                    <div class='col-8'>
                                        <h4 class='float-left'> Origens</h4>
                                    </div>
                                    <div class="col-4">
                                        <select class="form-control float-right" id='origin'>
                                            <option selected value="src">SRC</option>
                                            <option value="utm_source">UTM Source</option>
                                            <option value="utm_medium">UTM Medium</option>
                                            <option value="utm_campaign">UTM Campaign</option>
                                            <option value="utm_term">UTM Term</option>
                                            <option value="utm_content">UTM Content</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="data-holder">
                                <div class="row">
                                    <div class="col-12">
                                        <table class="table-vendas table table-striped "
                                               style="width:100%;margin: auto; margin-top:15px">
                                            <tbody id="origins-table">
                                                {{-- js carrega... --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <br/>
                            </div>
                            <div class="row">
                                <div class="col-11">
                                    <ul id="pagination-origins" class="pagination-sm float-right margin-chat-pagination"
                                        style="margin-top:10px; margin-left: 5%">
                                        {{-- js carrega... --}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('projects::empty')
    </div>
@endsection

@push('scripts')
    <!--script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script-->
    <script type='text/javascript' src='{{asset('modules/reports/js/moment.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/global/js/daterangepicker.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-tooltip.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/chartist-plugin-legend.min.js')}}'></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/reports.js?v=' . random_int(100, 10000))}}'></script>
@endpush

