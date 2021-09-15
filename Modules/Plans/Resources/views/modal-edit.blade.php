!-- Modal para Adicionar Planos -->
<div class="modal fade modal-3d-flip-vertical modal-new-layout modal-plans" id="modal_edit_plan" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom">
                <h4 class="modal-title bold">Detalhes de Kit Xiaomi Smart Bands</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-22">close</i>
                </button>
            </div>

            <div class="modal-body">

            </div>

            <div class="modal-footer border-on" style="justify-content: space-between !important;">
                <button id="btn-modal-plan-delete" type="button" class="btn btn-default btn-lg px-0" style="color: #838383; align-items: center !important; display: flex; padding: 10px 32px; background: transparent; border: none;" role="button">
                    <img class="mr-10" src="{{ asset('modules/global/img/icon-trash.svg') }}" alt="Icon Trash" />
                    <span>Excluir plano</span>
                </button>
                <button id="btn-modal-plan-close" type="button" data-stage="1" class="btn btn-primary btn-lg">Fechar</button>
            </div>
        </div>
    </div>
</div>