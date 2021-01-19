@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/sales/css/finances.css?v=04') }}">
@endpush
<div class="modal fade example-modal-lg" id="modal_detalhes_transacao" aria-hidden="true" aria-labelledby="exampleModalTitle"
     role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple modal-sidebar modal-lg">
        <div id='modal-transactionsDetails' class="modal-content p-20 " style="width: 500px;">
            <div class="header-modal">
                <div class="row justify-content-between align-items-center" style="width: 100%;">
                    <div class="col-lg-2 text-right">
                        <a role="button" data-dismiss="modal">
                            <i class="material-icons pointer">close</i></a>
                    </div>
                    {{-- <div class="col-lg-2"> &nbsp;</div>--}}
                    <div class="col-lg-8 text-left"><h4> Transações do saque </h4></div>
                </div>
            </div>
            <div class="modal-body">
                <div class="transition-details">
                    <h5>Informações da solicitação</h5>
                    <div id="withdrawal-code"></div>
                </div>

                <div class="tab-content mt-20" id="nav-tabContent">

                    <div id='div_transactions' >
                        <table id='transactions_table' class='table table-striped mb-10'>
                            <thead>
                            <tr>
                                <th>Forma</th>
                                <th>Status</th>
                                <th>Liberação</th>
                                <th>Valor</th>
                            </tr>
                            </thead>
                            <tbody id='transactions-table-data'>
                            {{-- js carregado--}}
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('/modules/finances/js/detail.js?v=' . random_int(100, 10000)) }}"></script>
@endpush
