updateWithdrawalsTable();

$("#extract_company_select").on("change", function () {

    updateWithdrawalsTable();
});

function updateWithdrawalsTable(link = null) {

    loadOnTable('table-withdrawals-body', 'transfersTable');
    $("#table-withdrawals-body").html('');

    if (link == null) {
        link = '/withdrawals';
    } else {
        link = '/withdrawals' + link;
    }

    $.ajax({
        method: "GET",
        url: link,
        data: {company: $("#extract_company_select").val()},
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        error: function () {
            $("#table-withdrawals-body").html('Erro ao encontrar dados');
        },
        success: function (response) {
            $("#withdrawals-table-data").html('');
            if (response.data == '') {
                $("#withdrawals-table-data").html("<tr><td colspan='5' class='text-center'>Nenhum saque realizado até o momento</td></tr>");
                $("#withdrawals-pagination").html("");
            } else {
                let cont = 0;
                $.each(response.data, function (index, value) {
                    data = '';
                    data += '<tr>';
                    data += "<td>" + value.account_information + "</td>";
                    data += "<td>" + value.date_request + "</td>";
                    data += "<td>" + value.date_release + "</td>";
                    data += "<td>" + value.value + "</td>";
                    data += '<td class="shipping-status">';
                    if (value.status == 1) {
                        data += '<span class="badge badge-warning">Pendente</span>';
                    } else if (value.status == 2) {
                        data += '<span class="badge badge-primary">Aprovado</span>';
                    } else if (value.status == 3) {
                        data += '<span class="badge badge-success">Transferido</span>';
                    } else {
                        data += '<span class="badge badge-danger">Recusado</span>';

                    }
                    data += '</td>';
                    data += '</tr>';

                    $("#withdrawals-table-data").append(data);
                    cont++;
                    $('#withdrawalsTable').addClass('table-striped')
                });
                pagination(response, 'withdrawals', updateWithdrawalsTable);
            }

        }
    });
}
