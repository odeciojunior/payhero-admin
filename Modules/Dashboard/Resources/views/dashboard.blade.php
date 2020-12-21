@extends("layouts.master")
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=6') }}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist.min.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/reports/css/chartist-plugin-tooltip.min.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/dashboard/css/index.css?v=3') }}">
    @endpush

    <div class="page dashboard">
        <div style="display: none" style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 mb-15">
                    <h1 class="page-title">Dashboard</h1>
                </div>
                <div class="col-lg-6" id="company-select" style="display:none">
                    <div class="d-lg-flex align-items-center justify-content-end">
                        <div class="mr-10 mb-5 text-lg-right">
                            Empresa:
                        </div>
                        <div class=" text-lg-right">
                            <select id="company" class="form-control new-select"> </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="project-not-empty" class="page-content container" style="display:none">
            <!-- Saldos -->
            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="card card-shadow bg-white">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc">Recebimentos Hoje</span>
                                    </div>
                                </div>
                                <div class="card-body font-size-24 d-flex align-items-topline">
                                    <div class="card-text align-items-center">
                                        <span class="moeda"></span>
                                        <span id="today_money" class="text-money"></span>
                                    </div>
                                </div>
                                <div class="card-bottom orangered"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card card-shadow bg-white">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc">Pendente</span>
                                    </div>
                                </div>
                                <div class="card-body font-size-24 d-flex align-items-topline">
                                    <div class="card-text align-items-center">
                                        <span class="moeda"></span>
                                        <span id="pending_money" class="text-money"></span>
                                    </div>
                                </div>
                                <div class="card-bottom orange"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card card-shadow bg-white">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <span class="card-desc">Disponível</span>
                                    </div>
                                </div>
                                <div class="card-body font-size-24 d-flex align-items-topline">
                                    <div class="card-text align-items-center">
                                        <span class="moeda"></span>
                                        <span id="available_money" class="text-money"></span>
                                    </div>
                                </div>
                                <div class="card-bottom green"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card card-shadow bg-white">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600 mr-auto">
                                        <span class="card-desc">Total</span>
                                    </div>
                                    <i class="material-icons gray" data-toggle="tooltip" id="info-total-balance"
                                       data-placement="bottom">help</i>
                                </div>
                                <div class="card-body font-size-24 d-flex align-items-topline">
                                    <div class="card-text align-items-center">
                                        <span class="moeda"></span>
                                        <span id="total_money" class="text-money"></span>
                                    </div>
                                </div>
                                <div class="card-bottom blue"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card card-shadow bg-white">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600">
                                        <img src="{{ asset('modules/global/img/svg/chargeback.svg') }}" width="30px">
                                        <span class="card-desc">Vendas neste mês</span>
                                    </div>
                                </div>
                                <div class="card-body p-5" style="height: 295px">
                                    <div id="scoreLineToMonth"
                                         class="ct-chart ct-golden-section chart-action tab-pane active"></div>
                                </div>
                                <div class="card-bottom orange"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="row">
                        <div class="col-12 d-flex align-items-stretch">
                            <div class="card card-shadow bg-white w-full">
                                <div
                                    class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                                    <div class="font-size-14 gray-600 mr-auto">
                                        <span class="card-desc">Saúde da Conta</span>
                                    </div>
                                    <i class="material-icons gray" data-toggle="tooltip" data-placement="bottom"
                                       title="Taxa geral de chargeback de sua empresa">help</i>
                                </div>
                                <div class="card-body">
                                    <div class="row d-flex align-items-topline align-items-center">
                                        <div class="col text-center px-0 d-flex justify-content-center">
                                            <div class="circle text-circle">
                                                <strong>0.00%</strong>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="pb-15"><b>Taxa de Chargeback</b></div>
                                            <div class="mb-10 d-flex flex-row justify-content-center">
                                                <span id="total_sales_approved" class="text-money mr-1">0</span>
                                                <div class="font-size-14 ml-10 w-p100">Vendas no Cartão</div>
                                            </div>
                                            <div class="d-flex flex-row justify-content-center">
                                                <span id="total_sales_chargeback" class="text-money mr-1">0</span>
                                                <div class="font-size-14 ml-10 w-p100">Chargebacks</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row no-gutters1">
                                                <div class="col-6 align-items-start w-25">
                                                    <hr class="bg-grey-50 my-20">
                                                </div>
                                            </div>
                                            <div class="row my-1">
                                                <div class="col-12 pb-15"><b>Atendimento</b></div>
                                            </div>
                                            <div class="row my-2">
                                                <div class="col d-flex justify-content-center">
                                                    <span id="open-tickets" class="text-money">0</span>
                                                    <div class="font-size-14 ml-10 w-p100">Abertos</div>
                                                </div>
                                                <div class="col d-flex justify-content-center">
                                                    <span id="closed-tickets" class="text-money">0</span>
                                                    <div class="font-size-14 ml-10 w-p100">Resolvidos</div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col d-flex justify-content-center">
                                                    <span id="mediation-tickets" class="text-money">0</span>
                                                    <div class="font-size-14 ml-10 w-p100">Em mediação</div>
                                                </div>
                                                <div class="col d-flex justify-content-center">
                                                    <span id="total-tickets" class="text-money">0</span>
                                                    <div class="font-size-14 ml-10 w-p100">Total</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row no-gutters1">
                                                <div class="col-6 align-items-start w-25">
                                                    <hr class="bg-grey-50 my-20">
                                                </div>
                                            </div>
                                            <div class="row my-1">
                                                <div class="col-12 pb-15 d-flex justify-content-between">
                                                    <b>Códigos de Rastreio</b>

                                                    <i class="material-icons gray" data-toggle="tooltip"
                                                       data-placement="bottom"
                                                       title="As vendas que permanecerem sem o código de rastreamento por 15 dias poderão ser estornadas. Geralmente o tempo médio de postagem é de 5 dias">help</i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">

                                            <div class="d-flex">
                                                <label>Tempo médio de postagem: &nbsp; </label>
                                                <span class="update-text" id="average_post_time"></span>
                                            </div>
                                            <div class="d-flex">
                                                <label>Venda mais antiga sem código: &nbsp; </label>
                                                <span class="update-text" id="oldest_sale"></span>
                                            </div>
                                            <div class="d-flex">
                                                <label>Códigos informados com problema: &nbsp; </label>
                                                <span class="update-text" id="problem"></span>
                                            </div>
                                            <div class="d-flex">
                                                <label>Códigos não informados: &nbsp; </label>
                                                <span class="update-text" id="unknown"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-bottom red"></div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="card card-shadow">
                                <div
                                    class="card-header d-flex justify-content-between align-items-center bg-blue pt-30 pb-20">
                                    <div class="font-size-16 text-white">
                                        <b class="card-desc">A CloudFox mudou.</b>
                                        <br/>
                                        <b class="card-desc">Bem-vindo(a) ao Sirius!</b>
                                    </div>
                                    <img class="img-fluid"
                                         src="{{ asset('modules/global/img/svg/sirius-stars-b.png') }}" height="60px"
                                         width="60px">
                                </div>
                                <div class="card-body pt-0 d-flex flex-column justify-content-between mb-15">
                                    <p class="font-size-12">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                        incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis
                                        nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                                    </p>
                                    <a class="font-size-14 text-blue" href="#"><b>Saiba mais ⇾</b></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Quando não tem projeto cadastrado  --}}
        @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>


    <!-- Modal Termos de Uso -->
    <div id="modal-user-term" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header flex-column-reverse align-items-center border-bottom py-1">
                    <h5 class="modal-title">Termos de Uso</h5>
                    <small class="py-1">Nossos termos de uso foram atualizados. Para continuar utilizando nossos
                        serviços, é preciso que esteja de acordo com os novos termos:</small>
                </div>
                <div class="modal-body p-0">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe type="application/pdf"
                                src="{{ asset('modules/userTerms/pdf/userTerms.pdf') }}#toolbar=0" width="100%"
                                height="300"></iframe>
                    </div>
                    <div class="modal-footer border-top py-2">
                        <button type="button" id='accepted-terms' class="btn btn-info col-sm-2">
                            Aceitar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dados pendentes -->
    {{--    <div id="modal-peding-data" class="modal fade" role="dialog" data-backdrop="static">--}}
    {{--        <div class="modal-dialog p-2">--}}
    {{--            <!-- Modal content-->--}}
    {{--            <div class="modal-content p-4">--}}
    {{--                <h4 class="modal-title font-size-20 text-center">Encontramos dados que precisam ser atualizados!</h4>--}}
    {{--                <span class="py-1 text-center">--}}
    {{--                        Estamos avaliando a parceria com outras adquirentes para aumentar ainda mais sua conversão.--}}
    {{--                        E por isso <b>é fundamental manter seu cadastro completo e atualizado</b>, conforme as exigências destas adquirentes. <b>Contamos com você!--}}
    {{--                        Afinal, assim será possível liberar o d+p para vendas no cartão em breve</b>.--}}
    {{--                    </span>--}}
    {{--                <span class='py-1 text-center'>--}}
    {{--                     Confira abaixo o que precisa ser atualizado.--}}
    {{--                </span>--}}
    {{--                <div class="modal-body p-2 text-center">--}}
    {{--                    <div class='text-center div-pending-profile' style='display:none;'>--}}
    {{--                        <label>Dados do perfil</label>--}}
    {{--                        <a class='btn btn-primary ml-10' href='{{ route('profile.index') }}' target='_blank'>Atualizar</a>--}}
    {{--                    </div>--}}
    {{--                    <table class='table table-pending-data table-striped table-hover mt-2 mb-10' style='overflow-x: auto !important;'>--}}
    {{--                        <tbody class='table-pending-data-body'>--}}
    {{--                            <tr class='tr-pending-profile' style='display:none;'>--}}
    {{--                                <td style='width:2px;' class='text-center'>--}}
    {{--                                    <span class="status status-lg status-away"></span>--}}
    {{--                                </td>--}}
    {{--                                <td class='text-left'>--}}
    {{--                                    Conta > Dados do Perfil--}}
    {{--                                </td>--}}
    {{--                                <td class='text-center'>--}}
    {{--                                    <a class='btn' style='color:darkorange;' href='{{ route('profile.index') }}' target='_blank'>--}}
    {{--                                        <b><i class="fa fa-pencil-square-o mr-2" aria-hidden="true"></i>Atualizar</b>--}}
    {{--                                    </a>--}}
    {{--                                </td>--}}
    {{--                            </tr>--}}
    {{--                            --}}{{-- js carrega... --}}
    {{--                        </tbody>--}}
    {{--                    </table>--}}
    {{--                    <a class='btn btn--later mt-10' style='color:darkorange;' data-dismiss="modal">--}}
    {{--                        <b>Atualizar mais tarde</b></a>--}}
    {{--                </div>--}}
    {{--                <p class='info pt-5 mb-0 text-center' style='font-size: 10px;'>--}}
    {{--                    <i class='icon wb-info-circle' aria-hidden='true'></i>--}}
    {{--                    A partir de 09/07/2020 a solicitação de saque estará disponível apenas para empresas com os dados atualizados.--}}
    {{--                </p>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    @push('scripts')
        <script src='{{ asset('modules/reports/js/chartist.min.js') }}'></script>
        <script src='{{ asset('modules/reports/js/chartist-plugin-tooltip.min.js') }}'></script>
        <script src='{{ asset('modules/reports/js/chartist-plugin-legend.min.js') }}'></script>
        <script src="{{ asset('modules/global/js/circle-progress.min.js') }}"></script>
        <script src="{{ asset('modules/dashboard/js/dashboard.js?v=' . random_int(100, 10000)) }}"></script>
    @endpush

@endsection


