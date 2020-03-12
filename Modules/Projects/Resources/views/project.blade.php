@extends("layouts.master")
 
@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css?v=1') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/modules/projects/css/style.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
@endpush

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title" style="min-height: 28px"></h1>
            <div class="page-header-actions">
                <a class="btn btn-success float-right" href="/projects">
                    Meus projetos
                </a>
            </div>
        </div>
        <div class="page-content container page-project">
            <div class="mb-15">
                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="color: #ee535e">
                        <li class="nav-item" role="presentation">
                            <a id="tab-info" class="nav-link active" data-toggle="tab" href="#tab_info_geral"
                               aria-controls="tab_info_geral" role="tab">Informações gerais
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab-domains" class="nav-link" data-toggle="tab" href="#tab_domains"
                               aria-controls="tab_cupons" role="tab">Domínios
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab_pixels" class="nav-link" data-toggle="tab" href="#tab_pixels-panel"
                               aria-controls="tab_pixels" role="tab">Pixels
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id='tab_coupons' class="nav-link" data-toggle="tab" href="#tab_coupons-panel"
                               aria-controls="tab_coupons" role="tab">Cupons
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id='tab_sms' class="nav-link" data-toggle="tab" href="#tab_sms-panel"
                               aria-controls="tab_coupons" role="tab">Notificações
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab-fretes" class="nav-link" data-toggle="tab" href="#tab-fretes-panel"
                               aria-controls="tab-fretes" role="tab">Frete
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab_plans" class="nav-link" data-toggle="tab" href="#tab_plans-panel" aria-controls="tab_plans" role="tab">
                                Planos
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab_upsell" class="nav-link" data-toggle="tab" href="#tab_upsell-panel" aria-controls="tab_plans" role="tab">
                                Upsell
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab_configuration" class="nav-link" data-toggle="tab" href="#tab_configuration_project"
                               aria-controls="tab_configuration_project" role="tab">Configurações
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="shadow" data-plugin="matchHeight">
                <div class="tab-content">
                    <div class="tab-content">
                        <!-- Painel de informações gerais -->
                        <div class="tab-pane active" id="tab_info_geral" role="tabpanel">
                            <div class="card">
                                <div class="row no-gutters">
                                    <div class="col-md-3">
                                        <img id="show-photo" class="card-img" src="" alt="">
                                    </div>
                                    <div class="col-md-9 pl-10">
                                        <div class="card-body">
                                            <div class="row justify-content-between align-items-baseline">
                                                <div class="col-md-6">
                                                    <h4 class="title-pad"></h4>
                                                    <p class="card-text sm" id="created_at"></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex">
                                                        <div class="p-2 d-flex flex-column">
                                                            <span class="details-text">Visibilidade</span>
                                                            <p id="show-visibility" class="card-text text-center sm badge-pill"></p>
                                                        </div>
                                                        <div class="p-2 d-flex flex-column">
                                                            <span class="details-text">Status</span>
                                                            <p id="show-status" class="card-text sm badge-pill"></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="sm-title"><strong> Descrição </strong></h5>
                                            <p id="show-description" class="card-text sm"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Painel de Dominios -->
                        <div id="tab_domains" class="tab-pane" role="tabpanel">
                            @include('domains::index')
                        </div>
                        <!-- Painel de Pixels -->
                        <div class="tab-pane" id="tab_pixels-panel" role="tabpanel">
                            @include('pixels::index')
                        </div>
                        <!-- Painel de Cupons de Descontos -->
                        <div class="tab-pane" id="tab_coupons-panel" role="tabpanel">
                            @include('discountcoupons::index')
                        </div>
                        <!-- Painel de Sms -->
                        <div class="tab-pane" id="tab_sms-panel" role="tabpanel">
                            @include('projectnotification::index')
                        </div>
                        <!-- Painel de Fretes -->
                        <div class="tab-pane" id="tab-fretes-panel" role="tabpanel">
                            @include('shipping::index')
                        </div>
                        <!--- Painel de Planos -->
                        <div class="tab-pane" id="tab_plans-panel" role="tabpanel">
                            @include('plans::index')
                        </div>
                        <div class="tab-pane" id="tab_upsell-panel" role="tabpanel">
                            @if(env('APP_ENV') == 'local')
                                @include('projectupsellrule::index')
                            @else
                                <div class="card shadow" style='height:300px;'>
                                    <div class="text-center mt-100">
                                        <h2>Em desenvolvimento!</h2>
                                        <img style="width:60px; margin-bottom: 20px;" src="{!! asset('modules/global/img/tools.svg') !!}">
                                    </div>
                                </div>
                            @endif
                        </div>
                        <!-- Painel de Parceiros -->
                        <div class="tab-pane" id="tab_partners" role="tabpanel">
                            @include('partners::index')
                        </div>
                        <!-- Painel de Configurações  Abre a tela edit-->
                        <div class="tab-pane" id="tab_configuration_project" role="tabpanel">
                            @include('projects::edit')
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
        <script src="{{asset('modules/partners/js/partners.js?v=1')}}"></script>
        <script src="{{asset('modules/shipping/js/shipping.js?v=2')}}"></script>
        <script src="{{asset('modules/domain/js/domainEdit.js?v=1')}}"></script>
        <script src="{{asset('modules/project-notification/js/projectNotification.js?v=1')}}"></script>
        <script src="{{asset('modules/pixels/js/pixels.js?v=2')}}"></script>
        <script src="{{asset('modules/discount-coupons/js/discountCoupons.js?v=2')}}"></script>
        <script src="{{asset('modules/projects/js/projects.js?v=10')}}"></script>
        <script src="{{asset('modules/plans/js/plans.js?v=3')}}"></script>
        <script src="{{asset('modules/projectupsell/js/index.js?v=1')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
        <script src="https://cdn.ckeditor.com/4.13.1/standard/ckeditor.js"></script>
    @endpush
@endsection

