var statusWithdrawals = {
    1: "warning",
    2: "primary",
    3: "success",
    4: "danger",
};

// loadWithdrawalsTable();

// $("#transfers_company_select").on("change", function () {
//     loadWithdrawalsTable();
// });

//NAO CHAMAR - DUPLICADO E ERRADO
function loadWithdrawalsTable(link = null, quemMeChamou = "ninguém") {
    loadOnTable("table-withdrawals-body", "transfersTable");
    $("#table-withdrawals-body").html("");
    if (link == null) {
        link = "/withdrawals";
    } else {
        link = "/withdrawals" + link;
        quemMeChamou = "pagination";
    }
    $.ajax({
        method: "GET",
        url: link,
        data: { company: $("#extract_company_select").val() },
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function (response) {
            errorAjaxResponse(response);
            $("#table-withdrawals-body").html("Erro ao encontrar dados");
        },
        success: function (response) {
            $("#withdrawals-table-data").html("");
            if (response.data == "") {
                $("#withdrawals-table-data").html(
                    "<tr><td colspan='5' class='text-center'>Nenhum saque realizado até o momento</td></tr>"
                );
                $("#withdrawals-pagination").html("");
            } else {
                let cont = 0;
                $.each(response.data, function (index, value) {
                    data = "";
                    data += "<tr>";
                    data += "<td>" + value.account_information + "</td>";
                    data += "<td>" + value.date_request + "</td>";
                    data += "<td>" + value.date_release + "</td>";
                    data += "<td>" + value.value + "</td>";
                    data += '<td class="shipping-status">';
                    data +=
                        '<span class="badge badge-' +
                        statusWithdrawals[value.status] +
                        '">' +
                        value.status_translated +
                        "</span>";
                    data += "</td>";
                    data += "</tr>";

                    $("#withdrawals-table-data").append(data);
                    cont++;
                    $("#withdrawalsTable").addClass("table-striped");
                });
                pagination(response, "withdrawals", loadWithdrawalsTable);
            }
        },
    });
}
