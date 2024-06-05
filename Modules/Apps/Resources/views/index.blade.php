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
                    {{-- <div
                         class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch div-notazz-integration">
                        <div class="card app-integration"
                             data-url="/apps/notazz/"
                             style='width: 270px;'>
                            <a id="notazz-bt"
                               href="/apps/notazz/"
                               class="add-btn">
                                <i id="notazz-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/notazz.png') !!}"
                                 alt=""
                                 align="middle">
                            <div class="card-body">
                                <h5 class="card-title">Notazz</h5>
                                <p class="card-text sm">Integre suas lojas com a Notazz </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/hotzapp/"
                             style='width: 270px;'>
                            <a id="hotzapp-bt"
                               href="/apps/hotzapp/"
                               class="add-btn">
                                <i id="hotzapp-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/hotzapp.png') !!}"
                                 alt="">
                            <div class="card-body">
                                <h5 class="card-title">Hotzapp</h5>
                                <p class="card-text sm">Integre suas lojas com HotZapp </p>
                            </div>
                        </div>
                    </div> --}}
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
                    {{-- <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/convertax"
                             style='width: 270px;'>
                            <a id="convertax-bt"
                               href="/apps/convertax"
                               class="add-btn">
                                <i id="convertax-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/convertax.png') !!}"
                                 alt=""
                                 align="middle">
                            <div class="card-body">
                                <h5 class="card-title">ConvertaX</h5>
                                <p class="card-text sm">Integre suas lojas com ConvertaX </p>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/activecampaign"
                             style='width: 270px;'>
                            <a id="activecampaign-bt"
                               href="/apps/activecampaign"
                               class="add-btn">
                                <i id="activecampaign-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/active_campaign.png') !!}"
                                 alt=""
                                 align="middle">
                            <div class="card-body">
                                <h5 class="card-title">ActiveCampaign</h5>
                                <p class="card-text sm">Integre suas lojas com ActiveCampaign </p>
                            </div>
                        </div>
                    </div>
                    {{--
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/whatsapp2"
                             style='width: 270px;'>
                            <a id="whatsapp2-bt"
                               href="/apps/whatsapp2"
                               class="add-btn">
                                <i id="whatsapp2-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/sak.png') !!}"
                                 alt=""
                                 align="middle">
                            <div class="card-body">
                                <h5 class="card-title">SAK (Whatsapp 2.0)</h5>
                                <p class="card-text sm">Integre suas lojas com Sak </p>
                            </div>
                        </div>
                    </div>
                    --}}
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
                    {{--
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/unicodrop"
                             style='width: 270px;'>
                            <a id="unicodrop-bt"
                               href="/apps/unicodrop"
                               class="add-btn">
                                <i id="unicodrop-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/unicodrop.png') !!}"
                                 alt=""
                                 align="middle">
                            <div class="card-body">
                                <h5 class="card-title">Unicodrop</h5>
                                <p class="card-text sm">Integre suas lojas com Unicodrop </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/smartfunnel"
                             style='width: 270px;'>
                            <a id="smartfunnel-bt"
                               href="/apps/smartfunnel"
                               class="add-btn">
                                <i id="smartfunnel-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/smartfunnel.png') !!}"
                                 alt=""
                                 align="middle">
                            <div class="card-body">
                                <h5 class="card-title">Smart Funnel</h5>
                                <p class="card-text sm">Integre suas lojas com Smart Funnel </p>
                            </div>
                        </div>
                    </div> --}}
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
                    {{--
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/melhorenvio"
                             style='width: 270px;'>
                            <a id="menv-bt"
                               href="/apps/melhorenvio"
                               class="add-btn">
                                <i id="menv-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll"
                                 src="{!! mix('build/global/img/melhorenvio.png') !!}"
                                 alt="">
                            <div class="card-body">
                                <h5 class="card-title">Melhor Envio</h5>
                                <p class="card-text sm">Integre suas lojas com Melhor Envio </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/hotbillet"
                             style='width: 270px;'>
                            <a id="hotbillet-bt"
                               href="/apps/hotbillet"
                               class="add-btn">
                                <i id="hotbillet-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll  "
                                     src="{!! asset('build/global/img/hotbillet.png') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">HotBillet</h5>
                                <p class="card-text sm">Integre suas lojas com HotBillet </p>
                            </div>
                        </div>
                    </div>
                    --}}

                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/astronmembers"
                             style='width: 270px;'>
                            <a id="astronmembers-bt"
                               href="/apps/astronmembers"
                               class="add-btn">
                                <i id="astronmembers-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll   "
                                     src="{!! asset('build/global/img/astromembers.jpeg') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Astron Members</h5>
                                <p class="card-text sm">Integre suas lojas com Astron Members </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/geradorrastreio"
                             style='width: 270px;'>
                            <a id="geradorrastreio-bt"
                               href="/apps/geradorrastreio"
                               class="add-btn">
                                <i id="geradorrastreio-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll   "
                                     src="{!! asset('build/global/img/geradorrastreio.png') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">GR Soluções</h5>
                                <p class="card-text sm">Aumente sua conversão!</p>
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

                    {{--
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration"
                             data-url="/apps/notificacoesinteligentes"
                             style='width: 270px;'>
                            <a id="notificacoesinteligentes-bt"
                               href="/apps/notificacoesinteligentes"
                               class="add-btn">
                                <i id="notificacoesinteligentes-icon"
                                   class="o-add-1"
                                   aria-hidden="true"></i></a>
                            <div style="height: 250px; line-height:250px">
                                <img class="card-img-top card-img-controll  "
                                     src="{!! asset('build/global/img/notificacoesinteligentes.png') !!}"
                                     alt="">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Notificações Inteligentes</h5>
                                <p class="card-text sm">Integre suas lojas com Notificações Inteligentes</p>
                            </div>
                        </div>
                    </div> --}}
                    {{-- <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration" data-url="/integrations" style='width:270px;'>
                            <a id="tool_integrations-bt" href="/integrations" class="add-btn">
                                <i id="tool_integrations-icon" class="o-add-1" aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll p-20" src="{!! mix('build/global/img/svg/api.svg') !!}" alt="">
                            <div class="card-body">
                                <h5 class="card-title">Integrações</h5>
                                <p class="card-text sm">Crie chaves de acesso para apps de terceiros</p>
                            </div>
                        </div>
                    </div> --}}

                    {{-- <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 pointer d-flex align-items-stretch">
                        <div class="card app-integration" data-url="/apps/digitalmanager" style='width: 270px;'>
                            <a id="digitalmanager-bt" href="/apps/digitalmanager" class="add-btn"><i id="digitalmanager-icon" class="o-add-1" aria-hidden="true"></i></a>
                            <img class="card-img-top card-img-controll" src="{!! mix('build/global/img/digital_manager_guru.png') !!}" alt="" align="middle">
                            <div class="card-body">
                                <h5 class="card-title">Digital Manager Guru</h5>
                                <p class="card-text sm">Integre suas lojas com Digital Manager Guru</p>
                            </div>
                        </div>
                    </div> --}}
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
