<!-- Page -->
<div class='panel pt-10 p-10' style='min-height: 300px'>
    <div class='page-invoice-table table-responsive'>
        <table id='tabela_fretes' class='table text-right table-fretes table-hover' style='width:100%'>
            <thead style='text-align:center;'>
                <tr>
                    <th style='vertical-align: middle;' class='table-title'><b>Descrição</b></th>
                    <th style='vertical-align: middle;' class='table-title'><b>Valor</b></th>
                    <th style='vertical-align: middle;' class='table-title'><b>Informação</b></th>
                    <th style='vertical-align: middle;' class='table-title'><b>Status</b></th>
                    <th style='vertical-align: middle;' class='table-title'><b>Pré Selecionado</b></th>
                </tr>
            </thead>
            <tbody id='dados-tabela-frete'>
                {{-- js carregando dados --}}
            </tbody>
        </table>
    </div>
    <!-- Modal detalhes da venda -->
    <div class='modal fade example-modal-lg' id='modal-detalhes' aria-hidden='true' aria-labelledby='exampleModalTitle' role='dialog' tabindex='-1'>
        <div class='modal-dialog modal-simple modal-sidebar modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'></span>
                    </button>
                    <h4 id='modal-frete-titulo' class='modal-title' style='width:100%; text-align:center;'></h4>
                </div>
                <div id='modal-venda-body' class='modal-body'>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</div>
