@push('css')

@endpush
<div class="modal fade example-modal-lg" id="modal_detalhes_transacao" aria-hidden="true" aria-labelledby="exampleModalTitle"
     role="dialog" tabindex="-1">
    <div class="modal-dialog modal-simple modal-sidebar modal-lg" style="height: 100vh;">
        <div id='modal-transactionsDetails' class="modal-content p-20 " style="width: 500px;">
            <div class="header-modal">
                <div class="row justify-content-between align-items-center" style="width: 100%;">
                    <div class="text-right">
                        <a role="button" data-dismiss="modal">
                            <i class="material-icons pointer">close</i></a>
                    </div>
                    {{-- <div class="col-lg-2"> &nbsp;</div>--}}
                    <div class=" text-left"><h4> Liquidação do saque por bandeira </h4></div>
                </div>
            </div>
{{--            <div class="header-modal">--}}
{{--                <div class="row justify-content-between align-items-center" style="width: 100%;">--}}
{{--                    <div class="col-lg-2"> &nbsp;</div>--}}
{{--                    <div class="col-lg-8 text-center"><h4> Detalhes da venda </h4></div>--}}
{{--                    <div class="col-lg-2 text-right">--}}
{{--                        <a role="button" data-dismiss="modal">--}}
{{--                            <i class="material-icons pointer">close</i></a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

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
                                <th>Transfêrencia</th>
                                <th>Valor</th>
                            </tr>
                            </thead>
                            <tbody id='transactions-table-data'>
                            {{-- js carregado--}}
                            </tbody>
                        </table>
                    </div>
                </div>


                    <div class="col-10 text-left p-2 d-flex">
                            <div class=" d-flex justify-content-start align-items-center mr-4">
                                <span class="transaction-status mr-2 d-flex justify-content-center align-items-center align-self-center rounded-circle rounded-circle" >
                                    <span class="rounded-circle is-released-on " ></span>
                                </span>Liberado
                            </div>
                            <div class="p-2 d-flex justify-content-start align-items-center">
                                <span class="transaction-status mr-2 d-flex justify-content-center align-items-center align-self-center rounded-circle rounded-circle" >
                                    <span class="rounded-circle is-released-off" ></span>
                                </span>Em processamento
                            </div>
                    </div>



            </div>
            <div class="align-self-end mr-auto mb-5">
{{--                <div class="row justify-content-between align-items-center" style="width: 100%;">--}}
                    @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
                        <div class="col-6 text-left">
                            <div class="justify-content-start align-items-center">
                                <div class="p-2 d-flex justify-content-start align-items-center">
                                    <span id="bt_get_csv_default" class="o-download-cloud-1 icon-export btn mr-2"></span>
                                    <div class="btn-group" role="group">
                                        <button id="bt_get_xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                                        <button id="bt_get_csv" type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
{{--                </div>--}}
            </div>

        </div>
    </div>
</div>
@push('scripts')
    <script src="{{ asset('/modules/finances/js/detail.js?v=' . random_int(100, 10000)) }}"></script>
@endpush
