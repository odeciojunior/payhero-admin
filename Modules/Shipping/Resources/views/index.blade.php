<!-- Page -->
<div class="card card-body" style="margin-bottom: 25px; padding-bottom: 0;">
    <div class='row no-gutters mb-20'>
        <div class="top-holder text-right mb-0" style="width: 100%;">
            <div class='d-flex align-items-center'>
                <div class='col-sm-12'>
                    <div class="d-flex justify-content-end">
                        <div class="btn-holder add-shipping d-flex align-items-center pointer" data-toggle="modal" data-target="#modal-create-shipping">
                            <span class="link-button-dependent blue"> Adicionar </span>
                            <a class="ml-10 rounded-add pointer" style="display: inline-flex;">
                                <img src="/build/global/img/icon-add.svg" style="width: 18px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow" style="margin: 0 -1.429rem;">
        <div style='min-height: 300px'>
            <div class='page-invoice-table table-responsive'>
                <table id='tabela-fretes' class='table text-left table-fretes table-striped unify' style="width: 100%; margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <td class='table-title' >Tipo</td>
                            <td class='table-title' >Descrição</td>
                            <td class='table-title' >Valor</td>
                            <td class='table-title' >Informação</td>
                            <td class='table-title text-center' >Status</td>
                            <td class='table-title display-sm-none display-m-none' style='text-align:center' >Pré-Selecionado</td>
                            <td class='table-title text-center options-column-width'></td>
                        </tr>
                    </thead>
                    <tbody id='dados-tabela-frete' class='min-row-height'>
                        {{-- js carregando dados --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center justify-content-md-end">
    <ul id="pagination-shippings" class="pagination-sm margin-chat-pagination text-right m-0">
        {{-- js carrega... --}}
    </ul>
</div>

<!-- Details -->
<div id="modal-detail-shipping" class="modal fade example-modal-lg modal-slide-bottom" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Detalhes do frete</h4>
                <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
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
<div id="modal-create-shipping" class="modal fade modal-new-layout" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title bold" id="modal-title">Cadastrar frete</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-22">close</i>
                </button>
            </div>
            <div class="modal-body py-0">
                @include('shipping::create')
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-mobile-modal-close" class="btn btn-lg btn-default mr-3" data-dismiss="modal">
                    Fechar
                </button>
                <button type="button" class="btn btn-lg btn-primary btn-save" data-dismiss="modal">
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit -->
<div id="modal-edit-shipping" class="modal fade modal-new-layout" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title bold" id="modal-title">Editar frete</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-22">close</i>
                </button>
            </div>
            <div class="modal-body py-0">
                @include('shipping::edit')
            </div>
            <div class="modal-footer">
                <button type="button" id="btn-mobile-modal-close" class="btn btn-lg btn-default mr-3" data-dismiss="modal">
                    Fechar
                </button>
                <button type="button" class="btn btn-lg btn-primary btn-update" data-dismiss="modal">
                    Salvar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div id="modal-delete-shipping" class="modal fade example-modal-lg modal-slide-bottom" aria-hidden="true" role="dialog" tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content">
            <div class="modal-header text-center">
                <a class="pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
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
                <button type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button id="btn-delete-frete" type="button" class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" frete="" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>
