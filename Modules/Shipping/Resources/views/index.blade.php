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
    <!-- Modal detalhes da venda -->
    <div class='modal fade example-modal-lg modal-3d-flip-vertical' id='modal-detalhes-frete' aria-hidden='true' aria-labelledby='exampleModalTitle' role='dialog' tabindex='-1'>
        <div class='modal-dialog modal-simple'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'></span>
                    </button>
                    <h4 id='modal-frete-titulo' class='modal-title' style='width:100%; text-align:center;'></h4>
                </div>
                <div id='modal-frete-body' class='modal-body'>
                    <div class='row'>
                        <div class='form-group col-12'>
                            <label for='information'>Descrição</label>
                            <input name='information' class='form-control' id='information' placeholder='descricao'>
                        </div>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' id='btn-save-updated' class='btn btn-success' data-dismiss='modal'>Salvar</button>
                    <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
</div>
