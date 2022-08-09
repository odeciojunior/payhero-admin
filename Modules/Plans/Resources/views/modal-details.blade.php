<div class="modal fade example-modal-lg modal-3d-flip-vertical"
     id="modal_details_plan"
     role="dialog"
     tabindex="-1">
    <div id="modal_add_size"
         class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10"
             id="conteudo_modal_add">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title"
                    id="modal-title-details"></h4>
                <a id="modal-button-close"
                   class="pointer close"
                   role="button"
                   data-dismiss="modal"
                   aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>

            <div id="modal-details-body"
                 class="modal-body"
                 style='min-height: 100px'>
                <div class='container-fluid'>
                    <table class='table table-bordered table-striped table-hover'
                           style='overflow-x: auto !important;'>
                        <tbody>
                            <tr>
                                <th style='width:40%;'
                                    class='text-center'>Nome:</th>
                                <td class='text-left'
                                    id='plan_name_details'></td>
                                <br>
                            </tr>
                            <tr>
                                <th style='width:40%;'
                                    class='text-center'>Descrição:</th>
                                <td class='text-left'
                                    id='plan_description_details'></td>
                            </tr>
                            <tr>
                                <th style='width:40%;'
                                    class='text-center'>Link:</th>
                                <td class='text-left'
                                    id='plan_code_edit_details'></td>
                            </tr>
                            <tr>
                                <th style='width:40%;'
                                    class='text-center'>Preço:</th>
                                <td class='text-left'
                                    id='plan_price_edit_details'></td>
                            </tr>
                            <tr>
                                <th style='width:40%;'
                                    class='text-center'>Status:</th>
                                <td class='text-left'
                                    id='plan_status_edit_details'>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class='table table-bordered table-striped table-hover mt-2 text-center'>
                        <thead>
                            <th>Produto:</th>
                            <th>Quantidade:</th>
                        </thead>
                        <tbody id='products_plan_details'>
                            {{-- tr carregado no js --}}
                        </tbody>
                    </table>
                    @if (!foxutils()->isProduction())
                        <form id="form-cart-shopify"
                              method="post"
                              action="{{ env('CHECKOUT_URL', 'https://checkout.cloudfox.net') }}"
                              target="_blank">
                            <button class="btn btn-success float-right d-flex py-1 px-2"
                                    type="submit">
                                <img class="w-20 mr-2"
                                     src="{{ mix('build/global/img/svg/shopify.svg') }}"
                                     alt="Shopify Logo">
                                <b>Abrir Carrinho</b>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
