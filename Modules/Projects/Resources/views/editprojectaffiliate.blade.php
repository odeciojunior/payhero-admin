<div class='card shadow p-30'>
    <form id='update-project'>
        @csrf
        <div class='row justify-content-between align-items-baseline mt-15'>
            <div class='col-lg-12'>
                <h3>Configurações Básicas</h3>
            </div>
{{--            <div class='col-lg-4'>--}}
{{--                <div class='d-flex flex-column text-center' id='div-img-project' style='position: relative;'>--}}
{{--                    <label for='photo'>Imagem capa do projeto</label>--}}
{{--                    <div style="width:100%" class="text-center">--}}
{{--                        <img id='previewimage' alt='Selecione a foto do projeto'--}}
{{--                             src="{{asset('modules/global/img/projeto.png')}}"--}}
{{--                             style="min-width: 250px; max-width: 250px;margin: auto">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class='row col-md-8 col-lg-8 col-sm-12'>
                <div class="col-12 row">
{{--                    <div class="form-group col-12">--}}
{{--                        <label for="contact">Email de Contato (checkout e email)</label>--}}
{{--                        <div class="input-group">--}}
{{--                            <div class="input-group-prepend">--}}
{{--                                <span class="input-group-text" id="input_group_contact" id="addon-contact">--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                            <input name="suport_contact" value="" type="text" class="input-pad form-control" id="contact" placeholder="Contato" maxlength='40' aria-describedby="addon-contact">--}}
{{--                        </div>--}}
{{--                        <span id='contact-error' class='text-danger'></span>--}}
{{--                        <p class='info pt-5' style='font-size: 10px;'>--}}
{{--                            <i class='icon wb-info-circle' aria-hidden='true'></i> Contato da loja informado no checkout e nos emails--}}
{{--                        </p>--}}
{{--                    </div>--}}
                    <div class="form-group col-12 mt-20">
                        <label for="contact">Telefone para suporte</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="input_group_support_phone" id="addon-suport_phone">
                                    <span class="material-icons">phone</span>
                                </span>
                            </div>
                            <input name="suport_phone" value="" type="text" class="input-pad form-control" id="suport_phone" placeholder="Telefone" data-mask="(00) 00000-0000" aria-describedby="addon-suport_phone">
                        </div>
                        <span id='contact-error' class='text-danger'></span>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Telefone para suporte. Em compras por boleto na página de obrigado quando o cliente clicar em receber pelo whats a mensagem é encaminhada para esse número
                        </p>
                    </div>
                </div>
            </div>
        </div>
{{--        <div class='row'>--}}
{{--            <div class='col-md-4 col-lg-4 col-sm-12'>--}}
{{--                <div class="text-center">--}}
{{--                    <label for='name'>Imagem para página do checkout e emails</label>--}}
{{--                </div>--}}
{{--                <div class='row'>--}}
{{--                    <div class="col-12">--}}
{{--                        <div class='d-flex flex-column text-center' id='div-img-project' style='position: relative;'>--}}
{{--                            <img id='image-logo-email' alt='Selecione a foto do projeto' src='{{asset('modules/global/img/projeto.png')}}' style='max-height:250px;max-width:250px;margin:auto'>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        <div class="mt-30">
            <div class="row">
                <div class="col-6">
                    <a id="bt-cancel-affiliation" role="button" class="pointer align-items-center" style="float: left;">
                        <i class="material-icons gray"> delete </i>
                        <span class="gray"> Excluir afiliação</span>
                    </a>
                </div>
                <div class="col-6">
                    <button id="bt-update-project" type="button" class="btn btn-success" style="float: right;">
                        Atualizar
                    </button>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-cancel-affiliation" aria-hidden="true" role="dialog" tabindex="-1">
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
                    <p class="gray"> Se você excluir a afiliação, não será possível recuperá-la! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Fechar</button>
                    <button type="button" class="col-4 btn btn-danger btn-cancel-affiliation" data-dismiss="modal" style="width: 20%;">Excluir</button>
                </div>
            </div>
        </div>
    </div>
</div>
