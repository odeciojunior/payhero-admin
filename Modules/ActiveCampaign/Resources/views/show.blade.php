@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endpush

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title" style="min-height: 28px"></h1>
            <div class="page-header-actions">
                <a class="btn btn-success float-right" href="/apps/activecampaign">
                    Minhas integrações ActiveCampaign
                </a>
            </div>
        </div>
        <div class="page-content container">
            <div class="mb-15">
                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="color: #ee535e">
                        <li class="nav-item" role="presentation">
                            <a id="tab_configuration" class="nav-link active" data-toggle="tab" href="#tab_configuration_integration"
                               aria-controls="tab_configuration_integration" role="tab">Configurações
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab_events" class="nav-link" data-toggle="tab" href="#tab_events-panel" aria-controls="tab_events" role="tab">
                                Eventos
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="shadow" data-plugin="matchHeight">
                <div class="tab-content">
                    <div class="tab-content">
                        <!--- Painel de Eventos -->
                        <div class="tab-pane" id="tab_events-panel" role="tabpanel">
                            @include('activecampaign::events')
                        </div>
                        <!-- Painel de Configurações  Abre a tela edit-->
                        <div class="tab-pane active" id="tab_configuration_integration" role="tabpanel">
                            @include('activecampaign::edit')
                        </div>
                    </div>
                    <!-- Modal para fazer-desfazer integração com shopify -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-change-shopify-integration" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div class="modal-dialog  modal-dialog-centered  modal-simple">
                            <div class="modal-content">
                                <div class="modal-header text-center">
                                    <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="bt-close-modal-change-shopify-integration">
                                        <i class="material-icons md-16">close</i>
                                    </a>
                                </div>
                                <div class="modal-body text-center p-20">
                                    <div class="d-flex justify-content-center">
                                        <i class="material-icons gradient" style="font-size: 70px;color: #ff4c52; margin-bottom: 30px"> sync </i>
                                    </div>
                                    <h3 class="black" id="modal-change-shopify-integration-title"> Você tem certeza? </h3>
                                    <p class="gray" id="modal-change-shopify-integration-text"></p>
                                </div>
                                <div class="modal-footer d-flex align-items-center justify-content-center">
                                    <button type="button" class="btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                                    <button id="bt-modal-change-shopify-integration" type="button" class="btn btn-success" style="width: 20%;">Confirmar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('modules/activecampaign/js/edit.js?v=1')}}"></script>
        <script src="{{asset('modules/activecampaign/js/events.js?v=1')}}"></script>
    @endpush
@endsection

