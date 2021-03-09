@extends("layouts.master")
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=02') }}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist-plugin-tooltip.min.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/dashboard/css/index.css?v=01') }}">
    @endpush

    <div class="page dashboard">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-8 mb-15">
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="col-lg-4" id="company-select" style="display:none">
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

                        <div class="col-12  d-flex align-items-stretch font-size-12 order-1 order-sm-0 sirius-performance">

                            <div class="card pb-15 card-shadow bg-white w-full performance-card">

                            </div>

{{--                            <div id="performance-card-1" class="card card-shadow bg-white w-full performance-card"> h-full --}}
{{--                                <div--}}
{{--                                    class="card-header mt-10 pb-0 d-flex justify-content-between align-items-center bg-white">--}}
{{--                                    <div class="font-size-14 gray-600 mr-auto">--}}
{{--                                        <span class="ml-0">Seu desempenho</span>--}}
{{--                                    </div>--}}
{{--                                    <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">--}}
{{--                                        <li class="active" data-slide-to="1"></li>--}}
{{--                                        <li class="" data-slide-to="2"></li>--}}
{{--                                        <i class="o-angle-down-1 control-prev active" data-slide-to="2"></i>--}}
{{--                                        <i class="o-angle-down-1 control-next active" data-slide-to="2"></i>--}}
{{--                                    </ol>--}}
{{--                                </div>--}}
{{--                                <div class="card-body pb-5 pt-0 mt-15 d-flex flex-column justify-content-start align-items-start">--}}
{{--                                    <div class="d-flex flex-row justify-content-start align-items-start align-self-start">--}}
{{--                                        <div class=" text-center px-0 d-flex justify-content-center mr-20">--}}
{{--                                            <div id="level-icon">--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="d-flex flex-column justify-content-center align-self-center">--}}
{{--                                            <div id="level" class="level mb-1"></div>--}}
{{--                                            <div id="level-description" class="level-description"></div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div id="achievements" class="mt-10 d-flex flex-column flex-nowrap justify-content-center align-items-stretch align-self-stretch ">--}}
{{--                                        <div class=" mb-10 d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch">--}}
{{--                                            <div id="achievements-item-1" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/1ccee66facee377777d3e3f943ccb0ae2a8bedd6r1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-2" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/11903cdeba102415c7a49cb4cad3ff5fab04297fr1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-3" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/37041bbe7e41e669a614cf99d0e9ae3585adc7f4r1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-4" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/6eb8248218eb601f2534656bccb0566fbd3070b8r1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-5" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/75f61864adba69fa157c052cf259f5cf9d098eadr1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-6" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/35146e6d525ad92d4ad71c3018824ddde4249a05r1-200-141v2_hq.jpg)"></div>--}}
{{--                                        </div>--}}
{{--                                        <div class="d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch">--}}
{{--                                            <div id="achievements-item-7" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/5dfc0ae74931e316225e6f1d50eaf52e943faeb7r1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-8" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/a6412a5b02d8a235677b5651a080dd4d5d0d65fcr1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-9" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/13df5623bdc33a79763d26aed5e09230a7932199r1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-10" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/01417fb22aaf1c7c0387cd306d8f17236750350dr1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-11" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/2fd11faccf8517a2ce5e5181f606259ca24bd5e4r1-200-141v2_hq.jpg)"></div>--}}
{{--                                            <div id="achievements-item-12" class="achievements-item" style="background-image: url(https://pm1.narvii.com/7191/4ea15727ab51ebc7697fa6b1785a31491f69fb2br1-200-141v2_hq.jpg)"></div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div id="tasks" class="mt-20 d-none d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">--}}
{{--                                    </div>--}}
{{--                                    <div id="cashback" class="mt-20  flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">--}}
{{--                                        <span class="title-performance">Cashback recebido</span>--}}
{{--                                        <div id="cashback-container" class="mt-15 d-flex flex-row justify-content-start align-items-center align-self-start">--}}
{{--                                            <span class="o-reload-1 cashback-container-icon"></span>--}}
{{--                                            <span class="cashback-container-icon">R$</span>--}}
{{--                                            <span id="cashback-container-money"></span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                    <div id="progress" class="mt-25 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">--}}
{{--                                        <div class="d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch">--}}
{{--                                            <span id="progress-message-1"></span>--}}
{{--                                            <span id="progress-message-2"></span>--}}
{{--                                        </div>--}}
{{--                                        <div id="progress-bar"--}}
{{--                                             class="mt-10 d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch"--}}
{{--                                             data-toggle="tooltip"--}}
{{--                                        >--}}
{{--                                                <div></div>--}}
{{--                                                <span></span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div id="performance-card-2" class="card card-shadow bg-white w-full performance-card" >--}}
{{--                                <div--}}
{{--                                    class="card-header mt-10 pb-0 d-flex justify-content-between align-items-center bg-white">--}}
{{--                                    <div class="mr-auto">--}}
{{--                                        <span class="ml-0 title-performance">Seu desempenho</span>--}}
{{--                                    </div>--}}
{{--                                    <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">--}}
{{--                                        <li class="" data-slide-to="1"></li>--}}
{{--                                        <li class="active" data-slide-to="2"></li>--}}
{{--                                        <i class="o-angle-down-1 control-prev active" data-slide-to="1"></i>--}}
{{--                                        <i class="o-angle-down-1 control-next active" data-slide-to="1"></i>--}}
{{--                                    </ol>--}}
{{--                                </div>--}}
{{--                                <div class="card-body pb-5 pt-0 mt-15 d-flex flex-column justify-content-start align-items-start">--}}
{{--                                    <div id="card-level-description" >--}}
{{--                                        <div class="p-15 d-flex flex-column flex-nowrap justify-content-start align-items-stretch align-self-stretch ">--}}
{{--                                            <div class="d-flex flex-row justify-content-between align-items-center">--}}
{{--                                                <div class="">--}}
{{--                                                    <span id="level-full" class="level mr-5"></span>--}}
{{--                                                    <span id="level-current">ATUAL</span>--}}
{{--                                                </div>--}}
{{--                                                --}}{{--                                            <div class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">--}}
{{--                                                <div class="">--}}
{{--                                                    <span id="billed" class="mr-10"></span>--}}
{{--                                                    <span id="billed-message" class="ml-0"></span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                            <p id="level-message" class="level-description">--}}
{{--                                            </p>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                    <div id="levels" class="mt-15 d-flex flex-row flex-nowrap justify-content-between align-items-start align-self-stretch">--}}
{{--                                    </div>--}}

{{--                                    <div class="benefits mt-10 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">--}}
{{--                                        <span class="mb-10 title-performance">Benefícios atual</span>--}}
{{--                                        <div id="benefits-active-container" class="d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}

{{--                                    <div class="benefits mt-10 mb-10 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">--}}
{{--                                        <span class="title-performance">Seus próximos benefícios</span>--}}
{{--                                        <div id="benefits-container" class="mt-10 d-flex flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                        </div>

                        <div class="col-12 mb-10 d-flex align-items-stretch font-size-12 order-1 order-sm-0 sirius-account">
                            <div class="card card-shadow bg-white w-full h-full sirius-account-health">

                            </div>
                        </div>


{{--                        <div class="col-lg-12 align-items-stretch order-0 order-sm-1">--}}
{{--                            <div class="card card-shadow sirius-card">--}}
{{--                                <div--}}
{{--                                    class="card-header d-flex justify-content-between align-items-center bg-blue pt-20 pb-10">--}}
{{--                                    <div class="font-size-16 text-white">--}}
{{--                                        <b class="card-desc">A CloudFox mudou.</b>--}}
{{--                                        <br/>--}}
{{--                                        <b class="card-desc">Bem-vindo(a) ao Sirius!</b>--}}
{{--                                    </div>--}}
{{--                                    <img class="img-fluid"--}}
{{--                                         src="{{ asset('modules/global/img/svg/sirius-stars-b.png') }}"--}}
{{--                                         height="60px" width="60px">--}}
{{--                                </div>--}}
{{--                                <div class="card-body d-flex flex-column justify-content-between">--}}
{{--                                    <p class="font-size-12">--}}
{{--                                        A CloudFox está crescendo de forma exponencial, e vamos compartilhar nos--}}
{{--                                        próximos meses novos produtos e serviços que vão te ajudar a vender mais, a--}}
{{--                                        começar pelo Sirius.--}}
{{--                                    </p>--}}
{{--                                    <p class="font-size-12">--}}
{{--                                        O Sirius é o gateway de pagamentos da CloudFox, ou seja, é o que a CloudFox era--}}
{{--                                        até este momento: uma empresa de processamento de pagamentos online.--}}
{{--                                    </p>--}}
{{--                                    --}}{{-- <a class="font-size-14 text-blue" href="#"><b>Faça um tour ⇾</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="col-lg-12 order-0 order-sm-1">--}}
{{--                            <div class="card card-shadow sirius-card">--}}
{{--                                <div--}}
{{--                                    class="card-header d-flex justify-content-between align-items-center bg-blue pt-20 pb-10">--}}
{{--                                    <div class="font-size-16 text-white">--}}
{{--                                        <b class="card-desc">A CloudFox mudou.</b>--}}
{{--                                        <br/>--}}
{{--                                        <b class="card-desc">Bem-vindo(a) ao Sirius!</b>--}}
{{--                                    </div>--}}
{{--                                    <img class="img-fluid"--}}
{{--                                         src="{{ asset('modules/global/img/svg/sirius-stars-b.png') }}"--}}
{{--                                         height="60px" width="60px">--}}
{{--                                </div>--}}
{{--                                <div class="card-body d-flex flex-column justify-content-between">--}}
{{--                                    <p class="font-size-12">--}}
{{--                                        A CloudFox está crescendo de forma exponencial, e vamos compartilhar nos--}}
{{--                                        próximos meses novos produtos e serviços que vão te ajudar a vender mais, a--}}
{{--                                        começar pelo Sirius.--}}
{{--                                    </p>--}}
{{--                                    <p class="font-size-12">--}}
{{--                                        O Sirius é o gateway de pagamentos da CloudFox, ou seja, é o que a CloudFox era--}}
{{--                                        até este momento: uma empresa de processamento de pagamentos online.--}}
{{--                                    </p>--}}
{{--                                    --}}{{-- <a class="font-size-14 text-blue" href="#"><b>Faça um tour ⇾</b></a>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
        {{-- Quando não tem projeto cadastrado  --}}
        @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>

    @push('scripts')
        <script src='{{ asset('modules/dashboard/js/gauge.js') }}'></script>
        <script src='{{ asset('modules/reports/js/chartist.min.js') }}'></script>
        <script src='{{ asset('modules/reports/js/chartist-plugin-tooltip.min.js') }}'></script>
        <script src='{{ asset('modules/reports/js/chartist-plugin-legend.min.js') }}'></script>
{{--        <script src="{{ asset('modules/global/js/circle-progress.min.js') }}"></script>--}}
        <script src="{{ asset('modules/dashboard/js/dashboard-performance.js?v=' . random_int(100, 10000)) }}"></script>
        <script src="{{ asset('modules/dashboard/js/dashboard.js?v=' . random_int(100, 10000)) }}"></script>
        <script src="{{ asset('modules/dashboard/js/dashboard-account-health.js?v=' . random_int(100, 10000)) }}"></script>
    @endpush

@endsection


