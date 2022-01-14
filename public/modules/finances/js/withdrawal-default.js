$('#bt-withdrawal').on('click', function () {

    let availableBalanceText = $('.available-balance').html().replace(',', '').replace('.', '');
    let toTransferText = $('#custom-input-addon').val().replace(',', '').replace('.', '');
    let availableBalance = parseInt(availableBalanceText);
    let toTransfer = parseFloat(toTransferText);

    if (toTransfer > availableBalance) {

        alertCustom('error', 'O valor requerido ultrapassa o limite disponivel');
        toTransferText = $('#custom-input-addon').val();
        toTransfer = toTransferText.slice(0, -2);
        $('#custom-input-addon').val('');
        $('.withdrawal-value').maskMoney({thousands: '.', decimal: ',', allowZero: true});

    } else if ($('#custom-input-addon').val() == '') {
        alertCustom('error', 'Valor do saque inválido!');

    } else {
        $.ajax({
                url: "/api/withdrawals/getaccountinformation",
                type: "POST",
                dataType: "json",
                data: {
                    company_id: $("#transfers_company_select").val(),
                    withdrawal_value: $('#custom-input-addon').val()
                },
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: (response) => {
                    errorAjaxResponse(response);
                },
                success: (response) => {

                    if (response.data.user_documents_status == 'pending') {
                        let route = '/profile';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Documentos pessoais ainda não validados</strong></h3>' + '<h4 align="center">Parece que ainda existe pendencias com seus documentos</h4>' + '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' + '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else if (response.data.documents_status == 'pending') {
                        let companie = $('#transfers_company_select').val();
                        let _route = '/companies/' + companie + '/edit';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Documentos da empresa ainda não validados</strong></h3>' + '<h4 align="center">Parece que ainda existe pendencias com os documentos de sua empresa</h4>' + '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' + '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else if (response.data.email_verified == 'false') {
                        let _route = '/profile';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Email de usuário ainda não foi verificado</strong></h3>' + '<h4 align="center">Para maior segurança é necessário validar o e-mail do usuário na página de perfil</h4>' + '<h5 align="center">Deseja ir a pagina de perfil? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                    } else if (response.data.cellphone_verified == 'false') {
                        let _route = '/profile';
                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Oooppsssss!");
                        $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Telefone de usuário ainda não foi verificado</strong></h3>' + '<h4 align="center">Para maior segurança é necessário validar o telefone do usuário na página de perfil</h4>' + '<h5 align="center">Deseja ir a pagina de perfil? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
                        $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');

                    } else {

                        $('#modal-withdrawal').modal('show');
                        $('#modal-withdrawal-title').text("Confirmar Saque");

                        let confirmationData = `<div class="row">
                                                    <div class="col">
                                                        <h4>Verifique os dados da conta:</h4>
                                                        <div><b>Banco:</b><span id="modal-withdrawal-bank"></span></div>
                                                        <div><b>Agência:</b><span id="modal-withdrawal-agency"></span><span id="modal-withdrawal-agency-digit"></span></div>
                                                        <div><b>Conta:</b><span id="modal-withdrawal-account"></span><span id="modal-withdrawal-account-digit"></span></div>
                                                        <div><b>Documento:</b><span id="modal-withdrawal-document"></span></div>
                                                        </div>`;

                        confirmationData += `</div>
                                                <hr>
                                                <h4>Valor do saque: <span id="modal-withdrawal-value" class='greenGradientText'></span>`;

                        // confirmationData += `</h4>
                        //                     <hr>
                        //                     <div class="alert alert-warning text-center">
                        //                         <p><b>Atenção! A taxa para saques é gratuita para saques com o valor igual ou superior a R$500,00. Caso contrário a taxa cobrada será de R$10,00.</b></p>
                        //                         <p><b>Os saques solicitados poderão ser liquidados em até um dia útil!</b></p>
                        //                     </div>
                        //                 </div>`;

                        $('#modal_body').html(confirmationData);

                        $("#modal-withdraw-footer").html(`
                            <div class="col-md-12 text-center">
                                <button
                                    id="bt-cancel-withdrawal"
                                    class="btn col-5 s-btn-border"
                                    data-dismiss="modal"
                                    aria-label="Close"
                                    style="font-size:20px; width:200px; border-radius: 12px; color:#818181;">
                                    Cancelar
                                </button>

                                <button
                                    id="bt-confirm-withdrawal"
                                    class="btn btn-success col-5 btn-confirmation s-btn-border"
                                    style="background-color: #41DC8F;font-size:20px; width:200px;">
                                    <strong>Confirmar</strong>
                                </button>
                            </div>
                        `);

                        $("#modal-withdrawal-value").html(' R$ ' + $('#custom-input-addon').val() + ' ');
                        $("#modal-withdrawal-bank").html('  ' + response.data.bank);
                        $("#modal-withdrawal-agency").html('  ' + response.data.agency);
                        if (response.data.agency_digit != '' && response.data.agency_digit != null) {
                            $("#modal-withdrawal-agency-digit").html(' - ' + response.data.agency_digit);
                        }
                        $("#modal-withdrawal-account").html('  ' + response.data.account);
                        if (response.data.account_digit != '' && response.data.account_digit != null) {

                            $("#modal-withdrawal-account-digit").html(' - ' + response.data.account_digit);
                        }
                        $("#modal-withdrawal-document").html('  ' + response.data.document);

                        $("#bt-confirm-withdrawal").off("click");
                        $("#bt-confirm-withdrawal").on("click", function (e) {

                            var click = $(this);
                            if (click.data('clicked')) {
                                e.preventDefault();
                                e.stopPropagation();
                                return false;
                            }
                            click.data('clicked', true);

                            window.setTimeout(function(){
                                click.removeData('clicked');
                            }, 2000);

                            loadOnModal('#modal-body');

                            $("#bt-confirm-withdrawal").attr('disabled', 'disabled');
                            $.ajax({
                                url: "/api/withdrawals",
                                type: "POST",
                                data: {
                                    company_id: $('#transfers_company_select').val(),
                                    withdrawal_value: $('#custom-input-addon').val(),
                                    gateway_id: window.gatewayCode,
                                },
                                dataType: "json",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                error: (response) => {
                                    loadingOnScreenRemove();
                                    errorAjaxResponse(response);
                                },
                                success: (response) => {
                                    loadingOnScreenRemove();
                                    $('#modal-withdrawal').modal('show');
                                    $('#modal-withdrawal-title').text("Sucesso!");
                                    $('#modal_body').html('<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>' + '<h3 align="center"><strong>Sua solicitação foi para avaliação!</strong></h3>');
                                    $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success btn-return" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
                                    $('.btn-return').on('click', function () {
                                        $('#custom-input-addon').val('');
                                    });

                                    updateBalances();

                                    $('.btn-return').on(function () {
                                        $('#modal_body').modal('hide');
                                    });
                                },
                                complete: (response) => {
                                    $("#bt-confirm-withdrawal").removeAttr('disabled');
                                }
                            });

                            window.setTimeout(function(){
                                $click.removeData('clicked');
                            }, 2000);
                        });
                    }
                }
            }
        );
    }
});
