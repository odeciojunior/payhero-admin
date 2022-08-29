<div class="card card-body"
     style="margin-bottom: 25px; padding-bottom: 0;">
    <div class='row no-gutters mb-20'>
        <div class="top-holder text-right mb-0"
             style="width: 100%;">
            <div class='d-flex align-items-center justify-content-end'>
                <div class='col-md-5'>
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               id='plan-name'
                               name="plan"
                               placeholder="Pesquisa por nome">
                        <span class="input-group-append"
                              id='btn-search-plan'>
                            <button type="submit"
                                    class="btn btn-primary btn-sm">
                                <img src="/build/global/img/icon-search_.svg">
                            </button>
                        </span>
                    </div>
                </div>
                <div class='col-md-7'>
                    <div class="d-flex justify-content-end">
                        <div id="config-cost-plan"
                             class="btn-holder d-flex align-items-center pointer"
                             style="padding-right: 10px; border-right: 1px solid #EDEDED; margin-top: -20px; margin-bottom: -20px; margin-right: 20px;">
                            <span class="link-button-dependent">Configurar custos </span>
                            <a class="rounded-add pointer bg-secondary">
                                <img src="{{ mix('build/global/img/svg/settings.svg') }}"
                                     height="22">
                            </a>
                        </div>
                        <div class="btn-holder add-plan d-flex align-items-center pointer">
                            <span class="link-button-dependent blue">Adicionar </span>
                            <a class="ml-10 rounded-add pointer"
                               style="display: inline-flex;">
                                <img src="/build/global/img/icon-add.svg"
                                     style="width: 18px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow"
         style="margin: 0 -1.429rem;">
        <input type="hidden" id="currency_type_project">
        <div>
            <div class='page-invoice-table table-responsive' style='border-radius: 0 0 12px 12px;'>
                <table id='table-plans' class='table text-left table-pixels table-striped unify mb-0' style='width: 100%;'>
                    <thead>
                        <tr>
                            <td class=''>Nome</td>
                            <td class=''>Descrição</td>
                            <td class=''>Valor</td>
                            <td class='text-center'style='max-width: 140px;'>URL Checkout</td>
                            <td class='text-center'>Status</td>
                            <td class='text-center options-column-width'></td>
                        </tr>
                    </thead>
                    <tbody id='data-table-plan'
                           class='min-row-height'>
                        {{-- js carregando dados --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center justify-content-md-end">
    <ul id="pagination-plans" class="pagination-sm margin-chat-pagination text-right m-0 pagination-style">
        {{-- js pagination carrega --}}
    </ul>
</div>

<div class="d-none">
    <select name="select-products"
            id="select-products"
            name="products[]">
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

<!-- Modal configurations-->
@include('plans::modal-configurations')
