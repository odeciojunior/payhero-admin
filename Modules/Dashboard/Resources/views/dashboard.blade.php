@extends('layouts.master')
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/dashboard/stylesheets.min.css') }}">
    @endpush

    <div class="page dashboard">

        @include('layouts.company-select', ['version' => 'mobile'])

        @include('dashboard::achievement-details')
        @include('dashboard::onboarding.presentation')

        <div class="page-header container mb-15 mb-sm-0">

            <div class="row align-items-center justify-content-between"
                 style="min-height:50px">
                <div class="col-lg-6 mb-25">
                    <h1 class="page-title">Dashboard</h1>
                </div>
            </div>

        </div>
        <div id="project-not-empty"
             class="page-content container">
            <!-- Saldos -->
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-8">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="card bg-white stats-card balances-card">
                                <div class="skeleton-loading loading-title d-none"></div>
                                <div class="skeleton-loading loading-content d-none"></div>
                                <div class="balance-card-data">
                                    <div
                                         class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                        <div class="font-size-14 gray-600 ">
                                            <span class="card-desc">Vendas aprovadas hoje</span>
                                        </div>
                                    </div>
                                    <div class="card-body font-size-24 d-flex align-items-topline">
                                        <div class="card-text d-flex align-items-center">
                                            <span class="moeda">R$</span>
                                            <span id="today_money"
                                                  class="text-money"></span>
                                        </div>
                                    </div>
                                    <div class="s-border-right purple"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="card bg-white stats-card balances-card">
                                <div class="skeleton-loading loading-title d-none"></div>
                                <div class="skeleton-loading loading-content d-none"></div>
                                <div class="balance-card-data">
                                    <div
                                         class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                        <div class="font-size-14 gray-600">
                                            <span class="card-desc">Pendente</span>
                                        </div>
                                    </div>
                                    <div class="card-body font-size-24 d-flex align-items-topline">
                                        <div class="card-text d-flex align-items-center">
                                            <span class="moeda">R$</span>
                                            <span id="pending_money"
                                                  class="text-money"></span>
                                        </div>
                                    </div>
                                    <div class="s-border-right yellow"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="card bg-white stats-card balances-card">
                                <div class="skeleton-loading loading-title d-none"></div>
                                <div class="skeleton-loading loading-content d-none"></div>
                                <div class="balance-card-data">
                                    <div
                                         class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                        <div class="font-size-14 gray-600">
                                            <span class="card-desc"
                                                  id="title_available_money"></span>
                                        </div>
                                    </div>
                                    <div class="card-body font-size-24 d-flex align-items-topline">
                                        <div class="card-text d-flex align-items-center">
                                            <span class="moeda">R$</span>
                                            <span id="available_money"
                                                  class="text-money"></span>
                                        </div>
                                    </div>
                                    <div class="s-border-right green"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="card bg-white stats-card balances-card">
                                <div class="skeleton-loading loading-title d-none"></div>
                                <div class="skeleton-loading loading-content d-none"></div>
                                <div class="balance-card-data">
                                    <div
                                         class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                        <div class="font-size-14 gray-600 mr-auto">
                                            <span class="card-desc">Total</span>
                                        </div>
                                        <i class="o-question-help-1"
                                           id="info-total-balance"></i>
                                    </div>
                                    <div class="card-body font-size-24 d-flex align-items-topline">
                                        <div class="card-text d-flex align-items-center">
                                            <span class="moeda">R$</span>
                                            <span id="total_money"
                                                  class="text-money"></span>
                                        </div>
                                    </div>
                                    <div class="s-border-right blue"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-none d-sm-block">
                            <div class="card bg-white chart-card">
                                <div class="skeleton-loading loading-title d-none"></div>
                                <div class="skeleton-loading loading-content d-none"></div>
                                <div class="skeleton-loading loading-content-inside d-none"></div>
                                <div class="skeleton-loading loading-content d-none"></div>

                                <div class="chart-data">
                                    <div
                                         class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                        <div class="font-size-14 gray-600">
                                            <span class="card-desc">Vendas nos últimos 30 dias</span>
                                        </div>
                                    </div>
                                    <div class="card-body my-30 d-flex flex-column justify-content-center align-items-center p-5"
                                         style="height: 270px">
                                        <div id="scoreLineToMonth"
                                             class="ct-chart"></div>
                                        <div id="empty-sale"
                                             class="row"
                                             style="display: none;">
                                            <div class="col-sm-8">
                                                <img src="{!! mix('build/global/img/sem-dados.svg') !!}"
                                                     alt="">
                                            </div>
                                            <p style="font-size: 23px"
                                               class="col-sm-4 gray justify-content-center align-items-center d-flex flex-column p-5">
                                                Nenhuma venda encontrada</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-4">
                    <div class="row">

                        <div class="col-12  d-flex align-items-stretch font-size-12 order-0 order-sm-0 sirius-performance">

                            <div class="card pb-15 bg-white w-full performance-card">
                                <div class="performance-loading d-none">
                                    <div class="skeleton-loading title"></div>
                                    <div class="card-body pb-5 pt-0 mt-15 d-flex flex-column justify-content-start ">
                                        <div class="level-icon-container row">
                                            <div class="skeleton-loading col level-image"></div>
                                            <div class="col">
                                                <div class="skeleton-loading level-title"></div>
                                                <div class="skeleton-loading level-subtitle"></div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                            <div class="skeleton-loading col-2 achievements"></div>
                                        </div>
                                        <div class="skeleton-loading invoicing"></div>
                                    </div>
                                </div>
                                <div class="performance-data">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex align-items-stretch font-size-12 order-1 order-sm-1 sirius-cashback">
                            <div class="card pb-15 bg-white w-full d-none">
                            </div>
                        </div>

                        <div
                             class="col-12 mb-10 d-flex align-items-stretch font-size-12 order-2 order-sm-2 sirius-account">
                            <div class="card bg-white w-full sirius-account-health">
                                <div class="sirius-account-health-loading d-none">
                                    <div class="skeleton-loading title"></div>
                                    <div class="row">
                                        <div class="col skeleton-loading gauge"></div>
                                        <div class="col">
                                            <div class="skeleton-loading score-title"></div>
                                            <div class="skeleton-loading score-description"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col skeleton-loading score-detail"></div>
                                        <div class="col skeleton-loading score-detail"></div>
                                        <div class="col skeleton-loading score-detail"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('projects::empty')
    </div>

    <!-- Modal User Term v2 -->
    <div class="modal fade modal-fade-in-scale-up"
         id="termsModal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="myLargeModalLabel"
         aria-hidden="true"
         data-keyboard="false"
         data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-simple"
             role="document">
            <div class="modal-content modal-terms-cloudfox">
                <div class="modal-header p-0 pt-15 pl-15 pb-15">
                    <span class="modal-title p-0"
                          id="exampleModalLabel">
                        <strong>Termos de uso Cloudfox atualizados</strong>
                    </span>
                </div>
                <hr style="margin-top: 0%; color: #F4F6FB; height: 1px">
                <div class="modal-body p-0">

                    <div class="row form-group col-md-12 pl-15 pr-15 m-0 pb-20 text-modal">
                        Atualizamos recentemente os Termos de Uso da nossa plataforma e produtos. Para continuar
                        utilizando os serviços Cloudfox é necessario ler e estar de acordo com o termo.
                    </div>

                    <div class="modal-terms">
                        <a href="//cloudfox.net/use-terms-and-privacy-policy.html"
                           target="_blank"
                           class="button-modal-term">
                            <span>
                                <span class="icon-modal-term"></span>
                                <span class="text-modal-term">Veja o documento aqui</span>
                            </span>
                            <span class="icon-vetctor-modal-term"></span>
                        </a>
                    </div>
                    <br>
                    <span class="pl-15">
                        <input id="terms-cloudfox"
                               type="checkbox"><span class="pl-2">Li e estou de acordo.</span>
                    </span>

                </div>
                <div class="modal-footer border-top mt-20 mb-20 pt-15 justify-content-center"
                     id="accept-term">
                    <button type="button"
                            class="btn btn-primary button-accept">Aceitar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script async
                src="https://cdn.announcekit.app/widget-v2.js"></script>
        <script src="{{ mix('build/layouts/dashboard/scripts.min.js') }}"></script>
    @endpush

@endsection
