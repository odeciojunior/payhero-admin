@extends("layouts.master")
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=1') }}">
        <link rel="stylesheet" href="{{ asset('modules/dashboard/css/index.css?v=3') }}">
    @endpush

    <div class="page">
        <div class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6">
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="col-lg-6" id="company-select" style="display:none">
                    <div class="d-lg-flex align-items-center justify-content-end">
                        <div class="mr-10 text-lg-right">
                            Empresa:
                        </div>
                        <div class=" text-lg-right">
                            <select id="company" class="form-control new-select">
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container" style="display:none">
            <!-- CARDS EXTRATO -->
            <div class="row" id="card-extrato">
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-vermelha.svg') }}" width="35px">
                                <span class="card-desc">Hoje</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="today_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom orangered"></div>
                    </div>
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-laranja.svg') }}" width="35px">
                                <span class="card-desc">Pendente</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="pending_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom orange"></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda.svg') }}" width="35px">
                                <span class="card-desc">Disponível</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="available_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom green"></div>
                    </div>
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-azul.svg') }}" width="35px">
                                <span class="card-desc">Total</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="total_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom blue"></div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img class="orange-gradient" src="{{ asset('modules/global/img/svg/chargeback.svg') }}"
                                     width="35px">
                                <span class="card-desc">Taxa de Chargebacks</span>
                            </div>
                            <i class="material-icons gray" data-toggle="tooltip" data-placement="bottom" title="Taxa geral de chargeback de sua empresa">help</i>
                        </div>
                        <div class="card-body font-size-24 text-center d-flex align-items-topline align-items-center">
                            <div class="col text-center">
                                <div class="circle">
                                    <strong>0.00%</strong>
                                </div>
                            </div>
                            <div class="col">
                                <span id="total_sales_approved" class="text-money">0</span>
                                <div class="font-size-14 mb-20">Vendas no Cartão</div>
                                <span id="total_sales_chargeback" class="text-money">0</span>
                                <div class="font-size-14">Chargebacks</div>
                            </div>
                        </div>
                        <div class="card-bottom red"></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8" id="news-col" style="display:none">
                    <div id="carouselNews" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                        </ol>
                        <div class="carousel-inner">
                        </div>
                        <a class="carousel-control-prev" href="#carouselNews" role="button" data-slide="prev">
                            <i class="material-icons font-size-60">navigate_before</i>
                        </a>
                        <a class="carousel-control-next" href="#carouselNews" role="button" data-slide="next">
                            <i class="material-icons font-size-60">navigate_next</i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4" id="releases-col" style="display:none">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-15">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img class="orange-gradient" src="{{ asset('modules/global/img/svg/releases.svg') }}"
                                     width="35px">
                                <span class="card-desc">Atualizações da Plataforma</span>
                            </div>
                        </div>
                        <div class="card-body pt-0 d-flex flex-column justify-content-between mb-15" id="releases-div" style="overflow-y: auto; height: 275px;">
                        </div>
                        <div class="card-bottom orange"></div>
                    </div>
                </div>
            </div>
        </div>
        @include('companies::empty')
    </div>

    @push('scripts')
        <script src="{{ asset('modules/global/js/circle-progress.min.js') }}"></script>
        <script src="{{ asset('modules/dashboard/js/dashboard.js?v=18') }}"></script>
    @endpush

@endsection


