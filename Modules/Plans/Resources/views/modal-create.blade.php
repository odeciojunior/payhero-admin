<div class="modal fade modal-new-layout modal-plans"
     id="modal_add_plan"
     role="dialog"
     tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
             id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom">
                <h4 class="modal-title bold">Adicionar novo plano</h4>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <i class="material-icons md-22">close</i>
                </button>
            </div>

            <div class="modal-body"
                 id="modal_add_body">
                <div class="height-auto">
                    <div class="tab-content"
                         id="tabs-modal-create-plans">
                        <div class="tab-pane fade"
                             id="stage1"
                             role="tabpanel"
                             aria-labelledby="stage1-tab">
                            @include('plans::stages/stage1-create')
                        </div>
                        <div class="tab-pane fade"
                             id="stage2"
                             role="tabpanel"
                             aria-labelledby="stage2-tab">
                            @include('plans::stages/stage2-create')
                        </div>
                        <div class="tab-pane fade"
                             id="stage3"
                             role="tabpanel"
                             aria-labelledby="stage3-tab">
                            @include('plans::stages/stage3-create')
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-on">
                <button id="btn-modal-plan-return"
                        type="button"
                        data-type="create"
                        class="btn btn-default btn-lg"
                        role="button">Voltar</button>
                <button id="btn-modal-plan-next"
                        type="button"
                        data-type="create"
                        data-stage="1"
                        class="btn btn-primary btn-lg">Continuar</button>
            </div>
        </div>
    </div>
</div>
