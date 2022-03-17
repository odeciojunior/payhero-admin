@extends("layouts.master")
@section('title', '- Relatório de Acessos')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! mix('build/layouts/reports/checkouts.min.css') !!}">
    @endpush

    <div class="page">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height: 50px;">
                <div class="col-12">
                    <h1 class="page-title">Relatório de Acessos</h1>
                    <span type="hidden" class="error-data"></span>
                </div>
            </div>
        </div>
        <div id="project-not-empty" style="display: none">
            <div id="reports-content" class="page-content container" style="padding-top: 0">
                <div class="row align-items-center justify-content-between">
                    <div class="col-sm-6 col-m-3 col-lg-3">
                        <select id='select_projects' class="sirius-select">
                            {{-- JS carrega.. --}}
                        </select>
                    </div>
                    <div class="col-sm-6 col-m-3 col-lg-3">
                            <div class="row align-items-center form-icons">
                                <i class="form-control-icon form-control-icon-right o-agenda-1 font-size-18" style="right: 10%;"></i>
                                <input id="date-filter" type="text" name="daterange" class="input-pad text-center font-size-14 pr-30 ml-5" value="" readonly style="width: 92%; height: 50px;">
                            </div>
                        {{-- <div class="row align-items-center">
                            <span class="o-agenda-1"></span>
                            <input id="date-filter" type="text" name="daterange" class="select-pad text-center font-size-14 ml-5"
                            style="width: 85%" value="" readonly>
                        </div> --}}
                    </div>
                </div>

                <div class="tab-content gutter_top mt-15 gutter_bottom mb-30" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-vendas" role="tabpanel">
                        <div class="row justify-content-between">
                            <div class='container col-sm-12 mt-20 d-lg-block'>
                                <div class='row' style="margin-left: 0;">
                                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <h6 class="font-size-14 gray-600"> Total </h6>
                                            <h4 id='qtd-total-checkouts' class="font-size-30 bold"></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <h6 class="font-size-14 gray-600"> Abandonos </h6>
                                            <h4 id='qtd-abandoned'></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <h6 class="font-size-14 gray-600"> Recuperados </h6>
                                            <h4 id='qtd-recovered'></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <h6 class="font-size-14 gray-600"> Venda Finalizada </h6>
                                            <h4 id='qtd-finalized'></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-lg-12">
                                <div class="card shadow">
                                    <div class="wrap">
                                        <div class="row justify-content-between gutter_top">
                                            <div class="col">
                                                <h6 class="label-price relatorios"> Total </h6>
                                                <h4 id='qtd-total-checkouts' class="number blue-800">0<i class="fas fa-check"></i>
                                                </h4>
                                            </div>
                                            <div class="col">
                                                <h6 class="label-price relatorios"> Abandonos </h6>
                                                <h4 id='qtd-abandoned' class="number red-500">0<i class="fas fa-check"></i>
                                                </h4>
                                            </div>
                                            <div class="col">
                                                <h6 class="label-price relatorios"> Recuperados </h6>
                                                <h4 id='qtd-recovered' class="number green-500">0<i class="fas fa-check"></i>
                                                </h4>
                                            </div>
                                            <div class="col">
                                                <h6 class="label-price relatorios"> Venda Finalizada </h6>
                                                <h4 id='qtd-finalized' class="number green-500">0<i class="fas fa-check"></i>
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="col-lg-12 gutter_top display-xsm-none display-sm-none" class="ct-chart" id="ecommerceChartView">
                                <div class="card">
                                    <div class="card-header card-header-transparent py-20">
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
                                            <img src="{!! mix('build/global/img/sem-dados.svg') !!}" alt="">
                                            <p style="font-size: 23px" class="gray">Nenhuma venda encontrada</p>
                                        </div>
                                        <div class="ct-chart tab-pane active" id="scoreLineToDay"></div>
                                        <div class="ct-chart tab-pane" id="scoreLineToWeek"></div>
                                        <div class="ct-chart tab-pane" id="scoreLineToMonth"></div>
                                    </div>
                                </div>
                            </div>

                            <div class='col-lg-8'>
                                <div class="card shadow ">
                                    <div class="card-header s-card-header">
                                        <h4> Mais Acessados </h4>
                                    </div>
                                    <div style='max-height: 150px; overflow-y: auto; height: 150px;'>
                                        <div style="padding: 0 20px;" class=" card-body data-holder">
                                            <table class="table-vendas-itens table table-striped" style="width:100%;margin: auto; margin-top:15px">
                                                <tbody id="origins-table-itens"  img-empty="{!! mix('build/global/img/vendas.svg')!!}">
                                                {{-- js carrega... --}}
                                                </tbody>
                                            </table>
                                            <br/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='col-lg-4'>
                                <div class="card shadow">
                                    <div class="card-header s-card-header">
                                        <h4> Dispositivos </h4>
                                    </div>
                                    <div class="custom-table min-250" style="height: 150px;">
                                        <div class="row">
                                            <div class="col-12 col-md-12 col-lg-12 ">
                                                <div class="data-holder b-bottom">
                                                    <div class="row justify-content-between align-items-center" style="padding: 15px 30px">
                                                        <div class="col-lg-6 col-sm-8 col-8">
                                                            <span class="mr-10 o-imac-screen-1"></span> Desktop
                                                        </div>
                                                        <div class="col-lg-6 col-sm-4 col-4">
                                                            <span class="money-td green" id='percent-desktop'>0</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-12 col-lg-12 ">
                                                <div class="data-holder b-bottom">
                                                    <div class="row justify-content-between align-items-center" style="padding: 15px 30px">
                                                        <div class="col-lg-6 col-sm-8 col-8">
                                                            <span class="ml-5 mr-15 o-iphone-1"></span> Mobile
                                                        </div>
                                                        <div class="col-lg-6 col-sm-4 col-4">
                                                            <span class="money-td green" id='percent-mobile'>0</span>
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
                                                <table class="table-vendas table table-striped " style="width:100%;margin: auto; margin-top:15px">
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
                                            <ul id="pagination-origins" class="pagination-sm float-right margin-chat-pagination" style="margin-top:10px; margin-left: 5%">
                                                {{-- js carrega... --}}
                                            </ul>
                                        </div>
                                    </div>
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
    <script type='text/javascript' src='{{ mix('build/layouts/reports/checkouts.min.js') }}'></script>
@endpush

