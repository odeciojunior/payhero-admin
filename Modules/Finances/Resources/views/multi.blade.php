@extends("layouts.master")

@push('css')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="{{ asset('modules/global/css/bootstrap-select-cloudfox-template.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=03') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/switch.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/new-finances.css?v='.uniqid()) }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/multi-finances.css?v='.uniqid()) }}">
@endpush

@section('content')
    <div class="page">
        <div class="page-header container px-25 px-md-30" style="padding-bottom: 0">
            <div class="row">
                <div class="col-12 col-md-7 order-0 p-0 px-md-15" style="margin-top: 30px">
                    <h1 class="page-title d-flex">
                        Finanças
                        <span class="badge badge-info new-badge d-inline-block d-md-none ml-10">NOVO!</span>
                    </h1>
                </div>
                <div class="col-12 col-md-5 d-flex d-fall align-items-end justify-content-end order-2 order-md-1 mt-20 p-0">
                    <div class="col-12 col-md-11 float-right px-0">
                        <div class="input-holder">
                            <select id="transfers_company_select" data-width="100%"> </select>
                        </div>
                    </div>
{{--                    <div class="col-sm-12 float-right d-none d-sm-flex justify-content-end">--}}
{{--                        <div class="d-flex justify-content-end align-items-center" style="font-size: 16px;color: #636363;">--}}
{{--                            <span style="margin-right: 9px" class="o-question-help-1"></span> Dúvidas sobre adquirentes?--}}
{{--                        </div>--}}
{{--                    </div>--}}
                </div>
                <div class="col-sm-12 order-1 order-md-2 p-0 px-md-15">
                    <p class="mt-10 mb-0">
                        <span class="badge badge-info new-badge d-none d-md-inline-block">NOVO!</span>
                        <span class="new-title">Uma nova central para você controlar seus extratos em diferentes adquirentes.</span>
                    </p>
                </div>
                <div class="col-12 col-md-6 order-3 p-0 px-md-15" id="container-available">
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
                <div class="col-sm-6 pointer default-hover order-3" style="display:none;" id="container-return">
                    <div class="d-flex align-items-center h-p100 default-hover">
                        <i class="o-arrow-right-1 back-button" style="margin-right: 8px"></i> Voltar
                    </div>
                </div>
                <div class="col-sm-6 d-none d-md-flex justify-content-end align-items-center order-4">
                    <a href="#" id="btn-config-all" class="btn btn-sm btn-default">
                        <svg width="23" height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11.5 7.5C9.01472 7.5 7 9.51472 7 12C7 14.4853 9.01472 16.5 11.5 16.5C12.8488 16.5 14.059 15.9066 14.8838 14.9666C15.5787 14.1745 16 13.1365 16 12C16 11.5401 15.931 11.0962 15.8028 10.6783C15.2382 8.838 13.5253 7.5 11.5 7.5ZM8.5 12C8.5 10.3431 9.84315 9 11.5 9C13.1569 9 14.5 10.3431 14.5 12C14.5 13.6569 13.1569 15 11.5 15C9.84315 15 8.5 13.6569 8.5 12ZM19.2093 20.3947L17.4818 19.6364C16.9876 19.4197 16.4071 19.4514 15.94 19.7219C15.4729 19.9923 15.175 20.4692 15.1157 21.0065L14.908 22.8855C14.8651 23.2729 14.584 23.5917 14.2055 23.6819C12.4263 24.106 10.5725 24.106 8.79326 23.6819C8.41476 23.5917 8.13363 23.2729 8.09081 22.8855L7.88343 21.0093C7.82251 20.473 7.5112 19.9976 7.04452 19.728C6.57783 19.4585 6.01117 19.4269 5.51859 19.6424L3.79071 20.4009C3.43281 20.558 3.01493 20.4718 2.74806 20.1858C1.50474 18.8536 0.57924 17.2561 0.0412152 15.5136C-0.074669 15.1383 0.0592244 14.7307 0.3749 14.4976L1.90219 13.3703C2.33721 13.05 2.59414 12.5415 2.59414 12.0006C2.59414 11.4597 2.33721 10.9512 1.90162 10.6305L0.375288 9.50507C0.0591436 9.27196 -0.0748729 8.86375 0.0414199 8.48812C0.580376 6.74728 1.50637 5.15157 2.74971 3.82108C3.01684 3.53522 3.43492 3.44935 3.79276 3.60685L5.51296 4.36398C6.00793 4.58162 6.57696 4.54875 7.04617 4.27409C7.51335 4.00258 7.82437 3.52521 7.88442 2.98787L8.09334 1.11011C8.13697 0.717971 8.42453 0.396974 8.80894 0.311314C9.69003 0.114979 10.5891 0.0106508 11.5131 0C12.4147 0.0104117 13.3128 0.114784 14.1928 0.311432C14.577 0.397275 14.8643 0.718169 14.9079 1.11011L15.117 2.98931C15.2116 3.85214 15.9387 4.50566 16.8055 4.50657C17.0385 4.50694 17.269 4.45832 17.4843 4.36288L19.2048 3.60562C19.5626 3.44812 19.9807 3.53399 20.2478 3.81984C21.4912 5.15034 22.4172 6.74605 22.9561 8.48689C23.0723 8.86227 22.9386 9.27022 22.6228 9.50341L21.0978 10.6297C20.6628 10.9499 20.4 11.4585 20.4 11.9994C20.4 12.5402 20.6628 13.0488 21.0988 13.3697L22.6251 14.4964C22.941 14.7296 23.0748 15.1376 22.9586 15.513C22.4198 17.2536 21.4944 18.8491 20.2517 20.1799C19.9849 20.4657 19.5671 20.5518 19.2093 20.3947ZM13.763 20.1965C13.9982 19.4684 14.4889 18.8288 15.1884 18.4238C16.0702 17.9132 17.1536 17.8546 18.0841 18.2626L19.4281 18.8526C20.291 17.8537 20.9593 16.7013 21.3981 15.4551L20.2095 14.5777L20.2086 14.577C19.398 13.9799 18.9 13.0276 18.9 11.9994C18.9 10.9718 19.3974 10.0195 20.2073 9.42265L20.2085 9.4217L21.3957 8.54496C20.9567 7.29874 20.2881 6.1463 19.4248 5.14764L18.0922 5.73419L18.0899 5.73521C17.6844 5.91457 17.2472 6.00716 16.8039 6.00657C15.1715 6.00447 13.8046 4.77425 13.6261 3.15459L13.6259 3.15285L13.4635 1.69298C12.8202 1.57322 12.1677 1.50866 11.513 1.50011C10.8389 1.50885 10.1821 1.57361 9.53771 1.69322L9.37514 3.15446C9.26248 4.16266 8.67931 5.05902 7.80191 5.5698C6.91937 6.08554 5.84453 6.14837 4.90869 5.73688L3.57273 5.14887C2.70949 6.14745 2.04092 7.29977 1.60196 8.54587L2.79181 9.42319C3.61115 10.0268 4.09414 10.9836 4.09414 12.0006C4.09414 13.0172 3.61142 13.9742 2.79237 14.5776L1.60161 15.4565C2.04002 16.7044 2.7085 17.8584 3.57205 18.8587L4.91742 18.2681C5.84745 17.8613 6.91573 17.9214 7.79471 18.4291C8.67398 18.9369 9.25934 19.8319 9.37384 20.84L9.37435 20.8445L9.53619 22.3087C10.8326 22.5638 12.1662 22.5638 13.4626 22.3087L13.6247 20.8417C13.6491 20.6217 13.6955 20.4054 13.763 20.1965Z" fill="#636363"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="row" id="container-gateways">
                <div class="col-md-12 col-xl-9 pr-45">
                    <div class="row" id="gateway-skeleton">
                        <div class="col-12 d-md-none">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-none d-md-block">
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
                        <div class="col-md-4 d-none d-md-block">
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
                        <div class="col-md-4 d-none d-md-block">
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
                <div class="col-md-12 col-xl-3 p-0">
                    <div class="container-history px-0" style="padding-top: 28px;">
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
                                            <div class="input-group-text">R$</div>
                                        </div>
                                        <input id="withdrawal_amount" name="withdrawal_amount" type="text" class="form-control" aria-label="Valor mínimo para saque" placeholder="Digite o valor">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
    <script src="{{ asset('modules/finances/js/jPages.min.js') }}"></script>
    <script src="{{ asset('modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.js?v='. uniqid()) }}"></script>
    <script src="{{ asset('modules/finances/js/multi-finances.js?v='. uniqid()) }}"></script>
    <script src="{{ asset('modules/finances/js/multi-finances-withdrawals.js?v='. uniqid()) }}"></script>
    <script src="{{ asset('modules/finances/js/settings.js?v='. uniqid()) }}"></script>
@endpush
