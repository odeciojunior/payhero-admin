@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=03') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/switch.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.css?v=14'.uniqid()) }}"><link rel="stylesheet" href="{{ asset('modules/finances/css/new-finances.css?v=21'.uniqid()) }}">
    <style>
        .popover {
            left: -50px !important;
        }

        .disableFields {
            background-color: #f3f7f9;
            opacity: 1;
        }
        /* .label-new{
            display: flex;
            flex-direction: row;
            align-items: flex-start;
            padding: 8px 16px;
            width: 80px;
            height: 30px;
            background: #2E85EC;
            border-radius: 36px;
        } */
        .badge-info{
            background: #2E85EC;
            border-radius: 36px;
            font-weight:900;
        }
        .btn-saque{
            background: #1BE4A8;
            border-radius: 8px;
            color: #f3f7f9;
            height: 48px;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            padding: 12px 32px;
        }
        .btn-saque:hover{
            color: #f3f7f9;
        }
        .circulo{
            border-radius: 50%;
            display: inline-block;
            height: 10px;
            width: 10px;
            margin-right: 8px;
        }
        .circulo-green{
            background: #1BE4A8;
        }
        .circulo-orange{
            background: #FF7A00;
        }
        .circulo-red{
            background: #FF003D;
        }
        .circulo-blue{
            background: #2E85EC;
        }
        .fa-eye-slash{
            color: #2E85EC
        }
        .font-size-32{
            font-size: 32px;
        }
        .badge-orange{
            background-color: #FF7A00
        }
        .owl-carousel .owl-nav .owl-next{
            right: -35px !important;
        }
        .owl-carousel .owl-nav .owl-prev{
            left: -35px !important;
        }
        /* @media only screen and (min-width: 768px){
            .item > .card {
                margin-right: 10px;
                /* max-width: calc(33.3% - 5px); */
            }
        } */
        .owl-carousel .nav-btn{
            height: 47px;
            position: absolute;
            width: 26px;
            cursor: pointer;
            top: 100px !important;
        }

        .owl-carousel .owl-prev.disabled,
        .owl-carousel .owl-next.disabled{
        pointer-events: none;
        opacity: 0.2;
        }

        .owl-carousel .prev-slide{
        background: url(nav-icon.png) no-repeat scroll 0 0;
        left: -33px;
        }
        .owl-carousel .next-slide{
        background: url(nav-icon.png) no-repeat scroll -24px 0px;
        right: -33px;
        }
        .owl-carousel .prev-slide:hover{
        background-position: 0px -53px;
        }
        .owl-carousel .next-slide:hover{
        background-position: -24px -53px;
        }
        #hide-withdraw{
            width: 150px;
            height: 29px;
            display: none;
            background: linear-gradient(90deg, #FFFFFF 10%, rgba(244, 244, 244, 0) 182.5%);
            border-radius: 60px;
        }
        .flex-center{
            display: flex;
            justify-items: center;
        }
        /* configuração do skeleton */
        .card-skeleton{
            min-height: 350px;
        }
        .skeleton{
            opacity: .7;
            animation: skeleton-loading 1s linear infinite alternate;
        }
        .skeleton-text{
            width: 100%;
            height: 20px;
            margin-bottom: 35px;
            border-radius: 10px;
        }
        .skeleton-text:last-child{
            margin-bottom: 0;
            width: 80%;
        }
        @keyframes skeleton-loading {
            0%{
                background-color: hsl(200,20%,70%);
            }
            100%{
                background-color: hsl(200,20%,95%);
            }
        }
    </style>
@endpush

@section('content')
    <div class="page">
        <div class="page-header container">
            <div class="row" style="margin-top: 50px">
                <div class="col-sm-6 mb-30">
                    <h1 class="page-title">Finanças</h1>
                    <p class="mt-10"><span class="badge badge-info">NOVO!</span> Uma nova central para você controlar seus extratos em diferentes adquirentes.</p>
                </div>
                <div class="col-sm-6 mb-30 text-right">
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
                <div class="col-sm-6 mb-35" id="container-disponivel">
                    <p>Dísponivel para Saque</p>
                    <div id="val-skeleton"><div class="skeleton skeleton-text" style="width:50% !important"></div></div>
                    <div id="container_val" style="display: none">
                        <span class="font-size-16 gray">R$</span> <span class="font-size-32 bold total-available-balance" style="color: #636363;">0,00</span> 
                        <span id="hide-withdraw"></span>
                        <i style="margin-left:24px;cursor:pointer" class="fa fa-eye-slash font-size-24"></i>
                    </div>
                </div>
                <div class="col-sm-6 mb-35" style="display:none;cursor: pointer" id="container-return">
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
                            <div class="card card-skeleton">
                                <div class="card-body">
                                    <div class="col-12 p-0 mb-35">
                                        <p><b>Histórico de saques</b></p>
                                    </div>
                                    <div id="container-withdraw" style="display:none"></div>
                                    <div class="row" id="skeleton-withdrawal">
                                        <div class="skeleton skeleton-text"></div>
                                        <div class="skeleton skeleton-text"></div>
                                    </div>
                                    {{-- <div class="row mt-10">
                                        <div class="col-sm-6">
                                            <svg width="85" height="19" viewBox="0 0 85 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M23.5442 15.9692H27.3133C28.1584 15.9692 28.8852 15.3524 29.0542 14.5189L30.4063 7.13433C30.5754 6.18417 29.95 5.30069 29.0035 5.13399C28.8852 5.13399 28.7838 5.08398 28.7162 5.08398H26.8569C26.0119 5.08398 25.2851 5.70075 25.1161 6.53423L24.3386 10.8516C24.1696 11.8018 24.7949 12.6853 25.7414 12.852C25.8597 12.852 25.9612 12.902 26.0795 12.902H27.8204L27.5499 14.5189C27.4992 14.6856 27.3809 14.8023 27.2119 14.8023H23.8315L23.5442 15.9692ZM28.9528 6.68425L27.9894 11.6851H26.1302C25.9612 11.6851 25.7921 11.5184 25.7921 11.3517V11.3017L26.6879 6.58424C26.7386 6.41754 26.8569 6.30086 27.026 6.30086H28.5978C28.7838 6.28419 28.9528 6.45088 28.9528 6.68425Z" fill="#414042"/>
                                                <path d="M53.0719 5.68415C52.7339 5.30076 52.2268 5.06738 51.7198 5.06738H49.8605C49.0155 5.06738 48.2887 5.68415 48.1197 6.51763L46.9365 12.902H48.5084L49.6915 6.61764C49.7422 6.45095 49.8605 6.33426 50.0296 6.33426H51.6014C51.7705 6.33426 51.9395 6.50096 51.9395 6.66765V6.71766L50.8071 12.8854H52.3789L53.4437 7.16774C53.5283 6.61765 53.4099 6.06755 53.0719 5.68415Z" fill="#414042"/>
                                                <path d="M39.1616 5.06738C38.3165 5.06738 37.5897 5.68415 37.4207 6.51763L36.2375 12.902H37.8094L38.9925 6.61764C39.0433 6.45095 39.1616 6.33426 39.3306 6.33426H41.4095L41.6292 5.11739L39.1616 5.06738Z" fill="#414042"/>
                                                <path d="M36.1699 5.68415C35.8319 5.30076 35.3249 5.06738 34.8178 5.06738H32.9586C32.1135 5.06738 31.3867 5.68415 31.2177 6.51763L30.4233 10.8517C30.2543 11.8019 30.8797 12.6853 31.8262 12.852C31.9445 12.852 32.0459 12.902 32.1642 12.902H35.2573L35.4263 11.7352H32.2149C32.0459 11.7352 31.8769 11.5685 31.8769 11.4018V11.3518L32.0459 10.4683H34.4122C35.2573 10.4683 35.984 9.85152 36.153 9.01805L36.4911 7.18441C36.677 6.61764 36.508 6.06755 36.1699 5.68415ZM35.0375 6.68432L34.6488 8.85136C34.5981 9.01805 34.4798 9.13474 34.3107 9.13474H32.3332L32.7896 6.51763C32.8403 6.35093 32.9586 6.23425 33.1276 6.23425H34.6995C34.8685 6.23425 35.0375 6.40094 35.0375 6.56764C35.0375 6.61764 35.0375 6.68432 35.0375 6.68432Z" fill="#414042"/>
                                                <path d="M46.8689 5.68415C46.5309 5.30076 46.0238 5.06738 45.5168 5.06738H43.6576C42.8125 5.06738 42.0857 5.68415 41.9167 6.51763L41.1223 10.8517C40.9533 11.8019 41.5786 12.6853 42.5252 12.852C42.6435 12.852 42.7449 12.902 42.8632 12.902H45.9731L46.1928 11.7352H42.9815C42.8125 11.7352 42.6435 11.5685 42.6435 11.4018V11.3518L42.8125 10.4683H45.1787C46.0238 10.4683 46.7506 9.85152 46.9196 9.01805L47.2577 7.18441C47.376 6.61764 47.207 6.06755 46.8689 5.68415ZM45.7534 6.68432L45.3647 8.85136C45.3139 9.01805 45.1956 9.13474 45.0266 9.13474H43.0491L43.5055 6.51763C43.5562 6.35093 43.6745 6.23425 43.8435 6.23425H45.4154C45.5844 6.23425 45.7534 6.40094 45.7534 6.56764C45.7534 6.61764 45.7534 6.68432 45.7534 6.68432Z" fill="#414042"/>
                                                <path d="M66.9821 5.68415C66.6441 5.30076 66.1371 5.06738 65.63 5.06738H63.0947L62.875 6.23425H65.5286C65.6976 6.23425 65.8666 6.40094 65.8666 6.56764V6.61764L65.6976 7.50113H63.3314C62.4863 7.50113 61.7595 8.1179 61.5905 8.95137L61.2524 10.785C61.0834 11.7352 61.7088 12.6187 62.6553 12.7854C62.7736 12.7854 62.875 12.8354 62.9933 12.8354H64.8525C65.6976 12.8354 66.4244 12.2186 66.5934 11.3851L67.3878 7.05105C67.4892 6.61764 67.3202 6.06755 66.9821 5.68415ZM64.6835 11.6852H63.1116C62.9426 11.6852 62.7736 11.5185 62.7736 11.3518V11.3018L63.1623 9.13474C63.213 8.96804 63.3314 8.85136 63.5004 8.85136H65.4779L64.9708 11.4685C64.9539 11.5185 64.8525 11.6352 64.6835 11.6852Z" fill="#414042"/>
                                                <path d="M55.8945 6.56784C55.9452 6.40115 56.1142 6.23445 56.2832 6.23445H58.7002L58.9199 5.01758H56.1142C55.2691 5.01758 54.4747 5.63435 54.3057 6.46782L53.5282 10.6852C53.3592 11.6354 53.9846 12.5689 54.9987 12.7356C55.117 12.7356 55.2184 12.7856 55.3367 12.7856H57.4663L57.6861 11.6187H55.3705C55.1508 11.6187 54.9818 11.452 54.9818 11.2353V11.1853L55.8945 6.56784Z" fill="#414042"/>
                                                <path d="M59.7142 5.73438L58.4297 12.8022H60.0016L61.303 5.73438H59.7142Z" fill="#414042"/>
                                                <path d="M60.0523 3.85059L59.8833 5.06746H61.4721L61.6411 3.85059H60.0523Z" fill="#414042"/>
                                                <path d="M73.3033 5.68415C72.9653 5.30076 72.4583 5.06738 71.9512 5.06738H70.092C69.2469 5.06738 68.5201 5.68415 68.3511 6.51763L67.168 12.902H68.7398L69.923 6.61764C69.9737 6.45095 70.092 6.33426 70.261 6.33426H71.8329C72.0019 6.33426 72.1709 6.50096 72.1709 6.66765V6.71766L71.0385 12.8854H72.6104L73.6921 7.18441C73.7428 6.61764 73.6414 6.06755 73.3033 5.68415Z" fill="#414042"/>
                                                <path d="M79.4893 5.68415C79.1513 5.30076 78.6442 5.06738 78.1371 5.06738H76.2779C75.4328 5.06738 74.7061 5.68415 74.537 6.51763L73.7427 10.8517C73.5736 11.8019 74.199 12.6853 75.1455 12.852C75.2638 12.852 75.3652 12.902 75.4836 12.902H78.5935L78.8132 11.7352H75.6019C75.4328 11.7352 75.2638 11.5685 75.2638 11.4018V11.3518L75.4328 10.4683H77.7991C78.6442 10.4683 79.371 9.85152 79.54 9.01805L79.878 7.18441C79.9456 6.61764 79.8273 6.06755 79.4893 5.68415ZM78.3738 6.68432L77.985 8.85136C77.9343 9.01805 77.816 9.13474 77.647 9.13474H75.6695L76.1765 6.51763C76.2272 6.35093 76.3455 6.23425 76.5146 6.23425H78.0864C78.2555 6.23425 78.4245 6.40094 78.4245 6.56764C78.3738 6.61764 78.3738 6.68432 78.3738 6.68432Z" fill="#414042"/>
                                                <path d="M82.2614 6.56792C82.3121 6.40122 82.4811 6.23453 82.6502 6.23453H84.2896L84.5094 5.01765H82.5318L82.7516 3.80078H81.1797L80.1149 9.56842L79.8952 10.6853C79.7261 11.6354 80.3515 12.5689 81.3656 12.7356C81.4839 12.7356 81.5853 12.7856 81.7036 12.7856H83.5629L83.7826 11.6188H81.8051C81.5853 11.6188 81.4163 11.4521 81.4163 11.2354V11.1854L82.2614 6.56792Z" fill="#414042"/>
                                                <path d="M9.92146 7.90134L19.7752 5.28423L20.7386 0L10.3102 4.45075C8.83974 5.06752 7.31858 5.40091 5.7467 5.40091H4.44526L3.21143 8.3014H6.87913C7.94394 8.3014 8.90735 8.18472 9.92146 7.90134Z" fill="#414042"/>
                                                <path d="M1.63965 12.0688H8.85674L18.4401 12.5689L19.4035 7.40137L9.51591 9.13499C8.83984 9.25168 8.16376 9.30169 7.48769 9.30169H2.82278L1.63965 12.0688Z" fill="#414042"/>
                                                <path d="M7.48752 13.019H1.23383L0 15.9029H6.59172C8.33261 15.9029 10.0904 16.1862 11.713 16.6863L17.3413 18.4033L18.0174 14.686L10.3101 13.3524C9.41433 13.0691 8.45093 13.019 7.48752 13.019Z" fill="#414042"/>
                                            </svg>
                                        </div>
                                        <div class="col-sm-6 text-right">
                                            Itaú Unibanco
                                        </div>
                                        <div class="col-sm-6" style="margin-top:10px"><h4 style="margin-top:3px"><span class="font-size-16 gray">R$</span> <span class="font-size-18 bold">0,00</span></h4></div>
                                        <div class="col-sm-6" style="margin-top:10px"><span class="label label-warning float-right"><span class="badge badge-round badge-success">Liquidado</span></span></div>
                                    </div> --}}
                                    {{-- <a href="#" class="col-12 btn-saque">Realizar Saque</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="display:none" id="container-config">
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
                                            <img style="height: 12px; margin-right: 4px" src=" http://dev.admin.com/modules/global/img/svg/check-all.svg ">
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
                                            <img style="height: 12px; margin-right: 4px" src=" http://dev.admin.com/modules/global/img/svg/check-all.svg ">
                                            &nbsp;Salvar&nbsp;
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
@endsection
@push('scripts')
    <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
    <script src="{{ asset('modules/finances/js/jPages.min.js') }}"></script>
    {{-- <script src="{{ asset('modules/finances/js/statement-index.js?v='. uniqid()) }}"></script> --}}
    <script src="{{ asset('modules/finances/js/balances.js?v='. uniqid()) }}"></script>
    {{-- <script src="{{ asset('modules/finances/js/withdrawals.js?v='. uniqid()) }}"></script> --}}
    <script src="{{ asset('modules/global/adminremark/global/vendor/owl-carousel/owl.carousel.min.js?v='. uniqid()) }}"></script>
    <script src="{{ asset('modules/finances/js/multi-finances.js?v='. uniqid()) }}"></script>
@endpush
