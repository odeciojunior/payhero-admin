@extends("layouts.master")

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/global/css/table.css?v='. versionsFile()) }}">
    @endpush
    
    <div class='page'>
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-6">
                    <h1 class="page-title">Afiliação</h1>
                </div>
                <div class="col-6 text-right">
                </div>
            </div>
        </div>
            <div id="project-not-empty" style="display:none !important;">
                <div class="page-content container">
                    <div class="mb-15">
                        <div class="nav-tabs-horizontal" data-plugin="tabs">
                            <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="color: #2E85EC">
                                <li class="nav-item" role="presentation">
                                    <a id="tab-affiliates" class="nav-link active" data-toggle="tab" href="#tab_affiliates_panel"
                                       aria-controls="tab_affiliates" role="tab">Afiliados
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a id="tab-affiliates-request" class="nav-link" data-toggle="tab" href="#tab_affiliates_request_panel" aria-controls="tab_affiliates_request" role="tab">Solicitações de Afiliação
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
                                <div class="card card-body shadow " style="min-height: 300px">
                                    <div class="row mb-15">                                        
                                        <div class="col-lg-5 col-md-6">
                                            <div class="form-group">
                                                <label for="type-products">Projeto</label>
                                                <select class="sirius-select" id="project-affiliate" data-name="projects">
                                                    <option value="0">Todos projetos</option>
                                                </select>
                                            </div>
                                        </div>                                        
                                        <div class="col-lg-5 col-md-6">
                                            <div class="form-group">
                                                <label for="name">Nome do afiliado</label>
                                                <input id="name-affiliate" class="input-pad form-control" placeholder="Nome" maxlength="100" style="height: 50px !important;">
                                            </div>
                                        </div>                
                                        <div class="col-lg-2 col-md-6" style="margin-top: 28px">
                                            <button id="btn-filter-affiliates" class="btn btn-primary w-full px-10" style="height: 50px;">
                                                <img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Aplicar filtros
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <table class='table text-left table-striped unify table-affiliate' style='width:100%'>
                                        <thead>
                                            <tr>
                                                <td class="table-title">Nome</td>
                                                {{-- <td class="table-title">Email</td> --}}
                                                <td class="table-title">Projeto</td>
                                                <td class="table-title">Data afiliação</td>
                                                <td class="text-center table-title">Porcentagem</td>
                                                <td class="text-center table-title">Status</td>
                                                <td class="text-center table-title">Opções</td>
                                                {{-- excluir, ativar/inativar --}}
                                            </tr>
                                        </thead>
                                        <tbody id="body-table-affiliates" img-empty="{!! asset('modules/global/img/afiliados.svg')!!}">
                                            <tr class='text-center'><td colspan='8' style='height: 257px; vertical-align: middle;'>
                                                <img style='width:124px;margin-right:12px;' src='{!! asset('modules/global/img/afiliados.svg')!!}'> Nenhum afiliado encontrado</td>
                                            </tr>
                                            {{-- js carrega --}}
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row d-flex justify-content-center justify-content-md-end">
                                    <ul id="pagination-affiliates" class="pagination-sm pagination-affiliates margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
                                        {{-- js carrega... --}}
                                    </ul>
                                </div>
                            </div>

                            <div id="tab_affiliates_request_panel" class="tab-pane" role="tabpanel">
                                <div class="fixhalf"></div>
                                <div class="card card-body shadow " style="min-height: 300px">
                                    <div class="row mb-15">                                        
                                        <div class="col-lg-5 col-md-6">
                                            <div class="form-group">
                                                <label for="type-products">Projeto</label>
                                                <select class="sirius-select" id="project-affiliate-request" data-name="projects">
                                                    <option value="0">Todos projetos</option>
                                                </select>
                                            </div>
                                        </div>                                        
                                        <div class="col-lg-5 col-md-6">
                                            <div class="form-group">
                                                <label for="name">Nome do afiliado</label>
                                                <input id="name-affiliate-request" class="input-pad form-control" placeholder="Nome" maxlength="100" style="height: 50px !important">
                                            </div>
                                        </div>                
                                        <div class="col-lg-2 col-md-6" style="margin-top: 28px">
                                            <button id="btn-filter-affiliates-request" class="btn btn-primary w-full px-10" style="height: 50px;">
                                                <img style="height: 12px; margin-right: 4px" src="https://sirius.cloudfox.net/modules/global/img/svg/check-all.svg">Aplicar filtros
                                            </button>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-affiliate-request">
                                        <thead>
                                            <tr>
                                                <td class="table-title">Nome</td>
                                                <td class="table-title">Email</td>
                                                <td class="table-title">Projeto</td>
                                                <td class="table-title">Data</td>
                                                {{-- <td class="table-title" class="text-center">Status</td> --}}
                                                <td class="table-title" class="text-center">Opções</td>
                                                {{-- aceitar, recusar --}}
                                            </tr>
                                        </thead>
                                        <tbody id="body-table-affiliate-requests"  img-empty="{!! asset('modules/global/img/afiliados.svg')!!}">
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row d-flex justify-content-center justify-content-md-end">
                                    <ul id="pagination-affiliates-request" class="pagination-sm pagination-affiliates-request margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
                                        {{-- js carrega... --}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- FIM projeto nao existem projetos--}}
        {{-- MODAL --}}
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-affiliate" aria-hidden="true" role="dialog" tabindex="-1">
            <div class="modal-dialog  modal-dialog-centered  modal-simple">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <button type="button" id="btn-close-invite" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div id="modal_excluir_body_affiliate" class="modal-body text-center p-20">
                        <div class="d-flex justify-content-center">
                            <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                        </div>
                        <h3 class="black"> Você tem certeza? </h3>
                        <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                    </div>
                    <div class="modal-footer d-flex align-items-center justify-content-center">
                        <button type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                            <b>Cancelar </b>
                        </button>
                        <button type="button" data-dismiss="modal" class="col-4 btn border-0 btn-delete btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" style="width: 20%;">
                            <b class="mr-2">Excluir </b>
                            <span class="o-bin-1"></span>
                        </button>
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
                        <button type="button" id="btn-close-invite" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
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
        {{-- MODAL DETAILS--}}
        <div id="modal-show-affiliate" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-simple">
                <div class="modal-content p-10">
                    <div class="modal-header simple-border-bottom mb-10">
                        <h4 class="modal-title" id="modal-title">Visualizar afiliado</h4>
                        <button type="button" id="btn-close-invite" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body" style='min-height: 100px'>
                        @include('affiliates::show')
                    </div>
                    <div class="modal-footer">
                        <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                            Fechar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- END MODAL --}}

        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>
    @push('scripts')
        <script src="{{asset('modules/affiliates/js/projectaffiliates.js?v=' . random_int(100, 10000)) }}"></script>
    @endpush
@endsection
