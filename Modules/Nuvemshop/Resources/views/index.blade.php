@extends('layouts.master')

@push('css')
    <link rel="stylesheet"
          href="{{ mix('build/layouts/nuvemshop/index.min.css') }}">
@endpush

@section('content')

    <div class="page">

        @include('layouts.company-select', ['version' => 'mobile'])

        <div class="page-header container">
            <div class="row jusitfy-content-between"
                 style="min-height:56px">
                <div class="col-lg-8  align-items-center">
                    <h1 class="page-title my-10"
                        style="min-height: 28px">
                        <a href="/apps">
                            <span class="o-arrow-right-1 font-size-30 ml-2"
                                  aria-hidden="true"></span>
                            Integrações com Nuvemshop
                        </a>
                    </h1>
                </div>
                <div class="col text-right"
                     id="integration-actions"
                     style="display:none">
                    <a data-toggle="modal"
                       id='btn-integration-model'
                       class="btn btn-floating btn-primary ml-10"
                       style="position: relative;float: right;color: white;display: flex;text-align: center;align-items: center;justify-content: center;">
                        <i class="o-add-1"
                           aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="page-content container">
            <div class="row"
                 id="content">
                {{-- js load dynamically --}}
            </div>
        </div>

        <div class="modal fade modal-3d-flip-vertical"
             id="modal_add_integration"
             role="dialog"
             tabindex="-1">
            <div class="modal-dialog modal-lg d-flex justify-content-center">
                <div class="modal-content w-450"
                     id="content_modal_add">
                    <div class="modal-header">
                        <button type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title"
                            style="font-weight: 700;"></h4>
                    </div>
                    <div class="px-20 modal_integration_body">
                        <form id='form_add_integration'
                              method="post"
                              action="#">
                            @csrf
                            <div style="width:100%">
                                <div class="row"
                                     style="margin-top:30px">
                                    <div class="input-group col-12">
                                        <label for="url_store">URL da sua loja Nuvemshop</label>
                                        <div class="d-flex input-group">
                                            <input type="text"
                                                   class="input-pad col-6 addon"
                                                   name="url_store"
                                                   id="url_store"
                                                   placeholder="Digite a URL da sua loja">
                                            <span class="d-flex align-items-center input-group-addon input-pad col-lg-6 font-size-16 p-1">.lojavirtualnuvem.com.br</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row"
                                     style="margin-top:30px">
                                    <div class="col-12">
                                        <label for="company">Empresa</label>
                                        <input type="text" disabled class="company_name"
                                               style="text-overflow: ellipsis;">
                                        <input type="hidden" name="company" id="company-navbar-value">
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer"
                         style="margin-top: 15px">
                        <button id="bt_integration"
                                type="button"
                                class="btn btn-success"
                                data-dismiss="modal"></button>
                        <button type="button"
                                class="btn btn-primary"
                                data-dismiss="modal">Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-3d-flip-vertical" id="modal-configs">
            <div class="modal-dialog modal-lg d-flex justify-content-center">
                <div class="modal-content w-450">
                    <div class="modal-header">
                        <button type="button"
                                class="close"
                                data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="font-weight-700">Configurações da Integração</h4>
                    </div>
                    <div class="modal-body pt-0">
                        <input type="hidden" id="configs-project-id">
                        <input type="hidden" id="configs-integration-id">
                        <div class="d-flex align-items-center">
                            <img id="configs-project-image" onerror="this.src = '/build/global/img/produto.svg'" />
                            <div id="configs-project-name" class="font-weight-bold font-size-18"></div>
                        </div>

                        <div class="d-flex align-items-end justify-content-between gap-1">
                            <div>
                                <div id="authorize-title" class="font-size-16 font-weight-bold pt-20" >
                                    Autorização
                                </div>
                                <small>Autorize o aplicativo Azcend em sua loja Nuvemshop</small>
                            </div>

                            <a class="btn btn-sm btn-primary mt-20" id="btn-authorize" target="_self">Autorizar</a>
                        </div>

                        <div  class="font-size-16 font-weight-bold mt-20" >
                            Sincronizar
                        </div>
                        <small>Escolha a opção que deseja sincronizar</small>

                        <div class="d-flex align-items-center justify-content-around gap-1 pt-20">
                            <div class="btn-sync" id="btn-sync-products">Produtos</div>
                            <div class="btn-sync" id="btn-sync-trackings">Rastreios</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('nuvemshop::not-integration')
    </div>



    @push('scripts')
        <script src="{{ mix('build/layouts/nuvemshop/index.min.js') }}"></script>
    @endpush
@endsection
