<div class="row no-gutters mb-10">
    <div class="top-holder w-full text-right mb-5">
        <div class="d-flex align-items-center justify-content-end">
            <div id="add-order-bump" class="btn-holder  d-flex align-items-center pointer" data-toggle="modal"
                 data-target="#modal-store-order-bump">
                <span class="link-button-dependent red"> Adicionar Order Bump </span>
                <a class="ml-10 rounded-add pointer">
                    <i class="o-add-1 text-white"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style="min-height: 300px">
        <div class="page-invoice-table table-responsive">
            <table id="table-order-bump" class="table text-left table-striped">
                <thead>
                <tr>
                    <td class="table-title">Descrição</td>
                    <td class="table-title">Status</td>
                    <td class="table-title text-center options-column-width">Opções</td>
                </tr>
                </thead>
                <tbody class="min-row-height">
                {{-- js carrega... --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-order-bump" class="pagination-s margin-chat-pagination float-right text-right">
    {{-- js carrega... --}}
</ul>

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
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center">Novo Order Bump</h4>
            </div>
            <div class="modal-body pb-0">
                <form id="form-store-order-bump" method="POST">
                    @csrf
                    <div class="form-group mb-20">
                        <label for="link">Descrição</label>
                        <div class="d-flex input-group">
                            <input type="text" class="form-control" name="description" id="store-description-order-bump"
                                   placeholder="Digite a descrição">
                        </div>
                    </div>
                    <div class="form-group mb-20">
                        <label for="link">Desconto</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="discount" id="store-discount-order-bump"
                                   placeholder="Digite o valor do desconto" maxlength="2" data-mask="0#">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-20">
                        <label for="link">Ao comprar o plano</label>
                        <select name="apply_on_plans[]" id="store-apply-on-plans-order-bump" class="form-control"
                                data-plugin="select2" multiple="multiple" style="width:100%"></select>
                    </div>
                    <div class="form-group mb-20">
                        <label for="link">Oferecer o plano</label>
                        <select name="offer_plans[]" id="store-offer-plans-order-bump" class="form-control"
                                data-plugin="select2" multiple="multiple" style="width:100%"></select>
                    </div>
                    <div class="form-group">
                        <label for="link">Status</label>
                        <select name="active_flag" id="store-active-flag-order-bump" class="form-control">
                            <option value="1" selected="selected">Ativo</option>
                            <option value="0">Desativado</option>
                        </select>
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
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center">Atualizar Order Bump</h4>
            </div>
            <div class="modal-body pb-0">
                <form id="form-update-order-bump" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group mb-20">
                        <label for="link">Descrição</label>
                        <div class="d-flex input-group">
                            <input type="text" class="form-control" name="description" id="update-description-order-bump"
                                   placeholder="Digite a descrição">
                        </div>
                    </div>
                    <div class="form-group mb-20">
                        <label for="link">Desconto</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="discount" id="update-discount-order-bump"
                                   placeholder="Digite o valor do desconto" maxlength="2" data-mask="0#">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-20">
                        <label for="link">Ao comprar o plano</label>
                        <select name="apply_on_plans[]" id="update-apply-on-plans-order-bump" class="form-control"
                                data-plugin="select2" multiple="multiple" style="width:100%"></select>
                    </div>
                    <div class="form-group mb-20">
                        <label for="link">Oferecer o plano</label>
                        <select name="offer_plans[]" id="update-offer-plans-order-bump" class="form-control"
                                data-plugin="select2" multiple="multiple" style="width:100%"></select>
                    </div>
                    <div class="form-group">
                        <label for="link">Status</label>
                        <select name="active_flag" id="update-active-flag-order-bump" class="form-control">
                            <option value="1" selected="selected">Ativo</option>
                            <option value="0">Desativado</option>
                        </select>
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
<div id="modal-delete-order-bump" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" role="dialog" tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content">
            <div class="modal-header text-center">
                <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body text-center p-20">
                <div class="d-flex justify-content-center">
                    <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                </div>
                <h3 class="black"> Você tem certeza? </h3>
                <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-center">
                <button type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button order-bump-id="" type="button" data-dismiss="modal"  class="col-4 btn border-0 btn-delete btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>
