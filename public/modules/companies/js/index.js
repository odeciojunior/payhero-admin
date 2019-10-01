$(document).ready(function () {

    atualizar(1);

    function atualizar(page) {

        $('#companies_table_data').html('');

        $.ajax({
            method: "GET",
            url: "/api/companies?page=" + page,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                console.log('GET /api/companies: success');
                $.each(response.data, function (index, value) {
                    dados = "<tr>";
                    dados += "<td>" + value.fantasy_name + "</td>";
                    dados += "<td>" + value.company_document + "</td>";
                    // dados += "<td>" + value.document_status + "</td>";
                    dados += '<td>';
                    if (value.document_status == 'Aprovado') {
                        dados += '<span class="badge badge-success">' + value.document_status + '</span>';
                    } else {
                        dados += '<span class="badge badge-primary">' + value.document_status + '</span>';

                    }
                    dados += '</td>';
                    dados += "<td><a href='/companies/" + value.id_code + "/edit' class='edit-company' data-company='" + value.id_code + "'  role='button'><i class='material-icons gradient'>  edit </i></a></td>";
                    dados += "<td><a class='pointer delete-company' company='" + value.id_code + "' data-toggle='modal' data-target='#modal-delete' role='button'><i class='material-icons gradient'>delete</i></a></td>";
                    dados += "</tr>";

                    $("#companies_table_data").append(dados);
                });

                if (response.data == '') {
                    $('#companies_table_data').html("<tr class='text-center'><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhuma empresa encontrada</td></tr>");
                }

                $(".delete-company").unbind('click');
                $(".delete-company").on("click", function (event) {
                    event.preventDefault();
                    var company = $(this).attr('company');
                    console.log('carai');

                    $("#bt-delete").unbind('click');
                    $("#bt-delete").on('click', function () {
                        $("#close-modal-delete").click();

                        $.ajax({
                            method: "DELETE",
                            url: "/api/companies/" + company,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function (response) {
                                console.log('deu ruim');
                                console.log(response)
                                errorAjaxResponse(response);
                            },
                            success: function success(data) {
                                console.log('DELETE /api/companies: success');
                                alertCustom("success", data.message);
                                atualizar(page);
                            }

                        });
                    });
                });
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
            var pagina_atual = "<button id='pagina_atual' class='btn btn-primary' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>" + response.meta.current_page + "</button>";
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
