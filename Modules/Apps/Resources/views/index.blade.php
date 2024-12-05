@extends('layouts.master')
@section('content')
    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/apps/index.min.css') }}">
    @endpush

    <div class="page">

        @include('layouts.company-select', ['version' => 'mobile'])

        <div class="page-header container">
            <div class="row align-items-center justify-content-between"
                 style="min-height:50px">
                <div class="col-6">
                    <h1 class="page-title">Aplicativos</h1>
                </div>
            </div>
        </div>
        <div>
            <div class="page-content container"
                 style="padding-top: 0">
                <div class="row loading-container"></div>
                <div class="row"
                     id="project-not-empty"
                     style="display: none !important;">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/shopify"
                             style='width: 270px;'>
                            <a id="shopify-bt"
                               href="/apps/shopify"
                               class="add-btn">
                                <i id="shopify-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/shopify.png') !!}"
                                 alt="">
                            <div class="card-body">
                                <h5 class="card-title">Shopify</h5>
                                <p class="card-text sm">Integre suas lojas com Shopify </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/reportana"
                             style='width: 270px;'>
                            <a id="reportana-bt"
                               href="/apps/reportana"
                               class="add-btn">
                                <i id="reportana-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/reportana.png') !!}"
                                 alt=""
                                 align="middle">
                            <div class="card-body">
                                <h5 class="card-title">Reportana</h5>
                                <p class="card-text sm">Integre suas lojas com Reportana </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/woocommerce"
                             style='width: 270px;'>
                            <a id="woocom-bt"
                               href="/apps/woocommerce"
                               class="add-btn">
                                <i id="woocom-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll "
                                     src="{!! asset('build/global/img/woocom.jpg') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">WooCommerce</h5>
                                <p class="card-text sm">Integre suas lojas com WooCommerce </p>
                            </div>
                        </div>
                    </div>


                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/utmify"
                             style='width: 270px;'>
                            <a id="utmify-bt"
                               href="/apps/utmify"
                               class="add-btn">
                                <i id="utmify-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll px-4"
                                     src="{!! asset('build/global/img/utmify.png') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"></h5>
                                <p class="card-text sm">Integre suas lojas com Utmify</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/vegacheckout"
                             style='width: 270px;'>
                            <a id="vegacheckout-bt"
                               href="/apps/vegacheckout"
                               class="add-btn">
                                <i id="vegacheckout-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll px-4"
                                     src="{!! asset('build/global/img/vega.png') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Vega Checkout</h5>
                                <p class="card-text sm">Checkout Vega + Gateway Azcend!</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/adooreicheckout"
                             style='width: 270px;'>
                            <a id="adooreicheckout-bt"
                               href="/apps/adooreicheckout"
                               class="add-btn">
                                <i id="adooreicheckout-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll px-4"
                                     src="{!! asset('build/global/img/adoorei.png') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Adoorei Checkout</h5>
                                <p class="card-text sm">Checkout Adoorei + Gateway Azcend!</p>
                            </div>
                        </div>
                    </div>

                    @if(!foxutils()->isProduction() || auth()->user()->email == 'jeanvcastro1@gmail.com')
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/nuvemshop"
                             style='width: 270px;'>
                            <a id="nuvemshop-btn"
                               href="/apps/nuvemshop"
                               class="add-btn">
                                <i id="nuvemshop-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll px-4"
                                     src="{!! asset('build/global/img/nuvemshop.png') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Nuvemshop (Beta)</h5>
                                <p class="card-text sm">Integre suas lojas com Nuvemshop</p>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
        {{-- Quando não tem loja cadastrado --}}
        @include('projects::empty')
        {{-- FIM loja nao existem lojas --}}
    </div>

    @push('scripts')
        <script src="{{ mix('build/layouts/apps/index.min.js') }}"></script>
    @endpush
@endsection
