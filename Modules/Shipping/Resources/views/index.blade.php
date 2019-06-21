<!-- Page -->
<div class='row'>
    <div style='width:100%'>
        <a id='add-shipping' class='btn btn-primary float-right' data-toggle='modal' data-target='#modal-content' style='color:white;'>
            <i class='icon wb-user-add' aria-hidden='true'></i>Adicionar Frete
        </a>
    </div>
</div>
<div class='panel pt-10 p-10' style='min-height: 300px'>
    <div class='page-invoice-table table-responsive'>
        <table id='tabela-fretes' class='table text-right table-fretes table-hover' style='width:100%'>
            <thead style='text-align:center;'>
                <tr>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Tipo</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Descrição</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Valor</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Informação</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Status</b></th>
                    <th style='vertical-align: middle;' class='table-title text-center'><b>Pré Selecionado</b></th>
                </tr>
            </thead>
            <tbody id='dados-tabela-frete'>
                {{-- js carregando dados --}}
            </tbody>
        </table>
    </div>
</div>
