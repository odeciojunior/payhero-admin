<!-- Page -->
<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div id="add-domain" class="d-flex align-items-center justify-content-end pointer" data-toggle="modal" data-target="#modal-content">
            <span class="link-button-dependent red"> Adicionar domínio </span>
            <a class="ml-10 rounded-add pointer"> <i class="icon wb-plus" aria-hidden="true"></i> </a>
        </div>
    </div>
</div>

<div class="card shadow">

    <div style='min-height: 300px'>
    <div class='page-invoice-table table-responsive'>
        <table id='tabela-dominios' class='table text-left table-dominios table-striped' style='width:100%'>
            <thead>
                <tr>
                    <td class='table-title' width='400px'>Nome</td>
                    <td class='table-title' width='400px'>Ip do domínio</td>
                    <td class='table-title' width='400px'>Status</td>
                    <td class='table-title' width='200px'>Opções</td>
                </tr>
            </thead>
            <tbody id='domain-table-body'>
                {{-- js carregando dados --}}
            </tbody>
        </table>
    </div>
    </div>

</div>

    <!-- Modal detalhes do domínio -->
    <div class='modal fade example-modal-lg modal-3d-flip-vertical' id='modal-detalhes-dominio' aria-hidden='true' aria-labelledby='exampleModalTitle' role='dialog' tabindex='-1'>
        <div class='modal-dialog modal-simple'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'></span>
                    </button>
                    <h4 id='modal-dominio-titulo' class='modal-title' style='width:100%; text-align:center;'></h4>
                </div>
                <div class='modal-body pr-30 pl-30' id='modal-dominio-body'>
                            <label for='information'>Descrição</label>
                            <input name='information' class='form-control' id='information' placeholder='descricao'>
                </div>
                <div class='modal-footer'>
                    <button type='button' id='btn-save-updated' class='btn btn-success' data-dismiss='modal'>Salvar</button>
                    <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
