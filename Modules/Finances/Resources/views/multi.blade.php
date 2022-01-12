@extends("layouts.master")

@push('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.css?v=' . versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=' . versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/switch.css?v=' . versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/new-finances.css?v=' . versionsFile()) }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/multi-finances.css?v=?v=' . versionsFile()) }}">
@endpush

@section('content')
    <div class="page">
        <div class="page-header container" style="padding-bottom: 0">
            <div class="row">
                <div class="col-sm-7" style="margin-top: 30px">
                    <h1 class="page-title">Finanças</h1>
                </div>
                <div class="col-sm-5 d-flex d-fall align-items-end justify-content-end">
                    <div class="col-sm-11 float-right">
                        <div class="input-holder">
                            <select style='border-radius:10px' class="form-control select-pad text-right"
                                    id="transfers_company_select"> </select>
                        </div>
                    </div>
{{--                    <div class="col-sm-12 float-right d-none d-sm-flex justify-content-end">--}}
{{--                        <div class="d-flex justify-content-end align-items-center" style="font-size: 16px;color: #636363;">--}}
{{--                            <span style="margin-right: 9px" class="o-question-help-1"></span> Dúvidas sobre adquirentes?--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
                <div class="col-sm-12">
                    <p class="mt-10 mb-0">
                        <span class="badge badge-info new-badge">NOVO!</span>
                        <span class="new-title">Uma nova central para você controlar seus extratos em diferentes adquirentes.</span>
                    </p>
                </div>
                <div class="col-sm-6" id="container-available">
                    <p class="m-0 color-default">Disponível para saque</p>
                    <div id="val-skeleton"><div class="skeleton skeleton-text mb-0 my-5" style="width:207px !important;height: 40px !important;border-radius:20px !important"></div></div>
                    <div id="container_val" style="display:none;">
                        <div style="margin-right: 24px">
                            <span class="font-size-24 gray">R$</span>
                            <span class="font-size-32 bold total-available-balance" style="color: #636363;">0,00</span>
                        </div>
                        <img id="eye-slash" src="{{ asset('modules/global/img/logos/2021/svg/eye-slash.svg') }}" alt="" class="pointer">
                        <img id="eye-no-slash" src="{{ asset('modules/global/img/logos/2021/svg/eye-no-slash.svg') }}" alt="" class="pointer d-none">
                    </div>
                </div>
                <div class="col-sm-6 pointer default-hover" style="display:none;" id="container-return">
                    <div class="d-flex align-items-center h-p100 default-hover">
                        <i class="o-arrow-right-1 back-button" style="margin-right: 8px"></i> Voltar
                    </div>
                </div>
                <div class="col-sm-6 d-flex justify-content-end align-items-center">
                    <a href="#" id="btn-config-all" class="btn btn-sm btn-default" style="background-color: #f3f7f9 !important"><i class="o-gear-1"></i></a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="row" id="container-gateways">
                <div class="col-sm-8">
                    <div class="row" id="gateway-skeleton">
                        <div class="col-sm-4">
                            <div class="skeleton skeleton-text" style="margin-bottom: 15px !important;width:80%; height: 15px"></div>
                            <div class="card card-skeleton mb-0">
                                <div class="card-body p-0 pt-20">
                                    <div class="col-sm-12 p-0">
                                        <div class="px-20 py-0">
                                            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
                                        </div>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <div class="px-20">
                                            <div class="skeleton skeleton-button"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="skeleton skeleton-text" style="margin-bottom: 15px !important;width:80%; height: 15px"></div>
                            <div class="card card-skeleton mb-0">
                                <div class="card-body p-0 pt-20">
                                    <div class="col-sm-12 p-0">
                                        <div class="px-20 py-0">
                                            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
                                        </div>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <div class="px-20">
                                            <div class="skeleton skeleton-button"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="skeleton skeleton-text" style="margin-bottom: 15px !important;width:80%; height: 15px"></div>
                            <div class="card card-skeleton mb-0">
                                <div class="card-body p-0 pt-20">
                                    <div class="col-sm-12 p-0">
                                        <div class="px-20 py-0">
                                            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
                                        </div>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <hr>
                                        <div class="px-20 py-0">
                                            <div class="row align-items-center mx-0 py-10">
                                                <div class="skeleton skeleton-circle"></div>
                                                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
                                            </div>
                                            <div class="skeleton skeleton-text"></div>
                                        </div>
                                        <div class="px-20">
                                            <div class="skeleton skeleton-button"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="container-all-gateways"></div>
                </div>
                <div class="col-sm-4">
                    <div class="row" style="display: flex;justify-content: flex-end">
                        <div class="col-sm-10 container-history" style="padding-top: 28px;">
                            <div class="card card-skeleton mb-0" id="card-history">
                                <div class="col-12 px-20 pt-25 mb-20">
                                    <p class="m-0"><b>Histórico de saques</b></p>
                                </div>
                                <div class="list-linear-gradient-top" style="display: none"></div>
                                <div id="container-withdraw" style="display:none;height: auto;max-height: 360px;"></div>
                                <div class="list-linear-gradient-bottom"></div>
                                    <div class="row justify-content-between skeleton-withdrawal p-20 mx-0" id="skeleton-withdrawal">
                                        <div class="col-3 skeleton skeleton-gateway-logo" style="height: 30px"></div>
                                        <div class="col-5 text-right skeleton skeleton-text" style="height: 30px"></div>
                                        <div class="col-7 skeleton skeleton-text mb-0" style="height: 33px"></div>
                                    </div>
                                    <div class="row justify-content-between skeleton-withdrawal p-20 mx-0" id="skeleton-withdrawal2">
                                        <div class="col-3 skeleton skeleton-gateway-logo" style="height: 30px"></div>
                                        <div class="col-5 text-right skeleton skeleton-text" style="height: 30px"></div>
                                        <div class="col-7 skeleton skeleton-text mb-0" style="height: 33px"></div>
                                    </div>
                                    <div class="row justify-content-between skeleton-withdrawal p-20 mx-0" id="skeleton-withdrawal3">
                                        <div class="col-3 skeleton skeleton-gateway-logo" style="height: 30px"></div>
                                        <div class="col-5 text-right skeleton skeleton-text" style="height: 30px"></div>
                                        <div class="col-7 skeleton skeleton-text mb-0" style="height: 33px"></div>
                                    </div>
                                @include('finances::components.empty-history')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:none" id="container-config">
                <div style="font-weight: bold; font-size: 24px; line-height: 30px; color: #636363;">Automatize seus saques</div>
                <div style="font-size: 16px; line-height: 20px; color: #70707E;">Crie regras para realizar saques automaticamente, sem precisar se preocupar!</div>
                <form id="finances-settings-form">
                    <div class="row">
                        <div class="col-md-6 mb-50">
                            <div class="card no-shadow mt-30" style="min-height: unset">
                                <div class="card-body">
                                    <h5 class="title-pad">
                                        Saque automático por período
                                        <label class="switch" style="float: right; top:3px">
                                            <input type="checkbox" id="withdrawal_by_period" name="withdrawal_by_period" class="check">
                                            <span class="slider round"></span>
                                        </label>
                                    </h5>
                                    <p class="p-0 m-0">
                                        Crie um saque automático de frequência diária, semanal ou
                                        mensal.
                                        <br>
                                        O valor será automaticamente solicitado quando superior a R$
                                        100,00.
                                    </p>
                                    <br>
                                    <p class="mb-0">Frequência</p>
                                    <div class="frequency-container py-10 d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center">
                                        <button type="button" data-frequency="daily" class="btn btn-block m-0 mr-5 py-10 disabled" disabled="">
                                            Diário
                                        </button>

                                        <button type="button" data-frequency="weekly" class="btn btn-block m-0 mx-5 py-10 disabled" disabled="">
                                            Semanal
                                        </button>

                                        <button type="button" data-frequency="monthly" class="btn btn-block m-0 ml-5 py-10 disabled" disabled="">
                                            Mensal
                                        </button>
                                    </div>

                                    <div class="weekdays-container d-flex flex-wrap flex-md-nowrap align-items-center justify-content-between mt-20">
                                        <button type="button" class="btn py-15 disabled" data-weekday="1" disabled="">
                                            SEG
                                        </button>
                                        <button type="button" class="btn py-15 disabled" data-weekday="2" disabled="">
                                            TER
                                        </button>
                                        <button type="button" class="btn py-15 disabled" data-weekday="3" disabled="">
                                            QUA
                                        </button>
                                        <button type="button" class="btn py-15 disabled" data-weekday="4" disabled="">
                                            QUI
                                        </button>
                                        <button type="button" class="btn py-15 disabled" data-weekday="5" disabled="">
                                            SEX
                                        </button>
                                        <button type="button" class="btn py-15 disabled" data-weekday="6" disabled="">
                                            SAB
                                        </button>
                                        <button type="button" class="btn py-15 disabled" data-weekday="0" disabled="">
                                            DOM
                                        </button>
                                    </div>
                                    <div class="day-container d-none flex-wrap flex-md-nowrap align-items-center justify-content-between mt-20">
                                        <button type="button" class="btn py-15 disabled" data-day="01" disabled="">
                                            01
                                        </button>

                                        <button type="button" class="btn py-15 disabled" data-day="05" disabled="">
                                            05
                                        </button>

                                        <button type="button" class="btn py-15 disabled" data-day="10" disabled="">
                                            10
                                        </button>

                                        <button type="button" class="btn py-15 disabled" data-day="15" disabled="">
                                            15
                                        </button>

                                        <button type="button" class="btn py-15 disabled" data-day="20" disabled="">
                                            20
                                        </button>

                                        <button type="button" class="btn py-15 disabled" data-day="25" disabled="">
                                            25
                                        </button>

                                        <button type="button" class="btn py-15 disabled" data-day="30" disabled="">
                                            30
                                        </button>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <button type="submit" class="btn btn-block btn-success-1 py-10 px-15 disabled" disabled="">
                                                <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }}">
                                                &nbsp;Salvar&nbsp;
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card no-shadow mt-30" style="min-height: unset">
                                <div class="card-body">
                                    <h5 class="title-pad">
                                        Saque automático por valor
                                        <label class="switch" style="float: right; top:3px">
                                            <input type="checkbox" id="withdrawal_by_value" name="withdrawal_by_value" class="check">
                                            <span class="slider round"></span>
                                        </label>
                                    </h5>
                                    <p class="p-0 m-0">
                                        Crie um saque automático quando o saldo disponível for
                                        superior ao valor informado abaixo.
                                        <br>O valor deve ser superior a R$ 100,00.
                                    </p>
                                    <br>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">R$</span>
                                        </div>
                                        <input id="withdrawal_amount" name="withdrawal_amount" type="text" class="form-control" aria-label="Valor mínimo para saque">
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <button type="submit" class="btn btn-block py-10 px-15 btn-success">
                                                <img style="height: 12px; margin-right: 4px" src=" {{asset('/modules/global/img/svg/check-all.svg')}} ">
                                                &nbsp;Salvar&nbsp;
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@include('finances::components.new-withdrawal-modal')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('modules/global/js-extra/moment.min.js?v=' . versionsFile()) }}"></script>
    <script src="{{ asset('modules/global/js/daterangepicker.min.js?v=' . versionsFile()) }}"></script>
    <script src="{{ asset('modules/finances/js/jPages.min.js?v=' . versionsFile()) }}"></script>
    {{-- <script src="{{ asset('modules/finances/js/statement-index.js?v=' . versionsFile()) }}"></script> --}}
    {{-- <script src="{{ asset('modules/finances/js/balances.js?v=' . versionsFile()) }}"></script> --}}
    {{-- <script src="{{ asset('modules/finances/js/withdrawals.js?v=' . versionsFile()) }}"></script> --}}
    <script src="{{ asset('modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.js?v=' . versionsFile()) }}"></script>
    <script src="{{ asset('modules/finances/js/multi-finances.js?v=' . versionsFile()) }}"></script>
    <script src="{{ asset('modules/finances/js/multi-finances-withdrawals.js?v=' . versionsFile()) }}"></script>
    <script src="{{ asset('modules/finances/js/settings.js?v=' . versionsFile()) }}"></script>
@endpush
