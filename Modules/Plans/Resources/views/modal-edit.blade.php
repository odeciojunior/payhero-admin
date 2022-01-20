<div class="modal fade modal-new-layout modal-plans" id="modal_edit_plan" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content conteudo_modal_edit">
            <div class="modal-header simple-border-bottom">
                <h4 class="modal-title bold">Detalhes</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-22">close</i>
                </button>
            </div>

            <div class="modal-body" id="modal_add_body">
                <div class="height-auto">
                    <div class="nav-tabs-horizontal nav-tabs-horizontal-custom" style="margin: -20px -30px 0 -30px;">
                        <ul class="nav nav-tabs nav-tabs-line" role="tablist">
                            <li class="nav-item text-center" style="width: 50%;">
                                <a id="tab-general-data" class="nav-link active show" data-toggle="tab" href="#tab-general-data_panel" role="tab">
                                    Dados gerais
                                </a>
                            </li>
                            <li class="nav-item text-center" style="width: 50%;">
                                <a id="tab-customizations" class="nav-link disabled" data-toggle="tab" href="#tab-customizations_panel" role="tab">
                                    Personalizações
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="tab-content_all" style="padding-top: 21px;">
                        <div class="tab-pane fade show active" id="tab-general-data_panel" role="tabpanel">
                            <div class="tab-content" id="tabs-modal-edit-plans">
                                <div class="tab-pane fade" id="stage1" role="tabpanel" aria-labelledby="stage1-tab">
                                    @include('plans::stages/stage1-edit')
                                </div>
                                <div class="tab-pane fade" id="stage2" role="tabpanel" aria-labelledby="stage2-tab">
                                    @include('plans::stages/stage2-edit')
                                </div>
                                <div class="tab-pane fade" id="stage3" role="tabpanel" aria-labelledby="stage3-tab">
                                    @include('plans::stages/stage3-edit')
                                </div>
                                <div class="tab-pane fade" id="stage4" role="tabpanel" aria-labelledby="stage4-tab">
                                    @include('plans::stages/stage4-edit')
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab-customizations_panel" role="tabpanel">
                            <div class="tab-content" id="tabs-modal-customization-products">
                                <div class="tab-pane fade show active" id="stage1-customization" role="tabpanel" aria-labelledby="stage1-customization-tab">
                                    @include('plans::stages/stage1-customization')
                                </div>
                                <div class="tab-pane fade" id="stage2-customization" role="tabpanel" aria-labelledby="stage2-customization-tab">
                                    @include('plans::stages/stage2-customization')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-on justify-content-between">
                <button id="btn-modal-plan-delete" type="button" class="btn btn-default btn-lg px-0" style="box-shadow: none !important; color: #838383; align-items: center !important; display: flex; padding: 10px 32px; background: transparent; border: none;" role="button">
                    <img class="mr-10" src="/modules/global/img/icon-trash.svg" alt="Icon Trash" />
                    <span>Excluir plano</span>
                </button>
                <button id="btn-modal-plan-close" type="button" data-dismiss="modal" class="btn btn-primary btn-lg">Fechar</button>
            </div>
        </div>
    </div>
</div>
