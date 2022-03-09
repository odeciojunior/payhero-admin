@extends("layouts.master")
@section('title', '- Relatório de Vendas')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! mix('modules/reports/css/chartist.min.css') !!}">
        <link rel="stylesheet" href="{!! mix('modules/reports/css/chartist-plugin-tooltip.min.css') !!}">
        <link rel="stylesheet" href="{!! mix('modules/reports/css/reports.min.css') !!}">
        <link rel="stylesheet" href="{!! mix('modules/global/css/empty.min.css') !!}">
    @endpush

    <div class="page mb-0">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height: 50px;">
                <div class="col-12">
                    <h1 class="page-title">Relatório de Vendas</h1>
                    <span type="hidden" class="error-data"></span>
                </div>
            </div>
        </div>
        <div id="project-not-empty" style="display: none">
            <div id="reports-content" class="page-content container" style="padding-top: 0">
                <div class="row align-items-center justify-content-between">
                    <div class="col-sm-6 col-m-3 col-lg-3">
                        <div class="">
                            <select id='select_projects' class="sirius-select">
                                {{-- JS carrega.. --}}
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-m-3 col-lg-3">
                        <div class="row align-items-center form-icons">
                            <i style="right:10%;" class="form-control-icon form-control-icon-right o-agenda-1 font-size-18"></i>
                            <input id="date-filter" type="text" name="daterange" class="input-pad text-center pr-30 font-size-14 ml-5" style="width: 92%;height: 50px;" value="" readonly>
                        </div>
                    </div>
                </div>
                <div class='container col-sm-12 mt-20 d-lg-block'>
                    <div class='row'>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Receita gerada </h6>
                                <h4 id='revenue-generated'>0</h4>
                            </div>
                            <div class="s-border-right yellow"></div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Aprovadas </h6>
                                <h4 id='qtd-aproved' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Pendentes </h6>
                                <h4 id='qtd-pending' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Canceladas </h6>
                                <h4 id='qtd-canceled' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Recusadas </h6>
                                <h4 id='qtd-recusadas' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Reembolsos </h6>
                                <h4 id='qtd-reembolso' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Chargeback </h6>
                                <h4 id='qtd-chargeback' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h6 class="font-size-14 gray-600"> Em disputa </h6>
                                <h4 id='qtd-dispute' class=" font-size-30 bold">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-between mt-20">
                    {{-- <div class="col-lg-12">
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
                    </div> --}}
                    <div class="col-lg-12 gutter_top display-xsm-none display-sm-none" class="ct-chart"
                         id="ecommerceChartView">
                        <div class="card">
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
                                <div id="empty-graph" class="row justify-content-center align-items-center d-flex" style="vertical-align: middle">
                                    <img src="{!! mix('modules/global/img/sem-dados.svg') !!}" alt="">
                                    <p style="font-size: 23px" class="gray">Nenhuma venda encontrada</p>
                                </div>
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
                            <div class="custom-table">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12 ">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-6 d-flex align-items-center">
                                                    <span class="mr-10 o-imac-screen-1"></span> Desktop
                                                </div>
                                                <div class="col-lg-6">
                                                    <span class="money-td green" id='percent-desktop'>0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-12 col-lg-12 ">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-6 d-flex align-items-center">
                                                    <span class="ml-5 mr-15 o-iphone-1"></span> Mobile
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
                            <div class="list-linear-gradient-top"></div>
                            <div id="conversion-items" class="custom-table scrollbar pb-0 pt-0">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <span class="mr-10 o-bank-cards-1"></span> Cartão
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
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <span class="mr-10 o-cash-dispenser-1"></span> Boleto
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
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="38.867" height="40.868" viewBox="0 0 38.867 40.868" style="width: 24px;" class="mr-10">
                                                        <g id="Grupo_61" data-name="Grupo 61" transform="translate(-2948.5 213.743)">
                                                            <g id="g992" transform="translate(2956.673 -190.882)">
                                                                <path id="path994" d="M-73.541-25.595a5.528,5.528,0,0,1-3.933-1.629l-5.68-5.68a1.079,1.079,0,0,0-1.492,0l-5.7,5.7a5.529,5.529,0,0,1-3.934,1.628H-95.4l7.193,7.194a5.753,5.753,0,0,0,8.136,0l7.214-7.214Z" transform="translate(95.4 34.202)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                            <g id="g996" transform="translate(2956.673 -212.243)">
                                                                <path id="path998" d="M-3.765-29.869A5.528,5.528,0,0,1,.169-28.24l5.7,5.7a1.056,1.056,0,0,0,1.493,0l5.68-5.68a5.529,5.529,0,0,1,3.934-1.629h.684l-7.214-7.214a5.753,5.753,0,0,0-8.136,0l-7.193,7.193Z" transform="translate(4.884 37.747)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                            <g id="g1000" transform="translate(2949 -201.753)">
                                                                <path id="path1002" d="M-121.731-14.725l-4.36-4.359a.83.83,0,0,1-.31.063h-1.982a3.917,3.917,0,0,0-2.752,1.14l-5.68,5.68a2.718,2.718,0,0,1-1.927.8,2.719,2.719,0,0,1-1.928-.8l-5.7-5.7a3.917,3.917,0,0,0-2.752-1.14h-2.437a.827.827,0,0,1-.293-.059l-4.377,4.377a5.753,5.753,0,0,0,0,8.136l4.377,4.377a.828.828,0,0,1,.293-.059h2.437a3.917,3.917,0,0,0,2.752-1.14l5.7-5.7a2.792,2.792,0,0,1,3.856,0l5.68,5.679a3.917,3.917,0,0,0,2.752,1.14h1.982a.83.83,0,0,1,.31.062l4.359-4.359a5.753,5.753,0,0,0,0-8.136" transform="translate(157.913 19.102)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                        </g>
                                                    </svg> PIX
                                                </div>
                                                <div class="col-lg-4">
                                                    <span class="" id='qtd-pix-convert'>0</span>
                                                </div>
                                                <div class="col-lg-4" id='percent-pix-convert'>
                                                    0
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-linear-gradient-bottom"></div>
                        </div>
                    </div>
                    <div class="col-lg-4 gutter_top">
                        <div class="card shadow">
                            <div class="card-header s-card-header">
                                <h4> Meios de Pagamento </h4>
                            </div>
                            <div class="list-linear-gradient-top"></div>
                            <div id="payment-type-items" class="custom-table scrollbar pb-0 pt-0">
                                <div class="row">
                                    <div class="col-6 col-md-12 col-lg-12  ">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <span class="mr-10 o-bank-cards-1"></span> Cartão
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
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <span class="mr-10 o-cash-dispenser-1"></span> Boleto
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
                                    <div class="col-6 col-md-12 col-lg-12">
                                        <div class="data-holder b-bottom">
                                            <div class="row px-25 py-10 justify-content-between align-items-center">
                                                <div class="col-lg-4 d-flex justify-content-start align-items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="38.867" height="40.868" viewBox="0 0 38.867 40.868" style="width: 24px;" class="mr-10">
                                                        <g id="Grupo_61" data-name="Grupo 61" transform="translate(-2948.5 213.743)">
                                                            <g id="g992" transform="translate(2956.673 -190.882)">
                                                                <path id="path994" d="M-73.541-25.595a5.528,5.528,0,0,1-3.933-1.629l-5.68-5.68a1.079,1.079,0,0,0-1.492,0l-5.7,5.7a5.529,5.529,0,0,1-3.934,1.628H-95.4l7.193,7.194a5.753,5.753,0,0,0,8.136,0l7.214-7.214Z" transform="translate(95.4 34.202)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                            <g id="g996" transform="translate(2956.673 -212.243)">
                                                                <path id="path998" d="M-3.765-29.869A5.528,5.528,0,0,1,.169-28.24l5.7,5.7a1.056,1.056,0,0,0,1.493,0l5.68-5.68a5.529,5.529,0,0,1,3.934-1.629h.684l-7.214-7.214a5.753,5.753,0,0,0-8.136,0l-7.193,7.193Z" transform="translate(4.884 37.747)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                            <g id="g1000" transform="translate(2949 -201.753)">
                                                                <path id="path1002" d="M-121.731-14.725l-4.36-4.359a.83.83,0,0,1-.31.063h-1.982a3.917,3.917,0,0,0-2.752,1.14l-5.68,5.68a2.718,2.718,0,0,1-1.927.8,2.719,2.719,0,0,1-1.928-.8l-5.7-5.7a3.917,3.917,0,0,0-2.752-1.14h-2.437a.827.827,0,0,1-.293-.059l-4.377,4.377a5.753,5.753,0,0,0,0,8.136l4.377,4.377a.828.828,0,0,1,.293-.059h2.437a3.917,3.917,0,0,0,2.752-1.14l5.7-5.7a2.792,2.792,0,0,1,3.856,0l5.68,5.679a3.917,3.917,0,0,0,2.752,1.14h1.982a.83.83,0,0,1,.31.062l4.359-4.359a5.753,5.753,0,0,0,0-8.136" transform="translate(157.913 19.102)" fill="none" stroke="#3a506c" stroke-width="1"/>
                                                            </g>
                                                        </g>
                                                    </svg> Pix
                                                </div>
                                                <div class="col-lg-3" id='percent-values-pix'>
                                                    0
                                                </div>
                                                <div class="col-lg-5">
                                                    <span class="money-td green" id='pix-value'></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-linear-gradient-bottom"></div>
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
                                        <tbody id="origins-table-itens" img-empty="{!! mix('modules/global/img/vendas.svg')!!}">
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
                                    <div class='row align-items-center h-100'>
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
                                        <select class="sirius-select float-right" id='origin'>
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
    <script type='text/javascript' src='{{ mix('modules/reports/js/moment.min.js') }}'></script>
    <script type='text/javascript' src='{{ mix('modules/global/js/daterangepicker.min.js') }}'></script>
    <script type='text/javascript' src='{{ mix('modules/reports/js/chartist.min.js') }}'></script>
    <script type='text/javascript' src='{{ mix('modules/reports/js/chartist-plugin-tooltip.min.js') }}'></script>
    <script type='text/javascript' src='{{ mix('modules/reports/js/chartist-plugin-legend.min.js') }}'></script>
    <script type='text/javascript' src='{{ mix('modules/reports/js/reports.min.js') }}'></script>
@endpush

