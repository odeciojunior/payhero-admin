<div class="card card-body" style="margin-bottom: 25px; padding-bottom: 0;">
    <div class="row no-gutters mb-20">
        <div class="top-holder w-full text-right mb-0" style="width: 100%;">
            <div class='d-flex align-items-center'>
                <div class='col-md-12'>
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="btn-holder add-order-bump d-flex align-items-center pointer" data-toggle="modal"
                             data-target="#modal-store-order-bump">
                            <span class="link-button-dependent blue"> Adicionar </span>
                            <a class="ml-10 rounded-add pointer" style="display: inline-flex;">
                                <img src="/build/global/img/icon-add.svg" style="width: 18px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow" style="margin: 0 -1.429rem;">
        <div style="min-height: 300px">
            <div class="page-invoice-table table-responsive">
                <table id="table-order-bump" class="table text-left table-striped unify"
                       style="width: 100%; margin-bottom: 0px;">
                    <thead>
                    <tr>
                        <td class="table-title">Descrição</td>
                        <td class="table-title text-center">Status</td>
                        <td class="table-title text-center options-column-width"></td>
                    </tr>
                    </thead>
                    <tbody class="min-row-height">
                    {{-- js carrega... --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center justify-content-md-end">
    <ul id="pagination-order-bump" class="pagination-s margin-chat-pagination float-right text-right m-0">
        {{-- js carrega... --}}
    </ul>
</div>

<!-- Modal Show -->
<div id="modal-show-order-bump" class="modal fade modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center">Detalhes do Order Bump</h4>
            </div>
            <div class="modal-body">
                <table id="order-bump-show-table" class="table table-striped mb-0 w-full">
                    <tr>
                        <td class="table-title">Descrição</td>
                        <td class="text-left order-bump-description"></td>
                    </tr>
                    <tr>
                        <td class="table-title">Desconto</td>
                        <td class="text-left order-bump-discount"></td>
                    </tr>
                    <tr>
                        <td class="table-title">Ao selecionar os fretes</td>
                        <td class="text-left order-bump-apply-shipping"></td>
                    </tr>
                    <tr>
                        <td class="table-title">Ao comprar planos</td>
                        <td class="text-left order-bump-apply-plans"></td>
                    </tr>
                    <tr>
                        <td class="table-title">Oferecer planos</td>
                        <td class="text-left order-bump-offer-plans"></td>
                    </tr>
                    <tr>
                        <td class="table-title">Status</td>
                        <td class="text-left order-bump-status"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Show -->

<!-- Modal Store -->
<div id="modal-store-order-bump" class="modal fade modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple w-md-450">
        <div class="modal-content">
            <div class="modal-header py-15 pl-20 pr-40">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center">Novo Order Bump</h4>
            </div>
            <div class="modal-body pb-0">
                <form id="form-store-order-bump" method="POST">
                    @csrf
                    <div class="form-group mb-10">
                        <label for="link">Descrição</label>
                        <div class="d-flex input-group">
                            <input type="text" class="form-control" name="description" id="store-description-order-bump"
                                   placeholder="Digite a descrição">
                        </div>
                    </div>
                    <div class="form-group mb-10">
                        <label for="link">Desconto</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="discount" id="store-discount-order-bump"
                                   placeholder="Digite o valor do desconto" maxlength="2" data-mask="0#">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mb-10">
                        <div class="switch-holder w-full">
                            <label for="store-active-flag-order-bump">Status</label>
                            <br>
                            <label class="switch">
                                <input type="checkbox" name="active_flag" id="store-active-flag-order-bump" class="check" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="switch-holder w-full">
                            <label>Usar variantes</label>
                            <br>
                            <label class="switch">
                                <input type="checkbox" name="use_variants" class="use-variants-order-bump check" value="1" checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-10">
                        <label for="store-apply-on-shipping-order-bump">Ao selecionar o frete:</label>
                        <select name="apply_on_shipping[]" id="store-apply-on-shipping-order-bump" class="form-control"
                                multiple="multiple" style="width:100%">
                        </select>
                    </div>
                    <div class="form-group mb-10 apply-on-plan-container">
                        <label for="link">Ao comprar o plano:</label>
                        <select name="apply_on_plans[]" id="store-apply-on-plans-order-bump" class="form-control"
                                multiple="multiple" style="width:100%"></select>
                    </div>
                    <div class="form-group mb-10 offer-plan-container">
                        <label for="link">Oferecer o plano:</label>
                        <select name="offer_plans[]" id="store-offer-plans-order-bump" class="form-control"
                                multiple="multiple" style="width:100%"></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-success" id="btn-store-order-bump">Salvar</button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Store -->

<!-- Modal Update -->
<div id="modal-update-order-bump" class="modal fade modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple w-md-450">
        <div class="modal-content">
            <div class="modal-header py-15 pl-20 pr-40">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center">Atualizar Order Bump</h4>
            </div>
            <div class="modal-body pb-0">
                <form id="form-update-order-bump" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-10">
                        <label for="link">Descrição</label>
                        <div class="d-flex input-group">
                            <input type="text" class="form-control" name="description"
                                   id="update-description-order-bump"
                                   placeholder="Digite a descrição">
                        </div>
                    </div>
                    <div class="form-group mb-10">
                        <label for="link">Desconto</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="discount" id="update-discount-order-bump"
                                   placeholder="Digite o valor do desconto" maxlength="2" data-mask="0#">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mb-10">
                        <div class="switch-holder w-full">
                            <label for="update-active-flag-order-bump">Status</label>
                            <br>
                            <label class="switch">
                                <input type="checkbox" name="active_flag" id="update-active-flag-order-bump" class="check">
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="switch-holder w-full">
                            <label>Usar variantes</label>
                            <br>
                            <label class="switch">
                                <input type="checkbox" name="use_variants" class="use-variants-order-bump check">
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group mb-10">
                        <label for="update-apply-on-shipping-order-bump">Ao selecionar o frete:</label>
                        <select name="apply_on_shipping[]" id="update-apply-on-shipping-order-bump" class="form-control"
                                multiple="multiple" style="width:100%"></select>
                    </div>
                    <div class="form-group mb-10 apply-on-plan-container">
                        <label for="link">Ao comprar o plano</label>
                        <select name="apply_on_plans[]" id="update-apply-on-plans-order-bump" class="form-control"
                                multiple="multiple" style="width:100%"></select>
                    </div>
                    <div class="form-group mb-10 offer-plan-container">
                        <label for="link">Oferecer o plano</label>
                        <select name="offer_plans[]" id="update-offer-plans-order-bump" class="form-control"
                                multiple="multiple" style="width:100%"></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-success" id="btn-update-order-bump">Atualizar</button>
            </div>
        </div>
    </div>
</div>
<!-- End Modal Update -->
<!-- Delete -->
<div id="modal-delete-order-bump" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true"
     role="dialog" tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body text-center p-20">
                <div class="d-flex justify-content-center">
                    <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                </div>
                <h3 class="black"> Você tem certeza? </h3>
                <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-center">
                <button type="button"
                        class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button order-bump-id="" type="button" data-dismiss="modal" id="btn-delete-orderbump"
                        class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>
