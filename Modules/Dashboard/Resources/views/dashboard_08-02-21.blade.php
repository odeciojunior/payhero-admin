@extends("layouts.master")
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=10') }}">
        <link rel="stylesheet" href="{!! mix('modules/reports/css/chartist.min.css') !!}">
        <link rel="stylesheet" href="{!! mix('modules/reports/css/chartist-plugin-tooltip.min.css') !!}">
        <link rel="stylesheet" href="{{ mix('modules/dashboard/css/index.min.css') }}">
    @endpush

    <div class="page dashboard">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 mb-15">
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="col-lg-6" id="company-select" style="display:none">
                    <div class="d-lg-flex align-items-center justify-content-end">
                        {{--                        <div class="mr-10 mb-5 text-lg-right">--}}
                        {{--                            Empresa:--}}
                        {{--                        </div>--}}
                        <div class=" text-lg-right">
                            <select id="company" class="form-control new-select"> </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="project-not-empty" class="page-content container" style="display:none">
            <!-- Saldos -->
            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="card card-shadow bg-white stats-card">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc">Vendas aprovadas hoje</span>
                                    </div>
                                </div>
                                <div class="card-body font-size-24 d-flex align-items-topline">
                                    <div class="card-text d-flex align-items-center">
                                        <span class="moeda"></span>
                                        <span id="today_money" class="text-money"></span>
                                    </div>
                                </div>
                                <div class="s-border-right purple"></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="card card-shadow bg-white stats-card">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc">Pendente</span>
                                    </div>
                                </div>
                                <div class="card-body font-size-24 d-flex align-items-topline">
                                    <div class="card-text d-flex align-items-center">
                                        <span class="moeda"></span>
                                        <span id="pending_money" class="text-money"></span>
                                    </div>
                                </div>
                                <div class="s-border-right yellow"></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="card card-shadow bg-white stats-card">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc">Disponível</span>
                                    </div>
                                </div>
                                <div class="card-body font-size-24 d-flex align-items-topline">
                                    <div class="card-text d-flex align-items-center">
                                        <span class="moeda"></span>
                                        <span id="available_money" class="text-money"></span>
                                    </div>
                                </div>
                                <div class="s-border-right green"></div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="card card-shadow bg-white stats-card">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600 mr-auto">
                                        <span class="card-desc">Total</span>
                                    </div>
                                    <i class="o-question-help-1" id="info-total-balance"></i>
                                </div>
                                <div class="card-body font-size-24 d-flex align-items-topline">
                                    <div class="card-text d-flex align-items-center">
                                        <span class="moeda"></span>
                                        <span id="total_money" class="text-money"></span>
                                    </div>
                                </div>
                                <div class="s-border-right blue"></div>
                            </div>
                        </div>

                        <div class="col-12 d-none d-sm-block">
                            <div class="card card-shadow bg-white chart-card">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc">Vendas nos últimos 30 dias</span>
                                    </div>
                                </div>
                                <div class="card-body my-30 d-flex flex-column justify-content-center align-items-center p-5" style="height: 270px">
                                    <div id="scoreLineToMonth" class="ct-chart"></div>
                                    <div id="empty-sale" style="display: none; font-size: 14px"> Nenhuma venda encontrada </div>
                                </div>
                                <div id="chart-loading"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="row">
                        <div class="col-12 d-flex align-items-stretch font-size-12 order-1 order-sm-0">
                            <div class="card card-shadow bg-white w-full chargeback-card">
                                <div
                                    class="card-chargeback card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600 mr-auto">
                                        <span class="card-desc">Saúde da Conta</span>
                                    </div>
                                    <i class="o-question-help-1" data-toggle="tooltip" data-placement="bottom"
                                       title="Taxa geral de chargeback de sua empresa"></i>
                                </div>
                                <div class="card-body pb-5">
                                    <div class="row d-flex align-items-topline align-items-center">
                                        <div class="col text-center px-0 d-flex justify-content-center">
                                            <div class="circle text-circle">
                                                <strong>0.00%</strong>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="pb-15"><b>Taxa de Chargeback</b></div>
                                            <div class="table-responsive">
                                                <table class="table table-condensed">
                                                    <tr class="pb-15">
                                                        <td class="text-right">
                                                            <span id="total_sales_approved" class="text-money mr-1">0</span>
                                                        </td>
                                                        <td class="text-left">
                                                            <div class="ml-10 w-p100">Vendas no Cartão</div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right">
                                                            <span id="total_sales_chargeback" class="text-money mr-1">0</span>
                                                        </td>
                                                        <td class="text-left">
                                                            <div class="ml-10 w-p100">Chargebacks</div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
{{--                                            <div class="mb-10 d-flex flex-row justify-content-center">--}}
{{--                                                <span id="total_sales_approved" class="text-money mr-1">0</span>--}}
{{--                                                <div class="ml-10 w-p100">Vendas no Cartão</div>--}}
{{--                                            </div>--}}
{{--                                            <div class="d-flex flex-row justify-content-center">--}}
{{--                                                <span id="total_sales_chargeback" class="text-money mr-1">0</span>--}}
{{--                                                <div class="ml-10 w-p100">Chargebacks</div>--}}
{{--                                            </div>--}}
                                        </div>
                                        <div class="col-12">
                                            <div class="row no-gutters1">
                                                <div class="col-6 align-items-start w-25">
                                                    <hr class="bg-grey-50 my-5">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 py-10 text-dark"><b>Atendimento</b></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-condensed">
                                                            <tr class="pb-15">
                                                                <td class="text-right">
                                                                    <span id="open-tickets" class="text-money">0</span>
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="ml-10 w-p100">Abertos</div>
                                                                </td>
                                                                <td class="text-right">
                                                                    <span id="closed-tickets"
                                                                          class="text-money">0</span>
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="ml-10 w-p100">Resolvidos</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right">
                                                                    <span id="mediation-tickets"
                                                                          class="text-money">0</span>
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="ml-10 w-p100">Em mediação</div>
                                                                </td>
                                                                <td class="text-right">
                                                                    <span id="total-tickets" class="text-money">0</span>
                                                                </td>
                                                                <td class="text-left">
                                                                    <div class="ml-10 w-p100">Total</div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                                {{--                                                <div class="col d-flex justify-content-center">--}}
                                                {{--                                                    <span id="open-tickets" class="text-money">0</span>--}}
                                                {{--                                                    <div class="ml-10 w-p100">Abertos</div>--}}
                                                {{--                                                </div>--}}
                                                {{--                                                <div class="col d-flex justify-content-center">--}}
                                                {{--                                                    <span id="closed-tickets" class="text-money">0</span>--}}
                                                {{--                                                    <div class="ml-10 w-p100">Resolvidos</div>--}}
                                                {{--                                                </div>--}}
                                            </div>
                                            {{--                                            <div class="row">--}}
                                            {{--                                                <div class="col d-flex justify-content-center">--}}
                                            {{--                                                    <span id="mediation-tickets" class="text-money">0</span>--}}
                                            {{--                                                    <div class="ml-10 w-p100">Em mediação</div>--}}
                                            {{--                                                </div>--}}
                                            {{--                                                <div class="col d-flex justify-content-center">--}}
                                            {{--                                                    <span id="total-tickets" class="text-money">0</span>--}}
                                            {{--                                                    <div class="ml-10 w-p100">Total</div>--}}
                                            {{--                                                </div>--}}
                                            {{--                                            </div>--}}
                                        </div>
                                        <div class="col-12">
                                            <div class="row no-gutters1">
                                                <div class="col-6 align-items-start w-25">
                                                    <hr class="bg-grey-50 m-1">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 py-10 d-flex justify-content-between">
                                                    <b class="text-dark">Códigos de Rastreio</b>

                                                    <i class="o-question-help-1" data-toggle="tooltip"
                                                       data-placement="bottom"
                                                       title="As vendas que permanecerem sem o código de rastreamento por 15 dias poderão ser estornadas. Geralmente o tempo médio de postagem é de 5 dias"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row no-gutters">
                                                <div class="col-6 mb-20 text-center d-flex flex-wrap flex-column">
                                                    <label class="update-text text-money font-size-18"
                                                           id="average_post_time"></label>
                                                    <span class="font-size-11">Tempo médio de postagem</span>
                                                </div>
                                                <div class="col-6 mb-20 text-center d-flex flex-wrap flex-column">
                                                    <label class="update-text text-money font-size-18"
                                                           id="oldest_sale"></label>
                                                    <span class="font-size-11">Venda mais antiga sem código</span>
                                                </div>
                                                <div class="col-6 mb-20 text-center d-flex flex-wrap flex-column">
                                                    <label class="update-text text-money font-size-18"
                                                           id="problem"></label>
                                                    <span class="font-size-11">Códigos com problema</span>
                                                </div>
                                                <div class="col-6 mb-20 text-center d-flex flex-wrap flex-column">
                                                    <label class="update-text text-money font-size-18"
                                                           id="unknown"></label>
                                                    <span class="font-size-11">Códigos não informados</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 order-0 order-sm-1">
                            <div class="card card-shadow sirius-card">
                                <div
                                    class="card-header d-flex justify-content-between align-items-center bg-blue pt-20 pb-10">
                                    <div class="font-size-16 text-white">
                                        <b class="card-desc">A CloudFox mudou.</b>
                                        <br/>
                                        <b class="card-desc">Bem-vindo(a) ao Sirius!</b>
                                    </div>
                                    <img class="img-fluid"
                                         src="{{ asset('modules/global/img/svg/sirius-stars-b.png') }}"
                                         height="60px" width="60px">
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <p class="font-size-12">
                                        A CloudFox está crescendo de forma exponencial, e vamos compartilhar nos
                                        próximos meses novos produtos e serviços que vão te ajudar a vender mais, a
                                        começar pelo Sirius.
                                    </p>
                                    <p class="font-size-12">
                                        O Sirius é o gateway de pagamentos da CloudFox, ou seja, é o que a CloudFox era
                                        até este momento: uma empresa de processamento de pagamentos online.
                                    </p>
                                    {{-- <a class="font-size-14 text-blue" href="#"><b>Faça um tour ⇾</b></a>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Quando não tem loja cadastrado  --}}
        @include('projects::empty')
        {{-- FIM loja nao existem lojas--}}
    </div>

    @push('scripts')
        <script src='{{ mix('modules/reports/js/chartist.min.js') }}'></script>
        <script src='{{ mix('modules/reports/js/chartist-plugin-tooltip.min.js') }}'></script>
        <script src='{{ mix('modules/reports/js/chartist-plugin-legend.min.js') }}'></script>
        <script src="{{ asset('modules/global/js/circle-progress.min.js') }}"></script>
        <script src="{{ mix('modules/dashboard/js/dashboard.min.js') }}"></script>
    @endpush

@endsection


