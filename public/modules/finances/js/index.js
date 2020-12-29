$(document).ready(function () {

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

    $("#date_range_statement_unique").val(moment().format('YYYY-MM-DD'));

    //END - Comportamentos da tela

    //Obtém as empresas
    function getCompanies() {
        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/companies?select=true",
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
                if (isEmpty(response.data)) {
                    $('.page-content').hide();
                    $('.content-error').show();
                    loadingOnScreenRemove();
                    return;
                }

                let itsApprovedTransactGetnet = false;

                $('.page-content').show();
                $('.content-error').hide();

                $(response.data).each(function (index, value) {
                    if (value.capture_transaction_enabled) {
                        itsApprovedTransactGetnet = true;
                        let dataHtml = `<option country="${value.country}" value="${value.id}">${value.name}</option>`;
                        $("#statement_company_select").append(dataHtml);
                        $("#transfers_company_select").append(dataHtml);
                    }
                });

                if (!itsApprovedTransactGetnet) {
                    $("#companies-not-approved-getnet").show();
                    loadingOnScreenRemove();
                    return;
                }
                $(".card-show-content-finances").show();

                updateAccountStatementData();
                updateBalances();
                checkAllowed();
                loadingOnScreenRemove();
            }
        });
    }

    function paginationStatement() {
        $("#pagination-statement").jPages({
            containerID: "table-statement-body",
            perPage: 10,
            startPage: 1,
            startRange: 1,
            first: false,
            previous: '',
            next: '',
            last: false,
            delay: 1,
        });
    }

    getCompanies();

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
            url: "/api/finances/getbalances",
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
                // $(".currency").html('R$ ');
                $(".available-balance").html(isEmpty(response.available_balance));
                $(".pending-balance").html(isEmpty(response.pending_balance));
                $(".pending-antifraud-balance").html(response.pending_antifraud_balance);
                $(".total-balance").html(isEmpty(response.total_balance));
                $(".loading").remove();
                $("#div-available-money").unbind('click');
                $("#div-available-money").on("click", function () {
                    $(".withdrawal-value").val(isEmpty(response.available_balance));
                });

                updateWithdrawalsTable();
                loadOnAny('.price', true);
            }
        });

        function isEmpty(value) {
            if (value.length === 0) {
                return 0;
            } else {
                return value
            }
        }

        function verifyWithdrawalIsValid(toTransfer, availableBalance) {
            if (toTransfer < 1) {
                alertCustom('error', 'Valor do saque inválido!');
                $('#custom-input-addon').val('');
                $('.withdrawal-value').maskMoney({thousands: '.', decimal: ',', allowZero: true});
                return false;
            }

            if (toTransfer > availableBalance) {
                alertCustom('error', 'O valor requerido ultrapassa o limite disponivel');
                $('#custom-input-addon').val('');
                $('.withdrawal-value').maskMoney({thousands: '.', decimal: ',', allowZero: true});
                return false;
            }

            if ($('#custom-input-addon').val() == '') {
                alertCustom('error', 'Valor do saque inválido!');
                return false;
            }

            return true;
        }

        // Fazer saque
        $('#bt-withdrawal').unbind("click");
        $('#bt-withdrawal').on('click', function () {
            let availableBalanceText = $('.available-balance').html().replace(',', '').replace('.', '');
            let toTransferText = $('#custom-input-addon').val().replace(',', '').replace('.', '');
            let availableBalance = parseInt(availableBalanceText);
            let toTransfer = parseFloat(toTransferText);

            if (!verifyWithdrawalIsValid(toTransfer, availableBalance)) {
                return;
            }


            $.ajax(
                {
                    url: "/api/withdrawals/getWithdrawalValues",
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
                        let withdrawalSingleValue = manipulateModalWithdrawal(response.data);

                        $("#bt-confirm-withdrawal").unbind("click");
                        $("#bt-confirm-withdrawal").on("click", function () {
                            loadOnModal('#modal-body');
                            let withdrawalValue = 0;
                            if (withdrawalSingleValue) {
                                withdrawalValue = $("#modal-withdrawal-value").val();
                            } else {
                                if ($("#inputRadioFirstValue:checked").val() != undefined) {
                                    withdrawalValue = $("#first-value").val();
                                } else {
                                    withdrawalValue = $("#second-value").val();
                                }
                            }

                            $("#bt-confirm-withdrawal").attr('disabled', 'disabled');
                            $.ajax({
                                url: "/api/withdrawals",
                                type: "POST",
                                data: {
                                    company_id: $('#transfers_company_select').val(),
                                    withdrawal_value: withdrawalValue
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
                                    loadOnAny('.price', true);
                                    manipulateModalSuccessWithdrawal();

                                    $('.btn-return').on('click', function () {
                                        $('#custom-input-addon').val('');
                                    });

                                    $('.btn-return').click(function () {
                                        $('#modal_body').modal('hide');
                                    });

                                    updateBalances();
                                },
                                complete: (response) => {
                                    $("#bt-confirm-withdrawal").removeAttr('disabled');
                                }
                            });
                        });
                    }
                }
            );
        });

        function userDocumentsPendingModal() {
            let route = '/profile';
            $('#modal-withdrawal').modal('show');
            $('#modal-withdrawal-title').text("Oooppsssss!");
            $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Documentos pessoais ainda não validados</strong></h3>' + '<h4 align="center">Parece que ainda existe pendencias com seus documentos</h4>' + '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' + '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + route + '">clique aqui</a></h5>');
            $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
        }

        function documentsStatusPendingModal() {
            let company = $('#transfers_company_select').val();
            let _route = '/companies/' + company + '/edit';
            $('#modal-withdrawal').modal('show');
            $('#modal-withdrawal-title').text("Oooppsssss!");
            $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Documentos da empresa ainda não validados</strong></h3>' + '<h4 align="center">Parece que ainda existe pendencias com os documentos de sua empresa</h4>' + '<h4 align="center">Seria bom conferir se todos os documentos já foram cadastrados</h4>' + '<h5 align="center">Deseja ir ao documentos? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
            $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');

        }

        function emailNotVerified() {
            let _route = '/profile';
            $('#modal-withdrawal-title').text("Oooppsssss!");
            $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Email de usuário ainda não foi verificado</strong></h3>' + '<h4 align="center">Para maior segurança é necessário validar o e-mail do usuário na página de perfil</h4>' + '<h5 align="center">Deseja ir a pagina de perfil? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
            $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
            $('#modal-withdrawal').modal('show');
        }

        function cellphoneNotVerified() {
            let _route = '/profile';
            $('#modal-withdrawal').modal('show');
            $('#modal-withdrawal-title').text("Oooppsssss!");
            $('#modal_body').html('<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center"><strong>Telefone de usuário ainda não foi verificado</strong></h3>' + '<h4 align="center">Para maior segurança é necessário validar o telefone do usuário na página de perfil</h4>' + '<h5 align="center">Deseja ir a pagina de perfil? <a class="red pointer" href="' + _route + '">clique aqui</a></h5>');
            $('#modal-withdraw-footer').html('<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>');
        }

        function manipulateModalWithdrawal(dataWithdrawal) {
            let currentBalance = $('.available-balance').html().replace(',', '').replace('.', '');
            let withdrawal = $('#custom-input-addon').val().replace(',', '').replace('.', '');
            let singleValue = false;

            if ((dataWithdrawal.lower_value == 0 || dataWithdrawal.bigger_value == withdrawal || dataWithdrawal.lower_value == withdrawal || currentBalance == dataWithdrawal.bigger_value)) {
                singleValue = true;
            }

            $('#modal-withdrawal-title').text("Confirmar Saque");

            $('#modal_body').html(`
                <div>
                    <!--<div style="background-color: yellow; color: black; border-radius: 10px">
                        Os valores disponíveis para saque representam a totalidade de vendas compensadas até o momento.
                        Após o pagamento ser aprovado, é preciso aguardar o prazo determinado para receber os valores na conta e conseguir sacá-los.
                        Essa medida tem o objetivo de garantir a segurança de clientes e lojistas durante o andamento das operaçoẽs financeiras.
                    </div>-->
                    <div class="mt-50 mb-50">
                        <h3 class="text-center">
                            ${singleValue ? 'Opção de saque disponível:' : 'Opções de saque disponíveis:'}
                            <div class="radio-custom radio-primary mt-25" id="more-than-on-values-show" style="${singleValue ? 'display:none;' : 'display:block'}">
                                <div class="text-center">
                                    <div class="">
                                        <input hidden id="first-value" value="${dataWithdrawal.bigger_value}">
                                        <input type="radio" id="inputRadioFirstValue" name="valueWithdrawal" checked>
                                        <label for="inputRadioFirstValue">
                                            ${
                                                ((dataWithdrawal.bigger_value / 100).toLocaleString('pt-BR', {
                                                    style: 'currency',
                                                    currency: 'BRL',
                                                }))
                                            }
                                        </label>
                                    </div>
                                     <div class="mt-15">
                                        <input hidden id="second-value" value="${dataWithdrawal.lower_value}">
                                        <input type="radio" id="inputRadioSecondValue" name="valueWithdrawal" >
                                        <label for="inputRadioSecondValue">
                                            ${
                                                ((dataWithdrawal.lower_value / 100).toLocaleString('pt-BR', {
                                                    style: 'currency',
                                                    currency: 'BRL'
                                                }))
                                            }
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div id="just-value-show" class="text-center mt-25 radio-custom radio-primary " style=" ${singleValue ? 'display:block' : 'display:none;'}">
                                <input hidden id="modal-withdrawal-value" value="${dataWithdrawal.bigger_value}">
                                <input type="radio" id="inputRadioSingleValue" ${singleValue ? 'checked': ''} name="valueWithdrawal" >
                                <label for="inputRadioSingleValue">
                                    ${
                                        ((dataWithdrawal.bigger_value / 100).toLocaleString('pt-BR', {
                                            style: 'currency',
                                            currency: 'BRL'
                                        }))
                                    }
                                </label>
                            </div>
                        </h3>
                    </div>
                </div>`
            );

            $('#modal-withdraw-footer').html('<button id="bt-confirm-withdrawal" class="btn btn-success" style="background-image: linear-gradient(to right, #23E331, #44A44B);font-size:20px; width:100%">' + '<strong>Confirmar</strong></button>' + '<button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal" aria-label="Close" style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">' + '<strong>Cancelar</strong></button>');

            $('#modal-withdrawal').modal('show');
            return singleValue;
        }

        function manipulateModalSuccessWithdrawal() {
            $('#modal-withdrawal-title').text("Sucesso!");
            $('#modal_body').html(`
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
                <h3 align="center">
                    <strong>Sua solicitação foi para avaliação!</strong>
                </h3>`
            );
            $('#modal-withdraw-footer').html(`
                <div style="width:100%;text-align:center;padding-top:3%">
                    <span class="btn btn-success btn-return" data-dismiss="modal" style="font-size: 25px">
                        Retornar
                    </span>
                </div>`
            );

            $('#modal-withdrawal').modal('show');
        }

        let statusWithdrawals = {
            1: 'warning',
            2: 'primary',
            3: 'success',
            4: 'danger',
            5: 'in_review',
            8: 'primary',
            9: 'partially-liquidating',

        };

        function updateWithdrawalsTable(link = null) {
            $("#withdrawals-table-data").html("");
            $("#pagination-withdrawals").html("");
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
                        return;
                    }

                    $.each(response.data, function (index, data) {

                        let tableData = '';
                        tableData += '<tr>';
                        tableData += "<td>" + data.account_information + "</td>";
                        tableData += "<td>" + data.date_request + "</td>";
                        tableData += "<td>" + data.date_release + "</td>";
                        tableData += "<td>" + data.value + "</td>";
                        tableData += '<td class="shipping-status">';
                        tableData += '<span class="badge badge-' + statusWithdrawals[data.status] + '">' + data.status_translated + '</span>';
                        tableData += '</td>';
                        tableData += '</tr>';
                        $("#withdrawals-table-data").append(tableData);
                        $('#withdrawalsTable').addClass('table-striped')
                    });
                    pagination(response, 'withdrawals', updateWithdrawalsTable);

                }
            });
        }
    }

    //atualiza a table de extrato
    $(document).on("click", "#bt_filtro", function () {
        $("#extract_company_select option[value=" + $('#extract_company_select option:selected').val() + "]").prop("selected", true);
        updateTransfersTable();
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
                $("#pagination-transfers").html("");


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
                    return;
                }

                let data = '';

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
                    } else if (value.reason === 'Antecipação') {
                        data += `<td style="vertical-align: middle;">${value.reason} <span style='color: black;'> #${value.anticipation_id} </span></td>`;
                    } else {
                        data += `<td style="vertical-align: middle;">${value.reason}${value.sale_id ? '<span> #' + value.sale_id + '</span>' : ''}</td>`;
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
        });

        function paginationTransfersTable(response) {
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

    let statusExtract = {
        'WAITING_FOR_VALID_POST': 'pendente',
        'WAITING_LIQUIDATION': 'info',
        'WAITING_WITHDRAWAL': 'withdrawal',
        'PAID': 'success',
        'REVERSED': 'warning',
        'ADJUSTMENT_CREDIT': 'dark',
        'ADJUSTMENT_DEBIT': 'warning',
        'ERROR': 'error',
    }


    function updateAccountStatementData() {
        loadOnAny('#nav-statement #available-in-period-statement', false, balanceLoader);

        $('#table-statement-body').html('');
        $('#pagination-statement').html('');
        loadOnTable('#table-statement-body', '#statementTable');

        let link = '/api/transfers/account-statement-data?dateRange=' + $("#date_range_statement").val() + '&company=' + $("#statement_company_select").val() + '&sale=' + $("#statement_sale").val() + '&status=' + $("#statement_status_select").val() + '&statement_data_type=' + $("#statement_data_type_select").val() + '&payment_method=' + $("#payment_method").val();

        $(".numbers").hide();

        $.ajax({
            method: "GET",
            url: link,
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                loadOnAny('#nav-statement #available-in-period-statement', true);

                let error = 'Erro ao gerar o extrato';
                errorAjaxResponse(error);
                $("#table-statement-body").html("<tr><td colspan='11' class='text-center'>" + error + "</td></tr>");
            },
            success: response => {
                updateClassHTML();

                let items = response.items;
                $('#statement-money #available-in-period-statement').html('R$ 0,00');

                if (isEmpty(items)) {
                    loadOnAny('#nav-statement #available-in-period-statement', true);
                    $("#table-statement-body").html("<tr><td colspan='11' class='text-center'>Nenhum dado encontrado</td></tr>");
                    return false;
                }


                items.forEach(function (item) {
                    let dataTable = `<tr><td style="vertical-align: middle;">`;

                    if (item.order && item.order.hashId) {

                        dataTable += `Transação`;

                        if (item.isInvite) {
                            dataTable += `
                                <a class="">
                                    <span>#${item.order.hashId}</span>
                                </a>
                            `;
                        } else {
                            dataTable += `
                                 <a class="detalhes_venda pointer" data-target="#modal_detalhes" data-toggle="modal" venda="${item.order.hashId}">
                                    <span style="color:black;">#${item.order.hashId}</span>
                                </a>
                            `;
                        }
                        dataTable += `<br>
                                        <small>(${item.details.description})</small>`;
                    } else {
                        dataTable += `${item.details.description}`;
                    }

                    dataTable += `
                         </td>
                         <td>
                            <span class="badge badge-sm badge-${statusExtract[item.details.type]} p-2">${item.details.status}</span>
                         </td>
                        <td style="vertical-align: middle;">
                            ${item.date}
                        </td>
                        <td style="vertical-align: middle; color:${item.amount >= 0 ? 'green' : 'red'};">
                        ${(item.amount.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        })
                    )}
                        </td>
                        </tr>`;
                    updateClassHTML(dataTable);
                });

                let totalInPeriod = response.totalInPeriod;

                let isNegativeStatement = false;
                if (totalInPeriod < 1) {
                    isNegativeStatement = true;
                }

                $('#statement-money #available-in-period-statement').html(`
                    <span${isNegativeStatement ? ' style="color:red;"' : ''}>
                        ${(totalInPeriod.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        })
                    )}
                    </span>`
                );
                paginationStatement();

                $("#pagination-statement span").addClass('jp-hidden');
                $("#pagination-statement a").removeClass('active').addClass('btn nav-btn');
                $("#pagination-statement a.jp-current").addClass('active');
                $("#pagination-statement a").on('click', function () {
                    $("#pagination-statement a").removeClass('active');
                    $(this).addClass('active');
                });

                $("#pagination-statement").on('click', function () {
                    $("#pagination-statement span").remove();
                });
                loadOnAny('#nav-statement #statement-money  #available-in-period-statement', true);
            }
        });

    }

    function updateClassHTML(dataTable = 0) {
        if (dataTable.length > 0) {
            $('#table-statement-body').append(dataTable);
            $("#statementTable").addClass('table-striped');
        } else {
            $('#table-statement-body').html('');
        }
    }

    //atualiza a table de statement
    $(document).on("click", "#bt_filtro_statement", function (e) {
        e.preventDefault();
        updateAccountStatementData();
    });

    let rangesToDateRangeStatement = {
        'Hoje': [moment(), moment()],
        'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
        'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
        'Este mês': [moment().startOf('month'), moment().endOf('month')],
        'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    };

    let envDebug = $("meta[name=app-debug]").attr('content');

    if (envDebug == 'true') {
        rangesToDateRangeStatement['TODO O PERÍODO - TESTE'] = [moment().subtract(1, 'year'), moment().add(40, 'days')];
    }

    $('#date_range_statement').daterangepicker({
        maxSpan: {
            days: 31,
        },
        startDate: moment().subtract(7, 'days'),
        endDate: moment().add(0, 'days'),
        opens: 'center',
        maxDate: moment().add(1, 'month'),
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
        ranges: rangesToDateRangeStatement
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

    $('#statement_sale').on('change paste keyup select', function () {

        let val = $(this).val();

        if (val === '') {
            $('#date_range_statement').attr('disabled', false).removeClass('disableFields');
            $('#statement_data_type_select').attr('disabled', false).removeClass('disableFields');
        } else {

            $('#date_range_statement').attr('disabled', true).addClass('disableFields');
            $('#statement_data_type_select').attr('disabled', true).addClass('disableFields');
        }
    });
});
