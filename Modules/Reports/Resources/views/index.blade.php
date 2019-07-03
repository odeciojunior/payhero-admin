@extends("layouts.master")
@section('title', '- Relatórios')

@section('content')

    @push('css')
        <link rel="stylesheet" href="https://getbootstrapadmin.com/remark/global/vendor/chartist/chartist.min.css?v4.0.2">
        <link rel="stylesheet" href="https://getbootstrapadmin.com/remark/global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.min.css?v4.0.2">
        <style>
            .ct-legend {
                position: relative;
                width: 100%;
                z-index: 10;
                margin: 0 0 20px 0;
                list-style: none;
                text-align: center;
            }
            .ct-legend li {
                display: inline;
                margin: 40px;
            }
            .ct-legend .ct-series-0 {
                color: #00FF7F;
            }
            .ct-legend .ct-series-1 {
                color: #1E90FF;
            }
        </style>
    @endpush

    <div class="page">
        <div class="page-header container">
            @if($userProjects->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <h1 class="page-title">Relatórios</h1>
                        <span type="hidden" class="error-data"></span>
                    </div>
                </div>
                <div class="page-content container">
                    <div class="row">
                        <div class="col-3">
                            <div class="">
                                <label for="project">Selecione o projeto:</label>
                                <select id='project' class="form-control">
                                    @foreach($projects as $project)
                                        <option value='{{$project->id_code}}'>{{$project->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-9 align-items-baseline">
                            <div class="row justify-content-end align-items-center">
                                <div class="input-group-prepend">
                                    <div class="input-group-text px-1 px-md-2">
                                        <svg class="icon-filtro" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                            <path d="M24 2v22h-24v-22h3v1c0 1.103.897 2 2 2s2-.897 2-2v-1h10v1c0 1.103.897 2 2 2s2-.897 2-2v-1h3zm-2 6h-20v14h20v-14zm-2-7c0-.552-.447-1-1-1s-1 .448-1 1v2c0 .552.447 1 1 1s1-.448 1-1v-2zm-14 2c0 .552-.447 1-1 1s-1-.448-1-1v-2c0-.552.447-1 1-1s1 .448 1 1v2zm6.687 13.482c0-.802-.418-1.429-1.109-1.695.528-.264.836-.807.836-1.503 0-1.346-1.312-2.149-2.581-2.149-1.477 0-2.591.925-2.659 2.763h1.645c-.014-.761.271-1.315 1.025-1.315.449 0 .933.272.933.869 0 .754-.816.862-1.567.797v1.28c1.067 0 1.704.067 1.704.985 0 .724-.548 1.048-1.091 1.048-.822 0-1.159-.614-1.188-1.452h-1.634c-.032 1.892 1.114 2.89 2.842 2.89 1.543 0 2.844-.943 2.844-2.518zm4.313 2.518v-7.718h-1.392c-.173 1.154-.995 1.491-2.171 1.459v1.346h1.852v4.913h1.711z"></path>
                                        </svg>
                                    </div>
                                    <input id="date-filter" type="text" name="daterange" class="form-control pull-right" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nav-tabs-line">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-vendas-tab" data-toggle="tab" href="#nav-vendas"
                               role="tab" aria-controls="nav-vendas" aria-selected="true">Vendas
                            </a>
                            <a class="nav-item nav-link" id="nav-visitas-tab" data-toggle="tab" href="#nav-visitas"
                               role="tab" aria-controls="nav-visitas" aria-selected="false">Visitas
                            </a>
                        </div>
                    </div>
                    <div class="tab-content gutter_top mt-15 gutter_bottom mb-30" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-vendas" role="tabpanel">
                            <div class="row justify-content-between">
                                <div class="col-lg-12">
                                    <div class="card shadow">
                                        <div class="wrap">
                                            <div class="row justify-content-between gutter_top">
                                                <div class="col-lg-2">
                                                    <h6 class="label-price relatorios"> Receita gerada </h6>
                                                    <h4 class="number green " id='revenue-generated'>0</h4>
                                                </div>
                                                <div class="col-lg-2">
                                                    <h6 class="label-price relatorios"> Aprovadas </h6>
                                                    <h4 class="number green" id='qtd-aproved'>0<i class="fas fa-check"></i>
                                                    </h4>
                                                </div>
                                                <div class="col-lg-2">
                                                    <h6 class="label-price relatorios"> Boletos </h6>
                                                    <h4 class="number gray" id='qtd-boletos'>0</h4>
                                                </div>
                                                <div class="col-lg-2">
                                                    <h6 class="label-price relatorios"> Recusadas </h6>
                                                    <h4 class="number red" id='qtd-recusadas'>0</h4>
                                                </div>
                                                <div class="col-lg-2">
                                                    <h6 class="label-price relatorios"> Reembolsos </h6>
                                                    <h4 class="number purple" id='qtd-reembolso'>0</h4>
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
                                <div class="col-lg-12 gutter_top" class="ct-chart" id="ecommerceChartView">
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
                                <div class="col-lg-6 gutter_top">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <h4> Dispositivos </h4>
                                        </div>
                                        <div class="custom-table min-250">
                                            <div class="row">
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                Desktop
                                                            </div>
                                                            <div class="col">
                                                                {{--0%--}} Em Breve
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <span class="money-td green">{{--R$500,00--}}Em Breve</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                Mobile
                                                            </div>
                                                            <div class="col">
                                                                {{-- 30%--}}Em Breve
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <span class="money-td green">{{--R$1.200,00--}}Em Breve</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!--div class="col-lg-12 ">
                                                <div class="data-holder b-bottom">
                                                    <div class="row wrap justify-content-between">
                                                        <div class="col-lg-6">
                                                            Tablet
                                                        </div>
                                                        <div class="col">
                                                            {{--60%--}}Em Breve
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <span class="money-td green">{{--R$500,00--}}Em Breve</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 gutter_top">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <h4> Meios de Pagamento </h4>
                                        </div>
                                        <div class="custom-table">
                                            <div class="row">
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-4">
                                                                Cartão
                                                            </div>
                                                            <div class="col-lg-4" id='percent-credit-card'>
                                                                0
                                                            </div>
                                                            {{--<div class="col-lg-2" id='percent-credit-total'>
                                                                16%
                                                            </div>--}}
                                                            <div class="col-lg-4">
                                                                <span class="money-td green" id='credit-card-value'></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-4">
                                                                Boleto
                                                            </div>
                                                            <div class="col-lg-4" id='percent-values-boleto'>
                                                                0
                                                            </div>
                                                            {{-- <div class="col-lg-2" id='percent-boleto-total'>
                                                                    16%
                                                                </div>--}}
                                                            <div class="col-lg-4">
                                                                <span class="money-td green" id='boleto-value'></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{--<div class="col-lg-6 gutter_top ">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <h4> Páginas </h4>
                                        </div>
                                        <div class="data-holder empty-400"></div>
                                    </div>
                                </div>--}}
                            </div>
                        </div>
                        <!-- VISITAS -->
                        {{--<div class="tab-pane fade" id="nav-visitas" role="tabpanel">
                            <div class="row justify-content-between">
                                <div class="col-lg-12">
                                    <div class="card shadow">
                                        <div class="wrap">
                                            <div class="row justify-content-between">
                                                <div class="col-lg-12">
                                                    <div class="grafico">
                                                        <div class="text">
                                                            <h1 class="text-muted"> Graph here </h1>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 gutter_top">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <h4> Origens </h4>
                                        </div>
                                        <div class="custom-table">
                                            <div class="row">
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                origem.html
                                                            </div>
                                                            <div class="col text-right">
                                                                200
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                origem.html
                                                            </div>
                                                            <div class="col text-right">
                                                                200
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 gutter_top">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <h4> Páginas </h4>
                                        </div>
                                        <div class="custom-table ">
                                            <div class="row">
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                Páginas
                                                            </div>
                                                            <div class="col text-right">
                                                                200
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                Checkout
                                                            </div>
                                                            <div class="col text-right">
                                                                200
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 gutter_top">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <h4> Referências </h4>
                                        </div>
                                        <div class="custom-table empty-200">
                                            <div class="empty-card d-flex flex-column text-center">
                                                <h2 class="op-5"> X </h2>
                                                <h5 class="op-5"> Não encontramos nenhuma referência </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 gutter_top">
                                    <div class="card shadow">
                                        <div class="card-header">
                                            <h4> Dispositivos </h4>
                                        </div>
                                        <div class="custom-table">
                                            <div class="row">
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                Desktop
                                                            </div>
                                                            <div class="col text-right">
                                                                0%
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                Mobile
                                                            </div>
                                                            <div class="col text-right">
                                                                0%
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 ">
                                                    <div class="data-holder b-bottom">
                                                        <div class="row wrap justify-content-between">
                                                            <div class="col-lg-6">
                                                                Tablet
                                                            </div>
                                                            <div class="col text-right">
                                                                0%
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>--}}
                    </div>
                </div>
            @else
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/assets/css/empty.css') !!}">
                @endpush

                <div class="content-error d-flex text-center">
                    <img src="{!! asset('modules/global/assets/img/emptyprojetos.svg') !!}" width="250px">
                    <h1 class="big gray">Você ainda não tem nenhum projeto!</h1>
                    <p class="desc gray">Que tal criar um primeiro projeto para começar a vender? </p>
                    <a href="/projects/create" class="btn btn-primary gradient">Cadastrar primeiro projeto</a>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://getbootstrapadmin.com/remark/global/vendor/chartist/chartist.min.js?v4.0.2"></script>
    <script type="text/javascript" src="https://getbootstrapadmin.com/remark/global/vendor/chartist-plugin-tooltip/chartist-plugin-tooltip.min.js?v4.0.2"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/chartist-plugin-legend/0.6.2/chartist-plugin-legend.min.js"></script>
    <script type='text/javascript' src='{{asset('modules/reports/js/reports.js')}}'></script>
@endpush

