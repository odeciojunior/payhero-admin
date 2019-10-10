<div class="modal fade example-modal-lg" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle"
     role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple modal-sidebar modal-lg">
        <div id='modal-saleDetails' class="modal-content p-20 " style="width: 500px;">
            <div class="header-modal">
                <div class="row justify-content-between align-items-center" style="width: 100%;">
                    <div class="col-lg-2"> &nbsp;</div>
                    <div class="col-lg-8 text-center"><h4> Detalhes da venda </h4></div>
                    <div class="col-lg-2 text-right">
                        <a role="button" data-dismiss="modal">
                            <i class="material-icons pointer">close</i></a>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="transition-details">
                    <h3 id="sale-code" class="text-uppercase"></h3>
                    <p id="payment-type" class="sm-text text-muted">
                    </p>
                    <div id="status" class="status d-inline">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="card shadow pr-20 pl-20 p-10">
                    <div class="row">
                        <div class="col-lg-3"><p class="table-title"> Produto </p></div>
                        <div class="col-lg-9 text-right"><p class="text-muted"> Qtde </p></div>
                    </div>
                    <div id="table-product">
                    </div>
                    <div class="row" style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
                        <div class="col-lg-6 align-items-center">
                            <span class="text-muted ft-12"> Subtotal </span>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span class="text-muted ft-12" id="subtotal-value"></span>
                        </div>
                        <div class="col-lg-6">
                            <span class="text-muted ft-12"> Frete </span>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span id="shipment-value" class="text-muted ft-12"></span>
                        </div>
                        <div id="iof-label" class="col-lg-6" style="display:none">
                            <span class="text-muted ft-12"> IOF </span>
                        </div>
                        <div id="iof-value" class="col-lg-6 text-right" style="display:none">
                            <span class="text-muted ft-12"></span>
                        </div>
                        <div class="col-lg-6">
                            <span class="text-muted ft-12"> Desconto</span>
                        </div>
                        <div class="col-lg-6 text-right">
                            <span id="desconto-value" class="text-muted ft-12"></span>
                        </div>
                        <div class="col-lg-6">
                            <h4 class="table-title"> Total </h4>
                        </div>
                        <div class="col-lg-6 text-right">
                            <h4 id="total-value" class="table-title"></h4>
                        </div>
                    </div>
                    <div class="row" style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
                        <div id="cambio-label" class='col-8' style="display:none">
                            <span class='text-muted ft-12'></span>
                        </div>
                        <div id="cambio-value" class='col-4 text-right' style="display:none">
                            <span class='text-muted ft-12'></span>
                        </div>
                        <div class='col-lg-8'>
                            <span id="taxas-label" class='text-muted ft-12'></span>
                        </div>
                        <div class='col-lg-4 text-right'>
                            <span id="taxareal-value" class='text-muted ft-12'></span>
                        </div>
                        <div id="convertax-label" class='col-lg-8' style="display:none">
                            <span class='text-muted ft-12'>App ConvertaX: </span>
                        </div>
                        <div id="convertax-value" class='col-lg-4 text-right' style="display:none">
                            <span class='text-muted ft-12'></span>
                        </div>
                        <div class='col-lg-6'>
                            <h4 class='table-title'>Comissão: </h4>
                        </div>
                        <div class='col-lg-6 text-right'>
                            <h4 id="comission-value" class='table-title'></h4>
                        </div>
                    </div>
                </div>
                <div class="nav-tabs-horizontal">
                    <div class="nav nav-tabs nav-tabs-line text-center" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
                           role="tab"
                           style="width:50%;">Cliente</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
                           role="tab"
                           style="width:50%;">Detalhes</a>
                    </div>
                </div>
                <div class="tab-content p-10" id="nav-tabContent">
                    <!-- CLIENTE -->
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <h4> Dados Pessoais </h4>
                        <span id="client-name" class="table-title gray"></span>
                        <br>
                        <span id="client-telephone" class='table-title gray'></span>
                        <a id="client-whatsapp" href="#" target='_blank'>
                            <img src="{{ asset('modules/global/img/whatsapplogo.png') }}" width="25px" title='Enviar mensagem pelo whatsapp'>
                        </a>
                        <br>
                        <span id="client-email" class="table-title gray"></span>
                        <br>
                        <span id="client-document" class="table-title gray"></span>
                        <h4> Entrega </h4>
                        <span id="delivery-address" class="table-title gray"></span>
                        <br>
                        <span id="delivery-zipcode" class="table-title gray"></span>
                        <br>
                        <span id="delivery-city" class="table-title gray"></span>
                        <div id='div_tracking_code' style='display:none;'>
                            <h4> Rastreio </h4>
                            <table class='table table-striped mt-15'>
                                <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Rastreio</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                                </thead>
                                <tbody id='data-tracking-products'>
                                {{-- js carregado--}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- DETALHES  -->
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <h4> Dados Gerais </h4>
                        <div id="details-card" style="display:none">
                            <span id="card-flag" class="table-title gray text-capitalize"></span>
                            <br>
                            <span id="card-installments" class="table-title gray"></span>
                            <br>
                        </div>
                        <div id="details-boleto" style="display:none">
            <span id="boleto-link" class="table-title gray">Link para o boleto: <a role='button' class='copy_link'
                                                                                   style='cursor:pointer;' link='' title='Copiar link'><i
                        class='material-icons gradient' style='font-size:17px;'>file_copy</i></a></span>
                            <br>
                            <span id="boleto-digitable-line" class="table-title gray">Linha Digitável: <a role='button'
                                                                                                          class='copy_link'
                                                                                                          style='cursor:pointer;'
                                                                                                          digitable-line='' title='Copiar link'><i
                                        class='material-icons gradient' style='font-size:17px;'>file_copy</i></a></span>
                            <br>
                            <span id="boleto-due" class="table-title gray"></span>
                            <br>
                        </div>
                        <span id="checkout-ip" class="table-title gray"></span>
                        <br>
                        <span id="checkout-operational-system" class="table-title gray"></span>
                        <br>
                        <span id="checkout-browser" class="table-title gray"></span>
                        <br>
                        <span id="checkout-attempts" class="table-title gray" style="display:none"></span>
                        <br>
                        <h4> Conversão </h4>
                        <span id="checkout-src" class="table-title gray"></span>
                        <br>
                        <span id="checkout-source" class="table-title gray"></span>
                        <br>
                        <span id="checkout-medium" class="table-title gray"></span>
                        <br>
                        <span id="checkout-campaign" class="table-title gray"></span>
                        <br>
                        <span id="checkout-term" class="table-title gray"></span>
                        <br>
                        <span id="checkout-content" class="table-title gray"></span>
                        <div id='div_notazz_invoice' style='display:none;'>
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
                                    {{-- js carregado--}}
                                </tbody>
                            </table>
                            <div id='div_notazz_schedule'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('/modules/sales/js/detail.js?v=1') }}"></script>
    @endpush
</div>
