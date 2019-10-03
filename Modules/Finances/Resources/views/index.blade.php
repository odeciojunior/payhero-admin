@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css') }}">
@endpush

@section('content')

    <div class="page">
        <div class="page-header container">
            <div class="row">
                <div class="col-lg-6 mb-30">
                    <h1 class="page-title">Finanças</h1>
                </div>
            </div>
        </div>
        <div class="page-content container" style="display:none" >
            <div class="card shadow">
                <nav class="pt-20">
                    <div class="nav-tabs-horizontal">
                        <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab"
                               href="#nav-transfers"
                               role="tab" aria-controls="nav-home" aria-selected="true">Transferências
                            </a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-extract"
                               role="tab" aria-controls="nav-profile" aria-selected="false">Extrato
                            </a>
                        </div>
                    </div>
                </nav>
                <div class="p-30 pt-20">
                    <div class="tab-content" id="nav-tabContent">
                        <!-- TRANSFERENCIAS -->
                        <div class="tab-pane fade show active" id="nav-transfers" role="tabpanel"
                             aria-labelledby="nav-home-tab">
                            <div class="row justify-content-start align-items-baseline">
                                <div class="col-12 mb-3">
                                    <h5 class="title-pad"> Nova transferência </h5>
                                    <p class="sub-pad"> Saque o dinheiro para sua conta bancária.
                                    </p>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-6 mb-15">
                                            <div class="price-holder">
                                                <h6 class="label-price"> Saldo pendente </h6>
                                                <h4 class="price saldoPendente">
                                                </h4>
                                                <div class="grad-border"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-6 mb-15">
                                            <div class="price-holder antecipacao" id="pop-antecipacao">
                                                <h6 class="label-price"> Disponível para antecipar </h6>
                                                <h4 class="price align-items-baseline disponivelAntecipar"
                                                    id='btn-disponible-antecipation'>
                                                    <a href="#">
                                                        <svg class="svg-antecipar"
                                                             xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                             viewBox="0 0 24 24">
                                                            <path
                                                                d="M11 6v8h7v-2h-5v-6h-2zm10.854 7.683l1.998.159c-.132.854-.351 1.676-.652 2.46l-1.8-.905c.2-.551.353-1.123.454-1.714zm-2.548 7.826l-1.413-1.443c-.486.356-1.006.668-1.555.933l.669 1.899c.821-.377 1.591-.844 2.299-1.389zm1.226-4.309c-.335.546-.719 1.057-1.149 1.528l1.404 1.433c.583-.627 1.099-1.316 1.539-2.058l-1.794-.903zm-20.532-5.2c0 6.627 5.375 12 12.004 12 1.081 0 2.124-.156 3.12-.424l-.665-1.894c-.787.2-1.607.318-2.455.318-5.516 0-10.003-4.486-10.003-10s4.487-10 10.003-10c2.235 0 4.293.744 5.959 1.989l-2.05 2.049 7.015 1.354-1.355-7.013-2.184 2.183c-2.036-1.598-4.595-2.562-7.385-2.562-6.629 0-12.004 5.373-12.004 12zm23.773-2.359h-2.076c.163.661.261 1.344.288 2.047l2.015.161c-.01-.755-.085-1.494-.227-2.208z"/>
                                                        </svg>
                                                    </a>
                                                </h4>
                                                <div class="custom-popover shadow-sm" id="antecipa-popover"
                                                     style="display: none">
                                                    <div class="d-flex flex-column text-center">
                                                        <p style="font-size: 12px; font-weight: 700;"> O valor
                                                            antecipado será incluido no seu
                                                            <strong style="color: green;"> Saldo Disponível </strong>
                                                        </p>
                                                        <h5 style="font-size: 16px; font-weight: 700; margin: 0;">
                                                            Saldo após antecipação </h5>
                                                        <h3 style="font-size: 25px;font-weight: 700;">
                                                            <span class="currency">R$</span>
                                                            <span id='balance-after-anticipation'>0,00</span>
                                                        </h3>
                                                        <p style="font-weight: 300; font-size: 11px; color: black; opacity: 0.8;">
                                                            Uma taxa de
                                                            <span class="currency">R$</span>
                                                            <span id='tax-value'>0,00</span>
                                                            será cobrada para liberar o valor antecipado.
                                                        </p>
                                                        <a class="btn btn-outline-success anticipation"
                                                           id='btn-anticipation' href="#"> Antecipar</a>
                                                    </div>
                                                </div>
                                                <div class="grad-border purple"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-6 mb-15">
                                            <div id="div-available-money" class="price-holder pointer">
                                                <h6 class="label-price"> Saldo Disponível </h6>
                                                <h4 class="price saldoDisponivel">
                                                </h4>
                                                <div class="grad-border green"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-6 mb-15">
                                            <div class="price-holder">
                                                <h6 class="label-price"> Saldo Total </h6>
                                                <h4 class="price saltoTotal">
                                                </h4>
                                                <div class="grad-border blue"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="row flex-column">
                                        <div class="col-12 mb-3">
                                            <div class="input-holder">
                                                <label for="company"> Empresa</label>
                                                <select class="form-control select-pad" name="company"
                                                        id="transfers_company_select">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="company"> Valor a transferir</label>
                                            <div class='row' style='display: flex; margin:0px'>
                                                <div class="input-group col-sm-12 col-md-12 col-lg-6"
                                                     style='padding:0px'>
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1 custom-addon"
                                                              style="height: auto; border: 1px solid #ddd;"><span
                                                                class="currency">$</span></span>
                                                    </div>
                                                    <input id="custom-input-addon" type="text"
                                                           class="form-control input-pad withdrawal-value"
                                                           placeholder="Digite o valor" aria-label="Digite o valor"
                                                           aria-describedby="basic-addon1">
                                                </div>
                                                <button id="bt-withdrawal"
                                                        class="btn btn-success btn-sacar ml-3 col-sm-12 col-md-12 col-lg-5"
                                                        data-toggle="modal">
                                                    <svg class="mr-2" style="fill: white; vertical-align: middle;"
                                                         xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         viewBox="0 0 24 24">
                                                        <path
                                                            d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"></path>
                                                    </svg>
                                                    Sacar dinheiro
                                                </button>
                                            </div>
                                            <small class="text-muted">Cada saque acarreta uma taxa de
                                                <span class="currency">R$</span>
                                                03,80*
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3 mt-3">
                                    <h5 class="card-title"> Histórico de transferências </h5>
                                </div>
                                <div class="col-12">
                                    <table id='withdrawalsTable' class="table table-striped table-condensed unify">
                                        <thead>
                                        <tr>
                                            <th scope="col">Conta</th>
                                            <th scope="col">Solicitação</th>
                                            <th scope="col">Liberação</th>
                                            <th scope="col">Valor</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                        </thead>
                                        <tbody id="withdrawals-table-data" class="custom-t-body">
                                        </tbody>
                                    </table>
                                    <ul id="pagination-withdrawals" class="pagination-sm"
                                        style="margin-top:10px;position:relative;float:right">
                                        js carrega...
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- EXTRATO -->
                        <div class="tab-pane fade" id="nav-extract" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row justify-content-between">
                                <div class="col-12 fix-5">
                                    <div class="d-flex no-gutters justify-content-between">
                                        <div class="p-2 mb-3">
                                            <h5 class="title-pad"> Extrato </h5>
                                            <p class="sub-pad"> Pra você controlar tudo que entra e sai da sua conta.
                                            </p>
                                        </div>
                                        <div class="p-2">
                                            <div class="price-holder">
                                                <h6 class="label-price"> Total disponível na conta </h6>
                                                <h4 class="price totalAvailableAccount total_available">
                                                </h4>
                                                <div class="grad-border blue"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="row justify-content-between align-items-baseline">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                                            <div class="input-holder">
                                                <label for="company"> Empresa</label>
                                                <select class="form-control select-pad" name="company"
                                                        id="extract_company_select">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <table id='transfersTable' class="table table-striped table-condensed unify">
                                        <thead>
                                        <tr>
                                            <th scope="col" class='headCenter' style='width:33%'>Razão</th>
                                            <th scope="col" class='headCenter' style='width:33%'>Data</th>
                                            <th scope="col" class='headCenter' style='width:34%'>Valor</th>
                                        </tr>
                                        </thead>
                                        <tbody id="table-transfers-body" class="custom-t-body">
                                        </tbody>
                                    </table>
                                    <ul id="pagination-transfers" class="pagination-sm"
                                        style="margin-top:10px;position:relative;float:right">
                                        js carrega...
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-error text-center" style="display:none">
            <img src="{!! asset('modules/global/img/emptyempresas.svg') !!}" width="250px">
            <h1 class="big gray">Você ainda não tem nenhuma empresa!</h1>
            <p class="desc gray">Vamos cadastrar a primeira empresa? </p>
            <a href="/companies/create" class="btn btn-primary gradient">Cadastrar empresa</a>
        </div>
    </div>
    <!-- Modal confirmar saque -->
    <div id="modal-withdrawal" class="modal fade modal-3d-flip-vertical " role="dialog" tabindex="-1">
        <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
            <div id="conteudo_modal_add" class="modal-content p-10">
                <div class="header-modal simple-border-bottom">
                    <h2 id="modal-withdrawal-title" class="modal-title">Confirmar Saque</h2>
                </div>
                <div id="modal_body" class="modal-body simple-border-bottom" style='padding-bottom:1%;padding-top:1%;'>
                    <div>
                        <h5>Verifique os dados da conta:</h5>
                        <h4>Banco:
                            <span id="modal-withdrawal-bank"></span>
                        </h4>
                        <h4>Agência:
                            <span id="modal-withdrawal-agency"></span>
                            <span id="modal-withdrawal-agency-digit"></span>
                        </h4>
                        <h4>Conta:
                            <span id="modal-withdrawal-account"></span>
                            <span id="modal-withdrawal-account-digit"></span>
                        </h4>
                        <h4>Documento:
                            <span id="modal-withdrawal-document"></span>
                        </h4>
                        <hr>
                        <h3>Valor do saque:
                            <span id="modal-withdrawal-value" class='greenGradientText'></span>
                            <span id="taxValue" class="" style="font-size: 6px">- R$3,80</span>
                        </h3>
                    </div>
                </div>
                <div id='modal-withdraw-footer' class="modal-footer">
                    <button id="bt-confirm-withdrawal" class="btn btn-success"
                            style="background-image: linear-gradient(to right, #23E331, #44A44B);font-size:20px; width:100%">
                        <strong>Confirmar</strong></button>
                    <button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal" aria-label="Close"
                            style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">
                        <strong>Cancelar</strong></button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal Detalhes -->
    @include('sales::details')
    <!-- End Modal -->

    @push('scripts')
        <script src="{{ asset('modules/finances/js/index.js?v=1') }}"></script>
    @endpush

@endsection

