<div class='card shadow p-30'>
    <div class='row justify-content-between align-items-baseline mt-15'>
        <div class='col-lg-12'>
            <h3>Configurações Básicas</h3>
        </div>
        <div class='col-lg-12'>
            <div class='row'>
                <div class='form-group col-lg-12'>
                    <button id="bt-cancel-affiliation" type="button" class="btn btn-danger">
                        Cancelar afiliação
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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
                <p class="gray"> Se você cancelar a afiliação, não será possível recuperá-la! </p>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-center">
                <button type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Fechar</button>
                <button type="button" class="col-4 btn btn-danger btn-cancel-affiliation" data-dismiss="modal" style="width: 20%;">Cancelar afiliação</button>
            </div>
        </div>
    </div>
</div>