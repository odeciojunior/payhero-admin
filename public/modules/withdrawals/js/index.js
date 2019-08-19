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
                    $('table').addClass('table-striped')
                });
                pagination(response);
            }

        }
    });
}

function pagination(response) {

    $("#withdrawals-pagination").html("");

    if (response.meta.last_page == '1') {
        return false;
    }

    var primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";

    $("#withdrawals-pagination").append(primeira_pagina);

    if (response.meta.current_page == '1') {
        $("#primeira_pagina").addClass('nav-btn');
        $("#primeira_pagina").addClass('active');
    }

    $('#primeira_pagina').on("click", function () {
        updateWithdrawalsTable('?page=1');
    });

    for (x = 3; x > 0; x--) {

        if (response.meta.current_page - x <= 1) {
            continue;
        }

        $("#withdrawals-pagination").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

        $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
            updateWithdrawalsTable('?page=' + $(this).html());
        });
    }

    if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
        var pagina_atual = "<button id='pagina_atual' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

        $("#withdrawals-pagination").append(pagina_atual);
    }

    for (x = 1; x < 4; x++) {

        if (response.meta.current_page + x >= response.meta.last_page) {
            continue;
        }

        $("#withdrawals-pagination").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

        $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
            updateWithdrawalsTable('?page=' + $(this).html());
        });
    }

    if (response.meta.last_page != '1') {
        var ultima_pagina = "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";

        $("#withdrawals-pagination").append(ultima_pagina);

        if (response.meta.current_page == response.meta.last_page) {
            $("#ultima_pagina").attr('disabled', true);
            $("#ultima_pagina").addClass('nav-btn');
            $("#ultima_pagina").addClass('active');
        }

        $('#ultima_pagina').on("click", function () {
            updateWithdrawalsTable('?page=' + response.meta.last_page);
        });
    }
}
