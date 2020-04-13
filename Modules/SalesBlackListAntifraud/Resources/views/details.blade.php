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
                    <p id='release-date'></p>
                    <div id='status' class='status d-inline'></div>
                </div>
                <div class='clearfox'></div>
                <div id='sale-details-card-blackAntifraud' class='card shadow pr-20 pl-20 p-10'>
                    <div class='row'>
                        <div class='col-lg-3'><p class='table-title'>Produto</p></div>
                        <div class='col-lg-9 text-right'><p class='text-muted'>Qtde</p></div>
                    </div>
                    <div id='table-product-black-antifraud'></div>
                </div>
            </div>
        </div>
    </div>
</div>
