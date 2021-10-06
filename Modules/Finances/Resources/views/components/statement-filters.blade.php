<div class="card shadow card-tabs py-15 px-0 px-md-15 mb-50">
    <div class="row justify-content-start align-items-center">
        <div class="col-md-8 fix-5 px-sm-15">
            <div class="d-flex align-items-center">
                <div class="p-2" style="flex:1">
                    <h5 class="title-pad"> Extrato </h5>
                    <p class="sub-pad"> Pra você controlar tudo que entra e sai da sua conta.
                    </p>
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
    <div class="row justify-content-start align-items-center">
        <div class="col-12 p-20 pb-0">
            <div class="col-lg-12 mb-15">
                <div class="row align-items-center">
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="input-holder form-group">
                            <label for="extract_company_select">Empresa</label>
                            <select class="form-control select-pad" name="company" id="extract_company_select"> </select>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="reason">Razão</label>
                            <input type="text" id="reason" class="form-control select-pad" placeholder="Digite a razão. Ex.: Saque">
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
                            <label for="type">Tipo</label>
                            <select class="form-control select-pad" id="type">
                                <option value="">Todos</option>
                                <option value="1">Entrada</option>
                                <option value="2">Saída</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="collapse" id="bt_collapse">
                    <div class="row">
                        <div class="col-sm-6 col-md-3 col-lg-3">
                            <div class="form-group">
                                <label for="transaction-value">Valor</label>
                                <input type="text" id="transaction-value" class="form-control select-pad withdrawal-value" placeholder="Digite o valor">
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3">
                            <div class="input-holder form-group">
                                <label for="date_type">Data</label>
                                <select class="form-control select-pad" id="date_type">
                                    <option value="transfer_date">Data da transferência</option>
                                    <option value="sale_start_date">Data da venda</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3 col-lg-3">
                            <div class="form-group">
                                <input name="date_range" id="date_range" class="select-pad mt-30"
                                placeholder="Clique para editar..." readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="row" style="height: 0">
                            <div class="col-6 col-xl-3 offset-xl-6 pr-0 mt-20">
                                <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center" data-toggle="collapse" data-target="#bt_collapse" aria-expanded="false" aria-controls="bt_collapse">
                                    <img id="icon-filtro"
                                            src="{{ asset('/modules/global/img/svg/filter-2-line.svg') }}"/>
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
</div>