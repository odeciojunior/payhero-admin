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
                <div class="box-breadcrumbs">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="box-stage products active d-flex align-items-center">
                            <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-products-plans.svg') }}" alt="Icon Products"></div>
                            <div class="title">Produtos</div>
                        </div>

                        <div class="line"><hr></div>
            
                        <div class="box-stage details d-flex align-items-center">
                            <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-costs-plans.svg') }}" alt="Icon Costs"></div>
                            <div class="title">Detalhes</div>
                        </div>

                        <div class="line"><hr></div>
            
                        <div class="box-stage informations d-flex align-items-center">
                            <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-info-plans.svg') }}" alt="Icon Informations"></div>
                            <div class="title">Informações</div>
                        </div>
                    </div>
                </div>

                <div class="box-description">
                    <label for="search-product">Selecione os produtos do novo plano</label>
                    <input class="form-control form-control-lg" type="text" id="search-product" placeholder="Pesquisa por nome">
                </div>

                <div class="box-products" id="load-products">
                    {{-- JS carrega --}}
                </div>
            </div>

            <div class="modal-footer">
                <button id="btn-modal-plan-voltar" type="button" class="btn btn-default btn-lg" role="button">Voltar</button>
                <button id="btn-modal-plan-prosseguir" type="button" data-stage="1" class="btn btn-primary btn-lg">Prosseguir</button>
            </div>
        </div>
    </div>
</div>