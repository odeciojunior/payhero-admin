$(document).ready(function () {
    var eventStatusBraspag = {
        'Scheduled': 'Agendado',
        'Pending': 'Pendente',
        'Settled': 'Liquidado',
        'Error': 'Erro',
        'WaitingFoAdjustementDebit': 'Aguardando débito de ajuste',
        'Anticipated': 'Antecipado',
    };
    var eventStatusBraspagBadge = {
        'Scheduled': 'badge-primary',
        'Pending': 'badge-info',
        'Settled': 'badge-success',
        'Error': 'badge-danger',
        'WaitingFoAdjustementDebit': 'badge-warning',
        'Anticipated': 'badge-secondary',
    };
    //Comportamentos da tela
    $('#date_range').daterangepicker({
        startDate: moment().startOf('week'),
        endDate: moment(),
        opens: 'center',
        maxDate: moment().endOf("day"),
        alwaysShowCalendar: true,
        showCustomRangeLabel: 'Customizado',
        autoUpdateInput: true,
        locale: {
            locale: 'pt-br',
            format: 'DD/MM/YYYY',
            applyLabel: "Aplicar",
            cancelLabel: "Limpar",
            fromLabel: 'De',
            toLabel: 'Até',
            customRangeLabel: 'Customizado',
            weekLabel: 'W',
            daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            firstDay: 0
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $('.withdrawal-value').maskMoney({thousands: '.', decimal: ',', allowZero: true});

    let balanceLoader = {
        styles: {
            container: {
                minHeight: '31px',
                justifyContent: 'flex-start',
            },
            loader: {
                width: '20px',
                height: '20px',
                borderWidth: '4px'
            },
        },
        insertBefore: '.grad-border',
    };

    //END - Comportamentos da tela

    //Obtém as empresas
    function getCompanies() {
        $.ajax({
            method: "GET",
            url: "/api/companies?select=true",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {

                if (!isEmpty(response.data)) {

                    $('.page-content').show();
                    $('.content-error').hide();

                    $(response.data).each(function (index, value) {
                        let data = `<option country="${value.country}" value="${value.id}">${value.name}</option>`;
                        $("#transfers_company_select").append(data);
                        $("#extract_company_select").append(data);
                    });

                    if (response.data[0].antecipation_enabled_flag) {
                        $('.div-antecipable-balance').show();
                    } else {
                        $('.div-antecipable-balance').hide();
                    }

                    checkAllowed();
                    updateBalances();
                    updateTransfersTable();
                } else {
                    $('.page-content').hide();
                    $('.content-error').show();
                }

            }
        });
    }

    getCompanies();

    checkBraspagCompany();

    //Verifica se o saque está liberado
    function checkAllowed() {
        $.ajax({
            url: "/api/withdrawals/checkallowed",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
                $('#bt-withdrawal').prop('disabled', true).addClass('disabled');
            },
            success: response => {
                if (response.allowed) {
                    $('#bt-withdrawal').prop('disabled', false).removeClass('disabled');
                    $('#blocked-withdrawal').hide();
                } else {
                    $('#bt-withdrawal').prop('disabled', true).addClass('disabled');
                    $('#blocked-withdrawal').show();
                }
            }
        });
    }

    //atualiza o saldo
    $(document).on("change", "#transfers_company_select", function () {
        $("#transfers_company_select option[value=" + $('#transfers_company_select option:selected').val() + "]").prop("selected", true);
        $('#custom-input-addon').val('');
        updateBalances();
        if ($(this).children("option:selected").attr('country') != 'brazil') {
            $("#col_transferred_value").show();
        } else {
            $("#col_transferred_value").hide();
        }
    });

    function updateBalances() {
        loadOnAny('.price', false, balanceLoader);
        loadOnTable('#withdrawals-table-data', '#withdrawalsTable');
        $.ajax({
            url: "api/finances/getbalances/",
            type: "GET",
            data: {company: $("#transfers_company_select option:selected").val()},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                loadOnAny('.price', true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                $('.saldoPendente').html('<span class="currency">R$</span><span class="pending-balance">0,00</span>');
                $('.saldoAntifraude').html('<span class="currency">R$</span><span class="pending-antifraud-balance">0,00</span>');
                $('.removeSpan').remove();
                $('.disponivelAntecipar').append('<span class="currency removeSpan">R$</span><span class="antecipable-balance removeSpan">0,00</span>');
                $('.saldoDisponivel').html('<span class="currency">R$</span><span class="available-balance">0,00 <i class="material-icons ml-5" style="color: #44a44b;">arrow_forward</i></span>');
                $('.saltoTotal').html('<span class="currency" style="color:#687089">R$</span><span class="total-balance" style="color:#57617c">0,00</span>');
                $('.saldoBloqueado').html('<span class="currency">R$</span><span class="blocked-balance">0,00</span>');

                //Saldo antecipavel
                $('.saldoAntecipavel').html('<span class="currency">R$</span><span class="antecipable-balance">' + response.anticipable_balance + '</span>');

                // Saldo bloqueado
                $('.saldoBloqueado').html('<span class="currency">R$</span><span class="blocked-balance">' + response.blocked_balance + '</span>');

                $('.totalConta').html('<span class="currency">R$</span><span class="total-balance">0,00</span>');
                $('.total_available').html('<span class="currency">R$</span>' + isEmpty(response.available_balance));
                $(".currency").html('R$ ');
                $(".available-balance").html(isEmpty(response.available_balance));
                $(".pending-balance").html(isEmpty(response.pending_balance));
                $(".pending-antifraud-balance").html(response.pending_antifraud_balance);
                $(".total-balance").html(isEmpty(response.total_balance));
                $(".loading").remove();
                $("#div-available-money").unbind('click');
                $("#div-available-money").on("click", function () {
                    $(".withdrawal-value").val(isEmpty(response.available_balance));
                });

                if (response.currency != 'real') {
                    $("#quotation_information").show();
                } else {
                    $("#quotation_information").hide();
                }

                $("#current_quotation").text("R$ " + response.currencyQuotation);
                $("#label_quotation").text("Cotação do " + response.currency);

                updateWithdrawalsTable();
                loadOnAny('.price', true);
            }
        });

        $('.div-antecipable-balance').popover({
            animation: true,
            placement: 'top',
            title: 'Antecipação de saldo pendente',
            content: '',
            html: true,
            template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header">Teste</h3><div class="popover-body"></div></div>'
        });

        // se clicar fora do popover ele fecha
        $('html').on('click', function (e) {
            if (typeof $(e.target).data('original-title') == 'undefined') {
                $('.div-antecipable-balance').popover('hide');
            }
        });

        $('.div-antecipable-balance').on('click', function () {

            loadingOnScreen();

            $.ajax({
                url: "api/anticipations/" + $("#transfers_company_select option:selected").val(),
                type: "GET",
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
                    let tooltipData = `
                        Disponível para antecipação: R$ ${response.data.antecipable_value} <br>
                        Taxa de antecipação: R$ ${response.data.tax_value} <br>
                        Saldo final antecipável: <b>R$ ${response.data.value_minus_tax}</b> <br>
                        <button id='confirm-anticipation' class='btn btn-success text-center mt-20 mb-20'>Confirmar antecipação</button>
                    `;
                    $('.div-antecipable-balance').attr('data-content', tooltipData);
                    $('.div-antecipable-balance').popover('show');

                    $("#confirm-anticipation").unbind("click");
                    $("#confirm-anticipation").on("click", function () {

                        loadingOnScreen();

                        $.ajax({
                            url: "api/anticipations",
                            type: "POST",
                            data: {company_id: $("#transfers_company_select option:selected").val()},
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: (response) => {
                                loadingOnScreenRemove();
                                alertCustom('error', 'Ocorreu algum erro');
                            },
                            success: (response) => {
                                loadingOnScreenRemove();
                                alertCustom('success', response.data.message);
                                updateBalances();
                                updateTransfersTable();
                            }
                        });

                    });
                }
            });

        });

        function isEmpty(value) {
            if (value.length === 0) {
                return 0;
            } else {
                return value
            }
        }

        // Fazer saque
        $('#bt-withdrawal').unbind("click");
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

                                if (response.data.currency !== 'real') {
                                    confirmationData += `<div class="col">
                                                            <h4>Transferência para o exterior:</h4>
                                                            <div><b>Moeda:</b> ${response.data.currency}</div>
                                                            <div><b>Cotação:</b> R$ ${response.data.quotation}</div>
                                                            <div><b>Taxa de IOF:</b> R$ ${response.data.iof.value} ( ${response.data.iof.tax}%)</div>
                                                            <div><b>Custo:</b> R$ ${response.data.cost.value} (${response.data.cost.tax}%)</div>
                                                            <div><b>Total:</b> R$ ${response.data.abroad_transfer.value} (${response.data.abroad_transfer.tax}%)</div>
                                                         </div>`;
                                }

                                confirmationData += `</div>
                                                     <hr>
                                                     <h4>Valor do saque: <span id="modal-withdrawal-value" class='greenGradientText'></span>`;

                                if (response.data.currency !== 'real') {
                                    confirmationData += `</h4>
                                                         <h4>Valor convertido:
                                                            <span class='greenGradientText'>${response.data.abroad_transfer.converted_money}</span>
                                                            <span id="taxValue" class="text-gray-dark" style="font-size: 14px; color:#999999" title="Taxa de saque"> ( em ${response.data.currency} )</span>`;
                                }

                                confirmationData += `</h4>
                                                    <hr>
                                                    <div class="alert alert-warning text-center">
                                                        <p><b>Atenção! A taxa para saques é gratuita para saques com o valor igual ou superior a R$500,00. Caso contrário a taxa cobrada será de R$10,00.</b></p>
                                                        <p><b>Os saques solicitados poderão ser liquidados em até um dia útil!</b></p>
                                                    </div>
                                              </div>`;

                                $('#modal_body').html(confirmationData);

                                $('#modal-withdraw-footer').html('<button id="bt-confirm-withdrawal" class="btn btn-success" style="background-image: linear-gradient(to right, #23E331, #44A44B);font-size:20px; width:100%">' + '<strong>Confirmar</strong></button>' + '<button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal" aria-label="Close" style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">' + '<strong>Cancelar</strong></button>');

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

                                $("#bt-confirm-withdrawal").unbind("click");
                                $("#bt-confirm-withdrawal").on("click", function () {
                                    loadOnModal('#modal-body');

                                    $("#bt-confirm-withdrawal").attr('disabled', 'disabled');
                                    $.ajax({
                                        url: "/api/withdrawals",
                                        type: "POST",
                                        data: {
                                            company_id: $('#transfers_company_select').val(),
                                            withdrawal_value: $('#custom-input-addon').val()
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

                                            $('.btn-return').click(function () {
                                                $('#modal_body').modal('hide');
                                            });
                                        },
                                        complete: (response) => {
                                            $("#bt-confirm-withdrawal").removeAttr('disabled');
                                        }
                                    });
                                });
                            }
                        }
                    }
                );
            }
        });

        let statusWithdrawals = {
            1: 'warning',
            2: 'primary',
            3: 'success',
            4: 'danger',
            5: 'primary',
            6: 'primary',
            7: 'danger',
        };

        function updateWithdrawalsTable(link = null) {
            $("#withdrawals-table-data").html("");
            loadOnTable('#withdrawals-table-data', '#transfersTable');
            if (link == null) {
                link = '/api/withdrawals';
            } else {
                link = '/api/withdrawals' + link;
            }
            $.ajax({
                method: "GET",
                url: link,
                data: {company: $("#transfers_company_select option:selected").val()},
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: (response) => {
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    $("#withdrawals-table-data").html('');
                    if (response.data === '' || response.data === undefined || response.data.length === 0) {
                        $("#withdrawals-table-data").html("<tr><td colspan='5' class='text-center'>Nenhum saque realizado até o momento</td></tr>");
                        $("#withdrawals-pagination").html("");
                    } else {
                        $.each(response.data, function (index, data) {

                            let tableData = '';
                            tableData += '<tr>';
                            tableData += "<td>" + data.account_information + "</td>";
                            tableData += "<td>" + data.date_request + "</td>";
                            tableData += "<td>" + data.date_release + "</td>";
                            if (data.tax_value < 50000) {
                                tableData += "<td>" + data.value + '<br><small>(taxa de R$10,00)</small>' + "</td>";
                            } else {
                                tableData += "<td>" + data.value + "</td>";
                            }
                            if ($("#transfers_company_select").children("option:selected").attr('country') != 'brazil') {
                                tableData += "<td class='text-center'>" + data.value_transferred + "</td>";
                            }
                            tableData += '<td class="shipping-status">';
                            tableData += '<span class="badge badge-' + statusWithdrawals[data.status] + '">' + data.status_translated + '</span>';
                            tableData += '</td>';
                            tableData += '</tr>';
                            $("#withdrawals-table-data").append(tableData);
                            $('#withdrawalsTable').addClass('table-striped')
                        });
                        pagination(response, 'withdrawals', updateWithdrawalsTable);
                    }

                }
            });
        }
    }

    //atualiza a table de extrato
    $(document).on("click", "#bt_filtro", function () {
        $("#extract_company_select option[value=" + $('#extract_company_select option:selected').val() + "]").prop("selected", true);
        updateTransfersTable();
        if ($(this).children("option:selected").attr('country') != 'brazil') {
            $("#transferred_value").show();
        } else {
            $("#transferred_value").hide();
        }
    });

    function updateTransfersTable(link = null) {
        $("#table-transfers-body").html('');
        loadOnAny('#available-in-period', false, balanceLoader);

        loadOnTable('#table-transfers-body', '#transfersTable');
        if (link == null) {
            link = '/transfers';
        } else {
            link = '/transfers' + link;
        }

        let data = {
            company: $("#extract_company_select option:selected").val(),
            date_type: $("#date_type").val(),
            date_range: $("#date_range").val(),
            reason: $('#reason').val(),
            transaction: $("#transaction").val(),
            type: $('#type').val(),
            value: $('#transaction-value').val(),
        };

        $.ajax({
            method: "GET",
            url: link,
            data: data,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {

                $("#table-transfers-body").html('');

                let balance_in_period = response.meta.balance_in_period;
                let isNegative = parseFloat(balance_in_period.replace('.', '').replace(',', '.')) < 0;
                let availableInPeriod = $('#available-in-period');
                availableInPeriod.html(`<span${isNegative ? ' style="color:red;"' : ''}><span class="currency">R$ </span>${balance_in_period}</span>`);
                if (isNegative) {
                    availableInPeriod.html(`<span style="color:red;"><span class="currency">R$ </span>${balance_in_period}</span>`)
                        .parent()
                        .find('.grad-border')
                        .removeClass('green')
                        .addClass('red');
                } else {
                    availableInPeriod.html(`<span class="currency">R$ </span>${balance_in_period}`)
                        .parent()
                        .find('.grad-border')
                        .removeClass('red')
                        .addClass('green');
                }

                loadOnAny('#available-in-period', true);

                if (response.data == '') {

                    $("#table-transfers-body").html("<tr><td colspan='3' class='text-center'>Nenhuma movimentação até o momento</td></tr>");
                    $("#pagination-transfers").html("");
                } else {
                    data = '';

                    $.each(response.data, function (index, value) {
                        data += '<tr >';
                        if (value.is_owner && value.sale_id) {
                            data += `<td style="vertical-align: middle;">
                                        ${value.reason}
                                        <a class="detalhes_venda pointer" data-target="#modal_detalhes" data-toggle="modal" venda="${value.sale_id}">
                                            <span style="color:black;">#${value.sale_id}</span>
                                        </a><br>
                                        <small>(Data da venda: ${value.sale_date})</small>
                                     </td>`;
                        } else {
                            if (value.reason === 'Antecipação') {
                                data += `<td style="vertical-align: middle;">${value.reason} <span style='color: black;'> #${value.anticipation_id} </span></td>`;
                            } else {
                                data += `<td style="vertical-align: middle;">${value.reason}${value.sale_id ? '<span> #' + value.sale_id + '</span>' : ''}</td>`;
                            }
                        }
                        data += '<td style="vertical-align: middle;">' + value.date + '</td>';
                        if (value.type_enum === 1) {
                            data += `<td style="vertical-align: middle; color:green;"> ${value.value}`;
                            if (value.reason === 'Antecipação') {
                                data += `<br><small style='color:#543333;'>(Taxa: ${value.tax})</small> </td>`;
                            } else if (value.value_anticipable != '0,00') {
                                data += `<br><small style='color:#543333;'>(${value.value_anticipable} antecipados em <b>#${value.anticipation_id}</b> )</small> </td>`;
                            } else {
                                data += `</td>`;
                            }
                        } else {
                            data += `<td style="vertical-align: middle; color:red;"> ${value.value}</td> `;
                        }
                        data += '</tr>';
                    });

                    $("#table-transfers-body").html(data);

                    paginationTransfersTable(response);
                }
            }
        });

        function paginationTransfersTable(response) {
            $("#pagination-transfers").html("");
            let primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";
            $("#pagination-transfers").append(primeira_pagina);
            if (response.meta.current_page == '1') {
                $("#primeira_pagina").attr('disabled', true);
                $("#primeira_pagina").addClass('nav-btn');
                $("#primeira_pagina").addClass('active');
            }
            $('#primeira_pagina').unbind("click");
            $('#primeira_pagina').on("click", function () {
                updateTransfersTable('?page=1');
            });
            for (x = 3; x > 0; x--) {
                if (response.meta.current_page - x <= 1) {
                    continue;
                }
                $("#pagination-transfers").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");
                $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                    updateTransfersTable('?page=' + $(this).html());
                });
            }
            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                let pagina_atual = "<button id='pagina_atual' class='btn nav-btn active'>" + (response.meta.current_page) + "</button>";
                $("#pagination-transfers").append(pagina_atual);
                $("#pagina_atual").attr('disabled', true).addClass('nav-btn').addClass('active');
            }
            for (x = 1; x < 4; x++) {
                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }
                $("#pagination-transfers").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");
                $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
                    updateTransfersTable('?page=' + $(this).html());
                });
            }
            if (response.meta.last_page != '1') {
                let ultima_pagina = "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";
                $("#pagination-transfers").append(ultima_pagina);
                if (response.meta.current_page == response.meta.last_page) {
                    $("#ultima_pagina").attr('disabled', true);
                    $("#ultima_pagina").addClass('nav-btn');
                    $("#ultima_pagina").addClass('active');
                }
                $('#ultima_pagina').on("click", function () {
                    updateTransfersTable('?page=' + response.meta.last_page);
                });
            }
            $('table').addClass('table-striped');
        }
    }

    function getFilters(urlParams = false) {
        let data = {
            'company': $("#extract_company_select").val(),
            'reason': $("#reason").val(),
            'transaction': $("#transaction").val().replace('#', ''),
            'type': $("#type").val(),
            'value': $("#transaction-value").val(),
            'date_range': $("#date_range").val(),
            'date_type': $("#date_type").val(),
        };

        if (urlParams) {
            let params = "";
            for (let param in data) {
                params += '&' + param + '=' + data[param];
            }
            return encodeURI(params);
        } else {
            return data;
        }
    }

    function extractExport(fileFormat) {

        let data = getFilters();
        data['format'] = fileFormat;
        $.ajax({
            method: "POST",
            url: '/api/finances/export',
            data: data,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                $('#export-email').text(response.email);
                $('#alert-export').show()
                    .shake();
            }
        });
    }

    //atualiza a table de braspag
    $(document).on("click", "#bt_filtro_braspag", function (e) {
        e.preventDefault();
        updateBraspagData();
    });
    function updateBraspagData(link = null) {
        loadOnTable('#table-braspag-body', '#braspagTable');
        if (link == null) {
            link = '/transfers/getbraspagdata?' + 'event_status=' + $("#event_status").val() + '&date_range=' + $("#date_range_braspag").val();
        } else {
            link = '/transfers/getbraspagdata' + link + '&event_status=' + $("#event_status").val() + '&date_range=' + $("#date_range_braspag").val();
        }
        $.ajax({
            method: "GET",
            url: link,
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                $('#table-braspag-body').html('');
                let schedules = response.schedules;
                if (!isEmpty(schedules)) {
                    let data = '';
                    for (let schedule of schedules) {
                        data = `
                        <tr>
                            <td>${schedule.Event}</td>
                            <td>${schedule.EventDescription}</td>
                            <td>
                                <span class='badge ${eventStatusBraspagBadge[schedule.EventStatus]}'>${eventStatusBraspag[schedule.EventStatus]}</span>
                            </td>

                        </tr>
                        `;
                        $('#table-braspag-body').append(data);
                    }
                    $('#braspagTable').addClass('table-striped');
                } else {
                    $("#table-braspag-body").html("<tr><td colspan='11' class='text-center'>Nenhum dado encontrado</td></tr>");
                }
                paginationBraspag(response.page_count, response.page_index);
            }
        });

    }
    function checkBraspagCompany() {
        $.ajax({
            method: "GET",
            url: '/api/companies/checkbraspagcompany',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
            },
            success: response => {
                if (response.has_merchant_id && response.env == 'local') {
                    $('#nav-braspag-tab').show();
                    updateBraspagData();
                }
            }
        });
    }

    $('#date_range_braspag').daterangepicker({
        startDate: moment().subtract(1, 'years'),
        endDate: moment().add(1, 'years'),
        opens: 'center',
        maxDate: moment().add(1, 'years').endOf("day"),
        alwaysShowCalendar: true,
        showCustomRangeLabel: 'Customizado',
        autoUpdateInput: true,
        locale: {
            locale: 'pt-br',
            format: 'DD/MM/YYYY',
            applyLabel: "Aplicar",
            cancelLabel: "Limpar",
            fromLabel: 'De',
            toLabel: 'Até',
            customRangeLabel: 'Customizado',
            weekLabel: 'W',
            daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            firstDay: 0
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $("#bt_get_csv").on("click", function () {
        extractExport('csv');
    });

    $("#bt_get_xls").on("click", function () {
        extractExport('xls');
    });

    $("#nav-profile-tab").on("click", function () {
        $('#export-excel').show();
    });

    $("#nav-home-tab").on("click", function () {
        $('#export-excel').hide();
    });
    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            $("#extract_company_select option[value=" + $('#extract_company_select option:selected').val() + "]").prop("selected", true);
            updateTransfersTable();
            if ($(this).children("option:selected").attr('country') != 'brazil') {
                $("#transferred_value").show();
            } else {
                $("#transferred_value").hide();
            }
        }
    });

    function paginationBraspag(pageCount, pageIndex) {

        let paginationContainer = "#pagination-braspag";

        $(paginationContainer).html("");

        let currentPage = pageIndex;
        let lastPage = pageCount;

        if (lastPage === 1 || lastPage === 0) {
            return false;
        }

        let first_page = `<button class='btn nav-btn first_page'>1</button>`;

        $(paginationContainer).append(first_page);

        if (currentPage === 1) {
            $(paginationContainer + ' .first_page').attr('disabled', true).addClass('nav-btn').addClass('active');
        }

        $(paginationContainer + ' .first_page').on("click", function () {
            updateBraspagData('?page=1');
        });

        for (let x = 3; x > 0; x--) {

            if (currentPage - x <= 1) {
                continue;
            }

            $(paginationContainer).append(`<button class='btn nav-btn page_${(currentPage - x)}'>${(currentPage - x)}</button>`);

            $(paginationContainer + " .page_" + (currentPage - x)).on("click", function () {
                updateBraspagData('?page=' + $(this).html());
            });
        }

        if (currentPage !== 1 && currentPage !== lastPage) {
            var current_page = `<button class='btn nav-btn active current_page'>${currentPage}</button>`;

            $(paginationContainer).append(current_page);

            $(paginationContainer + " .current_page").attr('disabled', true).addClass('nav-btn').addClass('active');
        }
        for (let x = 1; x < 4; x++) {

            if (currentPage + x >= lastPage) {
                continue;
            }

            $(paginationContainer).append(`<button class='btn nav-btn page_${(currentPage + x)}'>${(currentPage + x)}</button>`);

            $(paginationContainer + " .page_" + (currentPage + x)).on("click", function () {
                updateBraspagData('?page=' + $(this).html());
            });
        }

        if (lastPage !== 1) {
            var last_page = `<button class='btn nav-btn last_page'>${lastPage}</button>`;

            $(paginationContainer).append(last_page);

            if (currentPage === lastPage) {
                $(paginationContainer + ' .last_page').attr('disabled', true).addClass('nav-btn').addClass('active');
            }

            $(paginationContainer + ' .last_page').on("click", function () {
                updateBraspagData('?page=' + lastPage);
            });
        }
    }
});
