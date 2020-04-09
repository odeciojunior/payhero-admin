@push('css')
    <link rel='stylesheet' href='{{asset('/modules/sales/css/index.css?v=' . uniqid())}}'>
@endpush

<div class='modal fade example-modal-lg' id='modal-detalhes' aria-hidden='true' aria-labelledby='exampleModalTitle' role='dialog' tabindex='-1'>
    <div class='modal-dialog modal-simple modal-sidebar modal-lg'>
        <div id='modal-sale-details' class='modal-content p-20' style='width: 500px;'>
            <div class='header-modal'>
                <div class='row justify-content-between align-items-center' style='width: 100%'>
                    <div class='col-lg-2'></div>
                    <div class='col-lg-8 text-center'>
                        <h4>Detalhes</h4>
                    </div>
                    <div class='col-lg-20 text-right'>
                        <a role='button' data-dismiss='modal'>
                            <i class='material-icons pointer'>close</i>
                        </a>
                    </div>
                </div>
            </div>
            <div class='modal-body'>
            </div>
        </div>
    </div>
</div>
