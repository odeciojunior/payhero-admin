<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class='d-flex align-items-center'>
            <div class='col-md-5'>
                <div class="input-group">
                    <input type="text" class="form-control" id='plan-name' name="plan" placeholder="Pesquisa por nome">
                    <span class="input-group-append" id='btn-search-plan'>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <img src="/modules/global/img/icon-search_.svg">
                        </button>
                    </span>
                </div>
            </div>
            <div class='col-md-7'>
                <div class="d-flex justify-content-end">
                    <div id="config-cost-plan" class="btn-holder d-flex align-items-center pointer" style="margin-right: 32px;">
                        <span class="link-button-dependent">Configurar custos </span>
                        <a class="ml-10 rounded-add pointer bg-secondary">
                            <img src="/modules/global/img/icon-config.svg">
                        </a>
                    </div>
                    <div id="add-plan" class="btn-holder d-flex align-items-center pointer">
                        <span class="link-button-dependent blue">Adicionar plano </span>
                        <a class="ml-10 rounded-add pointer" style="display: inline-flex;">
                            <img src="/modules/global/img/icon-add.svg">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow" style="margin: 0 -1.429rem;">
    <input type="hidden" id="currency_type_project">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-plans' class='table text-left table-pixels table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title'>Nome</td>
                        <td class='table-title'>Descrição</td>
                        <td class='table-title'>Valor</td>
                        <td class='table-title text-center'>URL Checkout</td>
                        <td class='table-title text-center'>Status</td>
                        <td class='table-title text-center options-column-width'></td>
                    </tr>
                </thead>
                <tbody id='data-table-plan' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center justify-content-md-end" style="padding-right: 13px;">
    <ul id="pagination-plans" class="pagination-sm margin-chat-pagination text-right" style="margin-top: 10px; position: relative; float: right;">
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

<!-- Modal configurations-->
@include('plans::modal-configurations')