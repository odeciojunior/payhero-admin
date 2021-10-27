@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=03') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/switch.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.css?'.uniqid()) }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/new-finances.css?'.uniqid()) }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/multi-finances.css?'.uniqid()) }}">
@endpush

@section('content')
    <div class="page">
        <div class="page-header container">
            <div class="row" style="margin-top: 50px">
                <div class="col-sm-6">
                    <h1 class="page-title">Finanças</h1>
                    <p class="mt-10"><span class="badge badge-info">NOVO!</span> Uma nova central para você controlar seus extratos em diferentes adquirentes.</p>
                </div>
                <div class="col-sm-6 text-right">
                    <div class="col-sm-6 float-right">
                        <div class="input-holder">
                            <label for="transfers_company_select"> Empresa</label>
                            <select style='border-radius:10px' class="form-control select-pad"
                                    name="company"
                                    id="transfers_company_select"> </select>
                        </div>
                    </div>
                    <div class="col-sm-12 float-right"><span class="gray"><i class="fa fa-info-circle"></i> Dúvidas sobre adquirentes</span></div>
                </div>
                <div class="col-sm-6" id="container-disponivel">
                    <p>Dísponivel para Saque</p>
                    <div id="val-skeleton"><div class="skeleton skeleton-text" style="width:50% !important"></div></div>
                    <div id="container_val" style="display: none">
                        <span class="font-size-16 gray">R$</span> <span class="font-size-32 bold total-available-balance" style="color: #636363;">0,00</span>
                        <span id="hide-withdraw"></span>
                        <i style="margin-left:24px;cursor:pointer" class="fa fa-eye-slash font-size-24"></i>
                    </div>
                </div>
                <div class="col-sm-6" style="display:none;cursor: pointer" id="container-return">
                    <i class="fa fa-arrow-left"></i> Retornar para tela inicial
                </div>
                <div class="col-sm-6 text-right">
                    <a href="#" id="btn-config-all" class="btn btn-sm btn-default btn-outline" style="background-color: #f3f7f9 !important"><i class="fa fa-cog"></i></a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="row" id="container-gateways">
                <div class="col-sm-8">
                    <div class="row" id="gateway-skeleton" style="padding-top:30px;">
                        <div class="col-sm-4">
                            <div class="skeleton skeleton-text" style="margin-bottom: 10px !important"></div>
                            <div class="card card-skeleton">
                                <div class="card-body">
                                    <div class="col-sm-12 p-0 pt-5">
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="skeleton skeleton-text" style="margin-bottom: 10px !important"></div>
                            <div class="card card-skeleton">
                                <div class="card-body">
                                    <div class="col-sm-12 p-0">
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="skeleton skeleton-text" style="margin-bottom: 10px !important"></div>
                            <div class="card card-skeleton">
                                <div class="card-body">
                                    <div class="col-sm-12 p-0">
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding-top:30px;" id="container-all-gateways"></div>
                </div>
                <div class="col-sm-4">
                    <div class="row" style="display: flex;justify-content: flex-end">
                        <div class="col-sm-10" style="padding-top: 60px;">
                            <div class="card card-skeleton" id="card-history">
                                <div class="card-body">
                                    <div class="list-linear-gradient-top"></div>
                                    <div class="col-12 p-0 mb-35">
                                        <p><b>Histórico de saques</b></p>
                                    </div>
                                    <div id="container-withdraw" style="display:none"></div>
                                    <div class="row" id="skeleton-withdrawal">
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                    </div>
                                    @include('finances::components.empty-history')
                                    <div class="list-linear-gradient-bottom"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:none" id="container-config">
                <form id="finances-settings-form">
                    <div class="row">
                        <div class="col-md-6 mb-50">
                            <div class="card no-shadow mt-30">
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
                            <div class="card no-shadow mt-30">
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
@push('scripts')
    <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
    <script src="{{ asset('modules/finances/js/jPages.min.js') }}"></script>
    {{-- <script src="{{ asset('modules/finances/js/statement-index.js?v='. uniqid()) }}"></script> --}}
    {{-- <script src="{{ asset('modules/finances/js/balances.js?v='. uniqid()) }}"></script> --}}
    {{-- <script src="{{ asset('modules/finances/js/withdrawals.js?v='. uniqid()) }}"></script> --}}
    <script src="{{ asset('modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.js?v='. uniqid()) }}"></script>
    <script src="{{ asset('modules/finances/js/multi-finances.js?v='. uniqid()) }}"></script>
    <script src="{{ asset('modules/finances/js/settings.js?v='. uniqid()) }}"></script>
@endpush
