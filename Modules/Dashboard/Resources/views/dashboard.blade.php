@extends("layouts.master")
@section('title', '- Dashboard')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=5') }}">
        <link rel="stylesheet" href="{{ asset('modules/dashboard/css/index.css?v=3') }}">
    @endpush

    <div class="page">
        <div class="page-header container">
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
        <div class="page-content container" style="display:none">
            <!-- Saldos -->
            <div class="row">
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-vermelha.svg') }}" width="30px">
                                <span class="card-desc">Hoje</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="today_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom orangered"></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-laranja.svg') }}" width="30px">
                                <span class="card-desc">Pendente</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="pending_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom orange"></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda.svg') }}" width="30px">
                                <span class="card-desc">Disponível</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="available_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom green"></div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600">
                                <img src="{{ asset('modules/global/img/svg/moeda-azul.svg') }}" width="30px">
                                <span class="card-desc">Total</span>
                            </div>
                        </div>
                        <div
                            class="card-body font-size-24 text-center d-flex align-items-topline justify-content-center">
                            <span class="moeda">R$</span>
                            <span id="total_money" class="text-money"></span>
                        </div>
                        <div class="card-bottom blue"></div>
                    </div>
                </div>
            </div>
            <!-- Tracking e Chargeback -->
            <div class="row">
                <div class="col-12 col-lg-4 d-flex align-items-stretch">
                    <div class="card card-shadow bg-white w-full">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img class="orange-gradient" src="{{ asset('modules/global/img/svg/shipping.svg') }}"
                                     width="30px">
                                <span class="card-desc">Códigos de rastreio informados</span>
                            </div>
                            <i class="material-icons gray" data-toggle="tooltip" data-placement="bottom"
                               title="As vendas que permanecerem sem o código de rastreamento por 15 dias poderão ser estornadas. Geralmente o tempo médio de postagem é de 5 dias">help</i>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-lg-between py-15">
                            <div>
                                <label>Tempo médio de postagem:</label>
                                <span id="average_post_time"></span>
                            </div>
                            <div>
                                <label>Venda mais antiga sem código:</label>
                                <span id="oldest_sale"></span>
                            </div>
                            <div>
                                <label>Códigos informados com problema:</label>
                                <span id="problem"></span>
                            </div>
                            <div>
                                <label>Códigos não informados:</label>
                                <span id="unknown"></span>
                            </div>
                        </div>
                        <div class="card-bottom orangered"></div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex align-items-stretch">
                    <div class="card card-shadow bg-white w-full">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img class="orange-gradient" src="{{ asset('modules/global/img/svg/chargeback.svg') }}"
                                     width="30px">
                                <span class="card-desc">Taxa de Chargebacks</span>
                            </div>
                            <i class="material-icons gray" data-toggle="tooltip" data-placement="bottom" title="Taxa geral de chargeback de sua empresa">help</i>
                        </div>
                        <div class="card-body font-size-24 text-center d-flex align-items-topline align-items-center">
                            <div class="col text-center px-0">
                                <div class="circle">
                                    <strong>0.00%</strong>
                                </div>
                            </div>
                            <div class="col">
                                <span id="total_sales_approved" class="text-money">0</span>
                                <div class="font-size-14 mb-20">Vendas no Cartão</div>
                                <span id="total_sales_chargeback" class="text-money">0</span>
                                <div class="font-size-14">Chargebacks</div>
                            </div>
                        </div>
                        <div class="card-bottom red"></div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 d-flex align-items-stretch">
                    <div class="card card-shadow bg-white w-full">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white pt-20 pb-0">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img class="orange-gradient" src="{{ asset('modules/global/img/svg/tickets.svg') }}"
                                     width="30px">
                                <span class="card-desc">Atendimento</span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            <div class="row mb-15">
                                <div class="col text-center">
                                    <span id="open-tickets" class="text-money">0</span>
                                    <div class="font-size-14">Abertos</div>
                                </div>
                                <div class="col text-center">
                                    <span id="closed-tickets" class="text-money">0</span>
                                    <div class="font-size-14">Resolvidos</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-center">
                                    <span id="mediation-tickets" class="text-money">0</span>
                                    <div class="font-size-14">Em mediação</div>
                                </div>
                                <div class="col text-center">
                                    <span id="total-tickets" class="text-money">0</span>
                                    <div class="font-size-14">Total</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-bottom blue"></div>
                    </div>
                </div>
            </div>
            <!-- Notícias e Releases -->
            <div class="row">
                <div class="col-lg-8" id="news-col" style="display:none">
                    <div id="carouselNews" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                        </ol>
                        <div class="carousel-inner">
                        </div>
                        <a class="carousel-control-prev" href="#carouselNews" role="button" data-slide="prev">
                            <i class="material-icons font-size-60">navigate_before</i>
                        </a>
                        <a class="carousel-control-next" href="#carouselNews" role="button" data-slide="next">
                            <i class="material-icons font-size-60">navigate_next</i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4" id="releases-col" style="display:none">
                    <div class="card card-shadow bg-white">
                        <div class="card-header d-flex justify-content-start align-items-center bg-white py-10 border-bottom">
                            <div class="font-size-14 gray-600 mr-auto">
                                <img class="orange-gradient" src="{{ asset('modules/global/img/svg/releases.svg') }}"
                                     width="30px">
                                <span class="card-desc">Atualizações da Plataforma</span>
                            </div>
                        </div>
                        <div class="card-body pt-0 d-flex flex-column justify-content-between mb-15" id="releases-div" style="overflow-y: auto; height: 280px;">
                        </div>
                        <div class="card-bottom orange"></div>
                    </div>
                </div>
            </div>
        </div>
        @include('companies::empty')
    </div>


    <!-- Modal Termos de Uso -->
    <div id="modal-user-term" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header flex-column-reverse align-items-center border-bottom py-1">
                    <h5 class="modal-title">Termos de Uso</h5>
                    <small class="py-1">Nossos termos de uso foram atualizados. Para continuar utilizando nossos serviços, é preciso que esteja de acordo com os novos termos:</small>
                </div>
                <div class="modal-body p-0">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe type="application/pdf" src="{{ asset('modules/userTerms/pdf/userTerms.pdf') }}#toolbar=0" width="100%" height="300"></iframe>
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
    <div id="modal-peding-data" class="modal fade" role="dialog" data-backdrop="static">
        <div class="modal-dialog p-2">
            <!-- Modal content-->
            <div class="modal-content p-4">
                <h4 class="modal-title font-size-20 text-center">Encontramos dados que precisam ser atualizados!</h4>
                <span class="py-1 text-center">
                        Estamos avaliando a parceria com outras adquirentes para aumentar ainda mais sua conversão.
                        E por isso <b>é fundamental manter seu cadastro completo e atualizado</b>, conforme as exigências destas adquirentes. <b>Contamos com você!
                        Afinal, assim será possível liberar o d+p para vendas no cartão em breve</b>.
                    </span>
                <span class='py-1 text-center'>
                     Confira abaixo o que precisa ser atualizado.
                </span>
                <div class="modal-body p-2 text-center">
                    <div class='text-center div-pending-profile' style='display:none;'>
                        <label>Dados do perfil</label>
                        <a class='btn btn-primary ml-10' href='{{ route('profile.index') }}' target='_blank'>Atualizar</a>
                    </div>
                    <table class='table table-pending-data table-striped table-hover mt-2 mb-10' style='overflow-x: auto !important;'>
                        <tbody class='table-pending-data-body'>
                            <tr class='tr-pending-profile' style='display:none;'>
                                <td style='width:2px;' class='text-center'>
                                    <span class="status status-lg status-away"></span>
                                </td>
                                <td class='text-left'>
                                    Conta > Dados do Perfil
                                </td>
                                <td class='text-center'>
                                    <a class='btn' style='color:darkorange;' href='{{ route('profile.index') }}' target='_blank'>
                                        <b><i class="fa fa-pencil-square-o mr-2" aria-hidden="true"></i>Atualizar</b>
                                    </a>
                                </td>
                            </tr>
                            {{-- js carrega... --}}
                        </tbody>
                    </table>
                    <a class='btn btn-update-later mt-10' style='color:darkorange;' data-dismiss="modal">
                        <b>Atualizar mais tarde</b></a>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('modules/global/js/circle-progress.min.js') }}"></script>
        <script src="{{ asset('modules/dashboard/js/dashboard.js?v=1') }}"></script>
    @endpush

@endsection


