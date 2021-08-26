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
                                <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-products-plans.svg') }}" alt="Icon Products"></div>
                                <div class="title mr-15">Produtos</div>
                                <div class="line"><hr></div>
                            </div>
                
                            <div class="box-stage costs d-flex align-items-center flex-fill">
                                <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-costs-plans.svg') }}" alt="Icon Costs"></div>
                                <div class="title mr-15">Custos</div>
                                <div class="line"><hr></div>
                            </div>
                
                            <div class="box-stage informations d-flex align-items-center flex-fill">
                                <div class="icon mr-15"><img src="{{ asset('modules/global/img/icon-info-plans.svg') }}" alt="Icon Informations"></div>
                                <div class="title">Informações</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @include('plans::products-modal')

                {{-- @include('plans::costs-modal') --}}

                {{-- @include('plans::informations-modal') --}}
            </div>

            <div class="modal-footer">
                <button id="btn-modal-plan-voltar" type="button" class="btn btn-default btn-lg" role="button">Voltar</button>
                <button id="btn-modal-plan-finalizar" type="button" class="btn btn-primary btn-lg">Prosseguir</button>
            </div>
        </div>
    </div>
</div>