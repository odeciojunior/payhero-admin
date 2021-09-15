<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class='d-flex align-items-center justify-content-end'>
            <div class='col-md-6 pl-0'>
                <div class="input-group">
                    <input type="text" class="form-control" id='plan-name' name="plan" placeholder="Nome">
                    <span class="input-group-append" id='btn-search-plan'>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="icon wb-search" aria-hidden="true"></i></button>
                    </span>
                </div>
            </div>
            <div class='col-md-6 pr-0'>
                <div id="add-plan" class="d-inline-block align-items-center float-right justify-content-end pointer">
                    <span class="link-button-dependent red"> Adicionar Plano </span>
                    <a class="ml-10 rounded-add pointer" style="display: inline-flex;"><i class="o-add-1" aria-hidden="true"></i></a>
                </div>
                <div class='div-config2' style='display: inline-block;'>
                    <div id="config-cost-plan" class="btn-holder d-flex align-items-center pointer mr-10 float-right">
                        <span class="link-button-dependent red"> Configurações Custo Plano </span>
                        <a class="ml-10 rounded-add pointer bg-secondary">
                            <i class="icon wb-settings" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <input type="hidden" id="currency_type_project">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-plans' class='table text-left table-pixels table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title'>Nome</td>
                        <td class='table-title'>Descrição</td>
                        <td class='table-title'>Link</td>
                        <td class='table-title'>Preço</td>
                        <td class='table-title'>Status</td>
                        <td class='table-title text-center options-column-width'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-plan' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="row d-flex justify-content-center justify-content-md-end">
    <ul id="pagination-plans" class="pagination-sm margin-chat-pagination text-right" style="margin-top:10px;position:relative;float:right">
        {{-- js pagination carrega --}}
    </ul>
</div>

<div class="d-none">
    <select name="select-products" id="select-products" name="products[]">
        {{-- js carregando dados --}}
    </select>
</div>

{{-- Modal create plan --}}
@include('plans::modal-create')

{{-- Modal edit plan --}}
@include('plans::modal-edit')

{{-- Modal details plan --}}
@include('plans::modal-details')

{{-- Modal delete plan --}}
@include('plans::modal-delete')

{{-- Modal error plan --}}
@include('plans::modal-error')

<!-- Modal configurações-->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_config_cost_plan" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" style='overflow-y: scroll;'>
    <div class="modal-dialog modal-dialog-centered d-flex justify-content-center">
        <div class="modal-content" id="conteudo_modal_add">
            <div class="modal-header mb-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center" style="font-weight: 700;">Configurações Custo Plano</h4>
            </div>
            <div class="pt-10 pr-20 pl-20 modal_config_cost_plan_body">
                <div class="page-content container">
                    <div class="mb-15">
                        <div class="nav-tabs-horizontal" data-plugin="tabs">
                            <ul class="nav nav-tabs nav-tabs-line" role="tablist" style="color: #ee535e">
                                <li class="nav-item" role="presentation">
                                    <a id="tab_configuration" class="nav-link active" data-toggle="tab" href="#tab_configuration_cost-panel" aria-controls="tab_configuration_cost" role="tab">
                                        Configurações de Custo
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a id="tab_update_cost_block" class="nav-link" data-toggle="tab" href="#tab_update_cost_block-panel" aria-controls="tab_update_cost_block" role="tab">
                                        Alterar Custo em bloco
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="shadow2" data-plugin="matchHeight">
                        <div class="tab-content">
                            <div class="tab-content">
                                <!-- Painel de Configurações custo-->
                                <div class="tab-pane active" id="tab_configuration_cost-panel" role="tabpanel">
                                    <div class="row">
                                        <div class="form-group col-md-6 col-sm-12">
                                            <label for="cost_currency_type"><br>Moeda padrão de custo</label>
                                            <select name="cost_currency_type" class="form-control select-pad" id="cost_currency_type">
                                                <option value="BRL">Real</option>
                                                <option value="USD">Dólar</option>
                                            </select>
                                            <p class="info pt-5" style="font-size: 10px;">
                                                <i class="icon wb-info-circle" aria-hidden="true"></i> Definir uma moeda padrão para
                                                a configuração dos seus planos. Configuração utilizada para emissão de notas
                                                fiscais.
                                            </p>
                                        </div>
                                        <div class="form-group col-md-6 col-sm-12">
                                            <label for="update_cost_shopify">Atualizar custo de acordo com o shopify</label>
                                            <select name="update_cost_shopify" class="form-control select-pad" id="update_cost_shopify">
                                                <option value="0">Não</option>
                                                <option value="1">Sim</option>
                                            </select>
                                            <p class="info pt-5" style="font-size: 10px;">
                                                <i class="icon wb-info-circle" aria-hidden="true"></i> Se configurado como sim, os custos serão atualizados sempre que houver alteração no shopify
                                            </p>
                                        </div>
                                        <div class="form-group col-md-12" id="div_update_cost_shopify" style="display: none;">
                                            <label for="update_cost_shopify">Deseja alterar a moeda do custo de todos os planos do projeto?</label>
                                            <select id="update_all_currency_cost" class="form-control select-pad">
                                                <option value="0">Não</option>
                                                <option value="1">Sim</option>
                                            </select>
                                        </div>
                                        <div class='col-md-12 mt-10 text-right'>
                                            <button type="button" class="btn btn-success bt-update-cost-configs">Atualizar</button>
                                        </div>
                                    </div>
                                </div>
                                <!--- Painel de custo em bloco-->
                                <div class="tab-pane" id="tab_update_cost_block-panel" role="tabpanel">
                                    <div class="row">
                                        <div class='col-md-12'>
                                            <label>Plano</label><br>
                                            <select id="add_cost_on_plans" class="form-control" style='width:100%; height: 60px;' data-plugin="select2"> </select>
                                        </div>
                                        <div class='col-md-4 mt-10'>
                                            <label>Custo</label><br>
                                            <input type="text" class="form-control" id="cost_plan">
                                        </div>
                                        <div class='col-md-12 mt-10 text-right'>
                                            <button type="button" class="btn btn-success bt-update-cost-block">Atualizar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{-- <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button> --}}
            </div>
        </div>
    </div>
</div>
