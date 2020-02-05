<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class='d-flex align-items-center justify-content-end'>
                <div class="input-group">
                    <input type="text" class="form-control" id='plan-name' name="plan" placeholder="Nome">
                    <span class="input-group-append" id='btn-search-link'>
                      <button type="submit" class="btn btn-primary btn-sm"><i class="icon wb-search" aria-hidden="true"></i></button>
                    </span>
                </div>
            <div class='col-md-6'>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-links' class='table text-left table-links table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title'>Link Plano</td>
                        <td class='table-title'>Link Afiliado</td>
                        <td class='table-title text-center'>Preço</td>
                        <td class='table-title text-center'>Ações</td>
                    </tr>
                </thead>
                <tbody id='data-table-link' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-links" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
    {{-- js pagination carrega --}}
</ul>

