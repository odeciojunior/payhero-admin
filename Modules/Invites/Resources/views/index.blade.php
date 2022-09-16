@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/invites/index.min.css') }}">
        <style>
            .badge {
                color: white;
                font-size: 14px;
                padding: 10px 24px;
                border-radius: 16px;
                font-weight: 700;
            }

            .badge.badge-success {
                background-color: #5EE2A1;
            }

            #content-error {
                display: none;
                height: 100%;
                width: 100%;
                position: absolute;
                display: -webkit-flex;
                display: flex;
                -webkit-align-items: center;
                align-items: center;
                -webkit-justify-content: center;
                justify-content: center;
                padding-bottom: 20%;
            }

            @media only screen and (min-width: 768px) {
                .col-md-3.card {
                    margin-right: 10px;
                    max-width: calc(25% - 10px);
                }
            }

            @media only screen and (min-width: 576px) and (max-width : 767px) {
                .col-sm-6.card {
                    margin-right: 10px;
                    max-width: calc(50% - 10px);
                }
            }

            strong span {
                color: #57617c;
            }
        </style>
    @endpush
    <div class="page">

        @include('layouts.company-select',['version'=>'mobile'])

        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-8">
                    <h1 class="page-title">Convites</h1>
                </div>
                <div class="col-4 text-right">
                    <button id="store-invite"
                            title='Adicionar convite'
                            type="button"
                            class="btn btn-floating btn-primary"
                            style="position: relative; float: right"
                            {{-- data-target='#modal' data-toggle='modal' --}}>
                        <i class="o-add-1"
                           aria-hidden="true"></i></button>
                </div>
            </div>
            <p id='text-info'
               style="margin-top: 20px; margin-bottom:30px">A cada convite aceito, você vai ganhar 1% de
                comissão das vendas efetuadas pelos novos usuários que você convidou durante 6 meses.</p>

            <div class='container col-sm-12 d-lg-block'
                 id='card-invitation-data'
                 style='display:none;'>
                <div class='row'>
                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                        <div class="card-body">
                            <h5 class="font-size-14 gray-600">Convites enviados</h5>
                            <h4 id='invitations_sent' class="font-size-30 bold number"></h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                        <div class="card-body">
                            <h5 class="font-size-14 gray-600">Convites ativos</h5>
                            <h4 id='invitations_accepted' class="font-size-30 bold number"></i>
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                        <div class="card-body">
                            <h5 class="font-size-14 gray-600">Comissão pendente</h5>
                            <h4 id='commission_pending' class="number"></h4>
                        </div>
                        <div class="s-border-right yellow"></div>
                    </div>
                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                        <div class="card-body">
                            <h5 class="font-size-14 gray-600">Comissão paga </h5>
                            <h4 id='commission_paid' class="number"></h4>
                        </div>
                        <div class="s-border-right red"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="content-error" class='content-error text-center' style="display:none !important; margin-top: -40px;">
                <img src="build/global/img/convites.svg" width="156px"/>
                <h4 class="big gray">Você ainda não enviou convites!</h4> <br>
                <p class="desc gray">Envie convites, e
                    <strong>ganhe 1% de tudo que seu convidado vender durante 6 meses!</strong></p>
            </div>
        <div class="page-content container" id='page-invites'>

            <div class="card shadow" id='card-table-invite' data-plugin="matchHeight" style='display:none;'>
                <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">
                    <table class="table table-striped unify" id="table_invites">
                        <thead class="text-center">
                            <td class="text-left">Convite</td>
                            <td class="text-center">Email convidado</td>
                            <td class="text-center">Empresa Recebedora</td>
                            <td class="text-center">Status</td>
                            <td class="text-center">Data cadastro</td>
                            <td class="text-center">Data expiração</td>
                            <td class="text-center"></td>
                            <td class="text-center"></td>
                        </thead>
                        <tbody id='table-body-invites'>
                            {{-- js invites carrega --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row d-flex justify-content-center justify-content-md-end pb-35">
                <ul id="pagination-invites" class="pagination-sm margin-chat-pagination mb-0 pagination-style" style="margin-top:10px;position:relative;float:right">
                    {{-- js pagination carrega --}}
                </ul>
            </div>
            <div class="modal fade modal-3d-flip-vertical"
                 id="modal-invite"
                 aria-labelledby="exampleModalTitle"
                 role="dialog"
                 tabindex="-1">
                <div id='mainModalBody'
                     class="modal-dialog modal-simple">
                    <!-- Tem company -->
                    <div id='modal-then-companies'
                         class='modal-content'
                         style='display:none;'>
                        <div class='modal-header'>
                            <button type='button'
                                    id='btn-close-invite'
                                    class='close'
                                    data-dismiss='modal'
                                    aria-label='Close'>
                                <span aria-hidden='true'>×</span>
                            </button>
                            <h4 id='modal-reverse-title'
                                class='modal-title'
                                style='width:100%; text-align:center'></h4>
                        </div>
                        <div id='modal-reverse-body'
                             class='modal-body'>
                            <div id='body-modal'>
                                <div class='row'>
                                    <div class='form-group col-12'>
                                        <label for='email'>Email do convidado</label>
                                        <input name='email_invited'
                                               type='text'
                                               class='form-control'
                                               id='email'
                                               placeholder='Email'
                                               style="height: 50px !important;">
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='form-group col-12'>
                                        <label for='company'>
                                            Empresa para receber
                                        </label>
                                        <input type="text" disabled class="company_name" style="text-overflow: ellipsis;">
                                    </div>
                                </div>
                                <div class='row'>
                                    <div class='col-12'>
                                        <label for='email'>Link do Convite</label>
                                    </div>
                                    <div id='invite-link-select'
                                         class='input-group col-12'>
                                        <input type='text'
                                               class='form-control'
                                               id='invite-link'
                                               value=''
                                               readonly>
                                        <span class='input-group-btn'>
                                            <button id='copy-link'
                                                    class='btn btn-default'
                                                    type='button'>Copiar</button>
                                        </span>
                                    </div>
                                </div>
                                <div class='row'
                                     style='margin-top: 35px'>
                                    <div class='form-group col-12 text-right'>
                                        <input id='btn-send-invite'
                                               type='button'
                                               class='form-control btn btn-primary col-sm-12 col-m-3 col-lg-3'
                                               value='Enviar Convite'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Not Company -->
                    <div id='modal-not-companies'
                         class='modal-content p-10'
                         style='display: none;'>
                        <div class='header-modal simple-border-bottom'>
                            <h2 id='modal-tile'
                                class='modal-title'>Ooooppsssss!</h2>
                        </div>
                        <div class='modal-body simple-border-bottom'
                             style='padding-bottom: 1%; padding-top: 1% ;'>
                            <div class='swal2-icon swal2-error swal2-animate-error-icon'
                                 style='display:flex;'>
                                <span class='swal2-x-mark'>
                                    <span class='swal2-x-mark-line-left'></span>
                                    <span class='swal2-x-mark-line-right'></span>
                                </span>
                            </div>
                            <h3 align='center'>Você não cadastrou nenhuma empresa</h3>
                            <h5 align='center'>
                                Deseja cadastrar uma empresa?
                                <a class='red pointer'
                                   href='{{ env('ACCOUNT_FRONT_URL') }}/redirect/{{ \Vinkla\Hashids\Facades\Hashids::connection('login')->encode(auth()->user()->id) }}/{{ (string) \Vinkla\Hashids\Facades\Hashids::encode(\Carbon\Carbon::now()->addMinute()->unix()) }}/companies'>Clique
                                    aqui</a>
                            </h5>
                        </div>
                        <div style='width:100%; text-align: center; padding-top: 3%;'>
                            <span class='btn btn-primary'
                                  data-dismiss='modal'
                                  style='font-size: 25px;'>
                                Retornar
                            </span>
                        </div>
                    </div>
                    <!-- Not Approved documents companies -->
                    <div id='modal-not-approved-document-companies'
                         class='modal-content p-10'
                         style='display: none;'>
                        <div class='modal-body simple-border-bottom'
                             style='padding-bottom: 1%; padding-top: 1% ;'>
                            <div class='swal2-icon swal2-error swal2-animate-error-icon'
                                 style='display:flex;'>
                                <span class='swal2-x-mark'>
                                    <span class='swal2-x-mark-line-left'></span>
                                    <span class='swal2-x-mark-line-right'></span>
                                </span>
                            </div>
                            <p align='center'
                               style='font-size: 16px;'>
                                Para enviar convites você precisa ter pelo menos uma empresa aprovada para transacionar
                                e todos os documentos da empresa e do seu perfil precisam estar aprovados!
                            </p>
                        </div>
                        <div style='width:100%; text-align: center; padding-top: 3%;'>
                            <span class='btn btn-primary'
                                  data-dismiss='modal'
                                  style='font-size: 16px;'>
                                Retornar
                            </span>
                        </div>
                    </div>
                    <!-- Não pode enviar mais convites-->
                    <div id='modal-not-invites-today'
                         class='modal-content p-10'
                         style=' display:none;'>
                        <div class='header-modal simple-border-bottom'>
                            <h2 class='modal-title'></h2>
                        </div>
                        <div class='modal-body simple-border-bottom'
                             style='padding-bottom: 1%; padding-top: 1%;'>
                            <div></div>
                            <h3>O limite de convites para a versão beta foi atingindo, aguarde a versão oficial para
                                poder enviar novos convites!</h3>
                            <h3></h3>
                        </div>
                        <div style='width: 100%; text-align: center; padding-top: 3%;'>
                            <span class='btn btn-primary'
                                  data-dismiss='modal'
                                  style='font-size: 25px;'>
                                Retornar
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
            <div id='modal-invite-not'
                 class='modal fade show'
                 aria-labelledby='modal-invite'
                 role='dialog'
                 tabindex='-1'
                 style=' padding-right: 12px;'>
                <div class='modal-dialog modal-simple modal-center'>
                    <div class='modal-content text-center'>
                        <div class='modal-header text-center'>
                            {{-- <button class='close' type='button' data-dismiss='modal' aria-label='Close'>
                                <span aria-hidden='true'>×</span>
                            </button> --}}
                            <h4 class='modal-title'>Aviso</h4>
                        </div>
                        <div class='modal-body text-center'>
                            <h3>O limite de convites para a versão beta foi atingido, aguarde a versão oficial para
                                poder enviar novos convites!</h3>
                        </div>
                        <div class='modal-footer'
                             style='text-align: center; padding-top: 3%;'>
                            <button class='btn btn-primary'
                                    type='button'
                                    data-dismiss='modal'
                                    style='font-size: 16px;'>
                                Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
        </div>
    </div>
    <!-- Modal padrão para excluir -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
         id="modal-delete-invitation"
         aria-hidden="true"
         aria-labelledby="exampleModalTitle"
         role="dialog"
         tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div id="modal_excluir_body"
                     class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient"
                           style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id="btn-cancel-invitation"
                            type="button"
                            class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                            data-dismiss="modal"
                            style="width: 20%;">
                        <b>Cancelar</b>
                    </button>
                    <button id="btn-delete-invitation"
                            type="button"
                            class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                            data-dismiss="modal"
                            style="width: 20%;">
                        <b class="mr-2">Excluir </b>
                        <span class="o-bin-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para reenviar convite -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
         id="modal-resend-invitation"
         aria-hidden="true"
         aria-labelledby="exampleModalTitle"
         role="dialog"
         tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button"
                            id="btn-close-invite"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div id="modal_excluir_body"
                     class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons"
                           style="font-size: 80px;color:#16b248;"> email </i>
                    </div>
                    <h4 class="black"> Você realmente deseja reenviar o convite? </h4>
                    {{-- <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p> --}}
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id='btn-cancel'
                            type="button"
                            class="col-4 btn btn-gray"
                            data-dismiss="modal"
                            style="width: 20%;">Cancelar
                    </button>
                    <button id="btn-resend-invitation"
                            type="button"
                            class="col-4 btn btn-success"
                            style="width: 20%;"
                            data-dismiss="modal">Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ mix('build/layouts/invites/index.min.js') }}"></script>
    @endpush
@endsection
