$(document).ready(function () {

    updateSalesByOrigin();

    $("#origin").on("change", function () {
        updateSalesByOrigin();
    });

    function updateSalesByOrigin() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;


        $('#origins-data').html("<div class='row text-center' style='height:50px'> Carregando...</div>");

        if (link == null) {
            link = '/reports/getsalesbyorigin?' + 'project=' + $("#project").val() + '&start-date=' + $("#start_date").val() + '&end-date=' + $("#end-date").val() + '&origin=' + $("#origin").val();
        } else {
            link = '/reports/getsalesbyorigin' + link + '&project=' + $("#project").val() + '&start-date=' + $("#start-date").val() + '&end-date=' + $("#end-date").val() + '&origin=' + $("#origin").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                //
            },
            success: function success(response) {
                $('#origins-data').html('');

                $.each(response.data, function (index, data) {
                    dados = '<div class="row">';
                    dados += '<div class="col-6">' + data.origin + "</td></div>";
                    dados += '<div class="col-3">' + data.sales_amount + "</td></div>";
                    dados += '<div class="col-3">' + data.balance + "</td></div>";
                    dados += '</div>';
                    $("#origins-data").append(dados);
                });
                if (response.data == '') {
                    $('#dados_tabela').html("<tr><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                }
                pagination(response);
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
            updateSalesByOrigin('?page=1');
        });

        for (x = 3; x > 0; x--) {

            if (response.meta.current_page - x <= 1) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

            $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                updateSalesByOrigin('?page=' + $(this).html());
            });
        }

        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            var pagina_atual = "<button id='pagina_atual' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

            $("#pagination").append(pagina_atual);
        }

        for (x = 1; x < 4; x++) {

            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

            $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
                updateSalesByOrigin('?page=' + $(this).html());
            });
        }

        if (response.meta.last_page != '1') {
            var ultima_pagina = "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";

            $("#pagination").append(ultima_pagina);

            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr('disabled', true);
            }

            $('#ultima_pagina').on("click", function () {
                updateSalesByOrigin('?page=' + response.meta.last_page);
            });
        }
    }
});
