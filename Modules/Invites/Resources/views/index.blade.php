@extends("layouts.master")

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css') }}">
    @endpush
    <div class="page">
        <div class="page-header container">
            <button id="store-invite" type="button" class="btn btn-floating btn-danger" style="position: relative; float: right" {{--data-target='#modal' data-toggle='modal'--}}>
                <i class="icon wb-plus" aria-hidden="true"></i></button>
            <h2 class="page-title">Convites</h2>
            <p id='text-info' style="margin-top: 12px; display: none;">A cada convite aceito, você vai ganhar 1% de comissão das vendas efetuadas pelos novos usuários que você convidou durante 1 ano.</p>
            <div class="card shadow p-20" id='card-invitation-data' style='display:none;'>
                <div class="row justify-content-center">
                    <div style="width: 20%">
                        <h6 class="text-center green-gradient"><i class="material-icons align-middle mr-1 green-gradient"> card_giftcard </i> Convites disponíveis </h6>
                        <h4 id='invitations_amount' class="number text-center green-gradient"></h4>
                    </div>
                    <div style="width: 20%">
                        <h6 class="text-center orange-gradient"><i class="material-icons align-middle mr-1 orange-gradient"> group_add </i> Convites enviados </h6>
                        <h4 id='invitations_sent' class="number text-center orange-gradient"></h4>
                    </div>
                    <div style="width: 20%">
                        <h6 class="text-center green-gradient"><i class="material-icons align-middle green-gradient mr-1" > people </i>  Convites ativos </h6>
                        <h4 id='invitations_accepted' class="number text-center green-gradient"></i>
                        </h4>
                    </div>
                    <div style="width: 20%">
                        <h6 class="text-center orange-gradient"> <i class="material-icons align-middle orange-gradient"> attach_money </i> Comissão pendente </h6>
                        <h4 id='commission_pending' class="number text-center orange-gradient" style='color:green'></i>
                        </h4>
                    </div>
                    <div style="width: 20%">
                        <h6 class="text-center green-gradient"> <i class="material-icons align-middle green-gradient"> attach_money </i> Comissão paga </h6>
                        <h4 id='commission_paid' class="number text-center green-gradient"></i>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container" id='page-invites'>
            <div id="content-error" class='' style='display:none;'>
                <div class="content-modal-error text-center" style=''>
                    <img src="modules/global/img/emptyconvites.svg" width="250px"/>
                    <h4 class="big gray" style='width:100%'>Você ainda não enviou convites!</h4> <br>
                    <p class="desc gray" style='width:100%'>Envie convites, e
                        <strong>ganhe 1% de tudo que seu convidado vender durante um ano!</strong></p>
                </div>
            </div>
            <div class="card shadow" id='card-table-invite' data-plugin="matchHeight" style='display:none;'>
                <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">
                    <table class="table table-striped unify">
                        <thead class="text-center">
                            <th class="text-left">Convite</th>
                            <th class="text-center">Email convidado</th>
                            <th class="text-center">Empresa Recebedora</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Data cadastro</th>
                            <th class="text-center">Data expiração</th>
                        </thead>
                        <tbody id='table-body-invites'>
                            {{-- js invites carrega  --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <ul id="pagination-invites" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                {{-- js pagination carrega --}}
            </ul>
            <div class="modal fade modal-3d-flip-vertical" id="modal-invite" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                <div id='mainModalBody' class="modal-dialog modal-simple">

                </div>
            </div>
        </div>
    </div>
    <!-- Modal padrão para excluir -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-invitation" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                        <i class="material-icons md-16">close</i>
                    </a>
                </div>
                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id='btn-cancel-invitation' type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                    <button id="btn-delete-invitation" type="button" class="col-4 btn btn-danger" style="width: 20%;" data-dismiss="modal">Excluir</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para reenviar convite -->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-resend-invitation" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                        <i class="material-icons md-16">close</i>
                    </a>
                </div>
                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons" style="font-size: 80px;color:#16b248;"> email </i>
                    </div>
                    <h4 class="black"> Você realmente deseja reenviar o convite? </h4>
{{--                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>--}}
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id='btn-cancel' type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                    <button id="btn-resend-invitation" type="button" class="col-4 btn btn-success" style="width: 20%;" data-dismiss="modal">Enviar</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{asset('modules/invites/js/invites.js?v=1') }}"></script>
    @endpush

@endsection

