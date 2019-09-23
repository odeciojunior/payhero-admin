<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class='d-flex align-items-center justify-content-end'>
            <div id="add-plan" class="d-flex align-items-center justify-content-end pointer">
                <span class="link-button-dependent red"> Adicionar Plano </span>
                <a class="ml-10 rounded-add pointer"><i class="icon wb-plus" aria-hidden="true"></i></a>
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
                        <td class='table-title' >Nome</td>
                        <td class='table-title' >Descrição</td>
                        <td class='table-title' >Link</td>
                        <td class='table-title' >Preço</td>
                        <td class='table-title' >Status</td>
                        <td class='table-title text-center options-column-width' >Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-plan' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-plans" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
    {{-- js pagination carrega --}}
</ul>

<!-- Modal padrão para adicionar Adicionar e Editar -->
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_plan" role="dialog" tabindex="-1">
    <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10" id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title"></h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div id="modal-add-body" class="modal-body" style='min-height: 100px'>
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

{{--<div class="modal fade modal-3d-flip-vertical" id="modal-plans-error" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">--}}
{{--    <div class="modal-dialog modal-simple">--}}
{{--        <div id="modal-not-products" class='modal-content p-10'>--}}
{{--            <div class='header-modal simple-border-bottom'>--}}
{{--                <h2 id='modal-title-plans-erro' class='modal-title-plans-erro'>Ooooppsssss!</h2>--}}
{{--            </div>--}}
{{--            <div class='modal-body simple-border-bottom' style='padding-bottom:1%; padding-top:1%;'>--}}
{{--                <div class='swal2-icon swal2-error swal2-animate-error-icon' style='display:flex;'>--}}
{{--                                <span class='swal2-x-mark'>--}}
{{--                                    <span class='swal2-x-mark-line-left'></span>--}}
{{--                                    <span class='swal2-x-mark-line-right'></span>--}}
{{--                                </span>--}}
{{--                </div>--}}
{{--                <h3 align='center'>Você não cadastrou nenhum produto</h3>--}}
{{--                <h5 align='center'>Deseja cadastrar uma produto?--}}
{{--                    <a class='red pointer' href='/products/create'>clique aqui</a>--}}
{{--                </h5>--}}
{{--            </div>--}}
{{--            <div style='width:100%; text-align:center; padding-top:3%;'>--}}
{{--                <span class='btn btn-danger' data-dismiss='modal' style='font-size: 25px;'>Retornar</span>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
