<style>
    .tooltip-inner {
        background: #f5f7f8;
    }

    .logo-pixels:hover {
        padding: 5px;
        border-radius: 50px;
        border: 2px solid #2DA6F6;
    }

    .logo-pixels {
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        -ms-transition: all 0.3s;
        -o-transition: all 0.3s;
        transition: all 0.3s;

        width: 72px;
        margin: 0 auto;
    }

    .slider::before {
        height: 22px;
        width: 22px;
        left: -3px;
        top: 2px;
    }

    .font-text {
        font: normal normal normal 16px Muli;
    }
</style>

<div class="card card-body" style="margin-bottom: 25px; padding-bottom: 0;">
    <div class='row no-gutters mb-20'>
        <div class="top-holder text-right mb-0" style="width: 100%;">
            <div class="d-flex align-items-center">
                <div class="col-sm-12">
                    <div class="d-flex justify-content-end">
                        <div class="btn-holder d-flex align-items-center pointer btn-config-pixel" style="padding-right: 10px; border-right: 1px solid #EDEDED; margin-top: -20px; margin-bottom: -20px; margin-right: 20px;">
                            <span class="link-button-dependent">Configurações </span>
                            <a class="rounded-add pointer" style="background: none;">
                                <img src="{{ asset('modules/global/img/svg/settings.svg') }}" height="22">
                            </a>
                        </div>
                        <div id="add-pixel" class="btn-holder d-flex align-items-center pointer">
                            <span class="link-button-dependent blue">Adicionar </span>
                            <a class="ml-10 rounded-add pointer" style="display: inline-flex;">
                                <img src="/modules/global/img/icon-add.svg" style="width: 18px;">
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
                <table id='table-pixel' class='table text-left table-pixels table-striped unify' style="width: 100%; margin-bottom: 0px;">
                    <thead>
                    <tr>
                        <td class='table-title'>Nome</td>
                        <td class='table-title'>Código</td>
                        <td class='table-title'>Plataforma</td>
                        <td class='table-title text-center'>Status</td>
                        <td class='table-title options-column-width text-center'>Opções</td>
                    </tr>
                    </thead>
                    <tbody id='data-table-pixel' class='min-row-height'>
                    {{-- js carregando dados --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center justify-content-md-end">
    <ul id="pagination-pixels" class="pagination-sm margin-chat-pagination text-right m-0">
        {{-- js carrega... --}}
    </ul>
</div>

<!-- Create -->
<div id="modal-create-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        @include('pixels::create')
    </div>
</div>

<!-- Edit -->
<div id="modal-edit-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        @include('pixels::edit')
    </div>
</div>

<!-- Details -->
<div id="modal-detail-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Detalhes do pixel</h4>
                <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('pixels::show')
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div id="modal-delete-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" role="dialog" tabindex="-1">
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
                <button type="button"
                        class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button pixel="" type="button"
                        class="col-4 btn border-0 btn-outline btn-delete btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        data-dismiss="modal" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!--Modal Informações de pixels-->
<div id="modal-config-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        @include('pixels::config')
    </div>
</div>
<!--Modal Informações de pixels-->
