<div class="card shadow card-tabs p-20 mb-50">
    <div class="row justify-content-start align-items-center mx-0">
        <div class="col-md-8 px-0">
            <h5 class="title-pad bold"> Extrato </h5>
            <p class="sub-pad"> Pra você controlar tudo que entra e sai da sua conta.</p>
        </div>
        <div class="col-md-4 d-flex justify-content-start justify-content-lg-end px-0">
            <div class="price-holder px-0 p-md-0 text-left text-md-center"
                 style="position: relative">
                <h6 class="label-price bold">
                    <span class="badge-money mr-10">
                        <svg width="8"
                             height="15"
                             viewBox="0 0 8 15"
                             fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 9.96292C8 10.821 7.71589 11.5466 7.14766 12.1398C6.58941 12.7225 5.83676 13.0879 4.88972 13.2362V15H3.36449V13.268C2.69657 13.215 2.07352 13.0773 1.49533 12.8549C0.927103 12.6324 0.42866 12.3305 0 11.9492L0.657944 10.4555C1.52523 11.1758 2.46231 11.5731 3.46916 11.6472V8.34216C2.80125 8.16208 2.23801 7.9661 1.77944 7.75424C1.32087 7.53178 0.937072 7.20869 0.628037 6.78496C0.319003 6.35064 0.164486 5.7892 0.164486 5.10064C0.164486 4.5286 0.299065 4.00953 0.568224 3.54343C0.837383 3.06674 1.21121 2.67479 1.68972 2.36759C2.17819 2.04979 2.73645 1.84852 3.36449 1.76377V0H4.88972V1.74788C5.46791 1.82203 6.01122 1.97034 6.51963 2.1928C7.02804 2.41525 7.4567 2.69598 7.80561 3.03496L7.14766 4.5286C6.41994 3.89301 5.6324 3.51165 4.78505 3.38453V6.83263C5.45296 7.01271 6.00623 7.20869 6.44486 7.42055C6.88349 7.62182 7.25234 7.92903 7.5514 8.34216C7.85047 8.7553 8 9.29555 8 9.96292ZM2.09346 4.98941C2.09346 5.36017 2.21308 5.66208 2.45234 5.89513C2.69159 6.11759 3.03053 6.30826 3.46916 6.46716V3.40042C3.0405 3.49576 2.70156 3.68114 2.45234 3.95657C2.21308 4.23199 2.09346 4.57627 2.09346 4.98941ZM4.78505 11.5678C5.20374 11.4725 5.52274 11.2977 5.74206 11.0434C5.96137 10.7786 6.07103 10.4396 6.07103 10.0265C6.07103 9.69809 5.96137 9.43326 5.74206 9.23199C5.52274 9.03072 5.20374 8.86123 4.78505 8.72352V11.5678Z"
                                  fill="#1BE4A8" />
                        </svg>
                    </span>
                    Saldo no período
                </h6>
                <h4 id="available-in-period"
                    class="bold"
                    style="font-size: 25px;display: inline;">
                    R$ 0,00
                </h4>
            </div>
        </div>
    </div>
    <div id="default-statement-filters"
         class="row justify-content-start align-items-center mx-0 mt-10">
        <div class="col-12 pb-0 px-0">
            <div class="col-lg-12 mb-15 px-0">
                <div class="row align-items-center mx-0">
                    <div class="col-12 col-lg-3 px-0 px-lg-10">
                        <div class="input-holder form-group">
                            <label for="extract_company_select">Empresa</label>
                            <input type="text" disabled class="company_name">
                            {{-- <select class="sirius-select" name="company" id="extract_company_select"> </select> --}}
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 px-0 px-lg-10">
                        <div class="form-group">
                            <label for="transaction">Transação</label>
                            <input type="text"
                                   id="transaction"
                                   class="form-control select-pad"
                                   placeholder="Digite o código"
                                   style="height: 49px !important;">
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 px-0 px-lg-10">
                        <div class="input-holder form-group">
                            <label for="date_type">Data</label>
                            <select class="sirius-select"
                                    id="date_type">
                                <option value="transfer_date">Data da transferência</option>
                                <option value="sale_start_date">Data da venda</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3 px-0 px-lg-10">
                        <div class="form-group">
                            <input name="date_range"
                                   id="date_range"
                                   class="input-pad mt-30"
                                   placeholder="Clique para editar..."
                                   readonly>
                        </div>
                    </div>
                </div>
                <div class="collapse"
                     id="bt_collapse">
                    <div class="row mx-0">
                        <div class="col-12 col-lg-3 px-0 px-lg-10">
                            <div class="form-group">
                                <label for="reason">Razão</label>
                                <input type="text"
                                       id="reason"
                                       class="form-control select-pad"
                                       placeholder="Digite a razão. Ex.: Saque"
                                       style="height: 49px !important;">
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 px-0 px-lg-10">
                            <div class="form-group">
                                <label for="transaction-value">Valor</label>
                                <input type="text"
                                       id="transaction-value"
                                       class="form-control select-pad withdrawal-value"
                                       placeholder="Digite o valor"
                                       style="height: 49px !important;">
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 px-0 px-lg-10">
                            <div class="input-holder form-group">
                                <label for="type">Tipo</label>
                                <select class="sirius-select"
                                        id="type">
                                    <option value="">Todos</option>
                                    <option value="1">Entrada</option>
                                    <option value="2">Saída</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-nowrap mx-0 justify-content-center">
                    <div class="col-auto col-xl-3 px-lg-10 offset-xl-6">
                        <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center flex-column flex-lg-row"
                             data-toggle="collapse"
                             data-target="#bt_collapse"
                             aria-expanded="false"
                             aria-controls="bt_collapse">
                            <img id="icon-filtro"
                                 src="{{ mix('build/global/img/svg/filter-2-line.svg') }}" />
                            <span class="w-p100 w-md-auto"
                                  id="text-filtro">Filtros avançados</span>
                        </div>
                    </div>
                    <div class="col-auto col-xl-3 px-lg-10">
                        <div id="bt_filtro"
                             class="btn btn-primary-1 bold d-flex justify-content-center align-items-center flex-column flex-lg-row">
                            <img style="height: 12px; margin-right: 4px"
                                 src="{{ mix('build/global/img/svg/check-all.svg') }}">
                            <span class="w-p100 w-md-auto"> Aplicar filtros </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="custom-statement-filters"
         class="row justify-content-start align-items-center mx-0 mt-10"
         style="display: none">
        <div class="col-12 pb-0 px-0">
            <div class="col-lg-12 mb-15 px-0">
                <div class="row mx-0">
                    <div class="col-md-3">
                        <div class="input-holder form-group">
                            <label for="statement_company_select">Empresa</label>
                            <input type="text" disabled class="company_name">
                            {{-- <select class="sirius-select" name="company" id="statement_company_select">
                            </select> --}}
                        </div>
                    </div>
                    <div class="col-md-3"
                         style="display:none">
                        <div class="input-holder form-group">
                            <label for="statement_data_type_select">Data</label>
                            <select class="sirius-select"
                                    name="statement_data_type_select"
                                    id="statement_data_type_select">
                                <option value="schedule_date"
                                        selected>
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
                            <i style="right: 20px;"
                               class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-18"></i>
                            <input name="date_range_statement"
                                   id="date_range_statement"
                                   class="input-pad pr-30"
                                   placeholder="Clique para editar..."
                                   readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_method">Forma de pagamento</label>
                            <select name='payment_method'
                                    id="payment_method"
                                    class="sirius-select">
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
                                <i style="font-weight: normal"
                                   class="o-question-help-1 ml-5 font-size-14"
                                   data-toggle="tooltip"
                                   title=""
                                   data-original-title="Se for passado esse valor, o extrato vai listar as informações dessa transação independente do filtro de data">
                                </i>
                            </label>
                            <input name="statement_sale"
                                   id="statement_sale"
                                   class="input-pad"
                                   placeholder="Transação">
                        </div>
                    </div>
                </div>
                <div class="collapse"
                     id="bt-collapse-custom">
                    <div class="row mx-0">
                        <div class="col-md-3">
                            <div class="input-holder form-group">
                                <label for="statement_status_select">Status</label>
                                <select class="sirius-select"
                                        name="status"
                                        id="statement_status_select">
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
                            <input name="withdrawal_id"
                                   id="withdrawal_id"
                                   type="hidden"
                                   class="select-pad"
                                   placeholder="Id do Saque">
                        </div>
                    </div>
                </div>
                <div class="row flex-nowrap mx-0 justify-content-center">
                    <div class="col-auto col-xl-3 px-lg-10 offset-xl-6">
                        <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center flex-column flex-lg-row collapsed"
                             data-toggle="collapse"
                             data-target="#bt-collapse-custom"
                             aria-expanded="false"
                             aria-controls="bt_collapse">
                            <img id="icon-filtro"
                                 src="{{ mix('build/global/img/svg/filter-2-line.svg') }}" />
                            <span class="w-p100 w-md-auto"
                                  id="text-filtro">Filtros avançados</span>
                        </div>
                    </div>

                    <div class="col-auto col-xl-3 px-lg-10">
                        <div id="bt_filtro_statement"
                             class="btn btn-primary-1 bold d-flex justify-content-center align-items-center flex-column flex-lg-row">
                            <img style="height: 12px; margin-right: 4px"
                                 src="{{ mix('build/global/img/svg/check-all.svg') }}">
                            <span class="w-p100 w-md-auto"> Aplicar filtros </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
