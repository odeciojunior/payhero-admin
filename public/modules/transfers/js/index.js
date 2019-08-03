$(function () {

    updateTransfersTable();

    $("#extract_company_select").on("change", function () {

        updateTransfersTable();
    });

    function updateTransfersTable(link = null) {

        /*$("#table-transfers-body").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");*/
        loadOnTable('table-transfers-body', 'transfersTable');

        if (link == null) {
            link = '/transfers';
        } else {
            link = '/transfers' + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            data: {company: $("#extract_company_select").val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#table-transfers-body").html('Erro ao encontrar dados');
            },
            success: function (response) {
                $("#table-transfers-body").html('');

                if (response.data == '') {

                    $("#table-transfers-body").html("<tr><td colspan='3' class='text-center'>Nenhuma movimentação até o momento</td></tr>");
                    $("#pagination").html("");
                } else {
                    data = '';

                    $.each(response.data, function (index, value) {
                        data += '<tr >';
                        data += '<td style="vertical-align: middle;">' + value.description + '<a style="cursor:pointer;" class="detalhes_venda pointer" data-target="#modal_detalhes" data-toggle="modal" sale="' + value.sale_id + '">' +
                            '<span style="color:black;">'
                            + value.transaction_id +
                            '</span>'
                        '</a>'
                        '</td>';
                        data += '<td style="vertical-align: middle;">' + value.date + '</td>';
                        if(value.type_enum == 1)
                        {
                            data += '<td style="vertical-align: middle; color:green;">' + value.value + '</td>';
                        }
                        else {
                            data += '<td style="vertical-align: middle; color:red;">' + value.value + '</td>';
                        }
                        data += '</tr>';
                    });

                    $("#table-transfers-body").html(data);

                    paginationTransfersTable(response);
                }
                $('.detalhes_venda').on('click', function () {
                    var sale = $(this).attr('sale');

                    $('#modal_venda_titulo').html('Detalhes da venda ' + sale + '<br><hr>');
                    var data = {sale_id: sale};

                    $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    $.ajax({
                        method: "POST",
                        url: '/sales/venda/detalhe',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        },
                        success: function (response) {
                            $('.subTotal').mask('#.###,#0', {reverse: true});

                            $('.modal-body-details').html(response);

                        }
                    });
                });
            }
        });
    }

    function paginationTransfersTable(response) {

        $("#pagination").html("");

        alert(response.toSource());

        var primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == '1') {
            $("#primeira_pagina").addClass('nav-btn');
            $("#primeira_pagina").addClass('active');
        }

        $('#primeira_pagina').unbind("click");
        $('#primeira_pagina').on("click", function () {
            alert('hey');
            updateTransfersTable('?page=1');
        });

        for (x = 3; x > 0; x--) {

            if (response.meta.current_page - x <= 1) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

            $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                updateTransfersTable('?page=' + $(this).html());
            });

        }

        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            var pagina_atual = "<button id='pagina_atual' class='btn nav-btn active'>" + (response.meta.current_page) + "</button>";

            $("#pagination").append(pagina_atual);
        }

        for (x = 1; x < 4; x++) {

            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

            $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
                updateTransfersTable('?page=' + $(this).html());
            });

        }

        if (response.meta.last_page != '1') {
            var ultima_pagina = "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";

            $("#pagination").append(ultima_pagina);

            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr('disabled', true);
                $("#ultima_pagina").addClass('nav-btn');
                $("#ultima_pagina").addClass('active');
            }

            $('#ultima_pagina').on("click", function () {
                updateTransfersTable('?page=' + response.meta.last_page);
            });
        }

    }

});


