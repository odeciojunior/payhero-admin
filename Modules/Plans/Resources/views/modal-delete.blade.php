<div class="modal fade example-modal-lg" id="modal_delete_plan" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
                <h3 class="black">Você tem certeza?</h3>
                <p class="gray">Se você excluir esse registro, não será possível recuperá-lo!</p>
            </div>

            <div class="modal-footer d-flex align-items-center justify-content-center">
                <button id="btn-plan-cancel" type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button id="btn-delete-plan" type="button" class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b class="mr-2">Excluir</b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>