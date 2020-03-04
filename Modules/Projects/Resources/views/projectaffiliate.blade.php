@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css?v=1') }}">
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
        <div class="page-content container">
            <div class="mb-15">
                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="color: #ee535e">
                        <li class="nav-item" role="presentation">
                            <a id="tab-info" class="nav-link active" data-toggle="tab" href="#tab_info_geral"
                               aria-controls="tab_info_geral" role="tab">Informações gerais
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab_pixels" class="nav-link" data-toggle="tab" href="#tab_pixels-panel"
                               aria-controls="tab_pixels" role="tab">Pixels
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab_links" class="nav-link" data-toggle="tab" href="#tab_links-panel" aria-controls="tab_links" role="tab">
                                Links
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a id="tab_settings_affiliate" class="nav-link" data-toggle="tab" href="#tab_settings_affiliate-panel" aria-controls="tab_settings_affiliate" role="tab">
                                Configurações
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
                                                <div class="col-md-2">
                                                    <div class="d-flex">
                                                        <div class="p-2 d-flex flex-column">
                                                            <span class="details-text">Status</span>
                                                            <span id="show-status" class="card-text sm badge-pill"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class='row'>
                                                <div class='col-md-6'>
                                                    <h5 class=""><strong> Descrição </strong></h5>
                                                    <p id="show-description" class="card-text sm"></p>
                                                    <h5 class=""><strong> Produtor </strong></h5>
                                                    <p id="show-producer" class="card-text sm"></p>
                                                    <h5 class=""><strong> Comissão </strong></h5>
                                                    <p id="show-commission" class="card-text sm"></p>
                                                </div>
                                                <div class='col-md-6'>
                                                    <h5 class=""><strong> Dias para liberar dinheiro </strong></h5>
                                                    <p class="card-text sm">Cartão de débito: <span id='show-debit-release'></span></p>
                                                    <p class="card-text sm">Cartão de crédito: <span id='show-credit-release'></span></p>
                                                    <p class="card-text sm">Boleto: <span id='show-billet-release'></span></p>

                                                    <h5 class=""><strong> Duração do cookie </strong></h5>
                                                    <p id="show-cookie-duration" class="card-text sm"></p>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Painel de Pixels -->
                        <div class="tab-pane" id="tab_pixels-panel" role="tabpanel">
                            @include('pixels::index')
                        </div>
                        <!--- Painel de Planos -->
                        <div class="tab-pane" id="tab_links-panel" role="tabpanel">
                            @include('affiliates::links')
                        </div>
                        <!--- Painel de Configurações -->
                        <div class="tab-pane" id="tab_settings_affiliate-panel" role="tabpanel">
                            @include('projects::editprojectaffiliate')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{asset('modules/pixels/js/pixelsaffiliate.js?v=1')}}"></script>
        <script src="{{asset('modules/projects/js/projectaffiliate.js?v=8')}}"></script>
        <script src="{{asset('modules/affiliates/js/links.js?v=9')}}"></script>
    @endpush
@endsection

