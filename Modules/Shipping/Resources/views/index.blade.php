<!-- Page -->
<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class="d-flex align-items-center justify-content-end">
            <div id="add-shipping" class="btn-holder  d-flex align-items-center pointer" data-toggle="modal" data-target="#modal-create-shipping">
                <span class="link-button-dependent red"> Adicionar Frete </span>
                <a class="ml-10 rounded-add pointer"><i class="icon wb-plus" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='tabela-fretes' class='table text-left table-fretes table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title' >Tipo</td>
                        <td class='table-title' >Descrição</td>
                        <td class='table-title' >Valor</td>
                        <td class='table-title' >Informação</td>
                        <td class='table-title' >Status</td>
                        <td class='table-title display-sm-none display-m-none' style='text-align:center' >Pré-Selecionado</td>
                        <td class='table-title text-center options-column-width'>Opções</td>
                    </tr>
                </thead>
                <tbody id='dados-tabela-frete' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-shippings" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
    {{-- js carrega... --}}
</ul>

<!-- Details -->
<div id="modal-detail-shipping" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Detalhes do frete</h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('shipping::details')
            </div>
        </div>
    </div>
</div>

<!-- Create -->
<div id="modal-create-shipping" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Cadastrar frete</h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('shipping::create')
            </div>
            <div class="modal-footer">
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-save" data-dismiss="modal">
                    <i class="material-icons btn-fix"> save </i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit -->
<div id="modal-edit-shipping" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Editar frete</h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('shipping::edit')
            </div>
            <div class="modal-footer">
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-update" data-dismiss="modal">
                    <i class="material-icons btn-fix"> save </i> Atualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div id="modal-delete-shipping" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" role="dialog" tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content">
            <div class="modal-header text-center">
                <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body text-center p-20">
                <div class="d-flex justify-content-center">
                    <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                </div>
                <h3 class="black"> Você tem certeza? </h3>
                <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-center">
                <button type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                <button frete="" type="button" data-dismiss="modal" class="col-4 btn btn-danger btn-delete" style="width: 20%;">Excluir</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
{{--<div class='modal fade modal-3d-flip-vertical example-modal-lg' id='modal-content-shipping' tabindex='-1' role='dialog' aria-labelledby='TituloModal' aria-hidden='true'>--}}
{{--    <div class='modal-dialog modal-simple'>--}}
{{--        <div class='modal-content'>--}}
{{--            <div class='modal-header'>--}}
{{--                <h5 class='modal-title' id='TituloModal'>Configuração Frete</h5>--}}
{{--                <button type='button' class='close' data-dismiss='modal' aria-label='Fechar'>--}}
{{--                    <span aria-hidden='true'>&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class='modal-body form-group col-12'>--}}
{{--                <div class='row ' id='modal-data-shipping'>--}}
{{--                    <form id='form-config-shipping' class='row'>--}}
{{--                        <div class='form-group col-xl-6 col-lg-6'>--}}
{{--                            <label for='shipping_project'>Possui Frete</label>--}}
{{--                            <select name='shipment' class='form-control' id='shippement'>--}}
{{--                                <option value='1' {{$project->shippement == '1' ? 'selected': ''}}>Sim</option>--}}
{{--                                <option value='0' {{$project->shippement == '0' ? 'selected': ''}}>Não</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div id='div-carrier' class='form-group col-xl-6 col-lg-6' style='{{$project->frete? 'display:block;' : ''}}'>--}}
{{--                            <label for='carrier-transport'>Transportadora</label>--}}
{{--                            <select name='carrier' type='text' class='form-control' id='carrier-transport' required>--}}
{{--                                <option value='2' selected>Despacho próprio</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div id='div-shipment-responsible' class='form-group col-xl-6 col-lg-6' style='{{$project->frete? 'display:block;' : ''}}'>--}}
{{--                            <label for='shipment_responsible'>Responsável pelo frete</label>--}}
{{--                            <select name='shipment_responsible' type='text' class='form-control' id='shipment_responsible'>--}}
{{--                                <option value='owner' {{$project->shipment_responsible == 'owner'?'selected':''}}>Proprietário</option>--}}
{{--                                <option value='partners' disabled {{$project->shipment_responsible == 'partners'? 'selected':''}}>Proprietário + parceiros (Em Breve)</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class='modal-footer'>--}}
{{--                <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>--}}
{{--                <button type='button' id='bt-add-shipping-config' class='btn btn-success' data-dismiss="modal">Confirmar</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
