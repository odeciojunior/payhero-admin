@extends("layouts.master")

@section('content')
    <div class='page'>
        <div class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-6">
                    <h1 class="page-title">Afiliação</h1>
                </div>
                <div class="col-6 text-right">
                </div>
            </div>
        </div>
        <div class="page-content container">
            <div class="mb-15">
                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="color: #ee535e">
                        <li class="nav-item" role="presentation">
                            <a id="tab-affiliates" class="nav-link active" data-toggle="tab" href="#tab_affiliates_panel"
                               aria-controls="tab_affiliates" role="tab">Afiliados
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab-affiliates-request" class="nav-link" data-toggle="tab" href="#tab_affiliates_request_panel"
                               aria-controls="tab_affiliates_request" role="tab">Solicitações de Afiliação
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="shadow" data-plugin="matchHeight">
                <div class="tab-content">
                    <div class="tab-content">
                        <div id="tab_affiliates_panel" class="tab-pane active" role="tabpanel">
                            <div class="fixhalf"></div>
                            <div class="card shadow " style="min-height: 300px">
                                <table class='table text-left table-striped unify' style='width:100%'>
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Data</th>
                                            <th class="text-center">Porcentagem</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Opções</th>
                                            {{-- excluir, ativar/inativar --}}
                                        </tr>
                                    </thead>
                                    <tbody class="body-table-affiliates">
                                        {{-- js carrega --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="tab_affiliates_request_panel" class="tab-pane" role="tabpanel">
                            <div class="fixhalf"></div>
                            <div class="card shadow " style="min-height: 300px">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Data</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Opções</th>
                                            {{-- aceitar, recusar --}}
                                        </tr>
                                    </thead>
                                    <tbody class="body-table-affiliate-requests">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL --}}
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-affiliate" aria-hidden="true" role="dialog" tabindex="-1">
            <div class="modal-dialog  modal-dialog-centered  modal-simple">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir_affiliate">
                            <i class="material-icons md-16">close</i>
                        </a>
                    </div>
                    <div id="modal_excluir_body_affiliate" class="modal-body text-center p-20">
                        <div class="d-flex justify-content-center">
                            <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                        </div>
                        <h3 class="black"> Você tem certeza? </h3>
                        <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-center">
                        <button type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                        <button type="button" class="col-4 btn btn-danger btn-delete" data-dismiss="modal" style="width: 20%;">Excluir</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- END MODAL --}}
        {{-- MODAL --}}
        <div id="modal-edit-affiliate" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-simple">
                <div class="modal-content p-10">
                    <div class="modal-header simple-border-bottom mb-10">
                        <h4 class="modal-title" id="modal-title">Editar afiliado</h4>
                        <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                            <i class="material-icons md-16">close</i>
                        </a>
                    </div>
                    <div class="modal-body" style='min-height: 100px'>
                        @include('affiliates::edit')
                    </div>
                    <div class="modal-footer">
                        <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                            Fechar
                        </a>
                        <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-update" data-dismiss="modal">
                            <i class="material-icons btn-fix"> save </i> Atualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- END MODAL --}}
    </div>
    @push('scripts')
        <script src="{{asset('modules/affiliates/js/projectaffiliates.js') }}"></script>
    @endpush
@endsection
