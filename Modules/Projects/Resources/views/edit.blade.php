@push('css')
<link rel="stylesheet" href="{{ mix('build/layouts/projects/edit.min.css') }}">
@endpush

<div class='row card no-gutters p-30 rounded-top'>

    <div class="col-12 font-size-24 pl-0 mb-10">
        Configurações
    </div>

    <div class="col-md-12">
        <div class="badge badge-primary font-size-14 mr-10">
            NOVO
        </div>

        <span class="font-size-16">Repaginamos nossa área de configurações de loja. Para encontrar configurações de
            checkout, use o nosso novo editor.</span>
    </div>

</div>

<form id='update-project'>
    @method('PUT')
    @csrf

    <!-- IDENTIFICACAO -->
    <div class='card col-md-12'>

        <div class="row">
            <!-- TITULO CABECALHO -->
            <div class="col-md p-0">
                <div class="row no-gutters d-flex d-flex align-items-center pt-30 pb-20 pl-30">
                    <div class="bg-afiliate-icon mr-15">
                        <img src="{{ mix('build/global/img/projects/imgIcon.svg') }}" class="p-10">
                    </div>
                    <h3 class="mb-0">Identificação</h3>
                </div>
            </div>
        </div>

        <div class="row no-gutters z-index-0">
            <!-- FOTO -->
            <div class="col-md-5 col-lg-4 col-xl-3 px-15 pl-xl-15 d-flex flex-column" id='div-img-project' style='position: relative;'>

                <label for='project_photo' class="pl-0 pl-lg-10 pl-xl-0 mb-3 font-size-16">Capa da loja</label>

                <div style="width:100%" class="text-center">
                    <input type="file" id="project_photo" name="project_photo" data-height="651" data-max-width="651" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                </div>
            </div>

            <!-- NOME E DESCRICAO -->
            <div class="col-md-7 col-lg-8 col-xl-9 pl-10 pr-sm-50">

                <div class='form-group col-md-12'>
                    <label for='name' class="font-size-16">Nome da loja</label>
                    <input name='name' value="" type='text' class='input-pad font-size-16 name-project' id='name' placeholder='Nome da loja' maxlength='40' style="outline: none;" />
                    <span id='name-error' class='text-danger'></span>
                    <p class='info pt-5' style='font-size: 10px;'></p>
                </div>

                <div class='form-group col-lg-12'>
                    <label for='description' class="font-size-16">Descrição</label>
                    <textarea style='height:100px;' name='description' type='text' class='input-pad font-size-16' id='description' placeholder='Fale um pouco sobre sua loja' maxlength='100'></textarea>
                    <span id='description-error' class='text-danger'></span>
                    <p class="pt-25 mb-0 font-size-12">Recomendações: Imagem de 300x300px | Formatos: JPEG ou PNG</p>
                </div>

            </div>

        </div>
    </div>

    <!-- CARD GERAL AFILIACOES-->
    <div class="card mt-20" data-plugin="tabs">
        <div class="tab-pane" id="tabAffiliateConfiguration" role="tabpanel">

            <!-- ON/OFF COLLAPSE-->
            <div class='row no-gutters'>
                <div class='col-md-12 d-flex pt-20 pl-25 pb-20'>

                    <div class="col-md-6 p-0 d-flex align-items-center">
                        <div class="bg-afiliate-icon affiliation p-10 mr-15">
                            <img src="{{ mix('build/global/img/projects/afiliatesIcon.svg') }}" alt="icone afiliacao">
                        </div>
                        <label for='boleto_redirect' class="font-size-24 m-0" style="color: #37474f;">Afiliações</label>
                    </div>

                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <div id="affiliation-access" class="font-size-16 mr-10">Habilitadas</div>
                        <label class="switch">
                            <input type="checkbox" id="status-url-affiliates" name="status-url-affiliates" class="status-url-affiliates" data-toggle="collapse" data-target="#affiliation" aria-expanded="false" aria-controls="affiliation" value='0'>
                            <span class="slider round"></span>
                        </label>
                    </div>

                </div>
            </div>

            <!-- CONTAINER COL 5 E COL 7 -->
            <div class="collapse" id="affiliation">

                <div class="row no-gutters ml-5 ml-sm-10 ml-md-5 ml-lg-15">

                    <!-- COL - 5 URL, COOKI, PORCENTAGEM -->
                    <div class="col-md-4 px-15 px-sm-10 px-lg-0 form-group">
                        <!-- URL DA PAGINA -->
                        <div class="row no-gutters">
                            <div class="col-md-12">
                                <div class="row no-gutters">
                                    <div class="col-md-12 px-10 pr-sm-0 form-group">
                                        <label for="url-affiliates" class="font-size-16">URL da página principal</label>
                                        <div class="input-group">
                                            <input name="url_page" value="" type="text" class="input-pad" id="url-page" placeholder="URL da página" maxlength="60" style="outline: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TEMPO DE COOKIE E PORCENTAGEM -->
                        <div class="row no-gutters">
                            <div class="col-md-12 d-flex">

                                <!-- COOKIE -->
                                <div class="row no-gutters px-10 pr-sm-0">
                                    <div class='form-group col-md-12 col-lg-7 mr-lg-10 pr-0'>
                                        <label for="cookie-duration" class="font-size-16">Duração do cookie</label>
                                        <select class='cookie-duration sirius-select' name='cookie_duration'>
                                            <option value="0">Eterno</option>
                                            <option value="7">7 dias</option>
                                            <option value="15">15 dias</option>
                                            <option value="30">1 mês</option>
                                            <option value="60">2 meses</option>
                                            <option value="180">6 meses</option>
                                            <option value="365">1 ano</option>
                                        </select>
                                        <span id='error-cookie-duration' class='text-danger' style='display: none'></span>
                                    </div>

                                    <!-- PORCENTAGEM -->
                                    <div class="col-md-12 col-lg pl-0 mb-5 mb-md-5 mb-lg-0">
                                        <label for="percentage-affiliates" class="font-size-16">Porcentagem</label>

                                        <div class="row no-gutters">
                                            <div class="col-md-12">
                                                <div class="input-group mb-3 test">

                                                    <input id='percentage-affiliates' class="form-control select-pad" name='percentage_affiliates' value='0' type='text' min="0" max="100" maxlength="3" style="outline: none;">

                                                    <div class="input-group-append">
                                                        <span class="input-group-text select-pad percent-border">%</span>
                                                        <span id='input-pad-error' class='text-danger'></span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- TIPO DE COMISSAO -->
                        <div class="row no-gutters pl-10">
                            <div class="col-md-12">

                                <!-- LABEL -->
                                <div class="row no-gutters">
                                    <div class="col-12">
                                        <label for="commission-type-enum" class="font-size-16">Tipo comissão</label>
                                    </div>
                                </div>

                                <!-- PRIMEIRO / ULTIMO CLICK -->
                                <div class="row no-gutters pr-10 pr-xl-0">

                                    <div class="col-md-12 justify-content-between px-0 commission-type-enum" id="commission_type_enum" name="commission_type_enum">
                                        <div class="row no-gutters">
                                            <!-- PRIMEIRO -->
                                            <div class="col-md-12 col-xl-6 pl-0 mr-xl-10 mb-5 mb-md-5">
                                                <input type="radio" id="first-click" name="commission_type_enum" class="d-none" value="1">
                                                <label for="first-click" class="col-md-12 btn bg-gray font-size-16 font-weight-bold p-10 type-comission">Primeiro
                                                    clique</label>
                                            </div>

                                            <!-- ULTIMO -->
                                            <div class="col-md-12 col-xl pr-0">
                                                <input type="radio" id="last-click" name="commission_type_enum" class="d-none" value="2">
                                                <label for="last-click" class="col-md-12 btn bg-gray font-size-16 font-weight-bold p-10 type-comission">Último
                                                    clique</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COL-7 TERMOS AFFILIACAO-->
                    <div class="col-md-8 form-group pr-sm-40 px-md-20">
                        <div class="row no-gutters px-10">
                            <div class="col-md-12 form-group">

                                <label for="terms-affiliates" class="font-size-16">Termos de Afiliação</label>
                                <input type="hidden" name="terms_affiliates" id="terms_affiliates">

                                <!-- TEXTAREA QUILL -->
                                <div class="h-200" id='termsaffiliates' placeholder='Termos'></div>

                                <span id='terms-affiliates-error' class='text-danger'></span>

                            </div>
                        </div>
                    </div>

                </div>

                <!-- RODA PE CARD 5 & 7 -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="row no-gutters">

                            <!-- AFILIACAO AUTOMATICA -->
                            <div class="col-md-4 col-lg-3 col-lx-3 pl-5 pl-md-10 py-10 pl-lg-30 pr-20 d-flex align-items-center border-top border-right automatic-affiliation" name='automatic_affiliation'>
                                <input type="checkbox" id="auto-afiliation" class="col-1 h-20 mr-10">
                                <label for="auto-afiliation" class="font-size-16 m-0">Afiliação automática</label>
                            </div>

                            <!-- CONVITE AFILIADOS -->
                            <div class="col-12 col-md-6 col-lg-7 col-xl-8 py-10 pl-0 pr-0 align-items-center border-top border-right">
                                <div class="row no-gutters mt-3 d-flex justify-content-sm-start pl-0 pl-md-0 pl-lg-0">

                                    <div class="col-sm-12 col-lg-3 col-xl-5 font-weight-bold pr-0 pl-5 d-flex justify-content-lg-end align-items-center">
                                        Convide afiliados:</div>

                                    <div class="col-md-12 col-lg-9 col-xl-7">
                                        <input type="text" class="text-lg-right text-xl-left pl-5 pl-sm-5 pr-10 pr-xl-40 border-0" id="url-affiliates" readonly>
                                    </div>

                                </div>
                            </div>

                            <!-- COPIAR LINK -->
                            <div class="col-md-2 col-lg-2 col-xl-1 border-top d-flex align-items-center justify-content-center">
                                <button id="copy-link-affiliation" class="mx-0 bg-white border-0 font-size-16" type="button">

                                    <svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.5028 2.62704L1.5 4.75V15.2542C1.5 17.0491 2.95507 18.5042 4.75 18.5042L13.3663 18.5045C13.0573 19.3782 12.224 20.0042 11.2444 20.0042H4.75C2.12665 20.0042 0 17.8776 0 15.2542V4.75C0 3.76929 0.627445 2.93512 1.5028 2.62704ZM13.75 0C14.9926 0 16 1.00736 16 2.25V15.25C16 16.4926 14.9926 17.5 13.75 17.5H4.75C3.50736 17.5 2.5 16.4926 2.5 15.25V2.25C2.5 1.00736 3.50736 0 4.75 0H13.75ZM13.75 1.5H4.75C4.33579 1.5 4 1.83579 4 2.25V15.25C4 15.6642 4.33579 16 4.75 16H13.75C14.1642 16 14.5 15.6642 14.5 15.25V2.25C14.5 1.83579 14.1642 1.5 13.75 1.5Z" fill="#37474F" />
                                    </svg>
                                    <b>Copiar</b>

                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- DELETE PROJETO -->
    <div id="trash" class="row no-gutters mt-60 mb-60 d-none">
        <div class="col-md-12">
            <div class="row no-gutters">
                <div class="d-flex delete-project">
                    <div>
                        <svg width="20" height="23" viewBox="0 0 20 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4.00002C12 2.89545 11.1046 2.00002 10 2.00002C8.89543 2.00002 8 2.89545 8 4.00002H6.66667C6.66667 2.15907 8.15905 0.666687 10 0.666687C11.8409 0.666687 13.3333 2.15907 13.3333 4.00002H19.3333C19.7015 4.00002 20 4.2985 20 4.66669C20 5.03488 19.7015 5.33335 19.3333 5.33335H18.5947L16.8666 20.3057C16.7113 21.6513 15.572 22.6667 14.2175 22.6667H5.7825C4.428 22.6667 3.28867 21.6513 3.13341 20.3057L1.404 5.33335H0.666667C0.339387 5.33335 0.0671889 5.09752 0.0107409 4.78652L0 4.66669C0 4.2985 0.298477 4.00002 0.666667 4.00002H12ZM17.2507 5.33335H2.748L4.45796 20.1529C4.53558 20.8256 5.10525 21.3334 5.7825 21.3334H14.2175C14.8948 21.3334 15.4644 20.8256 15.542 20.1529L17.2507 5.33335ZM8 8.66669C8.32728 8.66669 8.59948 8.87304 8.65593 9.14517L8.66667 9.25002V17.4167C8.66667 17.7389 8.36819 18 8 18C7.67272 18 7.40052 17.7937 7.34407 17.5215L7.33333 17.4167V9.25002C7.33333 8.92785 7.63181 8.66669 8 8.66669ZM12 8.66669C12.3273 8.66669 12.5995 8.87304 12.6559 9.14517L12.6667 9.25002V17.4167C12.6667 17.7389 12.3682 18 12 18C11.6727 18 11.4005 17.7937 11.3441 17.5215L11.3333 17.4167V9.25002C11.3333 8.92785 11.6318 8.66669 12 8.66669Z" fill="#212121" />
                        </svg>

                    </div>

                    <a id="bt-delete-project" role="button" class="pointer align-items-center mt-3 ml-10" data-toggle="modal" data-target="#modal-delete-project" style="float: left;">
                        <span class="orion-icon-lixo"></span>
                        <span class="font-size-16"><b>Excluir loja</b></span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- CARD SALVAR OU CANCELAR ALTERACOES -->
    <div id="confirm-changes" class="row">
        <div class="container position-fixed pr-5 pr-sm-45 z-index" style="bottom: 0;">

            <div class="row">

                <div class="col-md-12">

                    <div class="row bg-primary no-gutters final-card padding-cards">

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 pl-0">
                                    <span class="padding-cards-l font-size-16">Você tem alterações que <b>não estão
                                            salvas</b> </span>
                                </div>
                            </div>
                        </div>

                        <div id="options-buttons" class="buttons-container col-md-6 d-flex justify-content-end align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 d-flex justify-content-end pr-0 padding-cards-r">
                                    <button type="button" id="cancel-edit" class="font-size-16 btn btn-primary border border-white mr-10 px-15 px-sm-40">Cancelar</button>
                                    <button type="submit" id="bt-update-project" class="font-size-16 btn btn-light text-primary mr-40 mr-sm-0 px-15 px-sm-40">Salvar
                                        alterações</button>
                                </div>
                            </div>
                            <div class="loader"></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- CARD ERROR  -->
    <div id="data-error" class="row">
        <div class="container position-fixed pr-5 pr-sm-45" style="bottom: 0;">

            <div class="row">

                <div class="col-md-12">

                    <div class="row no-gutters bg-danger text-white padding-cards error-card">

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 pl-0">
                                    <span class="padding-cards-l font-size-16"><strong>Ops!</strong> Seu arquivo é
                                        inválido.</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <div class="padding-cards-r">
                                <img src="{{ mix('build/global/img/projects/errorIcon.svg') }}" alt="icone success">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CARD ALTERACEOS SALVAS COM SUCESSO -->
    <div id="saved-alterations" class="row">
        <div class="container position-fixed pr-5 pr-sm-45 z-index" style="bottom: 0;">

            <div class="row">

                <div class="col-md-12">

                    <div class="row no-gutters success-card text-white padding-cards">

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 pl-0">
                                    <span class="padding-cards-l font-size-16">Alterações salvas com sucesso!</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <div class="padding-cards-r">
                                <img src="{{ mix('build/global/img/projects/successIcon.svg') }}" alt="icone success">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-project" aria-hidden="true" role="dialog" tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content">
            <div class="modal-header text-center">
                <a class="pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
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
                <button type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button type="button" class="col-4 btn border-0 btn-outline btn-delete btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>
