<div class="card shadow card-tabs py-15 px-0 px-md-15 mb-50">
    <div class="flex-row justify-content-start align-items-center">
        <div class="col-12 mb-3 text-xs-center text-lg-left">
            <div class="alert alert-danger alert-dismissible fade show" id='blocked-withdrawal'
                role="alert" style='display:none;'>
                <strong>Saque bloqueado!</strong> Entre em contato com o suporte para mais
                informações.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <h5 class="title-pad"> Nova transferência </h5>
            <p class="sub-pad"> Saque o dinheiro para sua conta bancária.
            </p>
        </div>
        <div class='container bg-gray sirius-radius'>
            <div class='row align-items-center my-20 py-20 d-none d-md-flex'
                 style="position: relative">
                <div class="col-sm-3">
                    <div id="div-available-money" class="price-holder pointer pl-10">
                        <h6 class="label-price mb-10"><b> Saldo Disponível </b></h6>
                        <h4 class="number saldoDisponivel">
                            <span style="color:#959595">R$ </span><span class="font-size-30 bold available-balance">0,00</span>
                        </h4>
                    </div>
                    <div class="s-border-left green"></div>
                </div>
                <div class="col-sm-3">
                    <div class="input-holder">
                        <label for="transfers_company_select"> Empresa</label>
                        <select style='border-radius:10px' class="form-control select-pad"
                                name="company"
                                id="transfers_company_select"> </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label for="custom-input-addon"> Valor a transferir</label>
                    <div class="input-group mb-3 align-items-center input-custom-transfer">
                        <div class="input-moeda">R$</div>
                        <input id="custom-input-addon" type="text"
                               class="form-control input-pad withdrawal-value"
                               placeholder="Digite o valor" aria-label="Digite o valor"
                               aria-describedby="basic-addon1"
                               style='border-radius: 0 12px 12px 0; border: none !important; border-left:1px solid #DDD !important;'>
                    </div>
                </div>
                <div class="col-sm-3 pt-1">
                    <button id="bt-withdrawal"
                            class="btn btn-success disabled btn-sacar mt-20"
                            data-toggle="modal"
                            style="border-radius: 8px;" disabled>
                        Sacar dinheiro
                    </button>
                </div>
            </div>
            <div class='row align-items-center justify-content-center my-20 py-20 bg-white d-md-none'
                 style="position: relative; height: 255px">
                <div class="col-md-12">
                    <div id="div-available-money_m" class="price-holder pointer pl-10">
                        <h6 class="label-price mb-10"><b> Saldo Disponível </b></h6>
                        <h4 class="price saldoDisponivel"></h4>
                    </div>
                    <div class="s-border-left green"></div>
                </div>
                <div class="px-10 mt-10">
                    <div class="col-md-12">
                        <div class="input-holder">
                            <label for="transfers_company_select_mobile"> Empresa</label>
                            <select style='border-radius:10px'
                                    class="form-control select-pad"
                                    name="company"
                                    id="transfers_company_select_mobile"> </select>
                        </div>
                    </div>
                    <div class="col-md-12 mt-10">
                        <label for="custom-input-addon"> Valor a transferir</label>
                        <div class="input-group mb-3 align-items-center input-custom-transfer">
                            <div class="input-moeda">R$</div>
                            <input id="custom-input-addon" type="text"
                                   class="form-control input-pad withdrawal-value"
                                   placeholder="Digite o valor" aria-label="Digite o valor"
                                   aria-describedby="basic-addon1"
                                   style='border-radius: 0 12px 12px 0; border: none !important; border-left:1px solid #DDD !important;'>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <button id="bt-withdrawal_m"
                            class="btn btn-success btn-sacar"
                            data-toggle="modal">
                        Sacar dinheiro
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>