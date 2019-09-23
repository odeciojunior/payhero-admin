@extends("layouts.master")

@push('css')
    <style type='text/css'>
        /* SWITCH CONFIG */
        label.switch {
            margin-bottom: 0 !important;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 35px;
            height: 15px;
            margin-right: 15px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: -3px;
            top: -2px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            box-shadow: 0 10px 10px 0 rgba(0, 0, 0, 0.15);
        }
        input:checked + .slider {
            background-color: #f78d1e;
        }
        input:focus + .slider {
            box-shadow: 0 0 1px #f78d1e;
        }
        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }
        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }
        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endpush

@section('styles')

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

@endsection

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Projeto {{ $project->name }}</h1>
            <div class="page-header-actions">
                <a class="btn btn-success float-right" href="/projects">
                    Meus projetos
                </a>
            </div>
        </div>
        <div class="page-content container">
            <input type='hidden' id='project-id' value='{{Hashids::encode($project->id)}}'/>
            <div class="mb-15">
                <div class="nav-tabs-horizontal" data-plugin="tabs">
                    <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="color: #ee535e">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-toggle="tab" href="#tab_info_geral"
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
                        {{--                        @if($project->shopify_id == '')--}}
                        <li class="nav-item" role="presentation">
                            <a id="tab_plans" class="nav-link" data-toggle="tab" href="#tab_plans-panel" aria-controls="tab_plans" role="tab">
                                Planos
                            </a>
                        </li>
                        {{--                        @endif--}}
                        {{--<li class="nav-item" role="presentation">--}}
                        {{--<a id='tab-partners' class="nav-link" data-toggle="tab" href="#tab_partners"--}}
                        {{--aria-controls="tab_partners" role="tab">Parceiros--}}
                        {{--</a>--}}
                        {{--</li>--}}
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
                                        <img src="{{ $project->photo ? $project->photo : '/modules/global/img/projeto.png' }}" class="card-img" alt="">
                                    </div>
                                    <div class="col-md-9 pl-10">
                                        <div class="card-body">
                                            <div class="row justify-content-between align-items-baseline">
                                                <div class="col-md-6">
                                                    <h4 class="title-pad">{{ $project->name }}</h4>
                                                    <p class="card-text sm"> Criado em {{$project->created_at->format('d/m/Y')}} </p>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="d-flex">
                                                        <div class="p-2 d-flex flex-column">
                                                            <span class="details-text">Visibilidade</span>
                                                            <p @if($project->visibility == 'public') class="card-text text-center sm badge-pill badge-primary" @else class="card-text text-center sm badge-pill badge-danger" @endif> {{ ($project->visibility == 'public') ? 'Público' : 'Privado' }} </p>
                                                        </div>
                                                        <div class="p-2 d-flex flex-column">
                                                            <span class="details-text">Status</span>
                                                            <p @if($project->status) class="card-text sm badge-pill badge-primary" @else class="card-text sm badge-pill badge-danger" @endif> {{ $project->status ? 'Ativo' : 'Inativo' }} </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <h5 class="sm-title"><strong> Descrição </strong></h5>
                                            <p class="card-text sm">
                                                {{ $project->description }}
                                            </p>
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
                            @include('sms::index')
                        </div>
                        <!-- Painel de Fretes -->
                        <div class="tab-pane" id="tab-fretes-panel" role="tabpanel">
                            @include('shipping::index')
                        </div>
                        <!--- Painel de Planos -->
                        <div class="tab-pane" id="tab_plans-panel" role="tabpanel">
                            @include('plans::index')
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

                    <!-- Modal padrão para adicionar Adicionar e Editar -->
{{--                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-content" role="dialog" tabindex="-1">--}}
{{--                        <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple">--}}
{{--                            <div class="modal-content p-10" id="conteudo_modal_add">--}}
{{--                                <div class="modal-header simple-border-bottom mb-10">--}}
{{--                                    <h4 class="modal-title" id="modal-title"></h4>--}}
{{--                                    <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">--}}
{{--                                        <i class="material-icons md-16">close</i>--}}
{{--                                    </a>--}}
{{--                                </div>--}}
{{--                                <div id="modal-add-body" class="modal-body" style='min-height: 100px'>--}}
{{--                                </div>--}}
{{--                                <div class="modal-footer">--}}
{{--                                    <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">--}}
{{--                                        Fechar--}}
{{--                                    </a>--}}
{{--                                    <button id="btn-modal" type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success" data-dismiss="modal">--}}
{{--                                        <i class="material-icons btn-fix"> save </i> Salvar--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <!-- Modal padrão para excluir -->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
                                    <button id='bt_cancel' type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                                    <button id="bt_excluir" type="button" class="col-4 btn btn-danger" style="width: 20%;">Excluir</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($project->shopify_id)
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
                    @endif
                </div>
            </div>
        </div>
    </div>

    <span id='shopifyIdLabel' data-shopifyId='{{$project->shopify_id}}'></span>

    @push('scripts')
        <script src="{{asset('modules/partners/js/partners.js')}}"></script>
        <script src="{{asset('modules/shipping/js/shipping.js')}}"></script>
        <script src="{{asset('modules/domain/js/domain.js')}}"></script>
        <script src="{{asset('modules/sms-message/js/smsMessage.js')}}"></script>
        <script src="{{asset('modules/pixels/js/pixels.js')}}"></script>
        <script src="{{asset('modules/discount-coupons/js/discountCoupons.js')}}"></script>
        <script src="{{asset('modules/projects/js/projects.js')}}"></script>
        <script src="{{asset('modules/plans/js/plans.js')}}"></script>
    @endpush
@endsection

