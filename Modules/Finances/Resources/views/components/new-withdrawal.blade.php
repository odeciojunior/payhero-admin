<div class="card shadow card-tabs py-15 px-0 px-md-15 mb-50">
    <div class="flex-row justify-content-start align-items-center">
        <div class="col-12 mb-3">
            <div class="alert alert-danger alert-dismissible fade show" id='blocked-withdrawal'
                role="alert" style='display:none;'>
                <strong>Saque bloqueado!</strong> Entre em contato com o suporte para mais
                informações.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <h5 class="title-pad bold"> Nova transferência </h5>
            <p class="sub-pad">Faça um saque para sua conta bancária</p>
        </div>
        <div class='container' style="background: #FCFCFC; border-radius: 8px;">
            <div id="header" class='row align-items-center my-20 py-25 d-none d-md-flex'
                 style="position: relative">
                <div class="col-sm-3 d-flex justify-content-center">
                    <div id="div-available-money" class="price-holder pointer">
                        <h6 class="label-price mb-10 d-flex justify-content-start align-items-center">
                            <span class="badge-money mr-10">
                                <svg width="8" height="15" viewBox="0 0 8 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 9.96292C8 10.821 7.71589 11.5466 7.14766 12.1398C6.58941 12.7225 5.83676 13.0879 4.88972 13.2362V15H3.36449V13.268C2.69657 13.215 2.07352 13.0773 1.49533 12.8549C0.927103 12.6324 0.42866 12.3305 0 11.9492L0.657944 10.4555C1.52523 11.1758 2.46231 11.5731 3.46916 11.6472V8.34216C2.80125 8.16208 2.23801 7.9661 1.77944 7.75424C1.32087 7.53178 0.937072 7.20869 0.628037 6.78496C0.319003 6.35064 0.164486 5.7892 0.164486 5.10064C0.164486 4.5286 0.299065 4.00953 0.568224 3.54343C0.837383 3.06674 1.21121 2.67479 1.68972 2.36759C2.17819 2.04979 2.73645 1.84852 3.36449 1.76377V0H4.88972V1.74788C5.46791 1.82203 6.01122 1.97034 6.51963 2.1928C7.02804 2.41525 7.4567 2.69598 7.80561 3.03496L7.14766 4.5286C6.41994 3.89301 5.6324 3.51165 4.78505 3.38453V6.83263C5.45296 7.01271 6.00623 7.20869 6.44486 7.42055C6.88349 7.62182 7.25234 7.92903 7.5514 8.34216C7.85047 8.7553 8 9.29555 8 9.96292ZM2.09346 4.98941C2.09346 5.36017 2.21308 5.66208 2.45234 5.89513C2.69159 6.11759 3.03053 6.30826 3.46916 6.46716V3.40042C3.0405 3.49576 2.70156 3.68114 2.45234 3.95657C2.21308 4.23199 2.09346 4.57627 2.09346 4.98941ZM4.78505 11.5678C5.20374 11.4725 5.52274 11.2977 5.74206 11.0434C5.96137 10.7786 6.07103 10.4396 6.07103 10.0265C6.07103 9.69809 5.96137 9.43326 5.74206 9.23199C5.52274 9.03072 5.20374 8.86123 4.78505 8.72352V11.5678Z" fill="#1BE4A8"/>
                                </svg>
                            </span>
                            Saldo Disponível
                        </h6>
                        <h4 class="number saldoDisponivel">
                            <span style="color:#959595">R$ </span>
                            <span class="font-size-30 bold available-balance">0,00</span>
                        </h4>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="input-holder">
                        <label for="transfers_company_select"> Empresa</label>
                        <select style='border-radius: 8px' class="sirius-select"
                                name="company"
                                id="transfers_company_select"> </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label for="custom-input-addon"> Valor a sacar</label>
                    <div class="input-group align-items-center input-custom-transfer">
                        <div class="input-moeda">R$</div>
                        <input id="custom-input-addon" type="text"
                               class="form-control input-pad withdrawal-value"
                               placeholder="Digite o valor" aria-label="Digite o valor"
                               aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-sm-3 pt-1">
                    <button id="bt-withdrawal"
                            style="margin-top: 21px; height: 48px;"
                            class="btn btn-success disabled btn-sacar"
                            data-toggle="modal" disabled>
                        Realizar saque
                    </button>
                </div>
            </div>
            <div class='my-20 py-20 d-md-none'
                 style="position: relative;">
                <div class="col-md-12 p-0">
                    <div id="div-available-money_m" class="price-holder pb-10 pointer d-flex flex-column">
                        <h6 class="label-price m-0 d-flex align-items-center">
                                <span class="badge-money mr-10">
                                    <svg width="8" height="15" viewBox="0 0 8 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M8 9.96292C8 10.821 7.71589 11.5466 7.14766 12.1398C6.58941 12.7225 5.83676 13.0879 4.88972 13.2362V15H3.36449V13.268C2.69657 13.215 2.07352 13.0773 1.49533 12.8549C0.927103 12.6324 0.42866 12.3305 0 11.9492L0.657944 10.4555C1.52523 11.1758 2.46231 11.5731 3.46916 11.6472V8.34216C2.80125 8.16208 2.23801 7.9661 1.77944 7.75424C1.32087 7.53178 0.937072 7.20869 0.628037 6.78496C0.319003 6.35064 0.164486 5.7892 0.164486 5.10064C0.164486 4.5286 0.299065 4.00953 0.568224 3.54343C0.837383 3.06674 1.21121 2.67479 1.68972 2.36759C2.17819 2.04979 2.73645 1.84852 3.36449 1.76377V0H4.88972V1.74788C5.46791 1.82203 6.01122 1.97034 6.51963 2.1928C7.02804 2.41525 7.4567 2.69598 7.80561 3.03496L7.14766 4.5286C6.41994 3.89301 5.6324 3.51165 4.78505 3.38453V6.83263C5.45296 7.01271 6.00623 7.20869 6.44486 7.42055C6.88349 7.62182 7.25234 7.92903 7.5514 8.34216C7.85047 8.7553 8 9.29555 8 9.96292ZM2.09346 4.98941C2.09346 5.36017 2.21308 5.66208 2.45234 5.89513C2.69159 6.11759 3.03053 6.30826 3.46916 6.46716V3.40042C3.0405 3.49576 2.70156 3.68114 2.45234 3.95657C2.21308 4.23199 2.09346 4.57627 2.09346 4.98941ZM4.78505 11.5678C5.20374 11.4725 5.52274 11.2977 5.74206 11.0434C5.96137 10.7786 6.07103 10.4396 6.07103 10.0265C6.07103 9.69809 5.96137 9.43326 5.74206 9.23199C5.52274 9.03072 5.20374 8.86123 4.78505 8.72352V11.5678Z" fill="#1BE4A8"/>
                                    </svg>
                                </span>
                            Saldo Disponível
                        </h6>
                        <h4 class="price saldoDisponivel m-0">
                            <span style="font-size: 16px; line-height: 20px; color: #9E9E9E;">R$ </span>
                            <span style="color: #70707E" class="font-size-30 bold available-balance">0,00</span>
                        </h4>
                    </div>
                </div>
                <div class="mt-10 w-p100">
                    <div class="col-md-12 px-0 mb-20">
                        <div class="input-holder">
                            <label for="transfers_company_select_mobile"> Empresa</label>
                            <select style='border-radius: 8px'
                                    class="sirius-select"
                                    name="company"
                                    id="transfers_company_select_mobile"> </select>
                        </div>
                    </div>
                    <div class="col-md-12 p-0 mt-10">
                        <label for="custom-input-addon"> Valor a sacar</label>
                        <div class="input-group align-items-center input-custom-transfer">
                            <div class="input-moeda">R$</div>
                            <input id="custom-input-addon" type="text"
                                   class="form-control input-pad withdrawal-value"
                                   placeholder="Digite o valor" aria-label="Digite o valor"
                                   aria-describedby="basic-addon1"
                                   style='border-radius: 0 8px 8px 0; border: none !important; border-left:1px solid #DDD !important; padding: 10px'>
                        </div>
                    </div>
                </div>
                <div class="col-6">

                </div>
                <button id="bt-withdrawal_m"
                        class="btn btn-success btn-sacar"
                        data-toggle="modal">
                    Realizar saque
                </button>
            </div>
        </div>
    </div>
</div>
