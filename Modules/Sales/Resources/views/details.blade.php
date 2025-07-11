@push('css')
    <link rel="stylesheet"
          href="{{ mix('build/layouts/sales/details.min.css') }}">
@endpush
<div class="modal fade example-modal-lg"
     id="modal_detalhes"
     aria-hidden="true"
     aria-labelledby="exampleModalTitle"
     role="dialog"
     tabindex="-1">
    <div class="modal-dialog modal-simple modal-sidebar modal-lg modal_loading_skeleton"
         style="display: none">
        <div class="modal-content detail-padding">
            <div>
                <div class="skeleton-loading"
                     style="width: 100%; height: 40px;"></div>
                <div class="skeleton-loading my-15"
                     style="width: 100%; height: 20px;"></div>
                <div class="skeleton-loading my-15"
                     style="width: 100%; height: 25px;"></div>
                <div class="skeleton-loading"
                     style="width: 100%; height: 40px;"></div>
            </div>
            <div class="d-flex flex-column align-items-center mt-50">
                <div class="skeleton-loading"
                     style="width: 90%; height: 60px;"></div>
                <div class="skeleton-loading mt-15"
                     style="width: 100%; height: 2px;"></div>
                <div class="skeleton-loading mt-15"
                     style="width: 90%; height: 60px;"></div>
                <div class="skeleton-loading mt-15"
                     style="width: 100%; height: 1px;"></div>
                <div class="skeleton-loading mt-15"
                     style="width: 90%; height: 60px;"></div>
            </div>
            <div class="skeleton-loading mt-50"
                 style="width: 100%; height: 60px;"></div>
            <div class="mt-50">
                <div class="d-flex justify-content-around"
                     style="width: 50%; height: 25px;">
                    <div class="skeleton-loading"
                         style="width: 35%"></div>
                    <div class="skeleton-loading"
                         style="width: 35%"></div>
                </div>
                <div class="skeleton-loading mt-25"
                     style="width: 100%; height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="modal-dialog modal-simple modal-sidebar modal-lg modal_sale_details_content">
        <div id='modal-saleDetails'
             class="modal-content detail-padding"
             style="width: 500px;">
            <div class="header-modal">
                <div class="row justify-content-between align-items-center"
                     style="width: 100%;">
                    <div class="d-flex col-8 pl-0">
                        <span>
                            <h3 class="mb-0">Venda&nbsp;</h3>
                        </span>
                        <h3 id="sale-code"
                            class="mb-0"></h3>
                    </div>

                    <div class="col-2 text-right">
                        <a role="button"
                           data-dismiss="modal">
                            <i class="material-icons pointer">close</i>
                        </a>
                    </div>
                </div>
            </div>

            <div style="height: 10px; width: 100%">&nbsp;</div>

            <div class="modal-body p-0">
                <div class="transition-details">
                    <p id="payment-type"
                       class="sm-text text-muted">
                    </p>
                    <p id='release-date'></p>
                    <p id='card-company'></p>
                    <div id="status"
                         class="status d-inline">
                    </div>
                    <div class="status d-inline">
                        <img id='chargeback-recovered'
                             class="orange-gradient"
                             src="{{ mix('build/global/img/svg/chargeback.svg') }}"
                             title="Chargeback recuperado"
                             style='width:20px; display:none;'>
                    </div>
                </div>
                <div class='div-refund-observation mt-20'
                     style='display:none;'>
                    <label for="refund-observation"
                           class="table-title gray mb-0">Causa do estorno:</label>&nbsp;
                    <div class="d-flex align-items-center">
                        <input id="refund-observation"
                               type="email"
                               name="refund_observation"
                               class="detail-input mr-2 table-title gray fake-label"
                               readonly>
                        <a class="pointer btn-edit-observation"><span class="o-edit-1"></span></a>
                        <a class="pointer btn-save-observation"
                           style="display:none;">
                            <i class="material-icons font-size-18">save</i></a>
                        <a class="pointer btn-close-observation ml-2"
                           style="display:none;">
                            <i class="material-icons font-size-18">close</i></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div id='sale-details-card'
                     class="card shadow pr-20 pl-20 p-10">
                    <div class='div-sale-by-affiliate'
                         style='display:none;'>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <p class="table-title"> Produto </p>
                        </div>
                        <div class="col-lg-9 text-right">
                            <p class="text-muted"> Qtde </p>
                        </div>
                    </div>

                    <div id="table-product">
                        {{-- js carrega... --}}
                    </div>

                    <div class="row"
                         style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
                        <div class="col-sm-6 col-6 align-items-center">
                            <span class="text-muted ft-12"> Subtotal </span>
                        </div>
                        <div class="col-sm-6 col-6 text-right">
                            <span class="text-muted ft-12"
                                  id="subtotal-value"></span>
                        </div>
                        <div class="col-sm-6 col-6">
                            <span class="text-muted ft-12"> Frete </span>
                        </div>
                        <div class="col-sm-6 col-6 text-right">
                            <span id="shipment-value"
                                  class="text-muted ft-12"></span>
                        </div>
                        <div id="iof-label"
                             class="col-sm-6 col-6"
                             style="display:none">
                            <span class="text-muted ft-12"> IOF </span>
                        </div>
                        <div id="iof-value"
                             class="col-sm-6 col-6 text-right"
                             style="display:none">
                            <span class="text-muted ft-12"></span>
                        </div>
                        <div id="discount-prog-title"
                             class="col-sm-6 col-6"
                             style="display: nonex;">
                            <span class="text-muted ft-12"> Desconto progressivo</span>
                        </div>
                        <div id="discount-prog-data"
                             class="col-sm-6 col-6 text-right"
                             style="display: nonex;">
                            <span id="desconto-prog-value"
                                  class="text-muted ft-12"></span>
                        </div>
                        <div id="discount-title"
                             class="col-sm-6 col-6"
                             style="display: none;">
                            <span class="text-muted ft-12"> Desconto do cupom</span>
                        </div>
                        <div id="discount-data"
                             class="col-sm-6 col-6 text-right"
                             style="display: none;">
                            <span id="desconto-value"
                                  class="text-muted ft-12"></span>
                        </div>
                        <div class="col-sm-6 col-6 cupom-info">
                            <span class="text-muted ft-12"> Cupom Utilizado</span>
                        </div>
                        <div class="col-sm-6 col-6 text-right cupom-info">
                            <span id="cupom-code"
                                  class="text-muted ft-12"></span>
                        </div>
                        <div class="col-sm-6 col-6">
                            <span class="text-muted ft-12 text-discount"></span>
                        </div>
                        <div class="col-sm-6 col-6 text-right">
                            <span id="automatic-discount-value"
                                  class="text-muted ft-12"></span>
                        </div>
                        <div class="col-sm-6 col-6">
                            <span class="text-muted ft-12 text-partial-refund"> Estorno parcial</span>
                        </div>
                        <div class="col-sm-6 col-6 text-right">
                            <span id="partial-refund-value"
                                  class="text-muted ft-12"></span>
                        </div>
                        <div class="col-sm-6 col-6">
                            <h4 class="table-title"> Total </h4>
                        </div>
                        <div class="col-sm-6 col-6 text-right">
                            <h4 id="total-value"
                                class="table-title"></h4>
                        </div>
                        <!-- <div class='col-sm-8 col-6'>
                            <span id="cashback-label"
                                  class="text-muted ft-12 d-none">Cashback</span>
                        </div>
                        <div class='col-sm-4 col-6 text-right'>
                            <span id="cashback-value"
                                  class="text-muted ft-12 d-none">R$ </span>
                        </div> -->
                    </div>
                    <div class="row"
                         style="border-top: 1px solid #e2e2e2; padding-top: 10px; margin-top: 10px;"
                         id="comission-details">
                        <div id="cambio-label"
                             class='col-8'
                             style="display:none">
                            <span class='text-muted ft-12'></span>
                        </div>
                        <div id="cambio-value"
                             class='col-4 text-right'
                             style="display:none">
                            <span class='text-muted ft-12'></span>
                        </div>
                        <div class='col-sm-8 col-7'>
                            <span id="taxas-label"
                                  class='text-muted ft-12'></span>
                        </div>
                        <div class='col-sm-4 col-5 text-right'>
                            <span id="taxareal-value"
                                  class='text-muted ft-12'></span>
                        </div>
                        <div style="color: #2E85EC"
                             class='col-sm-12 pointer mt-5'
                             data-toggle="collapse"
                             data-target="#taxas-detail"
                             aria-expanded="false"
                             aria-controls="taxas-detail">
                            <small data-toggle="tooltip"
                                   data-placement="top"
                                   data-container="#modal_detalhes"
                                   title="Clique para obter mais detalhes">Taxa detalhada</small>
                        </div>

                        <div class='col-lg-12 collapse mb-5'
                             id="taxas-detail">
                            <table class="table table-bordered mx-5 border-0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div id="tax-subtotal"
                                                 class="ft-12"></div>
                                        </td>
                                        <td>
                                            <div id="tax-subtotal-value"
                                                 style="color: black !important;"
                                                 class="text-right text-muted ft-12"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div id="tax-cashback"
                                                 class="ft-12"></div>
                                        </td>
                                        <td>
                                            <div id="tax-cashback-value"
                                                 style="color: #5EE2A1 !important;"
                                                 class="text-right text-muted ft-12"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div id="tax-value-total"
                                                 class="ft-12"></div>
                                        </td>
                                        <td>
                                            <div id="tax-value-total-value"
                                                 style="color: #5EE2A1 !important;"
                                                 class="text-right text-muted ft-12"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div id="tax-percentage"
                                                 class="ft-12"></div>
                                        </td>
                                        <td>
                                            <div id="tax-percentage-value"
                                                 style="color: #F41C1C !important;"
                                                 class="text-right text-muted ft-12"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div id="tax-fixed"
                                                 class="ft-12"></div>
                                        </td>
                                        <td>
                                            <div id="tax-fixed-value"
                                                 style="color: #F41C1C !important;"
                                                 class="text-right text-muted ft-12"></div>
                                        </td>
                                    </tr>

                                    <tr id="div_tax_checkout"
                                        style="display:none;">
                                        <td>
                                            <div id="tax-checkout"
                                                 class="ft-12"></div>
                                        </td>
                                        <td>
                                            <div id="tax-checkout-value"
                                                 style="color: #F41C1C !important;"
                                                 class="text-right text-muted ft-12"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div id="tax-total"
                                                 class="ft-12"></div>
                                        </td>
                                        <td>
                                            <div id="tax-total-value"
                                                 style="color: #F41C1C !important;"
                                                 class="text-right text-muted ft-12"></div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <div id="tax-comission"
                                                 class="ft-12"></div>
                                        </td>
                                        <td>
                                            <div id="tax-comission-value"
                                                 style="color: black !important;"
                                                 class="text-right text-muted ft-12"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class='col-sm-8 col-8'>
                            <span id="taxas-installment-free-label"
                                  style='display:none;'
                                  class='text-muted ft-12'>Taxa de parcelamento</span>
                        </div>
                        <div class='col-sm-4 col-4 text-right'>
                            <span id="taxa-installment-value"
                                  style='display:none;'
                                  class='text-muted ft-12'></span>
                        </div>
                        <div class='col-sm-6 col-6 div-user-type-comission'
                             style='display:none;'>
                            <h4 class='table-title'>Comissão do afiliado: </h4>
                        </div>
                        <div class='col-sm-6 col-6 text-right div-user-type-comission-value'
                             style='display:none;'>
                        </div>
                        <div class='col-sm-6 col-6 div-anticipated'
                             style='display:none;'>
                            <span id="anticipated-label"
                                  class='text-muted ft-12'>
                                Valor Antecipado:
                            </span>
                        </div>
                        <div class='col-sm-6 col-6 text-right div-value-anticipated'
                             style='display:none;'>
                        </div>

                        <div class='col-sm-6 col-6 div-main-comission'>
                        </div>
                        <div class='col-sm-6 col-6 text-right div-main-comission-value'>
                        </div>
                    </div>
                </div>
                {{-- resend shopfy order --}}
                <div id='resendShopfyOrder'
                     class='alert alert-warning text-center d-none'>
                    <span>Ordem do <b>SHOPIFY</b> não foi gerada</span>
                    <br>
                    <span style='font-size:12px'>clique no botão a seguir para gerar</span>
                    <br>
                    <button id='resendeShopifyOrderButton'
                            class="btn btn-warning btn-sm btn_new_order_shopify mt-10"
                            sale=''>
                        <b>Gerar ordem Shopify</b>
                    </button>
                </div>
                {{-- resend shopfy order --}}

                {{-- resend woo order --}}
                <div id='resendWoocommerceOrder'
                     class='alert alert-warning text-center d-none'>
                    <span>Ordem do <b>WOOCOMMERCE</b> não foi gerada</span>
                    <br>
                    <span style='font-size:12px'>clique no botão a seguir para gerar</span>
                    <br>
                    <button id='resendWoocommerceOrderButton'
                            class="btn btn-warning btn-sm btn_new_order_woocommerce mt-10"
                            sale=''>
                        <b>Gerar ordem Woocommerce</b>
                    </button>
                </div>
                {{-- resend woo order --}}

                <!-- Observation -->
                <div style="box-shadow: 0 10px 20px 0 rgba(0, 0, 0, 0.1);"
                     class="panel-group my-30"
                     aria-multiselectable="true"
                     role="tablist">
                    <div class="panel">
                        <div class="panel-heading"
                             id="exampleHeadingDefaultOne"
                             role="tab">
                            <a class="panel-title"
                               data-toggle="collapse"
                               href="#sale-observation"
                               data-parent="#exampleAccordionDefault"
                               aria-expanded="true"
                               aria-controls="exampleCollapseDefaultOne">
                                <strong>Observações</strong>
                            </a>
                        </div>
                        <div class="panel-collapse collapse"
                             id="sale-observation"
                             aria-labelledby="exampleHeadingDefaultOne"
                             role="tabpanel"
                             style="">
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="observation"></label>
                                    <textarea class="form-control"
                                              id="observation"
                                              rows="3"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-right">
                                        <button id="update-sale-observation"
                                                type="button"
                                                class="btn btn-success">Atualizar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nav-tabs-horizontal">
                    <div class="nav nav-tabs nav-tabs-line text-center"
                         id="nav-tab"
                         role="tablist">
                        <a class="nav-item nav-link active"
                           id="nav-home-tab"
                           data-toggle="tab"
                           href="#nav-home"
                           role="tab"
                           style="width:33%;">Cliente
                        </a>
                        <a class="nav-item nav-link"
                           id="nav-profile-tab"
                           data-toggle="tab"
                           href="#nav-profile"
                           role="tab"
                           style="width:33%;">Detalhes
                        </a>
                        <a class="nav-item nav-link"
                           id="nav-woo-tab"
                           data-toggle="tab"
                           href="#nav-woo"
                           role="tab"
                           style="width:33%; display:none">WooCommerce
                        </a>
                    </div>
                </div>
                <div class="tab-content p-10 sales-details"
                     id="nav-tabContent">
                    {{-- WOOCOMMERCE --}}
                    <div class="tab-pane fade"
                         id="nav-woo"
                         role="tabpanel"
                         aria-labelledby="nav-woo-tab">
                        <h4> Ordem Woocommerce </h4>

                        <br>

                        <div id="woo_order"
                             class="mb-20">

                        </div>

                        <br>

                    </div>

                    <!-- CLIENTE -->
                    <div class="tab-pane fade show active"
                         id="nav-home"
                         role="tabpanel"
                         aria-labelledby="nav-home-tab">
                        <h4> Dados Pessoais </h4>
                        <span id="client-name"
                              class="table-title gray"></span>
                        <br>

                        <div class="d-flex align-items-center">
                            <input id="client-telephone"
                                   name="client-telephone"
                                   class="detail-input mr-2 table-title gray fake-label"
                                   readonly>
                            <a class="pointer btn-edit-client">
                                <span class="o-edit-1"></span>
                            </a>

                            <a class="pointer btn-save-client"
                               style="display:none;">
                                <i class="material-icons font-size-18">save</i>
                            </a>

                            <a class="pointer btn-close-client ml-2"
                               style="display:none;">
                                <i class="material-icons font-size-18">close</i>
                            </a>
                        </div>

                        <div class="d-flex align-items-center">

                            <input id="client-email"
                                   type="email"
                                   name="client-email"
                                   class="detail-input mr-2 table-title gray fake-label"
                                   readonly>
                            <a class="pointer btn-edit-client">
                                <span class="o-edit-1"></span>
                            </a>

                            <a class="pointer btn-save-client"
                               style="display:none;">
                                <i class="material-icons font-size-18">save</i>
                            </a>

                            <a class="pointer btn-close-client ml-2"
                               style="display:none;">
                                <i class="material-icons font-size-18">close</i></a>
                        </div>

                        <span id="client-document"
                              class="table-title gray"></span>
                        <div class="mt-15 mb-5"
                             id="client-whatsapp-container">
                            <a class="pointer label d-inline-flex align-items-center pl-3 d-block"
                               id="client-whatsapp"
                               target='_blank'>
                                <span class="o-whatsapp-1"></span>
                                <b class="ml-2">Mensagem no Whatsapp</b>
                            </a>
                        </div>
                        <div id="saleReSendEmail"
                             class="mb-20">
                            <a class="pointer d-inline-flex align-items-center"
                               id="btnSaleReSendEmail">
                                <i class="material-icons">email</i><b class="ml-2">Reenviar e-mail</b>
                            </a>
                        </div>
                        <div id='div_delivery'
                             style='display:none;'>
                            <h4 class='delivery-title'> Entrega </h4>
                            <span id="delivery-address"
                                  class="table-title gray"></span>
                            <br>
                            <span id="delivery-neighborhood"
                                  class="table-title gray"></span>
                            <br>
                            <span id="delivery-zipcode"
                                  class="table-title gray"></span>
                            <br>
                            <span id="delivery-city"
                                  class="table-title gray"></span>
                        </div>
                        <div id='div_tracking_code'
                             style='display:none; overflow: auto'>
                            <h4> Rastreio </h4>
                            <table class='table table-striped mb-10'>
                                <thead>
                                    <tr>
                                        <th class="table-detail">Produto</th>
                                        <th>Rastreio</th>
                                        <th class="text-center">Status</th>
                                        <th>Postagem</th>
                                    </tr>
                                </thead>
                                <tbody id='data-tracking-products'>
                                    {{-- js carregado --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- DETALHES  -->
                    <div class="tab-pane fade"
                         id="nav-profile"
                         role="tabpanel"
                         aria-labelledby="nav-profile-tab">
                        <h4> Dados Gerais </h4>
                        <div id="details-api"
                             style="display: none; margin-bottom: 11px;">
                        </div>
                        <div id="details-card"
                             style="display:none">
                            <span id="card-flag"
                                  class="table-title gray text-capitalize"></span>
                            <br>
                            <span id="card-installments"
                                  class="table-title gray"></span>
                            <br>
                        </div>
                        <div id="details-boleto"
                             style="display:none">
                            <span id="boleto-link"
                                  class="table-title gray">Link para o boleto:
                                <a role='button'
                                   class='copy_link'
                                   style='cursor:pointer;'
                                   link=''
                                   title='Copiar link'>
                                    <span class="material-icons icon-copy-1"> content_copy </span>
                                </a>
                            </span>
                            <br>
                            <span id="boleto-digitable-line"
                                  class="table-title gray">Linha Digitável:
                                <a role='button'
                                   class='copy_link'
                                   style='cursor:pointer;'
                                   digitable-line=''title='Copiar link'>
                                    <span class="material-icons icon-copy-1"> content_copy </span>
                                </a>
                            </span>
                            <br>
                            <span id="boleto-due"
                                  class="table-title gray"></span>
                            <br>
                        </div>

                        <div class="dados-checkout">
                            <span id="checkout-ip"
                                  class="table-title gray"></span>
                            <br>
                            <span id="checkout-operational-system"
                                  class="table-title gray"></span>
                            <br>
                            <span id="checkout-browser"
                                  class="table-title gray"></span>
                            <br>
                            <span id="checkout-attempts"
                                  class="table-title gray"
                                  style="display:none"></span>
                            <br>
                            <span id="thank-page-url"
                                  class="table-title gray"
                                  style="display:none"></span>
                            <a role="button"
                               class="copy_link btn-copy-thank-page-url"
                               style="cursor:pointer;display:none;"
                               link=""
                               title="Copiar link">
                                <span class="material-icons icon-copy-1"> content_copy </span>
                            </a>
                            <br>
                            <span id="referer-url"
                                  class="table-title gray"
                                  style="display:none"></span>
                            <br>

                        </div>

                        <div class="dados-checkout">
                            <h4> Conversão </h4>
                            <span id="checkout-src"
                                  class="table-title gray"></span>
                            <br>
                            <span id="checkout-source"
                                  class="table-title gray"></span>
                            <br>
                            <span id="checkout-medium"
                                  class="table-title gray"></span>
                            <br>
                            <span id="checkout-campaign"
                                  class="table-title gray"></span>
                            <br>
                            <span id="checkout-term"
                                  class="table-title gray"></span>
                            <br>
                            <span id="checkout-content"
                                  class="table-title gray"></span>
                            <br>
                        </div>

                        <div id='div_notazz_invoice'
                             style='display:none;'>
                            <br>
                            <h4> Integração Notazz </h4>
                            <table class='table table-striped mt-15'>
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Status</th>
                                        <th>Code</th>
                                        <th>Message</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id='data-notazz-invoices'>
                                    {{-- js carregado --}}
                                </tbody>
                            </table>
                            <div id='div_notazz_schedule'>
                            </div>
                        </div>

                        <div id='div_refund'
                             class='mt-20'></div>

                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>

@push('scriptsModal')
    <script src="{{ mix('build/layouts/sales/details.min.js') }}"></script>
@endpush
