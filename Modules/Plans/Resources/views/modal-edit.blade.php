<div class="modal fade modal-3d-flip-vertical modal-new-layout modal-plans" id="modal_edit_plan" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content conteudo_modal_edit">
            <div class="modal-header simple-border-bottom">
                <h4 class="modal-title bold"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-22">close</i>
                </button>
            </div>

            <div class="modal-body">
                <div class="informations-edit">
                    <div>
                        <div class="d-flex" style="justify-content: space-between !important;">
                            <div class="d-flex align-items-center">
                                <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-info-plans-c.svg') }}" alt="Icon Informations"></div>
                                <div class="title">Informações do plano</div>
                            </div>
                            <button class="btn btn-edit" id="btn-edit-informations-plan">
                                <img src="{{ asset('modules/global/img/icon-edit.svg') }}" alt="Icon Edit">
                            </button>
                        </div>
                    </div>

                    <div class="informations-data">
                        <div class="row mb-20">
                            <div class="col-sm-6">
                                <label for="name">Nome</label>
                                <input type="text" class="form-control" id="name" readonly>
                            </div>
                            <div class="col-sm-6">
                                <label for="price">Preço de venda</label>
                                <input type="text" class="form-control" id="price" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="description">Descrição</label>
                                <input type="text" class="form-control" id="description" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="line"></div>

                <div class="products-edit">
                    <div>
                        <div class="d-flex" style="justify-content: space-between !important;">
                            <div class="d-flex align-items-center">
                                <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-products-plans.svg') }}" alt="Icon Informations"></div>
                                <div class="title">Produtos no plano <span></span></div>
                            </div>
                            <button class="btn btn-edit" id="btn-edit-products-plan">
                                <img src="{{ asset('modules/global/img/icon-edit.svg') }}" alt="Icon Edit">
                            </button>
                        </div>
                    </div>

                    <div class="products-data" id="load-products">
                        {{-- js carrega --}}
                    </div>
                </div>

                <div class="line"></div>

                <div class="review-edit">
                    <div>
                        <div class="d-flex" style="justify-content: space-between !important;">
                            <div class="d-flex align-items-center">
                                <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-review-plans-c.svg') }}" alt="Icon Informations"></div>
                                <div class="title">Revisão geral</div>
                            </div>
                        </div>
                    </div>

                    <div class="review-data">
                        <div class="d-flex">

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-on" style="justify-content: space-between !important;">
                <button id="btn-modal-plan-delete" type="button" class="btn btn-default btn-lg px-0" style="color: #838383; align-items: center !important; display: flex; padding: 10px 32px; background: transparent; border: none;" role="button">
                    <img class="mr-10" src="{{ asset('modules/global/img/icon-trash.svg') }}" alt="Icon Trash" />
                    <span>Excluir plano</span>
                </button>
                <button id="btn-modal-plan-close" type="button" data-dismiss="modal" class="btn btn-primary btn-lg">Fechar</button>
            </div>
        </div>
    </div>
</div>