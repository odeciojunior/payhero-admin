<!-- Page -->
<div class='row'>
    <div style='width:100%'>
        <div class='form-group'>
            <a id='add-shipping' class='btn btn-primary float-right' data-toggle='modal' data-target='#modal-content' style='color:white; margin-right:10px;'>
                <i class='icon wb-user-add' aria-hidden='true'></i>Adicionar Frete
            </a>
            <a id='config-shipping' class='btn btn-primary float-right' data-toggle='modal' data-target='#modal-content-shipping' style='color:white;'>
                <i class='icon wb-user-add' aria-hidden='true'></i>Configurações Frete
            </a>
        </div>
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
<!-- Modal -->
<div class='modal fade modal-3d-flip-vertical example-modal-lg' id='modal-content-shipping' tabindex='-1' role='dialog' aria-labelledby='TituloModal' aria-hidden='true'>
    <div class='modal-dialog modal-simple'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='TituloModal'>Configuração Frete</h5>
                <button type='button' class='close' data-dismiss='modal' aria-label='Fechar'>
                    <span aria-hidden='true'>&times;</span>
                </button>
            </div>
            <div class='modal-body form-group col-12'>
                <div class='row ' id='modal-data-shipping'>
                    <form id='form-config-shipping' class='row'>
                        <div class='form-group col-xl-6 col-lg-6'>
                            <label for='shipping_project'>Possui Frete</label>
                            <select name='shipment' class='form-control' id='shippement'>
                                <option value='1' {{$project->shippement == '1' ? 'selected': ''}}>Sim</option>
                                <option value='0' {{$project->shippement == '0' ? 'selected': ''}}>Não</option>
                            </select>
                        </div>
                        <div id='div-carrier' class='form-group col-xl-6 col-lg-6' style='{{$project->frete? 'display:block;' : ''}}'>
                            <label for='carrier-transport'>Transportadora</label>
                            <select name='carrier' type='text' class='form-control' id='carrier-transport' required>
                                <option value='2' selected>Despacho próprio</option>
                            </select>
                        </div>
                        <div id='div-shipment-responsible' class='form-group col-xl-6 col-lg-6' style='{{$project->frete? 'display:block;' : ''}}'>
                            <label for='shipment_responsible'>Responsável pelo frete</label>
                            <select name='shipment_responsible' type='text' class='form-control' id='shipment_responsible'>
                                <option value='owner' {{$project->shipment_responsible == 'owner'?'selected':''}}>Proprietário</option>
                                <option value='partners' disabled {{$project->shipment_responsible == 'partners'? 'selected':''}}>Proprietário + parceiros (Em Breve)</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>
                <button type='button' id='bt-add-shipping-config' class='btn btn-success' data-dismiss="modal">Confirmar</button>
            </div>
        </div>
    </div>
</div>
