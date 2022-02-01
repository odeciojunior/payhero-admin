@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/projects/css/edit.css?v=541') }}">
@endpush

<div class='row card no-gutters p-30 rounded-top'>

    <div class="col-12 font-size-24 pl-0 mb-10">
        Configurações
    </div>

    <div class="col-md-12">
        <div class="badge badge-primary font-size-14 mr-10">
            NOVO
        </div>

        <span class="font-size-16">Repaginamos nossa área de configurações de loja. Para encontrar configurações de checkout, use o nosso novo editor.</span>
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
                        <img src="{{asset('/modules/global/img/projects/imgIcon.svg')}}" class="p-10">
                    </div>
                    <h3 class="mb-0">Identificação</h3>
                </div>
            </div>
        </div>

        <div class="row no-gutters">
            <!-- FOTO -->
            <div class="col-md-5 col-lg-4 col-xl-3 px-15 pl-xl-15 d-flex flex-column" id='div-img-project' style='position: relative;'>

                <label for='product_photo' class="pl-0 pl-lg-10 pl-xl-0 mb-3 font-size-16">Capa da loja</label>

                <div style="width:100%" class="text-center">
                    <input type="file" id="product_photo" name="product_photo" data-height="651" data-max-width="651" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                </div>
            </div>

            <!-- NOME E DESCRICAO -->
            <div class="col-md-7 col-lg-8 col-xl-9 pl-10 pr-sm-50">

                <div class='form-group col-md-12'>
                    <label for='name' class="font-size-16">Nome do projeto</label>
                    <input name='name' value="" type='text' class='input-pad font-size-16' id='name' placeholder='Nome do Projeto' maxlength='40' required>
                    <span id='name-error' class='text-danger'></span>
                    <p class='info pt-5' style='font-size: 10px;'></p>
                </div>

                <div class='form-group col-lg-12'>
                    <label for='description' class="font-size-16">Descrição</label>
                    <textarea style='height:100px;' name='description' type='text' class='input-pad font-size-16' id='description' placeholder='Fale um pouco sobre seu Projeto' required='' maxlength='100'></textarea>
                    <span id='description-error' class='text-danger'></span>
                    <p class="pt-25 mb-0 font-size-12">Recomendações: Imagem de 300x300px  |  Formatos: JPEG ou PNG</p>
                </div>

            </div>

        </div>
    </div>

    <!-- CARD GERAL AFILIACOES-->
    <div class="card mt-20" data-plugin="tabs">
        <div class="tab-pane" id="tabAffiliateConfiguration" role="tabpanel">

            <!-- ON/OFF COLLAPSE-->
            <div class='row no-gutters'>
                <div class='col-md-12 d-flex pt-20 pl-30 pb-20'>

                    <div class="col-md-6 p-0 d-flex align-items-center">
                        <div class="bg-afiliate-icon affiliation p-10 mr-15">
                            <img src="{{ asset('/modules/global/img/projects/afiliatesIcon.svg') }}" alt="icone afiliacao">
                        </div>
                        <label for='boleto_redirect' class="font-size-24 m-0">Afiliações</label>
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
                                            <input name="url_page" value="" type="text" class="input-pad" id="url-page" placeholder="URL da página" maxlength="60">
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

                                                    <input id='percentage-affiliates' class="form-control select-pad" name='percentage_affiliates' value='' type='text' min="0" max="100" maxlength="3">

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
                                                    <label for="first-click" class="col-md-12 btn bg-gray font-size-16 font-weight-bold p-10 type-comission">Primeiro clique</label>
                                                </div>

                                                <!-- ULTIMO -->
                                                <div class="col-md-12 col-xl pr-0">
                                                    <input type="radio" id="last-click" name="commission_type_enum" class="d-none" value="2">
                                                    <label for="last-click" class="col-md-12 btn bg-gray font-size-16 font-weight-bold p-10 type-comission">Último clique</label>
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
                                    
                                    <div class="col-sm-12 col-lg-3 col-xl-5 font-weight-bold pr-0 pl-5 d-flex justify-content-lg-end align-items-center">Convide afiliados:</div>

                                    <div class="col-md-12 col-lg-9 col-xl-7">
                                        <input type="text" class="text-lg-right text-xl-left pl-5 pl-sm-5 pr-10 pr-xl-40 border-0" id="url-affiliates" readonly>
                                    </div>

                                </div>
                            </div>

                            <!-- COPIAR LINK -->
                            <div class="col-md-2 col-lg-2 col-xl-1 border-top d-flex align-items-center justify-content-end">
                                <button id="copy-link-affiliation" class="mx-0 bg-white border-0 pl-md-10 font-size-16" type="button">
                                    <svg width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.5028 2.62704L1.5 4.75V15.2542C1.5 17.0491 2.95507 18.5042 4.75 18.5042L13.3663 18.5045C13.0573 19.3782 12.224 20.0042 11.2444 20.0042H4.75C2.12665 20.0042 0 17.8776 0 15.2542V4.75C0 3.76929 0.627445 2.93512 1.5028 2.62704ZM13.75 0C14.9926 0 16 1.00736 16 2.25V15.25C16 16.4926 14.9926 17.5 13.75 17.5H4.75C3.50736 17.5 2.5 16.4926 2.5 15.25V2.25C2.5 1.00736 3.50736 0 4.75 0H13.75ZM13.75 1.5H4.75C4.33579 1.5 4 1.83579 4 2.25V15.25C4 15.6642 4.33579 16 4.75 16H13.75C14.1642 16 14.5 15.6642 14.5 15.25V2.25C14.5 1.83579 14.1642 1.5 13.75 1.5Z" fill="#37474F"/>
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
    <div class="row mt-60 mb-30">
        <div class="col-md-12">
            <div class="row no-gutters">
                <div class="d-flex delete-project">
                    <div>
                        <svg width="22" height="26" viewBox="0 0 22 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8.13381 1.97674C8.27694 1.8279 8.46464 1.75 8.65385 1.75H13.3462C13.4405 1.75 13.5346 1.76928 13.6235 1.80755C13.7124 1.84586 13.7951 1.90284 13.8662 1.97674C13.9373 2.05071 13.9952 2.1401 14.0352 2.24052C14.0752 2.341 14.0962 2.44964 14.0962 2.56V3.45H7.90385V2.56C7.90385 2.33589 7.98973 2.12659 8.13381 1.97674ZM15.5962 2.56V3.45H18.878H18.8899H21C21.4142 3.45 21.75 3.78579 21.75 4.2C21.75 4.61421 21.4142 4.95 21 4.95H19.5915L18.5187 23.0705C18.5015 23.7661 18.2283 24.4339 17.747 24.9344C17.2611 25.4398 16.5999 25.7354 15.9001 25.7498L15.8846 25.75H6.11538L6.09994 25.7498C5.40013 25.7354 4.73892 25.4398 4.25302 24.9344C3.77172 24.4339 3.49849 23.7661 3.48126 23.0705L2.40847 4.95H1C0.585786 4.95 0.25 4.61421 0.25 4.2C0.25 3.78579 0.585786 3.45 1 3.45H3.11015H3.12201H6.40385V2.56C6.40385 1.95663 6.63404 1.37235 7.05256 0.937082C7.47205 0.500817 8.04741 0.25 8.65385 0.25H13.3462C13.6458 0.25 13.9418 0.311421 14.2169 0.429942C14.492 0.54843 14.7399 0.721264 14.9474 0.937082C15.1549 1.15284 15.318 1.4074 15.4287 1.6855C15.5395 1.96357 15.5962 2.26064 15.5962 2.56ZM3.9111 4.95H18.0889L17.0205 22.9957L17.0198 23.0113L17.0194 23.0257C17.013 23.3575 16.8833 23.6685 16.6657 23.8948C16.4506 24.1185 16.1672 24.2422 15.8759 24.25H6.12407C5.83275 24.2422 5.54936 24.1185 5.33427 23.8948C5.11666 23.6685 4.98695 23.3575 4.98063 23.0257C4.98044 23.0157 4.98005 23.0057 4.97946 22.9957L3.9111 4.95ZM9.02185 7.19157C9.00492 6.7777 8.65568 6.45592 8.24182 6.47285C7.82795 6.48978 7.50617 6.83901 7.5231 7.25288L8.06855 20.5862C8.08548 21.0001 8.43472 21.3219 8.84858 21.3049C9.26245 21.288 9.58423 20.9388 9.5673 20.5249L9.02185 7.19157ZM14.4764 7.25288C14.4933 6.83901 14.1715 6.48978 13.7577 6.47285C13.3438 6.45592 12.9946 6.7777 12.9776 7.19157L12.4322 20.5249C12.4153 20.9388 12.737 21.288 13.1509 21.3049C13.5648 21.3219 13.914 21.0001 13.9309 20.5862L14.4764 7.25288ZM11.7497 7.22223C11.7497 6.80801 11.414 6.47223 10.9997 6.47223C10.5855 6.47223 10.2497 6.80801 10.2497 7.22223V20.5556C10.2497 20.9698 10.5855 21.3056 10.9997 21.3056C11.414 21.3056 11.7497 20.9698 11.7497 20.5556V7.22223Z" fill="#838383"/>
                        </svg>
                    </div>

                    <a id="bt-delete-project" role="button" class="pointer align-items-center mt-3 ml-10" data-toggle="modal" data-target="#modal-delete-project" style="float: left;">
                        <span class="orion-icon-lixo"></span>
                        <span class="font-size-16"><b>Excluir projeto</b></span>
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

                    <div class="row bg-primary no-gutters final-card py-15">

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 pl-0">
                                    <span class="pl-25">Você tem alterações que <b>não estão salvas</b> </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 d-flex justify-content-end pr-0 pr-md-25">
                                    <button type="button" id="cancel-edit" class="btn btn-primary border border-white mr-25 px-15 px-sm-40">Cancelar</button>

                                    <button type="button" id="bt-update-project" class="btn btn-light text-primary mr-40 mr-sm-0 px-15 px-sm-40">Salvar alteração</button>
                                </div>
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

                    <div class="row no-gutters success-card text-white py-10">

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 pl-0">
                                    <span class="pl-30 font-size-18">Alterações salvas com sucesso!</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <div class="pr-10 mr-15">
                                <img src="{{ asset('/modules/global/img/projects/successIcon.svg') }}" alt="icone success">
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
