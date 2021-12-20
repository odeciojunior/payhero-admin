window.loadWithdrawalsTable = function(link = null) {

    let statusWithdrawals = {
        1: 'warning',
        2: 'primary',
        3: 'success',
        4: 'danger',
        5: 'primary',
        6: 'primary',
        7: 'danger',
        8: "primary",
        9: "partially-liquidating",
    };

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
        data: {
            company_id: $("#transfers_company_select option:selected").val(),
            gateway_id: window.gatewayCode
        },
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
                const emptyImage = $("#withdrawals-table-data").attr("img-empty");
                $("#withdrawals-table-data").html(
                `<tr style='border-radius: 16px;'>
                            <td colspan='6' class='text-center' style='vertical-align: middle;height:257px;'>
                                <div class="row justify-content-center align-items-center h-p100 font-size-16">
                                    <img style="width: 124px;" src='${emptyImage}' alt="">Nenhum saque realizado até o momento
                                </div>
                            </td>
                        </tr>`
                );
                $("#withdrawals-pagination").html("");
            } else {
                $.each(response.data, function (index, data) {

                    console.log(data.date_request_time)
                    let tableData = '';
                    let dateRequest = data.date_release_time !== '' ? `ás ${data.date_release_time.replace(':', 'h')}` : ''
                    tableData += `<tr>;
                                <td class="text-center">#${data.id}</td>";
                                <td class="text-left" style="grid-area: sale">
                                    <div style="color: #636363;"> ${data.account_information_bank} </div>
                                    <small style="color: #9E9E9E; font-size: 11px"> ${data.account_information} </small>
                                </td>;
                                <td class="text-left" style="grid-area: date-start">
                                    <div class="bold-mobile"> ${data.date_request} </div>
                                    <small style="color: #9E9E9E; font-size: 11px"> ás ${data.date_request_time.replace(':', 'h')}  </small>
                                </td>;
                                <td class="text-left" style="grid-area: date-start">
                                    <div class="bold-mobile"> ${data.date_release} </div>
                                    <small style="color: #9E9E9E; font-size: 11px"> ${dateRequest} </small>
                                </td>;
                                <td class="shipping-status text-center"><span class="badge badge-${statusWithdrawals[data.status]} "> ${data.status_translated} </span></td>`;
                    if (data.tax_value > 0) {
                        tableData += ' <td class="text-left" style="grid-area: value"> <strong class="font-md-size-20">' + data.value + '</strong><br><small>(taxa de R$10,00)</small>';
                    } else {
                        tableData +=' <td colspan="2" class="text-left" style="grid-area: value"> <strong class="font-md-size-20">' + data.value + "</strong>";
                    }

                    if (window.gatewayCode == 'w7YL9jZD6gp4qmv' && data.debt_pending_value != null && data.debt_pending_value != "R$ 0,00") {
                        tableData += `<br> <a role='button' class='pending_debit_withdrawal_id pointer' withdrawal_id='${data.id}'><small class="gray" style="color: #F41C1C;">- ${data.debt_pending_value}</small></a>`;
                    }
                    tableData += '</td>';
                    if(window.gatewayCode == 'w7YL9jZD6gp4qmv') {
                        tableData += `</td><td class="d-none d-lg-block"><a role='button' class='details_transaction pointer' withdrawal='${data.id}'><span class='o-eye-1'></span></a></td></tr>`;
                    }
                    tableData += '</tr>';
                    $("#withdrawals-table-data").append(tableData);
                    $('#withdrawalsTable').addClass('table-striped')
                });
                pagination(response, 'withdrawals', loadWithdrawalsTable);
            }

        }
    });
}

