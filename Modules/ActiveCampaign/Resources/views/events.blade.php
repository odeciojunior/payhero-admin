<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class='d-flex align-items-center justify-content-end'>
            <div class='col-md-6'>
                <div class='col-md-2'></div>
                <div id="add-event" class="d-flex col-md-5 align-items-center float-right justify-content-end pointer">
                    <span class="link-button-dependent red"> Adicionar Evento </span>
                    <a class="ml-10 rounded-add pointer"><i class="o-add-1" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-events' class='table text-left table-pixels table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class=''>Evento</td>
                        <td class=''>Adicionar tags</td>
                        <td class=''>Remover Tags</td>
                        <td class=''>Adicionar na lista</td>
                        <td class=''>Remover da lista</td>
                        <td class='text-center options-column-width'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-event' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="container-pagination" class="d-none row justify-content-center justify-content-md-end pr-md-15 pb-25">
    <ul id="pagination-events" class="pagination-sm margin-chat-pagination pagination-style">
        {{-- js pagination carrega --}}
    </ul>
</div>
<!-- Modal padrão para adicionar Adicionar e Editar -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_event" role="dialog" tabindex="-1">
    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10" id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title-event"></h4>
                <a id="modal-button-close" class="pointer close btn-close-add-event" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-add-event-body" class="modal-body" style='min-height: 100px'>
                <div class="load-list-tags">
                    <div class="text-center">Carregando tags e listas <br>
                        <div class="loader loader-circle text-center"></div>
                    </div>
                </div>
                @include('activecampaign::createevent')
                @include('activecampaign::editevent')
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
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-error-event" role="dialog" tabindex="-1">
    <div id="modal-add-size-event-error" class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10" id="content-modal-event-error">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title-event-error"></h4>
                <a id="modal-button-close" class="pointer close btn-close-add-event" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-add-event-body-error" class="modal-body" style='min-height: 100px'>
            </div>
            <div class="modal-footer" id='modal-footer-event-error'>
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button id="btn-modal-event-error" type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success" data-dismiss="modal">
                    <i class="material-icons btn-fix"> save </i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Modal detalhes do plano -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_details_event" role="dialog" tabindex="-1">
    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10" id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title-details"></h4>
                <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-details-body" class="modal-body" style='min-height: 100px'>
                @include('activecampaign::details')
            </div>
        </div>
    </div>
</div>
<!-- Modal padrão para excluir -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-event" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content">
            <div class="modal-header text-center">
                <a class="pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
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
                <button id='btn-event-cancel' type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar </b>
                </button>
                <button id="btn-delete-event" type="button" class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>
