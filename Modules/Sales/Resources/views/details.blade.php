@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css?v=1') }}">
@endpush
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
                    <h3 id="sale-code" class=""></h3>
                    <p id="payment-type" class="sm-text text-muted">
                    </p>
                    <p id='release-date'></p>
                    <div id="status" class="status d-inline">
                    </div>
                </div>
                <div class="clearfix"></div>
                <div id='sale-details-card' class="card shadow pr-20 pl-20 p-10">
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
                        <div class='col-lg-8'>
                            <span id="taxas-installment-free-label" style='display:none;' class='text-muted ft-12'>Taxa de parcelamento</span>
                        </div>
                        <div class='col-lg-4 text-right'>
                            <span id="taxa-installment-value" style='display:none;' class='text-muted ft-12'></span>
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
                {{--resend shopfy order--}}
                <div id='resendShopfyOrder' class='alert alert-warning text-center d-none'>
                    <span>Ordem do <b>SHOPIFY</b> não foi gerada</span>
                    <br>
                    <span style='font-size:12px'>clique no botão a seguir para gerar</span>
                    <br>
                    <button id='resendeShopifyOrderButton' class="btn btn-warning btn-sm btn_new_order_shopify mt-10"
                            sale=''>
                        <b>Gerar ordem Shopify</b>
                    </button>
                </div>
                {{--resend shopfy order--}}
                <div class="nav-tabs-horizontal">
                    <div class="nav nav-tabs nav-tabs-line text-center" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home"
                           role="tab"
                           style="width:50%;">Cliente
                        </a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile"
                           role="tab"
                           style="width:50%;">Detalhes
                        </a>
                    </div>
                </div>
                <div class="tab-content p-10" id="nav-tabContent">
                    <!-- CLIENTE -->
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        <h4> Dados Pessoais </h4>
                        <span id="client-name" class="table-title gray"></span>
                        <br>
                        <div class="d-flex align-items-center">
                            <label for="client-telephone" class="table-title gray mb-0">Telefone:</label>&nbsp;
                            <input id="client-telephone" name="client-telephone" class="detail-input mr-2 table-title gray fake-label" readonly>
                            <a class="pointer btn-edit-client"><i class="material-icons font-size-18">edit</i></a>
                            <a class="pointer btn-save-client" style="display:none;"><i class="material-icons font-size-18">save</i></a>
                            <a class="pointer btn-close-client ml-2" style="display:none;"><i class="material-icons font-size-18">close</i></a>
                        </div>
                        <div class="d-flex align-items-center">
                            <label for="client-email" class="table-title gray mb-0">E-mail:</label>&nbsp;
                            <input id="client-email" type="email" name="client-email" class="detail-input mr-2 table-title gray fake-label" readonly>
                            <a class="pointer btn-edit-client"><i class="material-icons font-size-18">edit</i></a>
                            <a class="pointer btn-save-client" style="display:none;"><i class="material-icons font-size-18">save</i></a>
                            <a class="pointer btn-close-client ml-2" style="display:none;"><i class="material-icons font-size-18">close</i></a>
                        </div>
                        <span id="client-document" class="table-title gray"></span>
                        <div class="mt-15 mb-5">
                            <a class="pointer label d-inline-flex align-items-center pl-3" id="client-whatsapp" target='_blank'>
                                <svg fill="#76838f" width="20px" height="20px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="m2.574933,21.77839c0.301362,-1.165794 0.56307,-2.157115 0.578931,-2.212629c0.023792,-0.055514 -0.150681,-0.333084 -0.372737,-0.610654c-1.157863,-1.451294 -2.006434,-3.402214 -2.315726,-5.329342c-0.13482,-0.84064 -0.158611,-2.743977 -0.031722,-3.552895c0.753404,-5.004188 4.647313,-8.993264 9.619779,-9.849766c0.927876,-0.158611 2.973963,-0.158611 3.90184,0c5.012119,0.864432 8.906028,4.88523 9.63564,9.937002c0.118959,0.872362 0.047583,3.188088 -0.126889,3.981145c-0.491695,2.204698 -1.475086,4.068381 -2.973963,5.654495c-1.990573,2.101601 -4.568007,3.35463 -7.478526,3.6322c-2.054017,0.190334 -4.353882,-0.245848 -6.162051,-1.157863l-0.364806,-0.182403l-2.133323,0.904085c-1.181655,0.491695 -2.180906,0.904085 -2.23642,0.904085c-0.055514,0 0.118959,-0.793057 0.459973,-2.117462zm14.354328,-3.481519c1.015113,-0.301362 1.824031,-0.919946 2.133323,-1.641628c0.150681,-0.348945 0.174472,-0.499626 0.13482,-0.999252c-0.039653,-0.499626 -0.079306,-0.618584 -0.229986,-0.737543c-0.253778,-0.198264 -2.149184,-1.126141 -2.664671,-1.300613c-0.523417,-0.182403 -0.682029,-0.111028 -1.189585,0.523417c-0.816849,1.030974 -0.97546,1.189585 -1.173724,1.189585c-0.610654,0 -2.490198,-1.189585 -3.544964,-2.252281c-0.753404,-0.745473 -1.720933,-2.022295 -1.720933,-2.268142c0,-0.071375 0.095167,-0.261709 0.222056,-0.41239c0.808918,-1.054766 0.967529,-1.292683 0.967529,-1.467155c0,-0.396528 -1.094418,-3.029477 -1.403711,-3.370491c-0.182403,-0.198264 -1.205446,-0.198264 -1.649558,0.007931c-0.364806,0.166542 -0.912015,0.729612 -1.134071,1.173724c-0.682029,1.348197 -0.602723,2.743977 0.237917,4.417326c2.141253,4.250785 7.978152,8.033665 11.015559,7.137511z"/>
                                </svg>
                                <b class="ml-2">Mensagem no Whatsapp</b>
                            </a>
                        </div>
                        <div id="saleReSendEmail" class="mb-20">
                            <a class="pointer d-inline-flex align-items-center" id="btnSaleReSendEmail">
                                <i class="material-icons">email</i><b class="ml-2">Reenviar e-mail</b></a>
                        </div>
                        <h4> Entrega </h4>
                        <span id="delivery-address" class="table-title gray"></span>
                        <br>
                        <span id="delivery-neighborhood" class="table-title gray"></span>
                        <br>
                        <span id="delivery-zipcode" class="table-title gray"></span>
                        <br>
                        <span id="delivery-city" class="table-title gray"></span>
                        <div id='div_tracking_code' style='display:none;'>
                            <h4> Rastreio </h4>
                            <table class='table table-striped mb-10'>
                                <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Rastreio</th>
                                    <th>Status</th>
                                    {{-- <th>Ações</th> --}}
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
                        <span id="boleto-link" class="table-title gray">Link para o boleto:
                            <a role='button' class='copy_link' style='cursor:pointer;' link='' title='Copiar link'>
                                <i class='material-icons gradient' style='font-size:17px;'>file_copy</i>
                            </a>
                        </span>
                            <br>
                            <span id="boleto-digitable-line" class="table-title gray">Linha Digitável:
                            <a role='button' class='copy_link' style='cursor:pointer;' digitable-line=''
                               title='Copiar link'>
                                <i class='material-icons gradient' style='font-size:17px;'>file_copy</i>
                            </a>
                        </span>
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
                        <br>
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
                        <div id='div_refund_transaction' class='mt-20'></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('/modules/sales/js/detail.js?v=1') }}"></script>
@endpush
