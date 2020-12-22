@extends("layouts.master")
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=5') }}">
        <link rel="stylesheet" href="{{ asset('modules/dashboard/css/index.css?v=3') }}">
    @endpush

    <div class="page">
        <div style="display: none" style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 mb-15">
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="col-lg-6" id="company-select" style="display:none">
                    <div class="d-lg-flex align-items-center justify-content-end">
                        <div class="mr-10 mb-5 text-lg-right">
                            Empresa:
                        </div>
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
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-vermelha.svg') }}" width="30px">
                                <span class="card-desc">Hoje</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <div class="card-text row align-items-center">
                                <span class="moeda"></span>
                                <span id="today_money" class="text-money"></span>
                            </div>
                        </div>
                        <div class="card-bottom orangered"></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-laranja.svg') }}" width="30px">
                                <span class="card-desc">Pendente</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <div class="card-text row align-items-center">
                                <span class="moeda"></span>
                                <span id="pending_money" class="text-money"></span>
                            </div>
                        </div>
                        <div class="card-bottom orange"></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda.svg') }}" width="30px">
                                <span class="card-desc">Disponível</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <div class="card-text row align-items-center">
                                <span class="moeda"></span>
                                <span id="available_money" class="text-money"></span>
                            </div>
                        </div>
                        <div class="card-bottom green"></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img src="{{ asset('modules/global/img/svg/moeda-azul.svg') }}" width="30px">
                                <span class="card-desc">Total</span>
                            </div>
                            <i class="material-icons gray" data-toggle="tooltip" id="info-total-balance" data-placement="bottom">help</i>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <div class="card-text row align-items-center">
                                <span class="moeda"></span>
                                <span id="total_money" class="text-money"></span>
                            </div>
                        </div>
                        <div class="card-bottom blue"></div>
                    </div>
                </div>
            </div>
            <!-- Tracking e Chargeback -->
            <div class="row">
                <div class="col-12 col-lg-4 d-flex align-items-stretch">
                    <div class="card card-shadow bg-white w-full">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img src="{{ asset('modules/global/img/svg/shipping.svg') }}"
                                     width="30px">
                                <span class="card-desc">Códigos de rastreio informados</span>
                            </div>
                            <i class="material-icons gray" data-toggle="tooltip" data-placement="bottom"
                               title="As vendas que permanecerem sem o código de rastreamento por 15 dias poderão ser estornadas. Geralmente o tempo médio de postagem é de 5 dias">help</i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-lg-between py-15">
                            <div class="d-inline-flex">
                                <label>Tempo médio de postagem: &nbsp; </label>
                                <span class="update-text" id="average_post_time"></span>
                            </div>
                            <div class="d-inline-flex">
                                <label>Venda mais antiga sem código: &nbsp; </label>
                                <span class="update-text" id="oldest_sale"></span>
                            </div>
                            <div class="d-inline-flex">
                                <label>Códigos informados com problema: &nbsp; </label>
                                <span class="update-text" id="problem"></span>
                            </div>
                            <div class="d-inline-flex">
                                <label>Códigos não informados: &nbsp; </label>
                                <span class="update-text" id="unknown"></span>
                            </div>
                        </div>
                        <div class="card-bottom orangered"></div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex align-items-stretch">
                    <div class="card card-shadow bg-white w-full">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img class="orange-gradient" src="{{ asset('modules/global/img/svg/chargeback.svg') }}"
                                     width="30px">
                                <span class="card-desc">Taxa de Chargebacks</span>
                            </div>
                            <i class="material-icons gray" data-toggle="tooltip" data-placement="bottom" title="Taxa geral de chargeback de sua empresa">help</i>
                        </div>
                        <div class="card-body font-size-24 text-center d-flex align-items-topline align-items-center">
                            <div class="col text-center px-0 d-flex justify-content-center">
                                <div class="circle text-circle">
                                    <strong>0.00%</strong>
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-10 d-flex flex-wrap justify-content-center">
                                    <div class="font-size-14 w-p100">Vendas no Cartão</div>
                                    <span id="total_sales_approved" class="text-money">0</span>
                                </div>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <div class="font-size-14 w-p100">Chargebacks</div>
                                    <span id="total_sales_chargeback" class="text-money">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-bottom red"></div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex align-items-stretch">
                    <div class="card card-shadow bg-white w-full">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img src="{{ asset('modules/global/img/svg/tickets.svg') }}"
                                     width="30px">
                                <span class="card-desc">Atendimento</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row mb-15">
                                <div class="col text-center d-flex flex-wrap justify-content-center">
                                    <span id="open-tickets" class="text-money">0</span>
                                    <div class="font-size-14 w-p100">Abertos</div>
                                </div>
                                <div class="col text-center d-flex flex-wrap justify-content-center">
                                    <span id="closed-tickets" class="text-money">0</span>
                                    <div class="font-size-14 w-p100">Resolvidos</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-center d-flex flex-wrap justify-content-center">
                                    <span id="mediation-tickets" class="text-money">0</span>
                                    <div class="font-size-14 w-p100">Em mediação</div>
                                </div>
                                <div class="col text-center d-flex flex-wrap justify-content-center">
                                    <span id="total-tickets" class="text-money">0</span>
                                    <div class="font-size-14 w-p100">Total</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-bottom blue"></div>
                    </div>
                </div>
            </div>
            <!-- Notícias e Releases -->
            <div class="row">
                <div class="col-lg-8" id="news-col" style="display:none">
                    <div id="carouselNews" class="carousel slide" data-ride="carousel" data-interval="15000">
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
                        <div class="card-header d-flex justify-content-start align-items-center bg-white py-10 border-bottom">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img src="{{ asset('modules/global/img/svg/releases.svg') }}"
                                     width="30px">
                                <span class="card-desc">Atualizações da Plataforma</span>
                            </div>
                        </div>
                        <div class="card-body pt-0 d-flex flex-column justify-content-between mb-15" id="releases-div" style="overflow-y: auto; height: 280px;">
                        </div>
                        <div class="card-bottom orange"></div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>

    @push('scripts')
        <script src="{{ asset('modules/global/js/circle-progress.min.js') }}"></script>
        <script src="{{ asset('modules/dashboard/js/dashboard.js?v=' . random_int(100, 10000)) }}"></script>
    @endpush

@endsection


