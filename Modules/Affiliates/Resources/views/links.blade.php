<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class='d-flex align-items-center justify-content-end'>
                <div class="input-group">
                    <input type="text" class="form-control" id='plan-name' name="plan" placeholder="Nome">
                    <span class="input-group-append" id='btn-search-link'>
                      <button type="submit" class="btn btn-primary btn-sm"><i class="icon wb-search" aria-hidden="true"></i></button>
                    </span>
                </div>
            <div class='col-md-6'>
                <div class="d-flex align-items-center justify-content-end">
                    <span class="link-button-dependent red"> Adicionar Link </span>
                    <a id="add-link"  data-toggle="modal" data-target="#modal-create-link" class="ml-10 rounded-add pointer"><i class="o-add-1" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-links' class='table text-left table-links table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title'>Descrição</td>
                        <td class='table-title'>Link Afiliado</td>
                        <td class='table-title text-center'>Preço</td>
                        <td class='table-title text-center'>Ações</td>
                    </tr>
                </thead>
                <tbody id='data-table-link' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-links"  class="pagination-sm margin-chat-pagination text-right"
        style="margin-top:10px;position:relative;float:right">
    {{-- js pagination carrega --}}
</ul>

<!-- Create -->
<div id="modal-create-link" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Novo link</h4>
                <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div style='min-height: 100px'>
                @include('affiliates::createlink')
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
<div id="modal-edit-link" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Editar link</h4>
                <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('affiliates::editlink')
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

<!-- Details -->
<div id="modal-detail-link" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Detalhes do link</h4>
                <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('affiliates::showlink')
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div id="modal-delete-link" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" role="dialog" tabindex="-1">
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
                    <b>Cancelar </b>
                </button>
                <button link="" type="button" data-dismiss="modal" class="col-4 btn border-0 btn-delete btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>

