@push('css')

@endpush
<div class="modal hide fade in example-modal-lg" id="modal_detalhes_transacao" aria-hidden="true"
     aria-labelledby="exampleModalTitle"
     role="dialog" tabindex="-1" data-keyboard="false">

    <div class="modal-dialog modal-simple modal-sidebar modal-lg" style="height: 100vh;">
        <div id='modal-transactionsDetails' class="modal-content p-20 " style="width: 500px;">
            <div class="header-modal">
                <div class="d-flex flex-row justify-content-between align-items-start align-self-stretch"
                     style="width: 100%;">
                    <div class="col-lg-1">
                    </div>
                    <div class="col-lg-10 text-center"><h4> Liquidação do saque por bandeira </h4></div>
                    <div class="col-lg-1 text-right">
                        <a role="button" data-dismiss="modal">
                            <i class="material-icons pointer">close</i></a>
                    </div>
                </div>
            </div>

            <div class="modal-body">

                <div class="transition-details">
                    <h5>Informações da solicitação</h5>
                    <div id="withdrawal-code"></div>
                    <div id="pending_debt" style="display:none;"></div>
                </div>

                <div class="tab-content mt-20" id="nav-tabContent">
                    <div id="div_transactions" class="card">
                        <table id="transactions_table" class="table table-striped mb-10">
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
                            <span class="transaction-status mr-2 d-flex justify-content-center align-items-center align-self-center rounded-circle rounded-circle">
                                <span class="rounded-circle is-released-on "></span>
                            </span>Transferido
                    </div>
                    <div class="p-2 d-flex justify-content-start align-items-center">
                            <span class="transaction-status mr-2 d-flex justify-content-center align-items-center align-self-center rounded-circle rounded-circle">
                                <span class="rounded-circle is-released-off"></span>
                            </span>Em processamento
                    </div>
                </div>

                <div class="align-self-end mr-auto mb-5" id="btn_exports">
                    @can('finances_manage')
                        <div class="col-12 text-right">
                            <div class="justify-content-end align-items-center">
                                <div class="p-2 d-flex justify-content-end align-items-center" id="">
                                    <span style="cursor: default" id="bt_get_csv_default"
                                          class="o-download-cloud-1 icon-export btn mr-2"></span>
                                    <div class="btn-group" role="group">
                                        <button id="bt_get_xls_transfer" type="button"
                                                class="btn btn-round btn-default btn-outline btn-pill-left">.XLS
                                        </button>
                                        <button id="bt_get_csv_transfer" type="button"
                                                class="btn btn-round btn-default btn-outline btn-pill-right">.CSV
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>

                <div id="loading-ajax-transfer">
                </div>

                <!-- Aviso de Exportação -->
                <div id="alert-finance-export-transfer"
                     class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10" style="display:none;">
                    <div class="d-flex">
                        <span class="o-info-help-1"></span>
                        <div class="w-full">
                            <strong class="font-size-16">Exportando seu relatório</strong>
                            <p class="font-size-14 pr-md-100 mb-0">Sua exportação será entregue por e-mail para:
                                <strong id="export-finance-email-transfer"></strong> e aparecerá nas suas notificações.
                                Pode levar algum tempo, dependendo de quantos registros você estiver exportando.
                            </p>
                        </div>
                        <i class="material-icons pointer" data-dismiss="alert">close</i>
                    </div>
                </div>
                <!-- Resumo -->

                <div class="modal-content p-10 d-none" id="export-finance-getnet-transfer">
                    <div class='my-20 mx-20 text-center'>
                        <hr>
                        <h3 class="black"> Informe o e-mail para receber o relatório </h3>
                    </div>
                    <div class="modal-footer">

                        <input type="email" id="email_finance_export_transfer" class="mb-5">

                        <button type="button" class="btn btn-success btn-confirm-export-finance-getnet-transfer mt-5">
                            Enviar
                        </button>
                        <a id="btn-mobile-modal-close" class="btn btn-primary mt-5" style='color:white' role="button"
                           data-dismiss="modal" aria-label="Close">
                            Fechar
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>


</div>
@push('scripts')
    <script src="{{ mix('build/layouts/finances/detail.min.js') }}"></script>
@endpush
