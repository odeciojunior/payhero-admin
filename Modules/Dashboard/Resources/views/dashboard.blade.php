@extends("layouts.master")
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ mix('build/layouts/dashboard/stylesheets.min.css') }}">
    @endpush

    <div class="page dashboard">
        @include('dashboard::achievement-details')
        @include('dashboard::onboarding.presentation')
        @include('dashboard::pix.pix')
        <div style="display: none" class="page-header container mb-15 mb-sm-0">

            <div class="row align-items-center justify-content-between" style="min-height:50px">

                <div class="col-lg-6 mb-25">
                    <h1 class="page-title">Dashboard</h1>
                </div>

                <div class="announcekit-widget-mobile col-lg-2 mb-25 font-size-14 text-primary d-sm-none" name="Qkw3C">
                    <span id="announcekit-news" class="p-5">
                        <b>NOVO</b>
                    </span>
                    <b>Clique para ver as novidades</b>
                </div>

                <div class="col-lg-4" id="company-select" style="display:none">
                    <div class="d-lg-flex align-items-center justify-content-end">
                        <div>
                            <select id="company" class="sirius-select"> </select>
                        </div>
                    </div>
                </div>

            </div>

        </div>
        <div id="project-not-empty" class="page-content container" style="display:none">
            <!-- Saldos -->
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-8">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="card bg-white stats-card">
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
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="card bg-white stats-card">
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
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="card bg-white stats-card">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc" id="title_available_money"></span>
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
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="card bg-white stats-card">
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
                            <div class="card bg-white chart-card">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc">Vendas nos últimos 30 dias</span>
                                    </div>
                                </div>
                                <div class="card-body my-30 d-flex flex-column justify-content-center align-items-center p-5" style="height: 270px">
                                    <div id="scoreLineToMonth" class="ct-chart"></div>
                                    <div id="empty-sale" class="row" style="display: none;">
                                        <div class="col-sm-8">
                                            <img src="{!! mix('build/global/img/sem-dados.svg') !!}" alt="">
                                        </div>
                                        <p style="font-size: 23px" class="col-sm-4 gray justify-content-center align-items-center d-flex flex-column p-5">Nenhuma venda encontrada</p>
                                     </div>
                                </div>
                                <div id="chart-loading"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-4">
                    <div class="row">

                        <div class="col-12  d-flex align-items-stretch font-size-12 order-0 order-sm-0 sirius-performance">

                            <div class="card pb-15 bg-white w-full performance-card">

                            </div>
                        </div>

                        <div class="col-12 d-flex align-items-stretch font-size-12 order-1 order-sm-1 sirius-cashback">
                            <div class="card bg-white w-full d-none">
                                <div
                                    class="card-header d-flex justify-content-between align-items-center bg-white mt-10 pb-0 ">
                                    <div class="font-size-14 gray-600 mr-auto">
                                        <span class="ml-0">Cashback total recebido</span>
                                    </div>
                                    <ol class="card-indicators mb-0 d-flex justify-content-end align-items-center align-self-center">
                                    </ol>
                                </div>
                                <div class="card-body pt-0 mt-15 mb-5 d-flex flex-column justify-content-start align-items-start ">
                                    <div class="pt-5 pb-5 flex-column flex-nowrap justify-content-start align-items-start align-self-stretch">
                                        <div id="cashback-container" class="d-flex flex-row justify-content-start align-items-center align-self-start">
                                            <span class="cashback-container-icon">R$</span>
                                            <span id="cashback-container-money"></span>
                                            <span class="o-reload-1 cashback-container-icon"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-10 d-flex align-items-stretch font-size-12 order-2 order-sm-2 sirius-account">
                            <div class="card bg-white w-full sirius-account-health">

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @include('projects::empty')
    </div>

    @push('scripts')
        <script async src="https://cdn.announcekit.app/widget-v2.js"></script>
        <script src="{{ mix('build/layouts/dashboard/scripts.min.js') }}"></script>
    @endpush

@endsection


