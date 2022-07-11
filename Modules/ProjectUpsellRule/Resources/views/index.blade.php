<div class="card card-body" style="margin-bottom: 25px; padding-bottom: 0;">
    <div class='row no-gutters mb-20'>
        <div class="top-holder text-right mb-0" style="width: 100%;">
            <div class="d-flex align-items-center">
                <div class="col-sm-12">
                    <div class="d-flex justify-content-end">
                        <div class="div-config d-flex align-items-center" style="display: none; padding-right: 10px; border-right: 1px solid #EDEDED; margin-top: -20px; margin-bottom: -20px; margin-right: 20px;">
                            <div id="config-upsell" class="btn-holder d-flex align-items-center pointer">
                                <span class="link-button-dependent">Configurações </span>
                                <a class="rounded-add pointer" style="background: none;">
                                    <img src="{{ mix('build/global/img/svg/settings.svg') }}" height="22">
                                </a>
                            </div>
                        </div>
                        <div class="btn-holder add-upsell d-flex align-items-center pointer" data-toggle="modal" data-target="#modal_add_upsell">
                            <span class="link-button-dependent blue">Adicionar </span>
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
                <table id='table-upsell' class='table text-left table-striped unify' style="width: 100%; margin-bottom: 0px;">
                    <thead>
                        <tr>
                            <td class='table-title'>Descrição</td>
                            <td class='table-title text-center'>Status</td>
                            <td class='table-title text-center options-column-width'></td>
                        </tr>
                    </thead>
                    <tbody id='data-table-upsell' class='min-row-height'>
                        {{-- js carregando dados --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center justify-content-md-end">
    <ul id="pagination-upsell" class="pagination-sm margin-chat-pagination text-right m-0">
        {{-- js carrega... --}}
    </ul>
</div>

{{-- Modal add-edit upsell --}}
<div class="modal fade example-modal-lg" id="modal_add_upsell" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog">
    <div class="modal-dialog modal-lg d-flex justify-content-center">
        <div class="modal-content w-450" id="conteudo_modal_add">
            <div class="modal-header py-15 pl-20 pr-40">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center" style="font-weight: 700;"></h4>
            </div>
            <div class="pr-20 pl-20 modal_upsell_body">
                @include('projectupsellrule::create')
                @include('projectupsellrule::edit')
            </div>
            <div class="modal-footer" style="margin-top: 15px">
                <button type="button" class="btn btn-success bt-upsell-save" style='display:none;'>Salvar</button>
                <button type="button" class="btn btn-success bt-upsell-update" style='display:none;'>Atualizar</button>
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}

{{-- Modal config upsell --}}
<div class="modal fade example-modal-lg" id="modal_config_upsell" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" style='overflow-y: scroll;'>
    <div class="modal-dialog modal-lg d-flex justify-content-center">
        <div class="modal-content" id="conteudo_modal_add">
            <div class="modal-header mb-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title text-center" style="font-weight: 700;">Configurações Upsell</h4>
            </div>
            <div class="pt-10 pr-20 pl-20 modal_upsell_body">
                @include('projectupsellrule::config')
            </div>
            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-success bt-upsell-config-update">Atualizar</button>
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}

<!-- Details -->
<div id="modal-detail-upsell" class="modal fade example-modal-lg" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Detalhes do upsell</h4>
                <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('projectupsellrule::show')
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}

{{-- Modal delete upsell --}}
<div class="modal fade example-modal-lg" id="modal-delete-upsell" aria-hidden="true" role="dialog" tabindex="-1">
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
                <button type="button" class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button type="button" id="btn-delete-upsell" class="col-4 btn border-0 btn-outline btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row" data-dismiss="modal" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}

{{-- Modal visualizar configurações do upsell --}}
<div class="modal fade example-modal-lg" id="modal-view-upsell-config" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" style='overflow-y:auto;'>
    <div class="modal-dialog modal-lg d-flex justify-content-center">
        <div class="modal-content" id="">
            <div class="modal-header border-bottom d-flex justify-content-center">
                <h4 class="modal-title" id="modal-title" style="margin-left: 0;">Upsell no checkout</h4>
            </div>
            <div class="pt-0 px-0 modal_upsell_body">
                @include('projectupsellconfig::previewupsellconfig')
            </div>
            <div class="modal-footer text-right pt-20 border-top">
                <button class='btn btn-primary btn-sm btn-return-to-config' >
                    <i class="icon wb-settings" aria-hidden="true"></i>
                    Voltar para configurações
                </button>
            </div>
        </div>
    </div>
</div>
{{-- End Modal  --}}
