<!-- Modal padrão para adicionar Adicionar e Editar -->
<div class="modal fade modal-3d-flip-vertical modal-new-layout modal-plans" id="modal_add_plan" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom">
                <h4 class="modal-title bold">Adicionar novo plano</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-22">close</i>
                </button>
            </div>

            <div class="modal-body">
                <div class="row" style="margin-bottom: 24px;">
                    <div class="col-sm-12">
                        <div class="d-flex">
                            <div class="box-stage products d-flex align-items-center flex-fill">
                                <div class="icon mr-10"><img src="{{ asset('modules/global/img/icon-products-plans.png') }}" alt="Icon Products"></div>
                                <div class="title mr-10">Produtos</div>
                                <div class="line"><hr></div>
                            </div>

                            <div class="box-stage costs d-flex align-items-center flex-fill">
                                <div class="icon mr-10"><img src="{{ asset('modules/global/img/icon-costs-plans.png') }}" alt="Icon Costs"></div>
                                <div class="title mr-10">Custos</div>
                                <div class="line"><hr></div>
                            </div>

                            <div class="box-stage informations d-flex align-items-center flex-fill">
                                <div class="icon mr-10"><img src="{{ asset('modules/global/img/icon-info-plans.png') }}" alt="Icon Informations"></div>
                                <div class="title">Informações</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-bottom: 24px;">
                    <div class="col-sm-12">
                        <label for="search-product" style="margin-bottom: 12px;">Selecione os produtos do novo plano</label>
                        <input class="form-control form-control-lg" type="text" id="search-product" placeholder="Pesquisa por nome">
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6"></div>
                    <div class="col-sm-6"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button id="btn-modal-plan-voltar" type="button" class="btn btn-default btn-lg" role="button">Voltar</button>
                <button id="btn-modal-plan-finalizar" type="button" class="btn btn-primary btn-lg">Prosseguir</button>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-plans .box-stage {
        -ms-flex: 1 1 auto!important;
        flex: 1 1 auto!important;
    }
    .modal-plans .box-stage div {
        width: auto;
    }
    .modal-plans .box-stage .icon {
        width: 36px;
        height: 36px;
        text-align: center;
        line-height: 32px;
        background: #EDEDED;
        border-radius: 50%;
    }
    .modal-plans .box-stage.products .icon {
        background: #D2E7FF;
    }
    .modal-plans .box-stage .title {
        font-size: 16px;
        line-height: 20px;
        color: #636363;
        letter-spacing: 0;
        margin: 0;
    }
    .modal-plans .box-stage .line hr {
        width: 65px;
        border-color: #c4c4c4;
    }
</style>