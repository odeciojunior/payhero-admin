$(function () {

    updateTransfersTable();

    $("#extract_company_select").on("change", function(){

        updateTransfersTable();
    });

    function updateTransfersTable(link = null) {

        $("#table-transfers-body").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");

        if (link == null) {
            link = '/transfers';
        } else {
            link = '/transfers' + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            data: { company: $("#extract_company_select").val() },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#table-transfers-body").html('Erro ao encontrar dados');
            },
            success: function (response) {
                $("#table-transfers-body").html('');

                if(response.data == ''){

                    $("#table-transfers-body").html("<tr><td colspan='3' class='text-center'>Nenhuma movimentação até o momento</td></tr>");
                    $("#pagination").html("");
                }
                else{
                    data = '';

                    $.each(response.data, function (index, value) {
                        data += '<tr>';
                        data += '<td style="vertical-align: middle;">' + value.description + '</td>';
                        data += '<td style="vertical-align: middle;">' + value.date + '</td>';
                        data += '<td style="vertical-align: middle;">' + value.value + '</td>';
                    });

                    $("#table-transfers-body").html(data);

                    pagination(response);
                }
            }
        });
    }

    function pagination(response) {

        $("#pagination").html("");

        var primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == '1') {
            $("#primeira_pagina").addClass('nav-btn');
            $("#primeira_pagina").addClass('active');
        }

        $('#primeira_pagina').on("click", function () {
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
