<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class="d-flex align-items-center justify-content-end">
            <div class='div-config'>
                <div id="config-review" class="btn-holder d-flex align-items-center pointer mr-20">
                    <span class="link-button-dependent red"> Configurações Review </span>
                    <a class="ml-10 btn-config rounded-add pointer bg-secondary text-white">
                        <span class="o-cogwheel-1" style="font-size: 18px;"></span> 
                    </a>
                </div>
            </div>
            <div id="add-review" class="btn-holder d-flex align-items-center pointer" data-toggle="modal" data-target="#modal_review">
                <span class="link-button-dependent red"> Adicionar Review </span>
                <a class="ml-10 rounded-add pointer">
                    <i class="icon wb-plus" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='table-responsive'>
            <table id='table-reviews' class='table text-left table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title'>Nome</td>
                        <td class='table-title'>Descrição</td>
                        <td class='table-title'>Classificação</td>
                        <td class='table-title'>Status</td>
                        <td class='table-title text-center options-column-width'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-reviews' class='min-row-height'>
                    {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-review" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
    {{-- js carrega... --}}
</ul>
{{-- Modal add-edit review --}}
<div class="modal fade modal-3d-flip-vertical" id="modal_review" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog">
    <div class="modal-dialog modal-lg d-flex justify-content-center">
        <div class="modal-content" id="modal_content_review_form">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center" style="font-weight: 700;"></h4>
            </div>
            <div class="pt-10 pr-20 pl-20 modal_review_body">
                @include('projectreviews::form')
            </div>
            <div class="modal-footer" style="margin-top: 15px">
                <button type="button" class="btn btn-success bt-review-save" style='display:none;'>Salvar</button>
                <button type="button" class="btn btn-success bt-review-update" style='display:none;'>Atualizar</button>                
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}

{{-- Modal config review --}}
<div class="modal fade modal-3d-flip-vertical" id="modal_config_review" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" style='overflow-y: scroll;'>
    <div class="modal-dialog d-flex justify-content-center">
        <div class="modal-content" id="modal_review_config">
            <div class="modal-header mb-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center" style="font-weight: 700;">Configurações das reviews</h4>
            </div>
            <div class="pt-10 pr-20 pl-20 modal_review_body">
                @include('projectreviews::config')
            </div>
            <div class="modal-footer pt-0">                
                <button type="button" class="btn btn-success bt-review-config-update">Atualizar</button>
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}

<!-- Details -->
<div id="modal-detail-review" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Exibição do Review</h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('projectreviews::show')
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}

{{-- Modal delete review --}}
<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-review" aria-hidden="true" role="dialog" tabindex="-1">
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
                <button type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button type="button" class="col-4 btn border-0 btn-outline btn-delete-review btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}

{{-- Modal visualizar configurações do review --}}
{{--<div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-view-review-config" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" style='overflow-y:auto;'>--}}
{{--    <div class="modal-dialog modal-lg d-flex justify-content-center">--}}
{{--        <div class="modal-content" id="">--}}
{{--            <div class="modal-header border-bottom">--}}
{{--                <h4 class="modal-title" id="modal-title">review no checkout</h4>--}}
{{--            </div>--}}
{{--            <div class="pt-0 px-0 modal_review_body">--}}
{{--                @include('projectreviewconfig::previewreviewconfig')--}}
{{--            </div>--}}
{{--            <div class="modal-footer text-right pt-20 border-top">--}}
{{--                <button class='btn btn-primary btn-sm btn-return-to-config' >--}}
{{--                    <i class="icon wb-settings" aria-hidden="true"></i>--}}
{{--                    Voltar para configurações--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{-- End Modal  --}}
