@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/webhooks/index.min.css') }}">
    <link rel="stylesheet" href="{{ mix('build/input-copy-styles.min.css') }}">
    @endpush
    <div class="page new-layout">

        @include('layouts.company-select', ['version' => 'mobile'])

        <div style="display:none"
             class="page-header container pb-0">
            <div class="d-flex justify-content-between align-items-center mb-45">
                <div>
                    <h1 class="page-title my-10"
                        style="min-height:28px">
                        <img src="{{ mix('build/global/img/svg/api-sirius.png') }}"
                             style="height:40px;width:auto"
                             alt="Webhooks">
                        <span style="line-height:40px">Webhooks</span>
                    </h1>
                    <p id="text-info"
                       style="margin-bottom:0">Configure webhooks para integrar com outros sistemas. <a href="https://documenter.getpostman.com/view/32376631/2sAYQZGrLG" target="_blank">Acesse a documentação</a></p>
                </div>
                <div>
                    <button type="button"
                            class="btn btn-floating btn-primary store-webhook"
                            style="display:none;position:relative;float:right;box-shadow:none">
                        <i class="o-add-1"
                           aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="page-content container"
             id="page-webhooks">

            <div id="content-error"
                 class=""
                 style="display:none">
                <div class="content-modal-error text-center">
                    <img src="build/global/img/image-empty-state.svg"
                         width="260px"
                         style="margin-bottom:32px" />
                    <h1 class="m-0"
                        style="color:#636363;font-weight:normal;font-size:24px;line-height:30px">Você ainda
                        não cadastrou nenhum webhook!</h1>
                    <p class="m-0">Configure webhooks para integrar com outros sistemas.</p>
                    <button style="margin:34px auto 0;box-shadow:none"
                            type="button"
                            class="btn btn-floating btn-primary store-webhook">
                        <i class="o-add-1"
                           aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="mb-30"
                 id="content-script"
                 style="display:none">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-8"></div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-4 text-right">
                        {{-- <a href="https://docs.azcend.com.br/" target="_blank" class="font-weight-bold d-flex justify-content-end align-items-center" style="line-height: 46px;">
                        <span>Acesse a documentação da API</span>
                        <span class="o-arrow-right-1 ml-15" style="color: #2E85EC;" aria-hidden="true"></span>
                    </a> --}}
                    </div>
                </div>
            </div>

            <div class="card shadow"
                 id="card-table-webhook"
                 data-plugin="matchHeight"
                 style="display:none">
                <div class="tab-pane active"
                     id="tab_convites_enviados"
                     role="tabpanel">
                    <table class="table table-striped unify">
                        <thead>
                        <tr>
                            <td class="">Descrição</td>
                            <td class="">URL</td>
                            <td class=""></td>
                        </tr>
                        </thead>
                        <tbody id="table-body-webhook">
                        {{-- js webhooks carrega --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="pagination-container-webhooks"
                 class="d-none row no-gutters justify-content-center justify-content-md-end mb-50">

                <ul id="pagination-webhooks"
                    class="pagination-sm pagination-style"
                    style="margin-top:10px;position:relative;float:right">
                    {{-- js pagination carrega --}}
                </ul>

            </div>

            <div class="modal fade modal-new-layout"
                 id="modal-webhook"
                 data-backdrop="static"
                 role="dialog"
                 tabindex="-1">
                <div id="mainModalBody"
                     class="modal-dialog modal-dialog-centered modal-simple">
                    <div class="modal-content">
                        <div id="modal-loader">
                            <div class="modal-header simple-border-bottom">
                                <h4 class="modal-title bold text-center"
                                    style="width: 100%;"
                                    id="modal-title-plan"><span class="ml-15">Cadastrar Webhook</span></h4>
                                <button type="button"
                                        class="close"
                                        data-dismiss="modal"
                                        aria-label="Close">
                                    <i class="material-icons md-22">close</i>
                                </button>
                            </div>
                            <div id="modal-reverse-body"
                                 class="modal-body">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label for="description">Descrição</label>
                                        <input name="description"
                                               type="text"
                                               class="form-control form-control-lg"
                                               id="description"
                                               placeholder="Digite uma descrição para seu webhook">
                                    </div>
                                </div>
                                <div class="row companies-container">
                                    <div class="form-group col-sm-12 col-md">
                                        <label for="empresa">Empresa</label>
                                        <input type="text"
                                               disabled=""
                                               class="form-control company_name">
                                    </div>
                                </div>
                                <div class="row url-container">
                                    <div class="form-group col-sm-12 col-md">
                                        <label for="url">URL para receber os webhooks</label>
                                        <input name="url"
                                               type="text"
                                               class="form-control form-control-lg"
                                               id="url"
                                               placeholder="Digite uma URL válida">
                                        <small class="text-muted">Digite uma URL válida</small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button id="btn-save-webhook"
                                        type="button"
                                        class="btn btn-lg btn-primary">Cadastrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade modal-new-layout"
         id="modal-edit-webhook"
         data-backdrop="static"
         role="dialog"
         tabindex="-1">
        <div id="mainModalBody"
             class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content">
                <div id="modal-loader">
                    <div class="modal-header simple-border-bottom">
                        <h4 class="modal-title bold text-center"
                            style="width: 100%;"
                            id="modal-title-plan"><span class="ml-15">Editar Webhook</span></h4>
                        <button type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                            <i class="material-icons md-22">close</i>
                        </button>
                    </div>
                    <div id="modal-reverse-body"
                         class="modal-body">
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="description">Descrição</label>
                                <input name="description"
                                       type="text"
                                       class="form-control form-control-lg"
                                       id="description_edit"
                                       placeholder="Digite uma descrição para seu webhook">
                            </div>
                        </div>
                        <div class="row companies-container">
                            <div class="form-group col-sm-12 col-md">
                                <label for="empresa">Empresa</label>
                                <input type="text"
                                       disabled=""
                                       class="form-control company_name">
                            </div>
                        </div>
                        <div class="row url-container">
                            <div class="form-group col-sm-12 col-md">
                                <label for="url">URL para receber os webhooks</label>
                                <input name="url"
                                       type="text"
                                       class="form-control form-control-lg"
                                       id="url_edit"
                                       placeholder="Digite uma URL válida">
                                <small class="text-muted">Digite uma URL válida</small>
                            </div>
                        </div>

                        <!-- X-Signature Section -->
                        <div class="row">
                            <x-copy-input
                                label="X-Signature"
                                id="x-signature-edit"
                                name="x-signature"
                                value=""
                                placeholder="Cabeçalho de Assinatura"
                                tooltip="Copiar Cabeçalho Assinatura"
                                description="Todas as solicitações enviadas aos seus endpoints de webhook são assinadas para garantir que você
                                        possa verificar se o tráfego está realmente vindo da Azcend.
                                        Esta assinatura é incluída no X-Signature cabeçalho HTTP, permitindo que o cliente verifique se ela
                                        foi criada usando o mesmo segredo."
                            />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden"
                               id="webhook_id"
                               name="webhook_id">
                        <button id="btn-edit-webhook"
                                type="button"
                                class="btn btn-lg btn-primary">Atualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade example-modal-lg modal-new-layout"
         id="modal-delete-webhook"
         data-backdrop="static"
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
                           style="font-size: 80px;color: #ff4c52;">highlight_off</i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <input type="hidden"
                           id="webhook_id"
                           name="webhook_id">
                    <button id="btn-cancel-webhook"
                            type="button"
                            class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                            data-dismiss="modal"
                            style="width: 20%;">
                        <b>Cancelar</b>
                    </button>
                    <button id="btn-delete-webhook"
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

    @push('scripts')
        <script src="{{ mix('build/layouts/webhooks/index.min.js') }}"></script>
    @endpush
@endsection
