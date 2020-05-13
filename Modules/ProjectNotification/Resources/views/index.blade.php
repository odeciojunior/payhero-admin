<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        {{-- <div id="add-sms" class="d-flex align-items-center justify-content-end pointer" data-toggle="modal" data-target="#modal-content"> --}}
        <div id="add-sms" class="d-flex align-items-center justify-content-end pointer">
            <button type="button" class="ml-10 rounded-add pointer btn" disabled='true'>
                <i class="icon wb-plus" aria-hidden="true"></i></button>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='tabela-sms' class='table text-left table-sms table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title'>Tipo</td>
                        <td class='table-title display-m-none display-sm-none'>Evento</td>
                        <td class='table-title'>Tempo</td>
                        <td class='table-title'>Mensagem</td>
                        <td class='table-title'>Status</td>
                        <td class='table-title text-center options-column-width'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-sms' class='min-row-height'>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="pagination-project-notification" class="pagination-sm text-right margin-chat-pagination">
</div>

<!-- Edit -->
<div id="modal-edit-project-notification" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Editar Notificação</h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('projectnotification::edit')
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
<div id="modal-detail-project-notification" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Detalhes da Notificação</h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('projectnotification::show')
            </div>
        </div>
    </div>
</div>
