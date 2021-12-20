<div class="card shadow card-tabs py-15 px-0 px-md-15 mb-50">
    <div class="row justify-content-start align-items-center">
        <div class="col-md-8 fix-5 px-sm-15">
            <div class="d-flex align-items-center">
                <div class="p-2 text-xs-center text-lg-left" style="flex:1">
                    <h5 class="title-pad col-12"> Extrato </h5>
                    <p class="sub-pad col-12"> Pra você controlar tudo que entra e sai da sua conta.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 d-flex justify-content-start justify-content-lg-end">
            <div class="price-holder px-20 p-md-0" style="position: relative">
                <h6 class="label-price bold"> Saldo no período</h6>
                <h4 id="available-in-period" style="font-weight: 700;font-size: 25px;display: inline;">
                </h4>
                <div style="height: 16px;" class="d-none d-md-block s-border-top green mb-15"></div>
                <div class="d-md-none s-border-left green mb-15"></div>
            </div>
        </div>
    </div>
    <div id="default-statement-filters" class="row justify-content-start align-items-center">
        <div class="col-12 p-20 pb-0">
            <div class="col-lg-12 mb-15">
                <div class="row align-items-center">
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="input-holder form-group">
                            <label for="extract_company_select">Empresa</label>
                            <select class="sirius-select" name="company" id="extract_company_select"> </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="transaction">Transação</label>
                            <input type="text" id="transaction" class="form-control select-pad" placeholder="Digite o código">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="input-holder form-group">
                            <label for="date_type">Data</label>
                            <select class="sirius-select" id="date_type">
                                <option value="transfer_date">Data da transferência</option>
                                <option value="sale_start_date">Data da venda</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group">
                            <input name="date_range" id="date_range" class="select-pad mt-30" placeholder="Clique para editar..." readonly>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="bt_collapse">
                    <div class="row">
                        <div class="col-sm-6 col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="reason">Razão</label>
                                <input type="text" id="reason" class="form-control select-pad" placeholder="Digite a razão. Ex.: Saque">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="transaction-value">Valor</label>
                                <input type="text" id="transaction-value" class="form-control select-pad withdrawal-value" placeholder="Digite o valor">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3">
                            <div class="input-holder form-group">
                                <label for="type">Tipo</label>
                                <select class="sirius-select" id="type">
                                    <option value="">Todos</option>
                                    <option value="1">Entrada</option>
                                    <option value="2">Saída</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row" style="height: 0">
                            <div class="col-6 col-xl-3 offset-xl-6 pr-0 mt-20">
                                <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center" data-toggle="collapse" data-target="#bt_collapse" aria-expanded="false" aria-controls="bt_collapse">
                                    <img id="icon-filtro" src="{{ asset('/modules/global/img/svg/filter-2-line.svg') }}"/>
                                    <span id="text-filtro">Filtros avançados</span>
                                </div>
                            </div>
                            <div class="col-6 col-xl-3 mt-20">
                                <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                    <img style="height: 12px; margin-right: 4px" src="{{ asset('/modules/global/img/svg/check-all.svg') }}">
                                    Aplicar filtros
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="custom-statement-filters" class="row justify-content-start align-items-center" style="display: none">
        <div class="col-12 p-20 pb-0">
            <div class="col-lg-12 mb-15">
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-holder form-group">
                            <label for="statement_company_select">Empresa</label>
                            <select class="sirius-select" name="company" id="statement_company_select">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3" style="display:none">
                        <div class="input-holder form-group">
                            <label for="statement_data_type_select">Data</label>
                            <select class="sirius-select" name="statement_data_type_select" id="statement_data_type_select">
                                <option value="schedule_date" selected>
                                    Data
                                </option>
                                <option value="transaction_date">
                                    Data da venda
                                </option>
                                <option value="liquidation_date">
                                    Data da liquidação
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group form-icons">
                            <label for="date_range_statement">Período</label>
                            <i style="right: 20px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-18"></i>
                            <input name="date_range_statement" id="date_range_statement" class="select-pad pr-30" placeholder="Clique para editar..." readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_method">Forma de pagamento</label>
                            <select name='payment_method' id="payment_method" class="sirius-select">
                                <option value="ALL">Todos</option>
                                <option value="CREDIT_CARD">Cartão de crédito</option>
                                <option value="BANK_SLIP">Boleto</option>
                                <option value="PIX">PIX</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="statement_sale">
                                Transação
                                <i style="font-weight: normal" class="o-question-help-1 ml-5 font-size-14" data-toggle="tooltip" title=""
                                        data-original-title="Se for passado esse valor, o extrato vai listar as informações dessa transação independente do filtro de data">
                                </i>
                            </label>
                            <input name="statement_sale" id="statement_sale" class="select-pad" placeholder="Transação">
                        </div>
                    </div>
                </div>
                <div class="collapse" id="bt-collapse-custom">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="input-holder form-group">
                                <label for="statement_status_select">Status</label>
                                <select class="sirius-select" name="status" id="statement_status_select">
                                    <option value="ALL">Todos</option>
                                    <option value="WAITING_FOR_VALID_POST">
                                        Aguardando postagem válida
                                    </option>
                                    <option value="WAITING_LIQUIDATION">Aguardando
                                        liquidação
                                    </option>
                                    <option value="WAITING_WITHDRAWAL">Aguardando saque
                                    </option>
                                    <option value="WAITING_RELEASE">Aguardando liberação
                                    </option>
                                    <option value="PAID">Liquidado</option>
                                    <option value="REVERSED">Estornado</option>
                                    <option value="ADJUSTMENT_CREDIT">Ajuste de crédito
                                    </option>
                                    <option value="ADJUSTMENT_DEBIT">Ajuste de débito
                                    </option>
                                    <option value="PENDING_DEBIT">Débitos pendentes</option>
                                </select>
                            </div>
                            <input name="withdrawal_id" id="withdrawal_id" type="hidden" class="select-pad" placeholder="Id do Saque">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row" style="height: 0">
                            <div class="col-6 col-xl-3 offset-xl-6 pr-0 mt-20">
                                <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center" data-toggle="collapse" data-target="#bt-collapse-custom" aria-expanded="false" aria-controls="bt_collapse">
                                    <img id="icon-custom-filtro" src="{{ asset('/modules/global/img/svg/filter-2-line.svg') }}"/>
                                    <span id="text-custom-filtro" style="margin-left: 10px">Filtros avançados</span>
                                </div>
                            </div>

                            <div class="col-6 col-xl-3 mt-20">
                                <div id="bt_filtro_statement" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                    <img style="height: 12px; margin-right: 4px" src="{{ asset('/modules/global/img/svg/check-all.svg') }}">
                                    Aplicar filtros
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
