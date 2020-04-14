@push('css')
    <link rel='stylesheet' href='{{asset('/modules/sales/css/index.css?v=' . uniqid())}}'>
@endpush

<div class='modal fade example-modal-lg' id='modal-detalhes-black-antifraud' aria-hidden='true' aria-labelledby='exampleModalTitle' role='dialog' tabindex='-1'>
    <div class='modal-dialog modal-simple modal-sidebar modal-lg'>
        <div id='modal-sale-details-blackantifraud' class='modal-content p-20' style='width: 500px;'>
            <div class='header-modal'>
                <div class='row justify-content-between align-items-center' style='width: 100%'>
                    <div class='col-lg-2'></div>
                    <div class='col-lg-8 text-center'>
                        <h4>Detalhes</h4>
                    </div>
                    <div class='col-lg-2 text-right'>
                        <a role='button' data-dismiss='modal'>
                            <i class='material-icons pointer'>close</i>
                        </a>
                    </div>
                </div>
            </div>
            <div class='modal-body-detalhes-black-antifraud'>
                <div id='sale-details-black-antifraud'>
                    <h3 id='sale-code' class=''></h3>
                    <p id='payment-type' class='sm-text text-muted'></p>
                    <div id='status' class='d-inline'></div>
                </div>
                <div class='clearfox'></div>
                <div id='sale-details-card-blackAntifraud' class='card shadow pr-20 pl-20 p-10'>
                    <div class='row'>
                        <div class='col-lg-3'><p class='table-title'>Produto</p></div>
                        <div class='col-lg-9 text-right'><p class='text-muted'>Qtde</p></div>
                    </div>
                    <div id='table-product-black-antifraud'></div>
                </div>
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
                        <span id="customer-name" class="table-title gray"></span>
                        <br>
                        <div class="d-flex align-items-center">
                            <label for="customer-telephone" class="table-title gray mb-0">Telefone:</label>&nbsp;
                            <input id="customer-telephone" name="customer-telephone" class="detail-input mr-2 table-title gray fake-label" readonly>
                        </div>
                        <div class="d-flex align-items-center">
                            <label for="customer-email" class="table-title gray mb-0">E-mail:</label>&nbsp;
                            <input id="customer-email" type="email" name="customer-email" class="detail-input mr-2 table-title gray fake-label" readonly>
                        </div>
                        <span id="customer-document" class="table-title gray"></span>
                        <h4> Entrega </h4>
                        <span id="delivery-address" class="table-title gray"></span>
                        <br>
                        <span id="delivery-neighborhood" class="table-title gray"></span>
                        <br>
                        <span id="delivery-zipcode" class="table-title gray"></span>
                        <br>
                        <span id="delivery-city" class="table-title gray"></span>
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
                        <span id="checkout-ip" class="table-title gray"></span>
                        <br>
                        <span id="checkout-operational-system" class="table-title gray"></span>
                        <br>
                        <span id="checkout-browser" class="table-title gray"></span>
                        <br>
                        <span id="checkout-attempts" class="table-title gray" style="display:none"></span>
                        <br>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
