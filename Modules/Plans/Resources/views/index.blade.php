<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div id="add-plan" class="d-flex align-items-center justify-content-end pointer" data-toggle="modal" data-target="#modal-content">
            <span class="link-button-dependent red"> Adicionar Plano </span>
            <a class="ml-10 rounded-add pointer"><i class="icon wb-plus" aria-hidden="true"></i></a>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-plans' class='table text-left table-pixels table-striped' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title' width='300px'>Nome</td>
                        <td class='table-title' width='300px'>Descrição</td>
                        <td class='table-title' width='300px'>Link</td>
                        <td class='table-title' width='300px'>Preço</td>
                        <td class='table-title' width='300px'>Status</td>
                        <td class='table-title text-center' width='200px'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-plan'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>

