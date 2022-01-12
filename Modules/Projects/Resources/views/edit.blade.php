@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/projects/css/edit.css?v=05') }}">
@endpush

<div class='row card no-gutters p-30 rounded-top'>

    <div class="col-12 font-size-24 pl-0 mb-10">
        Configuracoes
    </div>

    <div class="col-md-12">
        <div class="badge badge-primary font-size-14 mr-10">
            NOVO
        </div>

        <span>
            Repaginamos nossa área de configurações de loja. Para encontrar configurações de checkout, use o nosso novo editor.
        </span>
    </div>

</div>

<form id='update-project'>
    @method('PUT')
    @csrf

    <!-- IDENTIFICACAO -->
    <div class='card col-md-12'>

        <div class="row">
            <!-- TITULO CABECALHO -->
            <div class="col-md pt-20 pl-10 pl-lg-25">
                <div class="row d-flex pl-20">
                    <img src="{{asset('/modules/global/img/projects/imgIcon.svg')}}" class="mb-15 mr-15">
                    <h3>Identificação</h3>
                </div>
            </div>

        </div>

        <div class="row">
            <!-- FOTO -->
            <div class="col-md-5 col-lg-4 col-xl-3 pl-xl-25 d-flex flex-column" id='div-img-project' style='position: relative;'>

                <label for='product_photo' class="pl-0 pl-lg-10 pl-xl-0 mb-3">Capa da loja</label>

                <div style="width:100%" class="text-center">
                    <input type="file" id="product_photo" name="product_photo" data-height="651" data-max-width="651" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                </div>
            </div>

            <!-- NOME E DESCRICAO -->
            <div class="col-md-7 col-lg-8 col-xl-9 pl-10 pr-sm-50">

                <div class='form-group col-md-12'>
                    <label for='name'>Nome do projeto</label>
                    <input name='name' value="" type='text' class='input-pad' id='name' placeholder='Nome do Projeto' maxlength='40' required>
                    <span id='name-error' class='text-danger'></span>
                    <p class='info pt-5' style='font-size: 10px;'></p>
                </div>

                <div class='form-group col-lg-12'>
                    <label for='description'>Descrição</label>
                    <textarea style='height:100px;' name='description' type='text' class='input-pad' id='description' placeholder='Fale um pouco sobre seu Projeto' required='' maxlength='100'></textarea>
                    <span id='description-error' class='text-danger'></span>
                    <p class='info pt-25 mb-0' style='font-size: 10px;'>
                        Recomendações: Imagem de 300x300px  |  Formatos: JPEG ou PNG
                    </p>
                </div>

            </div>

        </div>
    </div>

    <!-- CARD GERAL AFILIACOES-->
    <div class="card mt-20" data-plugin="tabs">
        <div class="tab-pane" id="tabAffiliateConfiguration" role="tabpanel">

            <!-- ON/OFF COLLAPSE-->
            <div class='row'>
                <div class='col-md-12 d-flex py-10'>

                    <div class="col-md-6 d-flex align-items-center pl-5 pl-sm-30">
                        <div class="bg-afiliate-icon p-5 mr-15">
                            <img src="{{ asset('/modules/global/img/projects/afiliatesIcon.svg') }}" alt="icone afiliacao">
                        </div>
                        <label for='boleto_redirect' class="font-size-24 m-0">Afiliações</label>
                    </div>

                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <label class="switch">
                            <input type="checkbox" id="status-url-affiliates" name="status-url-affiliates" class="status-url-affiliates" data-toggle="collapse" data-target="#affiliation" aria-expanded="false" aria-controls="affiliation" value='0'>
                            <span class="slider round"></span>
                        </label>
                    </div>

                </div>
            </div>

            <!-- CONTAINER COL 5 E COL 7 -->
            <div class="collapse" id="affiliation">

                <div class="row">

                    <!-- COL - 5 URL, COOKI, PORCENTAGEM -->
                    <div class="col-md-5 form-group">
                        <!-- URL DA PAGINA -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row no-gutters">
                                    <div class="col-md-12 px-5 pl-sm-30 pr-sm-0 form-group">
                                        <label for='url-affiliates font-size-16'>URL da página principal</label>
                                        <div class="input-group">
                                            <input name="url_page" value="" type="text" class="input-pad" id="url-page" placeholder="URL da página" maxlength="60">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TEMPO DE COOKIE E PORCENTAGEM -->
                        <div class="row">
                            <div class="col-md-12 d-flex">

                                <!-- COOKIE -->
                                <div class='form-group col-md-6 pl-5 pl-sm-30 pr-0'>
                                    <label for="cookie-duration">Duração do cookie</label>
                                    <select class='cookie-duration form-control select-pad' name='cookie_duration'>
                                        <option value="0"> Eterno</option>
                                        <option value="7"> 7 dias</option>
                                        <option value="15"> 15 dias</option>
                                        <option value="30"> 1 mês</option>
                                        <option value="60"> 2 meses</option>
                                        <option value="180"> 6 meses</option>
                                        <option value="365"> 1 ano</option>
                                    </select>
                                    <span id='error-cookie-duration' class='text-danger' style='display: none'></span>
                                </div>

                                <!-- PORCENTAGEM -->
                                <div class="col-md-6 form-group pr-3 pr-sm-0">
                                    <label for='percentage-affiliates'>Porcentagem</label>

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
                                        {{-- <div class='form-group col-md-6 pr-3 pr-sm-0'>
                                            <label for='percentage-affiliates'>Porcentagem</label>
                                            <input id='percentage-affiliates' name='percentage_affiliates' value='' class='input-pad' type='text' min="0" max="100" maxlength="3">

                                            <span id='input-pad-error' class='text-danger'></span>
                                        </div> --}}


                                    </div>

                                </div>

                            </div>
                        </div>

                        <!-- TIPO DE COMISSAO -->
                        <div class="row no-gutters">
                            <div class="col-md-12 pl-5 pl-sm-30">

                                <!-- LABEL -->
                                <div class="row">
                                    <div class="col-12">
                                        <label for='commission-type-enum'>Tipo comissão</label>
                                    </div>
                                </div>

                                <!-- PRIMEIRO / ULTIMO CLICK -->
                                <div class="row">
                                    <div class="d-flex col-md-12">
                                        <div class="col-md-12 d-flex justify-content-between px-0 commission-type-enum" id="commission_type_enum" name="commission_type_enum">

                                            <!-- PRIMEIRO -->
                                            <div class="col-md-6 pl-0">
                                                <input type="radio" id="first-click" name="commission_type_enum" class="d-none" value="1">
                                                <label for="first-click" class="col-md-12 btn bg-light font-size-16 p-10 type-comission">Primiero Click</label>
                                            </div>

                                            <!-- ULTIMO -->
                                            <div class="col-md-6 pr-0">
                                                <input type="radio" id="last-click" name="commission_type_enum" class="d-none" value="2">
                                                <label for="last-click" class="col-md-12 btn bg-light font-size-16 p-10 type-comission">Ultimo Click</label>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COL-7 TERMOS AFFILIACAO-->
                    <div class="col-md-7 form-group pl-10 pr-10 pr-sm-40">
                        <div class="row">
                            <div class="col-md-12 form-group">


                                <label for='terms-affiliates'>Termos de Afiliação</label>
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
                            <div class="automatic-affiliation col-md-4 col-lg-2 pl-5 pl-md-10 pl-lg-30 pr-20 d-flex align-items-center border-top border-right" name='automatic_affiliation'>
                                <input type="checkbox" id="auto-afiliation" class="col-1 h-20 mr-10">
                                <label for="auto-afiliation" class="m-0">Afiliação automática</label>
                            </div>

                            <!-- CONVITE AFILIADOS -->
                            <div class="col-12 col-md-5 col-lg-8 pl-0 pr-0 align-items-center border-top border-right">
                                <div class="row no-gutters mt-3">

                                    <div class="col-md-6 d-flex align-items-center justify-content-sm-end">
                                        <span class="font-weight-bold pr-0 pl-5">Convide afiliados:</span>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="text" class="text-left pl-5 pl-sm-5 pr-10 border-0" id="url-affiliates" readonly>
                                    </div>

                                </div>
                            </div>

                            <!-- COPIAR LINK -->
                            <div class="col-md-3 col-lg-2 p-5 border-top">
                                <button id="copy-link-affiliation" class="btn btn-default mx-0 bg-white border-0 pl-md-30" type="button">
                                    <img src="{{asset('/modules/global/img/projects/btnCopy.svg')}}" class="mr-10" alt="botao de copiar">Copiar
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

                <img class="control-img mr-5" src="{{ asset('/modules/global/img/projects/trash.svg') }}">

                <a id="bt-delete-project" role="button" class="pointer align-items-center" data-toggle="modal" data-target="#modal-delete-project" style="float: left;">
                    <span class="orion-icon-lixo"></span>
                    <span class="gray"> Deletar projeto</span>
                </a>
            </div>
        </div>
    </div>

    <!-- CARD SALVAR OU CANCELAR ALTERACOES -->
    <div id="confirm-changes" class="row">
        <div class="container position-fixed pr-5 pr-sm-45 z-index" style="bottom: 0;">

            <div class="row">

                <div class="col-md-12">

                    <div class="row bg-primary no-gutters final-card py-30">

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 pl-0">
                                    <span class="pl-30">Você tem alterações que <b>não estão salvas</b> </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 d-flex justify-content-end pr-0 pr-md-30">
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

                    <div class="row no-gutters success-card text-white py-30">

                        <div class="col-md-6 d-flex align-items-center">
                            <div class="row no-gutters">
                                <div class="col-md-12 pl-0">
                                    <span class="pl-30 font-size-18">Alterações salvas com sucesso!</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <div class="bg-afiliate-icon pr-10 mr-15 success-card">
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
