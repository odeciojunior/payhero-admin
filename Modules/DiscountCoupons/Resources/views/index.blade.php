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
                        <td class='table-title' style="min-width:120px;">Nome</td>
                        <td class='table-title' style="min-width:120px;">Tipo</td>
                        <td class='table-title' style="min-width:120px;">Valor</td>
                        <td class='table-title' style="min-width:120px;">Código</td>
                        <td class='table-title' style="min-width:120px;">Status</td>
                        <td class='table-title text-center'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-coupon'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
