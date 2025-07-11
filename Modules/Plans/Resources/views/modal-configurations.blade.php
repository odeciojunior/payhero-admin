<div class="modal fade modal-new-layout modal-plans"
     id="modal_config_cost_plan"
     data-backdrop="static"
     aria-hidden="true"
     role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
             id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom">
                <h4 class="modal-title bold">Configurações de custos</h4>
                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <i class="material-icons md-22">close</i>
                </button>
            </div>

            <div class="modal-body">
                <div class="height-auto">
                    <div class="nav-tabs-horizontal nav-tabs-horizontal-custom mb-30"
                         style="margin-left: -30px; margin-right: -30px;">
                        <ul class="nav nav-tabs nav-tabs-line"
                            role="tablist">
                            <li class="nav-item text-center"
                                style="width: 50%;">
                                <a id="tab_configuration_cost"
                                   class="nav-link active show"
                                   data-toggle="tab"
                                   href="#tab_configuration_cost-panel"
                                   aria-controls="tab_configuration_cost"
                                   role="tab">
                                    Configurações gerais
                                </a>
                            </li>
                            <li class="nav-item text-center"
                                style="width: 50%;">
                                <a id="tab_update_cost_block"
                                   class="nav-link"
                                   data-toggle="tab"
                                   href="#tab_update_cost_block-panel"
                                   aria-controls="tab_update_cost_block"
                                   role="tab">
                                    Alteração em bloco
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content"
                         id="tabs-modal-configuration">
                        <!-- Painel de Configurações custo -->
                        <div class="tab-pane fade show active"
                             id="tab_configuration_cost-panel"
                             role="tabpanel">
                            <div class="row"
                                 style="margin-bottom: 24px;">
                                <div class="col-sm-12">
                                    <h1
                                        style="font-size: 16px; line-height: 20px; color: #636363;font-weight: bold; margin-bottom: 4px;">
                                        Padrão de custos</h1>
                                    <p class="m-0"
                                       style="font-weight: normal; font-size: 12px; line-height: 15px; color: #989898;">
                                        Defina uma moeda padrão para emissão de notas fiscais</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6 div-cost_currency">
                                    <label for="cost_currency_type"
                                           class="d-flex align-items-center">
                                        <div class="mr-10">Moeda padrão</div>
                                        <span class="icon"><img src="/build/global/img/icon-info.svg"></span>
                                    </label>
                                    <select name="cost_currency_type"
                                            class="sirius-select"
                                            id="cost_currency_type">
                                        <option value="BRL"
                                                selected>R$ - Real Brasileiro (BRL)</option>
                                        <option value="USD">$ - Dólar Americano (USD)</option>
                                    </select>
                                </div>

                                {{-- <div class="form-group col-sm-6 div-cost_shopify">
                                    <label for="update_cost_shopify"
                                           class="d-flex align-items-center label-text">
                                        <div class="mr-10">Atualizar de acordo com Shopify</div>
                                        <span class="icon"><img src="/build/global/img/icon-info.svg"></span>
                                    </label>
                                    <div class="switch-holder d-flex align-items-center"
                                         style="height: 3.2876666667rem !important;">
                                        <label class="switch">
                                            <input type="checkbox"
                                                   id="update_cost_shopify"
                                                   name="check-values"
                                                   class="check"
                                                   value="1">
                                            <span class="slider round"></span>
                                        </label>
                                        <label for="update_cost_shopify_selector"
                                               style="margin: 0;">Ativado</label>
                                    </div>
                                </div> --}}
                            </div>

                            <div id="check-custom"
                                 style="margin-bottom: 1.429rem; display: none;">
                                <div class="switch-holder d-flex">
                                    <label class="switch">
                                        <input type="checkbox"
                                               id="cost_currency_type_all_plans"
                                               name="cost_currency_type_all_plans"
                                               class="check"
                                               value="0">
                                        <span class="slider round"></span>
                                    </label>
                                    <label for="product_amount_selector"
                                           style="margin: 0;">Alterar a moeda em todos os planos do projeto</label>
                                </div>
                            </div>
                        </div>

                        <!-- Painel de custo em bloco -->
                        <div class="tab-pane fade"
                             id="tab_update_cost_block-panel"
                             role="tabpanel">
                            <div class="box-description">
                                <div class="row"
                                     style="margin-bottom: 31px;">
                                    <div class="col-sm-6 form-group m-0">
                                        <label for="cost_plan">Novo custo</label>
                                        <input type="text"
                                               class="form-control"
                                               id="cost_plan"
                                               placeholder="R$">
                                    </div>
                                </div>

                                <div style="margin-bottom: 21px;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p
                                               style="margin-bottom: 0; font-weight: bold; font-size: 16px; line-height: 20px; color: #636363;">
                                                Selecione um produto</p>
                                            <span
                                                  style="font-weight: normal; font-size: 12px; line-height: 15px; color: #989898;">Selecione
                                                um ou mais produtos para alterar os custos</span>
                                        </div>
                                        {{-- <div class="d-flex justify-content-between align-items-center select-all" id="select-all" data-selected="false" style="cursor: pointer;">
                                            <div class="mr-10" style="font-weight: bold; font-size: 16px; margin-top: -4px;">Selecionar todos</div>
                                            <div class="check"></div>
                                        </div> --}}
                                    </div>
                                </div>

                                <div class="search-type"></div>
                            </div>

                            <div class="box-plans">
                                {{-- JS carrega --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-on">
                <button type="button"
                        class="btn btn-primary btn-lg bt-update-cost-block">Atualizar</button>
            </div>
        </div>
    </div>
</div>
