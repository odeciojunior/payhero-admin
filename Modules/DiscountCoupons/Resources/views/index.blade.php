<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div id="add-coupon" class="d-flex align-items-center justify-content-end pointer" data-toggle="modal" data-target="#modal-content">
            <span class="link-button-dependent red"> Adicionar Cupom </span>
            <a class="ml-10 rounded-add pointer"><i class="icon wb-plus" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='tabela-coupon' class='table text-left table-coupon table-striped' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title' width='300px'>Nome</td>
                        <td class='table-title' width='300px'>Tipo</td>
                        <td class='table-title' width='300px'>Valor</td>
                        <td class='table-title' width='300px'>Código</td>
                        <td class='table-title' width='200px'>Status</td>
                        <td class='table-title text-center' width='200px'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-coupon'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-coupons" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
    {{-- js carrega... --}}
</ul>
