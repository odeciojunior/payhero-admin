window.loadStatementTable = function() {
    if(window.gatewayCode == 'w7YL9jZD6gp4qmv') {
        updateAccountStatementData();
    }
    else {
        updateTransfersTable();
    }
}

window.updateTransfersTable = function(link = null) {
    $("#table-transfers-body").html('');
    // let balanceLoader = {
    //     styles: {
    //         container: {
    //             minHeight: '31px',
    //             justifyContent: 'flex-start',
    //         },
    //         loader: {
    //             width: '20px',
    //             height: '20px',
    //             borderWidth: '4px'
    //         },
    //     },
    //     insertBefore: '.grad-border',
    // };
    // loadOnAny('#available-in-period', false, balanceLoader);

    loadOnTable('#table-transfers-body', '#transfersTable');
    if (link == null) {
        link = '/api/transfers';
    } else {
        link = '/api/transfers' + link;
    }

    let data = {
        company_id: $("#extract_company_select option:selected").val(),
        gateway_id: window.gatewayCode,
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
            availableInPeriod.html(`<span ${isNegative ? ' style="color:red;"' : ''}><span class="currency">R$ </span>${balance_in_period}</span>`);
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

            // loadOnAny('#available-in-period', true);

            if (response.data == '') {
                $("#table-transfers-body").html(
                    "<tr class='text-center'><td colspan='11' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                        $("#table-transfers-body").attr("img-empty") +
                        "'>Nenhum dado encontrado</td></tr>"
                );
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

window.updateAccountStatementData = function() {
    // loadOnAnyEllipsis(
    //     "#nav-statement #available-in-period-statement",
    //     // false,
    //     // balanceLoader
    // );

    // $("#table-statement-body").html("");
    // $("#pagination-statement").html("");
    // loadOnTable("#table-statement-body", "#statementTable");

    // let link =
    //     "/api/transfers/account-statement-data?dateRange=" +
    //     $("#date_range_statement").val() +
    //     "&company=" +
    //     $("#statement_company_select").val() +
    //     "&sale=" + encodeURIComponent(
    //     $("#statement_sale").val()) +
    //     "&status=" +
    //     $("#statement_status_select").val() +
    //     "&statement_data_type=" +
    //     $("#statement_data_type_select").val() +
    //     "&payment_method=" +
    //     $("#payment_method").val() +
    //     "&withdrawal_id=" +
    //     $("#withdrawal_id").val();

    // $(".numbers").hide();

    // $.ajax({
    //     method: "GET",
    //     url: link,
    //     dataType: "json",
    //     headers: {
    //         Authorization: $('meta[name="access-token"]').attr("content"),
    //         Accept: "application/json",
    //     },
    //     error: (response) => {
    //         loadOnAnyEllipsis(
    //             "#nav-statement #available-in-period-statement",
    //             true
    //         );

    //         let error = "Erro ao gerar o extrato";
    //         $("#export-excel").css("opacity", 0);
    //         $("#table-statement-body").html(
    //             "<tr style='border-radius: 16px;'><td style='padding:  10px !important' style='' colspan='11' class='text-center'>" +
    //                 error +
    //                 "</td></tr>"
    //         );
    //         errorAjaxResponse(error);
    //     },
    //     success: (response) => {
    //         updateClassHTML();

    //         let items = response.items;
    //         $("#statement-money #available-in-period-statement").html(
    //             "R$ 0,00"
    //         );

    //         if (isEmpty(items)) {
    //             loadOnAnyEllipsis(
    //                 "#nav-statement #available-in-period-statement",
    //                 true
    //             );
    //             $("#export-excel").css("opacity", 0);
    //             $("#table-statement-body").html(
    //                 "<tr class='text-center'><td colspan='11' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
    //                     $("#table-statement-body").attr("img-empty") +
    //                     "'>Nenhum dado encontrado</td></tr>"
    //             );
    //             return false;
    //         }

    //         items.forEach(function (item) {
    //             let dataTable = `<tr class="s-table table-finance-schedule"><td style="vertical-align: middle; grid-area: sale;">`;

    //             if (item.order && item.order.hashId) {
    //                 dataTable += `Transação`;

    //                 if (item.isInvite) {
    //                     dataTable += `
    //                         <a>
    //                             <span class="bold">#${item.order.hashId}</span>
    //                         </a>
    //                     `;
    //                 } else {
    //                     dataTable += `
    //                          <a class="detalhes_venda disabled pointer-md" data-target="#modal_detalhes" data-toggle="modal" venda="${item.order.hashId}">
    //                             <span class="bold">#${item.order.hashId}</span>
    //                         </a>
    //                     `;
    //                 }
    //                 dataTable += `<br>
    //                                 <small>${item.details.description}</small>`;
    //             } else {
    //                 dataTable += `${item.details.description}`;
    //             }

    //             dataTable += `
    //                  </td>
    //                 <td style="vertical-align: middle; grid-area: date">
    //                     ${item.date}
    //                 </td>
    //                  <td style="grid-area: status" class="text-center">
    //                     <span data-toggle="tooltip" data-placement="left" title="${
    //                         item.details.status
    //                     }" class="badge badge-sm badge-${
    //                 statusExtract[item.details.type]
    //             } p-2">${item.details.status}</span>
    //                  </td>
    //                 <td class="text-xs-right text-md-left bold" style="vertical-align: middle;grid-area: value;};">
    //                 ${item.amount.toLocaleString("pt-BR", {
    //                     style: "currency",
    //                     currency: "BRL",
    //                 })}
    //                 </td>
    //                 </tr>`;

    //             $(function () {
    //                 $('[data-toggle="tooltip"]').tooltip();
    //             });

    //             updateClassHTML(dataTable);
    //         });

    //         let totalInPeriod = response.totalInPeriod ?? "0,00";

    //         let isNegativeStatement = false;
    //         if (totalInPeriod < 1) {
    //             isNegativeStatement = true;
    //         }

    //         let aux = totalInPeriod.toLocaleString("pt-BR", {
    //             style: "currency",
    //             currency: "BRL",
    //         });

    //         $("#statement-money #available-in-period-statement").html(`
    //             <span${isNegativeStatement ? ' style="color:red;"' : ""}>
    //                <small class="font-size-12">R$ </small> ${totalInPeriod.toLocaleString(
    //                    "pt-BR"
    //                )}
    //             </span>`);
    //         paginationStatement();

    //         $("#export-excel").css("opacity", 1);
    //         $("#pagination-statement span").addClass("jp-hidden");
    //         $("#pagination-statement a")
    //             .removeClass("active")
    //             .addClass("btn nav-btn");
    //         $("#pagination-statement a.jp-current").addClass("active");
    //         $("#pagination-statement a").on("click", function () {
    //             $("#pagination-statement a").removeClass("active");
    //             $(this).addClass("active");
    //         });

    //         $("#pagination-statement").on("click", function () {
    //             $("#pagination-statement span").remove();
    //         });

    //         loadOnAnyEllipsis(
    //             "#nav-statement #statement-money  #available-in-period-statement",
    //             true
    //         );
    //     },
    // });
}

$(window).on("load", function() {

    //atualiza a table de extrato
    $(document).on("click", "#bt_filtro", function () {
        $("#extract_company_select option[value=" + $('#extract_company_select option:selected').val() + "]").prop("selected", true);

        $("#transferred_value").hide();
    });

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

    function extractExport(fileFormat, email) {

        let data = getFilters();
        data['format'] = fileFormat;
        data['email'] = email;
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

                setTimeout(function () {
                    $("#bt_get_csv").prop("disabled", false);
                    $("#bt_get_xls").prop("disabled", false);
                }, 6000)
            }
        });
    }

    let exportFinanceFormat = 'xls'
    $("#bt_get_csv").on("click", function () {
    //  $(this).prop("disabled", true);
    // $("#bt_get_xls").prop("disabled", true);
        $('#modal-export-old-finance-getnet').modal('show');
        exportFinanceFormat = 'csv'
    });

    $("#bt_get_xls").on("click", function () {
    //   $(this).prop("disabled", true);
    // $("#bt_get_csv").prop("disabled", true);
        $('#modal-export-old-finance-getnet').modal('show');
    });

    $(".btn-confirm-export-old-finance-getnet").on("click", function () {
        var regexEmail = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
        var email = $('#email_finance_export').val();

        if( email == '' || !regexEmail.test(email) ) {
            alertCustom('error', 'Preencha o e-mail corretamente');
            return false;
        } else {
            extractExport('xls', email);
            $('#modal-export-old-finance-getnet').modal('hide');
        }
    });

    $(".nav-link-finances-show-export").on("click", function () {
        $("#export-excel").removeClass('d-none');
    });

    $(".nav-link-finances-hide-export").on("click", function () {
        $("#export-excel").addClass('d-none');
    });

    $("#nav-transfers-tab").on("click", function () {
        $('#export-excel').hide();
    });

    $(document).on('keypress', function (e) {
        if (e.keyCode == 13) {
            $("#extract_company_select option[value=" + $('#extract_company_select option:selected').val() + "]").prop("selected", true);
            updateTransfersTable();
        }
    });

    $(".btn-light-1").on('click', function () {
        var collapse = $("#icon-filtro");
        var text = $("#text-filtro");

        text.fadeOut(10);
        if (
            collapse.css("transform") == "matrix(1, 0, 0, 1, 0, 0)" ||
            collapse.css("transform") == "none"
        ) {
            collapse.css("transform", "rotate(180deg)");
            text.text("Minimizar filtros").fadeIn();
        } else {
            collapse.css("transform", "rotate(0deg)");
            text.text("Filtros avançados").fadeIn();
        }

        var collapse = $("#icon-custom-filtro");
        var text = $("#text-custom-filtro");

        text.fadeOut(10);
        if (
            collapse.css("transform") == "matrix(1, 0, 0, 1, 0, 0)" ||
            collapse.css("transform") == "none"
        ) {
            collapse.css("transform", "rotate(180deg)");
            text.text("Minimizar filtros").fadeIn();
        } else {
            collapse.css("transform", "rotate(0deg)");
            text.text("Filtros avançados").fadeIn();
        }

    });
    //abaixo função para apagar numero zerado no botão de valor na aba extrato
    document.getElementById("transaction-value").addEventListener("focusout", inputOutOfFocus);
    function inputOutOfFocus() {
        if($('#transaction-value').val()=='0,00'){
            document.getElementById("transaction-value").value = null;
        }
    }
});
