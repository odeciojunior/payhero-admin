@extends("layouts.master")

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
                        @if($project->shopify_id == '')
                            <li class="nav-item" role="presentation">
                                <a id="tab_plans" class="nav-link" data-toggle="tab" href="#tab_plans-panel" aria-controls="tab_plans" role="tab">
                                    Planos
                                </a>
                            </li>
                        @endif
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
                                        <img src="{{ $project->photo }}" class="card-img" alt="">
                                    </div>
                                    <div class="col-md-9 pl-10">
                                        <div class="card-body">

                                            <div class="row justify-content-between align-items-baseline">
                                                <div class="col-md-6">
                                                    <h4 class="title-pad">{{ $project->name }}</h4>
                                                    <p class="card-text sm"> Criado em 14/06/2019 </p>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="d-flex">
                                                        <div class="p-2 d-flex flex-column">
                                                            <span class="details-text">Visibilidade</span>
                                                            <p class="card-text sm"> {{ ($project->visibility == 'public') ? 'Público' : 'Privado' }} </p>

                                                        </div>

                                                        <div class="p-2 d-flex flex-column">
                                                            <span class="details-text">Status</span>
                                                            <p class="card-text sm"> {{ $project->status ? 'Ativo' : 'Inativo' }} </p>

                                                        </div>
                                                    </div>

                                                </div>

                                            </div>


                                            <h5 class="sm-title mt-30"> <strong> Descrição </strong> </h5>
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
                        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-content" aria-hidden="true" aria-labelledby="exampleModalTitle"
                             role="dialog" tabindex="-1">
                            <div id="modal_add_size" class="modal-dialog modal-simple">
                                <div class="modal-content" id="conteudo_modal_add">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <h4 id="modal-title" class="modal-title" style="width: 100%; text-align:center"></h4>
                                    <div class="row">
                                        <div id="modal-add-body" class="form-group col-12">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button id="btn-modal" type="button" class="btn btn-success" data-dismiss="modal"></button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modal padrão para excluir -->
                        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                            <div class="modal-dialog modal-simple">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                        <h4 id="modal_excluir_titulo" class="modal-title" style="width: 100%; text-align:center">Excluir ?</h4>
                                    </div>
                                    <div id="modal_excluir_body" class="modal-body">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                        <button id="bt_excluir" type="button" class="btn btn-success">Confirmar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        @push('scripts')
            <script src='{{asset('modules/partners/js/partners.js')}}'></script>
            <script src='{{asset('modules/Shipping/js/shipping.js')}}'></script>
            <script src='{{asset('modules/domain/js/domain.js')}}'></script>
            <script src='{{asset('modules/SmsMessage/js/smsMessage.js')}}'></script>
            <script src='{{asset('modules/Pixels/js/pixels.js')}}'></script>
            <script src='{{asset('modules/DiscountCoupons/js/discountCoupons.js')}}'></script>
            <script src='{{asset('modules/projects/js/projects.js')}}'></script>
            <script src='{{asset('modules/plans/js/plans.js')}}'></script>
    @endpush
@endsection

