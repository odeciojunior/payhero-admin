@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/projects/css/edit.css?v=05') }}">
@endpush

<div class='card shadow p-30 rounded-top'>

    <div class="col-2 font-size-24 pl-0 mb-10">
        Configuracoes
    </div>

    <div class="d-flex">
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
    <div class='card col-md-12'>

        <!-- Titulo -->
        <div class="row">
            <div class="col-md pt-15 pl-30">
                <h3>Identificação</h3>
            </div>
        </div>

        <!-- Information about projects  -->
        <div class="row">

            <div class='col-md-3 d-flex flex-column' id='div-img-project' style='position: relative;'>
                <input name='photo' type='file' class='form-control' id='photoProject' style='display:none;' accept='image/*'>
                <label for='photo' class="pl-20 mb-3">Capa da loja</label>

                <div style="width:100%" class="text-center">
                    <img id='previewimage' alt='Selecione a foto do projeto' src="{{asset('modules/global/img/projeto.svg')}}" style="min-width: 250px; max-width: 250px;margin: auto">
                </div>

                <input type='hidden' id='photo_x1' name='photo_x1'><input id='photo_y1' type='hidden' name='photo_y1'>

                <input type='hidden' id='photo_w' name='photo_w'><input id='photo_h' type='hidden' name='photo_h'>
            </div>

            <div class="col-md-9 pl-0">

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
                        <i class='icon wb-info-circle' aria-hidden='true'></i>
                        Recomendações: Imagem de 300x300px  |  Formatos: JPEG ou PNG
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- AFILIACOES -->
    <div class="card mt-20" data-plugin="tabs">
        <div class="tab-pane" id="tabAffiliateConfiguration" role="tabpanel">

            <!-- CABECALHO ON/OFF-->
            <div class='row'>

                <div class='col-md-12 d-flex py-10'>

                    <div class="col-md-6 d-flex align-items-center pl-30">
                        <label for='boleto_redirect' class="font-size-24 m-0">Afiliações</label>
                    </div>

                    <div class="col-md-6 d-flex justify-content-end align-items-center">
                        <label class="switch">
                            <input type="checkbox" id="status-url-affiliates" name="status-url-affiliates" class='check status-url-affiliates' value='0'>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    
                </div>
            </div>

            <!-- LINHA CONTAINER GERAL -->
            <div class="row div-url-affiliate">

                <!-- CONTAINER 5 COL -->
                <div class="col-md-5 form-group">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12 pl-30 pr-0 form-group">
                                <label for='url-affiliates font-size-16'>URL da página principal</label>
                                <div id="affiliate-link-select" class="input-group">
                                    <input type="text" class="form-control" id="url-affiliates" value="" readonly="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 d-flex">

                            <div class='form-group col-md-6 pl-30 pr-0'>
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
    
                            <div class='form-group col-md-6 pr-0'>
                                <label for='percentage-affiliates'>Porcentagem</label>
                                <input id='percentage-affiliates' name='percentage_affiliates' value='' class='input-pad' type='text' min="0" max="100" maxlength="3">
                                <span id='input-pad-error' class='text-danger'></span>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 pl-30">
                            <div class="col-12">
                                <label for='commission-type-enum'>Tipo comissão</label>
                            </div>

                            <div class="d-flex col-md-12">
                                <div class="col-md-12 d-flex justify-content-between px-0" id='commission_type_enum'>

                                    <div class="col-md-6 pl-0">
                                        <input type="radio" id="first-click" name="commission_type_enum" class="d-none" value="1" checked>
                                        <label for="first-click" class="col-md-12 btn bg-light font-size-16 p-10 type-comission">Primiero Click</label>
                                    </div>

                                    <div class="col-md-6 pr-0">
                                        <input type="radio" id="last-click" name="commission_type_enum" class="d-none" value="2">
                                        <label for="last-click" class="col-md-12 btn bg-light font-size-16 p-10 type-comission">Ultimo Click</label>
                                    </div>

                                </div>
                            </div>

                            {{-- <div class='form-group col-md-12 pr-0'>
                                <label for='commission-type-enum'>Tipo comissão</label>
                                <select class='commission-type-enum form-control select-pad' name='commission_type_enum' class='form-control select-pad'>
                                    <option value='1'>Primeiro clique</option>
                                    <option value='2'>Último clique</option>
                                </select>
                            </div> --}}
                            
                        </div>
                    </div>
                </div>

                <!-- Container de 7 col -->
                <div class="col-md-7 form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <div class='form-group col-md-12'>
                                <label for='terms-affiliates'>Termos de Afiliação</label>
                                <input type="hidden" name="terms_affiliates" id="terms_affiliates">
                                <textarea class='input-pad' id='termsaffiliates' placeholder='Termos'></textarea>
                                <span id='terms-affiliates-error' class='text-danger'></span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 d-flex">
                            <div class="col-md-2 pl-30 pr-20 d-flex align-items-center border-top border-right" name='automatic_affiliation'>
                                <input type="checkbox" id="auto-afiliation" class="col-1 h-20 mr-10">
                                <label for="auto-afiliation" class="m-0">Afiliação automática</label>
                            </div>

                            {{-- <div class="col-md-2 pl-30 pr-0">
                                <select class='automatic-affiliation form-control select-pad' name='automatic_affiliation' class='form-control select-pad'>
                                    <option value='0'>Não</option>
                                    <option value='1'>Sim</option>
                                </select>
                            </div> --}}
    
                            <div class="col-md-9 d-flex pl-0 pr-0 align-items-center border-top border-right">
                                <span class="col-7 text-right font-weight-bold pr-0">Convide afiliados:</span>
                                <input type="text" class="text-right pl-0 pr-10 border-0" id="url-affiliates" readonly>
                            </div>

                            <div class="col-md-1 p-5 border-top">
                                <button id="copy-link-affiliation" class="btn btn-default mx-10" type="button">Copiar</button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{--END Configurações--}}

    <div class="row">
        <div class="col-12">
            <a id="bt-delete-project" role="button" class="pointer align-items-center" data-toggle="modal" data-target="#modal-delete-project" style="float: left;">
                <span class='orion-icon-lixo'></span>
                <span class="gray">Excluir projeto</span>
            </a>
        </div>
    </div>

    <div class="card mt-25 bg-primary">
        <div class="row">

            <div class="col-md-6 d-flex align-items-center">

                <div class="col-md-12 pl-0">
                    <span class="pl-30">Você tem alterações que <b>não estão salvas</b> </span>
                </div>

            </div>

            <div class="col-md-6 d-flex justify-content-end align-items-center">

                <div class="col-md-12 d-flex justify-content-end pt-25 pb-20 pr-30">
                    <button type="button" class="btn btn-primary border border-white mr-25 px-40">Cancelar</button>

                    <button type="button" id="bt-update-project" class="btn btn-light text-primary px-40">Salvar alteração</button>
                </div>

            </div>
            
        </div>
    </div>
</form>

