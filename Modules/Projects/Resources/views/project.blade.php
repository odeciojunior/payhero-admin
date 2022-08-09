@extends('layouts.master')

@push('css')
    <link rel="stylesheet"
          href="{{ mix('build/layouts/projects/project.min.css') }}">
    <style>
        @font-face {
            font-family: raty;
            src: url('/build/layouts/projects/raty.svg');
        }
    </style>
@endpush

@section('content')
    <!-- Page -->
    <div class="page">

        <input type="hidden"
               id="project_type">

        <div style="display: none"
             class="page-header container">
            <h1 class="page-title my-10"
                style="min-height: 28px">
                <a href="/projects"
                   style="outline: none">
                    <span class="o-arrow-right-1 font-size-30 ml-2"
                          aria-hidden="true"></span>
                    Minhas Lojas
                </a>
            </h1>
        </div>

        <div class="page-content container page-project"
             style="display: none">
            <!-- Painel de informações gerais -->
            <div class="row"
                 id="tab_info_geral"
                 role="tabpanel">

                <div class="col-md-12">

                    <div class="row no-gutters">

                        <div class="col-md-12 col-lg-9 card mr-0 mr-md-25 mr-lg-25 mr-xl-30 px-0">

                            <div class="row no-gutters">

                                <div class="col-md-4">
                                    <div class="pl-0">
                                        <img id="show-photo"
                                             class="card-img-edit"
                                             src=""
                                             alt="">
                                    </div>
                                </div>

                                <div class="col-md-8 d-flex flex-column justify-content-between pl-10 pr-25">

                                    <div alt="titulo"
                                         class="pt-10 pt-md-10 pt-lg-15 pt-xl-20 mr-15">

                                        <!-- TITULO -->
                                        <div class="row d-flex row-title justify-content-between pt-0 align-items-start">
                                            <div class="col-lg-9 col-xl-10 title-pad s-title pl-10 pl-sm-0"></div>
                                            <div id="show-status"
                                                 class="col-3 col-md-3 col-lg-3 col-xl-2 text-white text-center details-text my-5 ml-10 ml-sm-0 badge-pill badge-success">
                                            </div>
                                        </div>

                                        <!-- CRIADO EM -->
                                        <div style="color: #C8C8C8"
                                             class="card-text gray font-size-14 pl-10 pl-sm-0"
                                             id="created_at"></div>
                                    </div>

                                    <!-- DESCRISAO PRODUTO -->
                                    <div class="my-10 pl-10 pl-sm-0">
                                        <h5 style="line-height: unset"
                                            class="sm-title s-title-description mb-5"><strong> Descrição da loja </strong>
                                        </h5>
                                        <p id="show-description"
                                           class="card-text sm s-description"></p>
                                    </div>

                                    <!-- RODA PE -->
                                    <div class="row no-gutters">
                                        <!-- CHARGEBACK -->
                                        <div
                                             class="col-md-4 align-items-center py-10 product-chargeback product-info-color">

                                            <div class="d-flex justify-content-start padding-cards-l">
                                                <div class="pl-5">
                                                    <img class="img-default mr-3 mr-lg-10"
                                                         src="{{ mix('build/global/img/projects/icon-arrowback.svg') }}">

                                                </div>

                                                <span class="s-data-project-values pl-5 pl-md-5 align-self-center"
                                                      id="value-chargeback"></span>
                                            </div>

                                            <div
                                                 class="d-flex align-items-start justify-content-start padding-cards-l font-size-12">
                                                <small class="font-size-12">chargebacks</small>
                                            </div>
                                        </div>

                                        <!-- VENDAS S/ RASTREIO -->
                                        <div
                                             class="col-md-4 align-items-center py-10 product-info-color border-product-alert">

                                            <div class="d-flex justify-content-start padding-cards-l">
                                                <div class="d-flex pl-5">
                                                    <img class="img-default mr-3 mr-lg-10"
                                                         src="{{ mix('build/global/img/projects/trackIcon.svg') }}">
                                                </div>
                                                <span class="s-data-project-values pl-5 align-self-center"
                                                      id="value-without-tracking"></span>
                                            </div>

                                            <div
                                                 class="d-flex align-items-center justify-content-start padding-cards-l font-size-10">
                                                <small class="font-size-12">vendas sem rastreio</small>
                                            </div>

                                        </div>

                                        <!-- CHAMADOS ABERTOS -->
                                        <div
                                             class="col-md-4 align-items-center py-10 open-calls product-info-color border-product-alert">

                                            <div class="d-flex justify-content-start padding-cards-l">
                                                <div class="d-flex pl-5">
                                                    <img class="img-default mr-3 mr-lg-10"
                                                         src="{{ mix('build/global/img/projects/icon-chat.svg') }}">
                                                </div>

                                                <span class="s-data-project-values pl-5 align-self-center"
                                                      id="value-open-tickets"></span>
                                            </div>

                                            <div
                                                 class="d-flex align-items-center justify-content-start padding-cards-l font-size-10">
                                                <small class="font-size-12">chamados abertos</small>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- RESUMO VENDAS -->
                        <div class="row f-grow mx-0">
                            <div class="card d-flex justify-content-between col-sm-12 col-md-6 col-lg col-xl px-0">

                                <!-- VENDAS APROVADAS -->
                                <div class="pl-20 pl-md-30 pt-15 pt-lg-40 pt-md-20 pl-lg-20 pl-xl-30 pt-xl-45 pb-xl-25">
                                    <div class="d-flex">
                                        <div class="p-5 resume-sales mr-10">
                                            <img class="control-img mr-5"
                                                 src="{{ mix('build/global/img/projects/groceryCart.svg') }}">
                                        </div>
                                        <span class="d-flex align-items-center font-size-14"> Vendas aprovadas </span>
                                    </div>

                                    <div>
                                        <strong style="color: #707070"
                                                class="font-size-24"
                                                id="total-approved">0</strong>
                                    </div>
                                </div>

                                <!-- RECEITA TOTAL -->
                                <div class="pl-20 pl-md-30 pb-15 pl-lg-20 pl-xl-30 pb-lg-40 pb-xl-50">
                                    <div class="d-flex pt-10">
                                        <div class="p-5 resume-sales mr-10">
                                            <img class="mr-5"
                                                 src="{{ mix('build/global/img/projects/arrowBalance.svg') }}">
                                        </div>
                                        <span class="d-flex align-items-center font-size-14">Total em receita</span>
                                    </div>

                                    <div style="color: #707070">
                                        <small> R$ </small>
                                        <strong class="font-size-24"
                                                id="total-approved-value">0</strong>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div>
                <div class="nav-tabs-horizontal"
                     data-plugin="tabs">
                    <div class="row ml-0">
                        <ul class="nav nav-tabs nav-tabs-line col-9 col-md-11 vertical-scroll"
                            role="tablist">
                            <li class="nav-item tab-domains"
                                role="presentation">
                                <span style="color: #2E85EC"
                                      id="first-category"
                                      class="category-tabs">PRINCIPAL</span>
                                <a id="tab-domains"
                                   class="nav-link active"
                                   data-toggle="tab"
                                   href="#tab_domains"
                                   aria-controls="tab-domains"
                                   role="tab">
                                    Domínios
                                    <span id="count-cupons"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_plans"
                                role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_plans"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_plans-panel"
                                   aria-controls="tab_plans"
                                   role="tab">Planos <span id="count-plans"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab-fretes"
                                role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab-fretes"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab-fretes-panel"
                                   aria-controls="tab-fretes"
                                   role="tab">Frete <span id="count-fretes"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab-checkout"
                                role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab-checkout"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_checkout-panel"
                                   aria-controls="tab-checkout"
                                   role="tab">Checkout<span id="count-checkout"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_pixels"
                                role="presentation">
                                <span id="second-category"
                                      class="category-tabs">MARKETING</span>
                                <a id="tab_pixels"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_pixels-panel"
                                   aria-controls="tab_pixels"
                                   role="tab">Pixels <span id="count-pixels"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_upsell"
                                role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_upsell"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_upsell-panel"
                                   aria-controls="tab_upsell"
                                   role="tab">Upsell <span id="count-upsell"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_order_bump"
                                role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_order_bump"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab-order-bump-panel"
                                   aria-controls="tab_order_bump"
                                   role="tab">Order Bump <span id="count-order-bump"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_coupons"
                                role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id='tab_coupons'
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_coupons-panel"
                                   aria-controls="tab_coupons"
                                   role="tab">Descontos <span id="count-coupons"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_reviews"
                                role="presentation">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_reviews"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_project_reviews"
                                   aria-controls="tab_project_reviews"
                                   role="tab"> Reviews <span id="count-project-reviews"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                            <li class="nav-item tab_sms"
                                role="presentation">
                                <span id="third-category"
                                      class="category-tabs">RECUPERAÇÃO</span>
                                <a id='tab_sms'
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_sms-panel"
                                   aria-controls="tab_sms"
                                   role="tab">Notificações <span id="count-notifications"
                                          class=" d-none tab-counter"> 0 </span>
                                </a>
                            </li>
                        </ul>
                        <ul class="nav nav-tabs nav-tabs-line col-3 col-md-1">
                            <li class="nav-item tab_configuration mr-0"
                                role="presentation"
                                style="margin-left: auto;margin-right: 10px">
                                <span class="category-tabs">&nbsp;</span>
                                <a id="tab_configuration"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_configuration_project"
                                   aria-controls="tab_configuration_project"
                                   role="tab">
                                    <svg width="18"
                                         height="18"
                                         viewBox="0 0 23 24"
                                         fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.5 7.5C9.01472 7.5 7 9.51472 7 12C7 14.4853 9.01472 16.5 11.5 16.5C12.8488 16.5 14.059 15.9066 14.8838 14.9666C15.5787 14.1745 16 13.1365 16 12C16 11.5401 15.931 11.0962 15.8028 10.6783C15.2382 8.838 13.5253 7.5 11.5 7.5ZM8.5 12C8.5 10.3431 9.84315 9 11.5 9C13.1569 9 14.5 10.3431 14.5 12C14.5 13.6569 13.1569 15 11.5 15C9.84315 15 8.5 13.6569 8.5 12ZM19.2093 20.3947L17.4818 19.6364C16.9876 19.4197 16.4071 19.4514 15.94 19.7219C15.4729 19.9923 15.175 20.4692 15.1157 21.0065L14.908 22.8855C14.8651 23.2729 14.584 23.5917 14.2055 23.6819C12.4263 24.106 10.5725 24.106 8.79326 23.6819C8.41476 23.5917 8.13363 23.2729 8.09081 22.8855L7.88343 21.0093C7.82251 20.473 7.5112 19.9976 7.04452 19.728C6.57783 19.4585 6.01117 19.4269 5.51859 19.6424L3.79071 20.4009C3.43281 20.558 3.01493 20.4718 2.74806 20.1858C1.50474 18.8536 0.57924 17.2561 0.0412152 15.5136C-0.074669 15.1383 0.0592244 14.7307 0.3749 14.4976L1.90219 13.3703C2.33721 13.05 2.59414 12.5415 2.59414 12.0006C2.59414 11.4597 2.33721 10.9512 1.90162 10.6305L0.375288 9.50507C0.0591436 9.27196 -0.0748729 8.86375 0.0414199 8.48812C0.580376 6.74728 1.50637 5.15157 2.74971 3.82108C3.01684 3.53522 3.43492 3.44935 3.79276 3.60685L5.51296 4.36398C6.00793 4.58162 6.57696 4.54875 7.04617 4.27409C7.51335 4.00258 7.82437 3.52521 7.88442 2.98787L8.09334 1.11011C8.13697 0.717971 8.42453 0.396974 8.80894 0.311314C9.69003 0.114979 10.5891 0.0106508 11.5131 0C12.4147 0.0104117 13.3128 0.114784 14.1928 0.311432C14.577 0.397275 14.8643 0.718169 14.9079 1.11011L15.117 2.98931C15.2116 3.85214 15.9387 4.50566 16.8055 4.50657C17.0385 4.50694 17.269 4.45832 17.4843 4.36288L19.2048 3.60562C19.5626 3.44812 19.9807 3.53399 20.2478 3.81984C21.4912 5.15034 22.4172 6.74605 22.9561 8.48689C23.0723 8.86227 22.9386 9.27022 22.6228 9.50341L21.0978 10.6297C20.6628 10.9499 20.4 11.4585 20.4 11.9994C20.4 12.5402 20.6628 13.0488 21.0988 13.3697L22.6251 14.4964C22.941 14.7296 23.0748 15.1376 22.9586 15.513C22.4198 17.2536 21.4944 18.8491 20.2517 20.1799C19.9849 20.4657 19.5671 20.5518 19.2093 20.3947ZM13.763 20.1965C13.9982 19.4684 14.4889 18.8288 15.1884 18.4238C16.0702 17.9132 17.1536 17.8546 18.0841 18.2626L19.4281 18.8526C20.291 17.8537 20.9593 16.7013 21.3981 15.4551L20.2095 14.5777L20.2086 14.577C19.398 13.9799 18.9 13.0276 18.9 11.9994C18.9 10.9718 19.3974 10.0195 20.2073 9.42265L20.2085 9.4217L21.3957 8.54496C20.9567 7.29874 20.2881 6.1463 19.4248 5.14764L18.0922 5.73419L18.0899 5.73521C17.6844 5.91457 17.2472 6.00716 16.8039 6.00657C15.1715 6.00447 13.8046 4.77425 13.6261 3.15459L13.6259 3.15285L13.4635 1.69298C12.8202 1.57322 12.1677 1.50866 11.513 1.50011C10.8389 1.50885 10.1821 1.57361 9.53771 1.69322L9.37514 3.15446C9.26248 4.16266 8.67931 5.05902 7.80191 5.5698C6.91937 6.08554 5.84453 6.14837 4.90869 5.73688L3.57273 5.14887C2.70949 6.14745 2.04092 7.29977 1.60196 8.54587L2.79181 9.42319C3.61115 10.0268 4.09414 10.9836 4.09414 12.0006C4.09414 13.0172 3.61142 13.9742 2.79237 14.5776L1.60161 15.4565C2.04002 16.7044 2.7085 17.8584 3.57205 18.8587L4.91742 18.2681C5.84745 17.8613 6.91573 17.9214 7.79471 18.4291C8.67398 18.9369 9.25934 19.8319 9.37384 20.84L9.37435 20.8445L9.53619 22.3087C10.8326 22.5638 12.1662 22.5638 13.4626 22.3087L13.6247 20.8417C13.6491 20.6217 13.6955 20.4054 13.763 20.1965Z"
                                              fill="#636363" />
                                    </svg>
                                </a>

                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="shadow"
                 data-plugin="matchHeight">
                <div class="tab-content">
                    <div class="tab-content">
                        <!-- Painel de Dominios -->
                        <div id="tab_domains"
                             class="tab-pane active"
                             role="tabpanel">
                            <div class="card card-body">
                                @include('domains::index')
                            </div>
                        </div>
                        <!-- Painel de Editor de Checkout -->
                        <div class="tab-pane"
                             id="tab_checkout-panel"
                             role="tabpanel">
                            <div>
                                @include('checkouteditor::index')
                            </div>
                        </div>
                        <!-- Painel de Pixels -->
                        <div class="tab-pane"
                             id="tab_pixels-panel"
                             role="tabpanel">
                            @include('pixels::index')
                        </div>
                        <!-- Painel de Cupons de Descontos -->
                        <div class="tab-pane"
                             id="tab_coupons-panel"
                             role="tabpanel">
                            @include('discountcoupons::index')
                        </div>
                        <!-- Painel de Sms -->
                        <div class="tab-pane"
                             id="tab_sms-panel"
                             role="tabpanel">
                            @include('projectnotification::index')
                        </div>
                        <!-- Painel de Fretes -->
                        <div class="tab-pane"
                             id="tab-fretes-panel"
                             role="tabpanel">
                            @include('shipping::index')
                        </div>
                        <!--- Painel de Planos -->
                        <div class="tab-pane"
                             id="tab_plans-panel"
                             role="tabpanel">
                            @include('plans::index')
                        </div>
                        <!--- Upsell -->
                        <div class="tab-pane"
                             id="tab_upsell-panel"
                             role="tabpanel">
                            @include('projectupsellrule::index')
                        </div>
                        <!--- Order Bump -->
                        <div class="tab-pane"
                             id="tab-order-bump-panel"
                             role="tabpanel">
                            @include('orderbump::index')
                        </div>
                        <!-- Reviews -->
                        <div class="tab-pane"
                             id="tab_project_reviews"
                             role="tabpanel">
                            @include('projectreviews::index')
                        </div>
                        <!-- Painel de Configurações  Abre a tela edit-->
                        <div class="tab-pane"
                             id="tab_configuration_project"
                             role="tabpanel">
                            @include('projects::edit')
                        </div>
                    </div>
                    <!-- Modal para fazer-desfazer integração com shopify -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
                         id="modal-change-shopify-integration"
                         aria-hidden="true"
                         aria-labelledby="exampleModalTitle"
                         role="dialog"
                         tabindex="-1">
                        <div class="modal-dialog  modal-dialog-centered  modal-simple">
                            <div class="modal-content p-10 s-border-radius">
                                <div class="modal-header text-center">
                                    <a class="pointer close"
                                       role="button"
                                       data-dismiss="modal"
                                       aria-label="Close"
                                       id="bt-close-modal-change-shopify-integration">
                                        <i class="material-icons md-16">close</i>
                                    </a>
                                </div>
                                <div class="modal-body text-center p-20">
                                    <div class="d-flex justify-content-center">
                                        <span class="o-reload-1"></span>
                                    </div>
                                    <h3 class="black"
                                        id="modal-change-shopify-integration-title"> Você tem
                                        certeza? </h3>
                                    <p class="gray"
                                       id="modal-change-shopify-integration-text"></p>
                                </div>
                                <div class="modal-footer d-flex align-items-center justify-content-center">
                                    <button type="button"
                                            class="btn btn-gray"
                                            data-dismiss="modal"
                                            style="width: 20%;">
                                        Cancelar
                                    </button>
                                    <button id="bt-modal-change-shopify-integration"
                                            type="button"
                                            class="btn btn-success"
                                            style="width: 20%;">Confirmar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para integração com woocommerce -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
                         id="modal-woocom-integration-apikeys"
                         aria-hidden="true"
                         aria-labelledby="exampleModalTitle"
                         role="dialog"
                         tabindex="-1">
                        <div class="modal-dialog  modal-dialog-centered  modal-simple">

                            <div class="modal-content">
                                <div class="modal-header text-center">
                                    <a class="pointer close"
                                       role="button"
                                       data-dismiss="modal"
                                       aria-label="Close"
                                       id="bt-close-modal-change-shopify-integration">
                                        <i class="material-icons md-16">close</i>
                                    </a>
                                </div>

                                <div class="modal-body p-10 ">
                                    <div class='col-md-12 col-sm-12 col-12'>

                                        <h4>Atualizar chaves de acesso REST API</h4>
                                    </div>

                                    <div class='col-md-10 col-sm-12 col-12'>
                                        <label class="control-label"> Consumer key </label>
                                        <input class="form-control"
                                               id="consumer_k"
                                               type='text'
                                               class="form-control">

                                    </div>

                                    <div class='col-md-10 col-sm-12 col-12 mt-20'>
                                        <label class="control-label"> Consumer secret </label>
                                        <input class="form-control"
                                               id="consumer_s"
                                               type='text'
                                               class="form-control">

                                    </div>
                                </div>

                                <div class="modal-footer d-flex align-items-center justify-content-center">
                                    <button id="close-modal"
                                            type="button"
                                            class="btn btn-gray"
                                            data-dismiss="modal"
                                            style="width: 20%;">
                                        Cancelar
                                    </button>
                                    <button id="bt-modal-woocommerce-apikeys"
                                            type="button"
                                            class="btn btn-success"
                                            style="width: 20%;">Confirmar
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
                         id="modal-woocom-integration"
                         aria-hidden="true"
                         aria-labelledby="exampleModalTitle"
                         role="dialog"
                         tabindex="-1">
                        <div class="modal-dialog  modal-dialog-centered  modal-simple">

                            <div class="modal-content">
                                <div class="modal-header text-center">
                                    <a class="pointer close"
                                       role="button"
                                       data-dismiss="modal"
                                       aria-label="Close"
                                       id="bt-close-modal-change-shopify-integration">
                                        <i class="material-icons md-16">close</i>
                                    </a>
                                </div>

                                <div class="modal-body text-center p-10">
                                    <div class="d-flex justify-content-center ">
                                        <span class="o-reload-1"></span>
                                    </div>
                                </div>

                                <div id="_loading"
                                     style="display:none">
                                    <div class="modal-body text-center p-10">

                                        <h3 class="black"
                                            id="modal-title">
                                            Processando requisição.
                                        </h3>
                                    </div>

                                </div>

                                <div id="_content">

                                    <div class="modal-body text-center p-10">

                                        <h3 class="black"
                                            id="modal-title">Selecione as opções para sincronizar</h3>
                                        <p class="gray pt-10"
                                           id="modal-text">

                                        <div class="switch-holder">
                                            <label class="switch"
                                                   style="top:3px">
                                                <input type="checkbox"
                                                       id="opt_prod"
                                                       name="product_amount_selector"
                                                       class="check"
                                                       value="1">
                                                <span class="slider round"></span>
                                            </label>
                                            <label class="text-left"
                                                   for="opt_prod"
                                                   style="margin-right:15px;margin-bottom: 3px; width:346px">
                                                Sincronizar produtos com WooCommerce</label>
                                        </div>

                                        <div class="switch-holder"
                                             style="margin-top:4px">
                                            <label class="switch"
                                                   style="top:3px">
                                                <input type="checkbox"
                                                       id="opt_track"
                                                       name="product_amount_selector"
                                                       class="check"
                                                       value="1">
                                                <span class="slider round"></span>
                                            </label>
                                            <label class="text-left"
                                                   for="opt_track"
                                                   style="margin-right:15px;margin-bottom: 3px; width:346px">
                                                Sincronizar códigos de rastreio com WooCommerce</label>
                                        </div>

                                        <div class="switch-holder"
                                             style="margin-top:4px">
                                            <label class="switch"
                                                   style="top:3px">
                                                <input type="checkbox"
                                                       id="opt_webhooks"
                                                       name="product_amount_selector"
                                                       class="check"
                                                       value="1">
                                                <span class="slider round"></span>
                                            </label>
                                            <label class="text-left"
                                                   for="opt_webhooks"
                                                   style="margin-right:15px;margin-bottom: 3px; width:346px">
                                                Sincronizar webhooks com WooCommerce</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex align-items-center justify-content-center">
                                        <button id="close-modal"
                                                type="button"
                                                class="btn btn-gray"
                                                data-dismiss="modal"
                                                style="width: 20%;">
                                            Cancelar
                                        </button>
                                        <button id="bt-modal-sync-woocommerce"
                                                type="button"
                                                class="btn btn-success"
                                                style="width: 20%;">Confirmar
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
        <script src="{{ mix('build/layouts/projects/ckeditor.js') }}"></script>
        <script src="{{ mix('build/layouts/projects/project.min.js') }}"></script>
    @endpush
@endsection
