@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ mix('build/layouts/integrations/index.min.css') }}">
    @endpush
    <div class="page new-layout">
        <div style="display: none" class="page-header container pb-0">
            <div class="d-flex justify-content-between align-items-center mb-45">
                <div>
                    <h1 class="page-title my-10" style="min-height: 28px">
                        <img src="{{ mix('build/global/img/svg/api-sirius.png') }}" style="height: 40px; width: auto;"
                            alt="API">
                        <span style="line-height: 40px;">API</span>
                    </h1>
                    <p id='text-info' style="margin-bottom: 0;">Use nosso checkout API ou faça integrações externas.</p>
                </div>
                <div>
                    <button type="button" class="btn btn-floating btn-primary store-integrate"
                        style="display: none; position: relative; float: right; box-shadow: none;">
                        <i class="o-add-1" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="page-content container" id='page-integrates'>
            <div id="content-error" class='' style='display: none;'>
                <div class="content-modal-error text-center">
                    <img src="build/global/img/image-empty-state.svg" width="260px" style="margin-bottom: 32px;" />
                    <h1 class="m-0" style="color: #636363; font-weight: normal; font-size: 24px; line-height: 30px;">Você
                        ainda não
                        cadastrou nenhuma integração!</h1>
                    <p class="m-0">Use nossa checkout API ou faça uma integração externa para sua loja.</p>

                    <button style="margin: 34px auto 0; box-shadow: none;" type="button"
                        class="btn btn-floating btn-primary store-integrate">
                        <i class="o-add-1" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="mb-30" id="content-script" style='display:none;'>
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-8">
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-primary">
                                    <span class="icon-antifraud-1 mr-5"></span> Script do Antifraude para Checkout API
                                </span>
                            </div>
                            <input type="text" class="form-control" id="input-url-antifraud" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-primary bg-white btnCopiarLinkAntifraud" type="button"
                                    data-toggle="tooltip" title="Copiar URL antifraud"
                                    style="width: 46px; border-left: 1px solid #F4F4F4;">
                                    <img src="/build/global/img/icon-copy-b.svg">
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-4 text-right">
                        <a href="https://docs.cloudfox.net/" target="_blank"
                            class="font-weight-bold d-flex justify-content-end align-items-center"
                            style="line-height: 46px;">
                            <span>Acesse a documentação da API</span>
                            <span class="o-arrow-right-1 ml-15" style="color: #2E85EC;" aria-hidden="true"></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow" id='card-table-integrate' data-plugin="matchHeight" style='display:none;'>
                <div class="tab-pane active" id="tab_convites_enviados" role="tabpanel">
                    <table class="table table-striped unify">
                        <thead>
                            <tr>
                                <td class="table-title">Descrição</td>
                                <td class="table-title text-center">Tipo</td>
                                <td class="table-title">Token</td>
                                <td class="table-title"></td>
                            </tr>
                        </thead>
                        <tbody id='table-body-integrates'>
                            {{-- js integrates carrega --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <ul id="pagination-integrates" class="pagination-sm margin-chat-pagination"
                style="margin-top:10px;position:relative;float:right">
                {{-- js pagination carrega --}}
            </ul>

            <!-- Modal para criar integração -->
            <div class="modal fade modal-new-layout" id="modal-integrate" role="dialog" tabindex="-1">
                <div id='mainModalBody' class="modal-dialog modal-dialog-centered modal-simple">
                    <div id="modal-create-integration" class="modal-content">
                        <div class="modal-header simple-border-bottom">
                            <h4 class="modal-title bold text-center" style="width: 100%;" id="modal-title-plan"><span
                                    class="ml-15">Nova Integração</span></h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="material-icons md-22">close</i>
                            </button>
                        </div>

                        <div id="modal-reverse-body" class="modal-body">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="description">Nome da integração</label>
                                    <input name="description" type="text" class="form-control form-control-lg"
                                        id="description" placeholder="Dê um nome para sua integração">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="token_type_enum">Tipo de Integração</label>
                                    <div id="enum-list">
                                        <select name="token_type_enum" id="select-enum-list" class="sirius-select">
                                            <option value="3">Integração Externa</option>
                                            <option value="4" selected>Checkout API</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row companies-container">
                                <div class="form-group col-sm-12 col-md">
                                    <label for="empresa">Empresa</label>
                                    <select name="company_id" id="companies" class="sirius-select">
                                        <option value="">Selecione a empresa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row postback-container">
                                <div class="form-group col-sm-12 col-md">
                                    <label for="postback">Postback</label>
                                    <input name="postback" type="text" class="form-control form-control-lg"
                                        id="postback" placeholder="Insira a URL de postback">
                                    <small class="text-muted">Insira uma URL válida para receber as notificações referentes
                                        a integração</small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button id="btn-save-integration" type="button" class="btn btn-lg btn-primary">Gerar
                                Token</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar integração -->
    <div class="modal fade modal-new-layout" id="modal-edit-integration" role="dialog" tabindex="-1">
        <div id='mainModalBody' class="modal-dialog modal-dialog-centered modal-simple">
            <div id="modal-create-integration" class="modal-content">
                <div class="modal-header simple-border-bottom">
                    <h4 class="modal-title bold text-center" style="width: 100%;" id="modal-title-plan"><span
                            class="ml-15">Editar Integração</span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="material-icons md-22">close</i>
                    </button>
                </div>

                <div id="modal-reverse-body" class="modal-body">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="description">Nome da integração</label>
                            <input name="description" type="text" class="form-control form-control-lg"
                                id="description" placeholder="Dê um nome para sua integração">
                        </div>
                    </div>
                    <div class="row postback-container" style="display: none;">
                        <div class="form-group col-sm-12 col-md">
                            <label for="postback">Postback</label>
                            <input name="postback" type="text" class="form-control form-control-lg" id="postback"
                                placeholder="Insira a URL de postback">
                            <small class="text-muted">Insira uma URL válida para receber as notificações referentes a
                                integração</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <input type="hidden" name="token_type_enum">
                    <button id="btn-edit-integration" type="button" class="btn btn-lg btn-primary">Atualizar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal padrão para excluir -->
    <div class="modal fade example-modal-lg modal-new-layout" id="modal-delete-integration" aria-hidden="true"
        aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>

                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id='btn-cancel-integration' type="button"
                        class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        data-dismiss="modal" style="width: 20%;">
                        <b>Cancelar</b>
                    </button>
                    <button id="btn-delete-integration" type="button"
                        class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        data-dismiss="modal" style="width: 20%;">
                        <b class="mr-2">Excluir </b>
                        <span class="o-bin-1"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para reenviar convite -->
    <div class="modal fade example-modal-lg modal-new-layout" id="modal-refresh-integration" aria-hidden="true"
        aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons" style="font-size: 80px;color:#16b248;"> loop </i>
                    </div>
                    <h4 class="black"> Você realmente deseja regerar o token? </h4>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button id='btn-cancel' type="button" class="col-4 btn btn-gray" data-dismiss="modal"
                        style="width: 20%;">Cancelar</button>
                    <button id="btn-refresh-integration" type="button" class="col-4 btn btn-success"
                        style="width: 20%;" data-dismiss="modal">Regerar</button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ mix('build/layouts/integrations/index.min.js') }}"></script>
    @endpush
@endsection
