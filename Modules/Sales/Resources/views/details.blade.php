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
        <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" style="width:50%;">Cliente</a>
        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" style="width:50%;">Detalhes</a>
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
            <img src="{{ asset('modules/global/img/whatsapplogo.png') }}" width="25px">
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
            {{--            @if (empty($sale->shopify_order) && $sale->status == 1)--}}
            <table class='table table-striped mt-20'>
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
            {{--            @endif--}}
        </div>

{{--        <span class="table-title gray table-code-tracking">--}}
{{--            <div class='row' style='line-height: 1.5;'>--}}
{{--                <span class="table-title gray ml-15">Código Rastreio:</span>--}}
{{--                    <div id="tracking-actions" class='col-xl-3 col-lg-3 col-md-3 col-3 icondemo-wrap vertical-align-middle' style="display:none">--}}
{{--                        <a id='btn-edit-trackingcode' class='edit pointer' title='Editar Código de rastreio' data-code=''><i class='icon wb-edit' aria-hidden='true'></i></a>--}}
{{--                        <a id='btn-sent-tracking-user' style="display:none" class='pointer' title='Enviar Email' data-code=''><i class='icon wb-inbox' aria-hidden='true'></i></a>--}}
{{--                    </div>--}}
{{--            </div>--}}
{{--            <div class='tracking-code'>--}}
{{--                <span class='tracking-code-value'></span>--}}
{{--            </div>--}}
{{--        </span>--}}
{{--        <input type='text' class='input-value-trackingcode my-10' style='display:none;' value=''>--}}
{{--        <button type='button' class='btn-save-tracking mb-10' style='display: none;' data-code=''>Salvar</button>--}}
{{--        <button type='button' class='btn-cancel-tracking mb-10' style='display: none;'>Cancelar</button>--}}
{{--        <br>--}}

{{--        <span id="delivery-address" class="table-title gray"></span>--}}
{{--        <br>--}}
{{--        <span id="delivery-zipcode" class="table-title gray"></span>--}}
{{--        <br>--}}
{{--        <span id="delivery-city" class="table-title gray"></span>--}}
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
            <span id="boleto-link" class="table-title gray">Link para o boleto: <a role='button' class='copy_link' style='cursor:pointer;' link=''><i class='material-icons gradient' style='font-size:17px;'>file_copy</i></a></span>
            <br>
            <span id="boleto-digitable-line" class="table-title gray">Linha Digitável: <a role='button' class='copy_link' style='cursor:pointer;' digitable-line=''><i class='material-icons gradient' style='font-size:17px;'>file_copy</i></a></span>
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
    </div>
</div>
@push('scripts')
    <script src="{{ asset('/modules/sales/js/details.js') }}"></script>
@endpush

