<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class='d-flex align-items-center justify-content-end'>
                <div class="input-group">
                    <input type="text" class="form-control" id='plan-name' name="plan" placeholder="Nome">
                    <span class="input-group-append" id='btn-search-plan'>
                      <button type="submit" class="btn btn-primary btn-sm"><i class="icon wb-search" aria-hidden="true"></i></button>
                    </span>
                </div>
            <div class='col-md-6'>
                <div class='col-md-2'></div>
                <div id="add-plan" class="d-flex col-md-5 align-items-center float-right justify-content-end pointer">
                    <span class="link-button-dependent red"> Adicionar Plano </span>
                    <a class="ml-10 rounded-add pointer"><i class="icon wb-plus" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-plans' class='table text-left table-pixels table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title'>Nome</td>
                        <td class='table-title'>Descrição</td>
                        <td class='table-title'>Link</td>
                        <td class='table-title'>Preço</td>
                        <td class='table-title'>Status</td>
                        <td class='table-title text-center options-column-width'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-plan' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-plans" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
    {{-- js pagination carrega --}}
</ul>
<!-- Modal padrão para adicionar Adicionar e Editar -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_plan" role="dialog" tabindex="-1">
    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10" id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title-plan"></h4>
                <a id="modal-button-close" class="close-card pointer close btn-close-add-plan" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-add-plan-body" class="modal-body" style='min-height: 100px'>
                @include('plans::create')
                @include('plans::edit')
            </div>
            <div class="modal-footer">
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button id="btn-modal" type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success" data-dismiss="modal">
                    <i class="material-icons btn-fix"> save </i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>
{{-- Modal error --}}
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-error-plan" role="dialog" tabindex="-1">
    <div id="modal-add-size-plan-error" class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10" id="content-modal-plan-error">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title-plan-error"></h4>
                <a id="modal-button-close" class="close-card pointer close btn-close-add-plan" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-add-plan-body-error" class="modal-body" style='min-height: 100px'>
            </div>
            <div class="modal-footer" id='modal-footer-plan-error'>
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button id="btn-modal-plan-error" type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success" data-dismiss="modal">
                    <i class="material-icons btn-fix"> save </i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal detalhes do plano -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_details_plan" role="dialog" tabindex="-1">
    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10" id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title-details"></h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-details-body" class="modal-body" style='min-height: 100px'>
                @include('plans::details')
            </div>
        </div>
    </div>
</div>
<!-- Modal padrão para excluir -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-plan" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content">
            <div class="modal-header text-center">
                <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal_excluir_body" class="modal-body text-center p-20">
                <div class="d-flex justify-content-center">
                    <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                </div>
                <h3 class="black"> Você tem certeza? </h3>
                <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-center">
                <button id='btn-plan-cancel' type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                <button id="btn-delete-plan" type="button" class="col-4 btn btn-danger" style="width: 20%;">Excluir</button>
            </div>
        </div>
    </div>
</div>
