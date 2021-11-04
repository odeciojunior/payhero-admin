<div id="modal-withdrawal" class="modal fade modal-3d-flip-vertical" role="dialog" tabindex="-1" data-keyboard="false" data-backdrop="static" style="display: none">
    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
        <div id="conteudo_modal_add" class="modal-content modal-content-style">
            <div class="modal-header header-modal simple-border-bottom modal-title-withdrawal" style="height: 60px;">
                <h2 id="modal-withdrawal-title" class="modal-title" style="color: #FFFFFF;">Confirmar Saque</h2>
            </div>
            <div id="modal_body" class="modal-body simple-border-bottom"
                 style='padding-bottom:1%;padding-top:1%;'>
                <div>
                    <h5>Verifique os dados da conta:</h5>
                    <h4>Banco:
                        <span id="modal-withdrawal-bank"></span>
                    </h4>
                    <h4>Agência:
                        <span id="modal-withdrawal-agency"></span>
                        <span id="modal-withdrawal-agency-digit"></span>
                    </h4>
                    <h4>Conta:
                        <span id="modal-withdrawal-account"></span>
                        <span id="modal-withdrawal-account-digit"></span>
                    </h4>
                    <h4>Documento:
                        <span id="modal-withdrawal-document"></span>
                    </h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-8 mt-10">
                            <p style="color: #5A5A5A;" class="text-uppercase">Valor do saque:</p>
                        </div>
                        <div class="col-md-4 mt-10 text-right">
                            <span id="modal-withdrawal-value" class='greenGradientText'></span>
                            <span id="taxValue" class="" style="font-size: 6px">- R$3,80</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modal-withdraw-footer" class="modal-footer mt-20">
                <div class="col-md-12 text-center">
                    <button id="bt-cancel-withdrawal" class="btn col-5 s-btn-border" data-dismiss="modal" aria-label="Close" style="font-size:20px; width:200px; border-radius: 12px; color:#818181;">
                        Cancelar
                    </button>

                    <button id="bt-confirm-withdrawal" class="btn btn-success col-5 btn-confirmation s-btn-border" style="background-color: #41DC8F;font-size:20px; width:200px;">
                        <strong>Confirmar</strong>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-withdrawal-custom" class="modal fade modal-3d-flip-vertical " role="dialog" tabindex="-1" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content modal-content-style">
            <div class="modal-header header-modal simple-border-bottom modal-title-withdrawal" style="height: 60px;">
                <h3 id="modal-withdrawal-custom-title" class="modal-title" style="color: #FFFFFF;"></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div id="modal-body-withdrawal-custom" class="col-12 mt-30" style="min-height: 70px">
                            <!-- js .... -->
                        </div>
                    </div>
                    <div id="debit-pending-informations" class="col-12 mt-20" style="display:none;background:  0 0 no-repeat padding-box;">
                        <!-- js .... -->
                    </div>
                </div>
            </div>
            <div id='modal-withdrawal-custom-footer' class="modal-footer mt-20">
                <!-- js .... -->
            </div>
        </div>
    </div>
</div>
