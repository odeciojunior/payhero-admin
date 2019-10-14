@extends("layouts.master")
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
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
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-vermelha.svg') }}" width="35px">
                                <span class="card-desc">Hoje</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="antecipation_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom orangered"></div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
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
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
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
                </div>
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white p-20">
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
            </div>

            <div class="row" id="cardWelcome">
                <div class="col-lg-12">
                    <div class="card shadow br15">
                        <a class="close-card pointer" id="closeWelcome" role="button">
                            <i class="material-icons md-16">close</i>
                        </a>
                        <img class="card-img-top product-image br15"
                             src="{!! asset('modules/global/img/welcome-gradient.png') !!}">
                    </div>
                </div>
            </div>
        </div>
        @include('companies::empty')
    </div>

    @push('scripts')
        <script src="{{ asset('modules/dashboard/js/dashboard.js?v=2') }}"></script>
    @endpush

@endsection


