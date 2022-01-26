@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/raty/3.0.0/jquery.raty.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css?v=13') }}">
    <link rel="stylesheet" href="{{ asset('/modules/global/css/table.css?v='. versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('/modules/projects/css/style.css?v=15') }}">
    <style>
        @font-face {
            font-family: raty;
            src: url(https://cdnjs.cloudflare.com/ajax/libs/raty/3.0.0/fonts/raty.svg);
        }
    </style>
@endpush

@section('content')

    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container">
            <h1 class="page-title my-10" style="min-height: 28px">
                <a class="gray" href="/projects">
                    <span class="o-arrow-right-1 font-size-30 ml-2 gray" aria-hidden="true"></span>
                    Meus projetos
                </a>
            </h1>
        </div>
        <div class="page-content container page-project" style="display: none">
            <!-- Painel de informações gerais -->
            <input type="hidden" id="project_type">
            <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                <div class="card">
                    <div class="row no-gutters">
                        <div class="col-md-3">
                            <img id="show-photo" class="card-img" src="" alt="">
                        </div>
                        <div class="col-md-9 pl-10">
                            <div class="card-body h-p100">
                                <div class="row h-p100 justify-content-between align-items-start">
                                    <div style="line-height: normal" class="col-md-9">
                                        <div>
                                            <div class="row row-flex row-title justify-content-between">
                                                <h4 class="title-pad mr-5 s-title"></h4>
                                                <span id="show-status" class="text-white details-text md p-2 pr-4 pl-4 badge-pill mr-10"></span>
                                            </div>
                                            <div style="color: #C8C8C8" class="card-text gray font-size-10" id="created_at"></div>
                                        </div>

                                        <div class="my-20 s-control-magin">
                                            <h5 style="line-height: unset" class="sm-title s-title-description mb-5"><strong> Descrição </strong></h5>
                                            <p id="show-description" class="card-text sm s-description"></p>
                                        </div>

                                        <div class="col-12 my-10 px-0">
                                            <div class="row">
                                                {{-- <div id="value-cancel" class="col-3 text-center">1.2K</div>--}}
                                                <div class="col-3 col-md-2 d-flex justify-content-center align-items-center">
                                                    <img class="control-img mr-5" src="{{ asset('/modules/global/img/svg/chamados-abertos.svg') }}">
                                                    <span class="s-data-project-values" id="value-chargeback"></span>
                                                </div>
                                                <div class="col-3 col-md-2 d-flex justify-content-center align-items-center border-between">
                                                    <img class="control-img mr-5" src="{{ asset('/modules/global/img/svg/atendimentos-abertos.svg') }}">
                                                    <span class="s-data-project-values" id="value-open-tickets"></span>
                                                </div>
                                                <div class="col-3 col-md-2 d-flex justify-content-center align-items-center">
                                                    <img class="control-img mr-5" src="{{ asset('/modules/global/img/svg/vendas-rastreio.svg') }}">
                                                    <span class="s-data-project-values" id="value-without-tracking"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-9 col-md-6" style="border-bottom: 1px solid #EEEEEE; margin: 5px 0;"></div>

                                        <div class="row">
                                            {{--                                                <div class="col-3 text-center font-size-10">--}}
                                            {{--                                                    <small> CANCELADAS </small>--}}
                                            {{--                                                </div>--}}
                                            <div class="col-3 col-md-2 text-center font-size-10 s-data-project">
                                                <small> CHARGEBACKS </small>
                                            </div>
                                            <div class="col-3 col-md-2 text-center font-size-10 s-data-project">
                                                <small> CHAMADOS ABERTOS </small>
                                            </div>
                                            <div class="col-3 col-md-2 text-center font-size-10 s-data-project">
                                                <small> VENDAS S/ RASTREIO </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 h-auto h-md-p100 d-flex flex-wrap align-items-center justify-content-between justify-content-md-start my-10 my-md-0" style="border-left: 1px solid #EEEEEE">
                                        <div class="col-12 my-10 mt-md-auto">
                                            <div class="d-flex">
                                                <img class="control-img mr-5" src="{{ asset('/modules/global/img/svg/store-sales.svg') }}">
                                                <span class="font-size-12"> Vendas Aprovadas </span>
                                            </div>
                                            <div>
                                                <strong style="color: #707070" class="font-size-18" id="total-approved">0</strong>
                                            </div>
                                        </div>

                                        <div class="col-11" style="border-bottom: 1px solid #EEEEEE; margin: 5px auto;"></div>

                                        <div class="col-12 my-10 mb-md-auto">
                                            <div class="d-flex">
                                                <img class="control-img mr-5" src="{{ asset('/modules/global/img/svg/store-coin.svg') }}">
                                                <span class="font-size-12"> Total </span>
                                            </div>
                                            <div style="color: #707070">
                                                <small> R$ </small>
                                                <strong class="font-size-18" id="total-approved-value">0</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <div class="row ml-0">
                        <ul id="slick-tabs" class="nav nav-tabs nav-tabs-line col-9 col-md-11" role="tablist">
                            <li class="nav-item tab-domains" role="presentation">
                                <span style="color: #2E85EC" id="first-category" class="category-tabs">PRINCIPAL</span>
                                <a id="tab-domains" class="nav-link active" data-toggle="tab" href="#tab_domains"
                                   aria-controls="tab-domains" role="tab">Domínios <span id="count-cupons" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_plans" role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_plans" class="nav-link" data-toggle="tab" href="#tab_plans-panel"
                                   aria-controls="tab_plans" role="tab">Planos <span id="count-plans" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab-fretes" role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab-fretes" class="nav-link" data-toggle="tab" href="#tab-fretes-panel"
                                   aria-controls="tab-fretes" role="tab">Frete <span id="count-fretes" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_pixels" role="presentation">
                                <span id="second-category" class="category-tabs">MARKETING</span>
                                <a id="tab_pixels" class="nav-link" data-toggle="tab" href="#tab_pixels-panel"
                                   aria-controls="tab_pixels" role="tab">Pixels <span id="count-pixels" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_upsell" role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_upsell" class="nav-link" data-toggle="tab" href="#tab_upsell-panel"
                                   aria-controls="tab_upsell" role="tab">Upsell <span id="count-upsell" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_order_bump" role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_order_bump" class="nav-link" data-toggle="tab" href="#tab-order-bump-panel"
                                   aria-controls="tab_order_bump" role="tab">Order Bump <span id="count-order-bump" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_coupons" role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id='tab_coupons' class="nav-link" data-toggle="tab" href="#tab_coupons-panel"
                                   aria-controls="tab_coupons" role="tab">Cupons <span id="count-coupons" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_reviews" role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_reviews" class="nav-link" data-toggle="tab"
                                   href="#tab_project_reviews"
                                   aria-controls="tab_project_reviews" role="tab"> Reviews <span id="count-project-reviews" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_sms" role="presentation">
                                <span id="third-category" class="category-tabs">RECUPERAÇÃO</span>
                                <a id='tab_sms' class="nav-link" data-toggle="tab" href="#tab_sms-panel"
                                   aria-controls="tab_sms" role="tab">Notificações <span id="count-notifications" class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-tabs nav-tabs-line col-3 col-md-1">
                            <li class="nav-item tab_configuration" role="presentation" style="margin-left: auto;">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_configuration" class="nav-link" data-toggle="tab" style="padding: 14px 16px 12px 16px;"
                                   href="#tab_configuration_project"
                                   aria-controls="tab_configuration_project" role="tab"> <img height="22" src="{{ asset('modules/global/img/svg/settings.svg') }}"/>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="shadow" data-plugin="matchHeight" style="margin-top: 2px;">
                <div class="tab-content">
                    <div class="tab-content">
                        <!-- Painel de Dominios -->
                        <div id="tab_domains" class="tab-pane active" role="tabpanel">
                            <div class="card card-body">
                                @include('domains::index')
                            </div>
                        </div>
                        <!-- Painel de Pixels -->
                        <div class="tab-pane" id="tab_pixels-panel" role="tabpanel">
                            <div class="card card-body">
                                @include('pixels::index')
                            </div>
                        </div>
                        <!-- Painel de Cupons de Descontos -->
                        <div class="tab-pane" id="tab_coupons-panel" role="tabpanel">
                            <div class="card card-body">
                                @include('discountcoupons::index')
                            </div>
                        </div>
                        <!-- Painel de Sms -->
                        <div class="tab-pane" id="tab_sms-panel" role="tabpanel">
                            <div class="card card-body">
                                @include('projectnotification::index')
                            </div>
                        </div>
                        <!-- Painel de Fretes -->
                        <div class="tab-pane" id="tab-fretes-panel" role="tabpanel">
                            <div class="card card-body">
                                @include('shipping::index')
                            </div>
                        </div>
                        <!--- Painel de Planos -->
                        <div class="tab-pane" id="tab_plans-panel" role="tabpanel">
                            @include('plans::index')
                        </div>
                        <!--- Upsell -->
                        <div class="tab-pane" id="tab_upsell-panel" role="tabpanel">
                            <div class="card card-body">
                                @include('projectupsellrule::index')
                            </div>
                        </div>
                        <!--- Order Bump -->
                        <div class="tab-pane" id="tab-order-bump-panel" role="tabpanel">
                            <div class="card card-body">
                                @include('orderbump::index')
                            </div>
                        </div>
                        <!-- Reviews -->
                        <div class="tab-pane" id="tab_project_reviews" role="tabpanel">
                            <div class="card card-body">
                                @include('projectreviews::index')
                            </div>
                        </div>
                        <!-- Painel de Configurações  Abre a tela edit-->
                        <div class="tab-pane" id="tab_configuration_project" role="tabpanel">
                            @include('projects::edit')
                        </div>
                    </div>
                    <!-- Modal para fazer-desfazer integração com shopify -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-change-shopify-integration" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div class="modal-dialog  modal-dialog-centered  modal-simple">
                            <div class="modal-content p-10 s-border-radius">
                                <div class="modal-header text-center">
                                    <a class="pointer close" role="button" data-dismiss="modal" aria-label="Close" id="bt-close-modal-change-shopify-integration">
                                        <i class="material-icons md-16">close</i>
                                    </a>
                                </div>
                                <div class="modal-body text-center p-20">
                                    <div class="d-flex justify-content-center">
                                        <span class="o-reload-1"></span>
                                    </div>
                                    <h3 class="black" id="modal-change-shopify-integration-title"> Você tem
                                        certeza? </h3>
                                    <p class="gray" id="modal-change-shopify-integration-text"></p>
                                </div>
                                <div class="modal-footer d-flex align-items-center justify-content-center">
                                    <button type="button" class="btn btn-gray" data-dismiss="modal" style="width: 20%;">
                                        Cancelar
                                    </button>
                                    <button id="bt-modal-change-shopify-integration" type="button"
                                            class="btn btn-success" style="width: 20%;">Confirmar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para integração com woocommerce -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
                         id="modal-woocom-integration-apikeys" aria-hidden="true" aria-labelledby="exampleModalTitle"
                         role="dialog" tabindex="-1">
                        <div class="modal-dialog  modal-dialog-centered  modal-simple">

                            <div class="modal-content">
                                <div class="modal-header text-center">
                                    <a class="pointer close" role="button" data-dismiss="modal"
                                       aria-label="Close" id="bt-close-modal-change-shopify-integration">
                                        <i class="material-icons md-16">close</i>
                                    </a>
                                </div>


                                <div class="modal-body p-10 ">
                                    <div class='col-md-12 col-sm-12 col-12'>

                                        <h4>Atualizar chaves de acesso REST API</h4>
                                    </div>

                                    <div class='col-md-10 col-sm-12 col-12'>
                                        <label class="control-label"> Consumer key </label>
                                        <input class="form-control" id="consumer_k"  type='text'   class="form-control">

                                    </div>

                                    <div class='col-md-10 col-sm-12 col-12 mt-20'>
                                        <label class="control-label"> Consumer secret </label>
                                        <input class="form-control" id="consumer_s"  type='text'   class="form-control">

                                    </div>
                                </div>



                                <div class="modal-footer d-flex align-items-center justify-content-center">
                                        <button id="close-modal" type="button" class="btn btn-gray" data-dismiss="modal" style="width: 20%;">
                                            Cancelar
                                        </button>
                                        <button id="bt-modal-woocommerce-apikeys" type="button"
                                                class="btn btn-success" style="width: 20%;">Confirmar
                                        </button>
                                    </div>

                            </div>
                        </div>
                    </div>


                    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
                         id="modal-woocom-integration" aria-hidden="true" aria-labelledby="exampleModalTitle"
                         role="dialog" tabindex="-1">
                        <div class="modal-dialog  modal-dialog-centered  modal-simple">

                            <div class="modal-content">
                                <div class="modal-header text-center">
                                    <a class="pointer close" role="button" data-dismiss="modal"
                                       aria-label="Close" id="bt-close-modal-change-shopify-integration">
                                        <i class="material-icons md-16">close</i>
                                    </a>
                                </div>

                                <div class="modal-body text-center p-10">
                                        <div class="d-flex justify-content-center ">
                                            <span class="o-reload-1"></span>
                                        </div>
                                </div>

                                <div id="_loading" style="display:none">
                                    <div class="modal-body text-center p-10">

                                        <h3 class="black" id="modal-title">
                                            Processando requisição.
                                        </h3>
                                    </div>

                                </div>

                                <div id="_content">

                                    <div class="modal-body text-center p-10">

                                        <h3 class="black" id="modal-title">Selecione as opções para sincronizar</h3>
                                        <p class="gray pt-10" id="modal-text">

                                            <div class="switch-holder">
                                                <label class="switch" style="top:3px">
                                                    <input type="checkbox" id="opt_prod" name="product_amount_selector" class="check" value="1">
                                                    <span class="slider round"></span>
                                                </label>
                                                <label class="text-left" for="opt_prod" style="margin-right:15px;margin-bottom: 3px; width:346px">
                                                    Sincronizar produtos com WooCommerce</label>
                                            </div>

                                            <div class="switch-holder" style="margin-top:4px">
                                                <label class="switch" style="top:3px">
                                                    <input type="checkbox" id="opt_track" name="product_amount_selector" class="check" value="1">
                                                    <span class="slider round"></span>
                                                </label>
                                                <label class="text-left" for="opt_track" style="margin-right:15px;margin-bottom: 3px; width:346px">
                                                    Sincronizar códigos de rastreio com WooCommerce</label>
                                            </div>

                                            <div class="switch-holder" style="margin-top:4px">
                                                <label class="switch" style="top:3px">
                                                    <input type="checkbox" id="opt_webhooks" name="product_amount_selector" class="check" value="1">
                                                    <span class="slider round"></span>
                                                </label>
                                                <label class="text-left" for="opt_webhooks" style="margin-right:15px;margin-bottom: 3px; width:346px">
                                                    Sincronizar webhooks com WooCommerce</label>
                                            </div>

                                        </p>
                                    </div>
                                    <div class="modal-footer d-flex align-items-center justify-content-center">
                                        <button id="close-modal" type="button" class="btn btn-gray" data-dismiss="modal" style="width: 20%;">
                                            Cancelar
                                        </button>
                                        <button id="bt-modal-sync-woocommerce" type="button"
                                                class="btn btn-success" style="width: 20%;">Confirmar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
{{--        <script src="{{asset('modules/partners/js/partners.js?v='.uniqid())}}"></script>--}}
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js"></script>
        <script type="text/javascript" src="{{asset('modules/global/ckeditor5/ckeditor.js')}}"></script>
        <script type="text/javascript" src="{{asset('modules/global/ckeditor5/pt-br.js')}}"></script>
        <script src="{{asset('modules/domain/js/domainEdit.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/plans/js/loading.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/plans/js/plans.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/shipping/js/shipping.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/pixels/js/pixels.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/projectupsell/js/index.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/orderbump/js/index.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/discount-coupons/js/discountCoupons.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/projectreviews/js/index.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/project-notification/js/projectNotification.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/projects/js/projects.js?v='.uniqid())}}"></script>
        <script src="{{asset('modules/global/js/select2.min.js')}}"></script>
        <script src="{{asset('modules/global/js/jquery.raty.min.js')}}"></script>
        <script src="{{asset('modules/global/js-extra/jquery-loading.min.js')}}"></script>
        <script src="{{asset('modules/woocommerce/js/syncproducts.js?v='.uniqid())}}"></script>
    @endpush
@endsection
