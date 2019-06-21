$(document).ready(function () {
    atualizar(1);

    function atualizar(page) {

        $('#companies_table_data').html('');

        $.ajax({
            method: "GET",
            url: "/api/companies?page=" + page,
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (response) {
                $('#companies_table_data').html("<tr class='text-center'><td colspan='11'>Error</td></tr>");
            },
            success: function (response) {
                $.each(response.data, function (index, value) {
                    console.log(value.id_code);

                    dados = "<tr>";
                    dados += "<td>" + value.fantasy_name + "</td>";
                    dados += "<td>" + value.cnpj + "</td>";
                    dados += "<td>" + value.document_status + "</td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><a href='/companies/" + value.id_code + "/edit' class='btn btn-sm btn-outline btn-danger edit-company' data-company='" + value.id_code + "'  type='button'><i class='icon wb-pencil' aria-hidden='true'></i></a></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><a href='/companies/" + value.id_code + "/destroy' class='btn btn-sm btn-outline btn-danger delete-company' data-company='" + value.id_code + "' type='button'><i class='icon wb-trash' aria-hidden='true'></i></a></td>";
                    dados += "</tr>";

                    $("#companies_table_data").append(dados);

                });

                if (response.data == '') {
                    $('#companies_table_data').html("<tr class='text-center'><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhuma empresa encontrada</td></tr>");
                }

                //pagination(response);
            }
        });
    }

    function pagination(response) {

        $("#pagination").html("");

        var primeira_pagina = "<button id='primeira_pagina' class='btn' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>1</button>";

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == '1') {
            $("#primeira_pagina").attr('disabled', true);
        }

        $('#primeira_pagina').on("click", function () {
            atualizar('1');
        });

        for (x = 3; x > 0; x--) {

            if (response.meta.current_page - x <= 1) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>" + (response.meta.current_page - x) + "</button>");

            $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                atualizar($(this).html());
            });

        }

        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            var pagina_atual = "<button id='pagina_atual' class='btn btn-primary' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>" + (response.meta.current_page) + "</button>";

            $("#pagination").append(pagina_atual);

            $("#pagina_atual").attr('disabled', true);
        }

        for (x = 1; x < 4; x++) {

            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>" + (response.meta.current_page + x) + "</button>");

            $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
                atualizar($(this).html());
            });

        }

        if (response.meta.last_page != '1') {
            var ultima_pagina = "<button id='ultima_pagina' class='btn' style='background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>" + response.meta.last_page + "</button>";

            $("#pagination").append(ultima_pagina);

            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr('disabled', true);
            }

            $('#ultima_pagina').on("click", function () {
                atualizar(response.meta.last_page);
            });
        }

    }

});

