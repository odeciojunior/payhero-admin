$(document).ready(function () {

    atualizar();

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        atualizar();
    });

    function atualizar(link = null) {

        $('#dados_tabela').html("<tr><td colspan='11'> Carregando...</td></tr>");

        if (link == null) {
            link = '/sales/getsales?' + 'projeto=' + $("#projeto").val() + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        } else {
            link = '/sales/getsales' + link + '&projeto=' + $("#projeto").val() + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                //
            },
            success: function (response) {
                $('#dados_tabela').html('');

                $.each(response.data, function (index, value) {
                    dados = '';
                    dados += '<tr>';
                    dados += "<td>" + value.sale_code + "</td>";
                    dados += "<td>" + value.project + "</td>";
                    dados += "<td>" + value.product + "</td>";
                    dados += "<td>" + value.client + "</td>";

                    if (value.method == '2') {
                        dados += "<td><img src='/modules/global/assets/img/cartoes/boleto.png' style='width: 60px'></td>";
                    } else {
                        if (value.brand == 'mastercard') {
                            dados += "<td><img src='/modules/global/assets/img/cartoes/master.png' style='width: 60px'></td>";
                        } else if (value.brand == 'visa') {
                            dados += "<td><img src='/modules/global/assets/img/cartoes/visa.png' style='width: 60px'></td>";
                        } else if (value.brand == 'hipercard') {
                            dados += "<td><img src='/modules/global/assets/img/cartoes/hiper.png' style='width: 60px'></td>";
                        } else if (value.brand == 'amex') {
                            dados += "<td><img src='/modules/global/assets/img/cartoes/amex.png' style='width: 60px'></td>";
                        } else if (value.brand == 'diners') {
                            dados += "<td><img src='/modules/global/assets/img/cartoes/diners.png' style='width: 60px'></td>";
                        } else if (value.brand == 'elo') {
                            dados += "<td><img src='/modules/global/assets/img/cartoes/elo.png' style='width: 60px'></td>";
                        } else {
                            dados += "<td><img src='/modules/global/assets/img/cartoes/generico.png' style='width: 60px'></td>";
                        }
                    }
 
                    if (value.status == '1') {
                        dados += "<td><span class='badge badge-success'>Aprovada</span></td>";
                    } else if (value.status == '3') {
                        dados += "<td><span class='badge badge-danger'>Recusada</span></td>";
                    } else if (value.status == '4') {
                        dados += "<td><span class='badge badge-secondary'>Estornada</span></td>";
                    } else if (value.status == '2') {
                        dados += "<td><span class='badge badge-pendente'>Pendente</span></td>";
                    } else {
                        dados += "<td><span class='badge badge-primary'>" + value.status + "</span></td>";
                    }

                    dados += "<td>" + value.start_date + "</td>";
                    dados += "<td>" + value.end_date + "</td>";
                    dados += "<td style='white-space: nowrap'><b>" + value.total_paid + "</b></td>";
                    dados += "<td><a role='button' class='detalhes_venda pointer' venda='" + value.id + "' data-target='#modal_detalhes' data-toggle='modal' style='margin-right:10px'><i class='material-icons gradient'>remove_red_eye</i></button></a></td>";
                    dados += '</tr>';
                    $("#dados_tabela").append(dados);

                });
                if (response.data == '') {
                    $('#dados_tabela').html("<tr><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                }
                pagination(response);

                $('.detalhes_venda').unbind('click');

                $('.detalhes_venda').on('click', function () {
                    var venda = $(this).attr('venda');

                    $('#modal_venda_titulo').html('Detalhes da venda ' + venda + '<br><hr>');

                    $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                    var data = {sale_id: venda};

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

                            $('.modal-body').html(response);

                        }
                    });
                });

                $('.estornar_venda').unbind('click');

                $('.estornar_venda').on('click', function () {

                    id_venda = $(this).attr('venda');

                    $('#modal_estornar_titulo').html('Estornar venda #' + id_venda + ' ?');
                    $('#modal_estornar_body').html('');

                });

            }
        });
    }

    $("#bt_get_csv").on("click", function () {
        $('<input>').attr({
            id: 'export-sales',
            type: 'hidden',
            name: 'type',
            value: 'csv'
        }).appendTo('form');

        $('#filter_form').submit();
        $('export-sales').remove();
    });

    $("#bt_get_xls").on("click", function () {
        $('<input>').attr({
            id: 'export-sales',
            type: 'hidden',
            name: 'type',
            value: 'xls'
        }).appendTo('form');

        $('#filter_form').submit();
        $('export-sales').remove();
    });

    function downloadFile(data, fileName, type = "text/plain") {
        // Create an invisible A element
        const a = document.createElement("a");
        a.style.display = "none";
        document.body.appendChild(a);

        // Set the HREF to a Blob representation of the data to be downloaded
        a.href = window.URL.createObjectURL(
            new Blob([data], {type})
        );

        // Use download attribute to set set desired file name
        a.setAttribute("download", fileName);

        // Trigger the download by simulating click
        a.click();

        // Cleanup
        window.URL.revokeObjectURL(a.href);
        document.body.removeChild(a);
    }

    function csvSalesExport(link = null) {

        if (link == null) {
            link = '/sales/getcsvsales?' + 'projeto=' + $("#projeto").val() + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        } else {
            link = '/sales/getcsvsales' + link + '&projeto=' + $("#projeto").val() + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (response) {
                console.log(response);
            },
            success: function (response) {
                downloadFile(response, 'export.xlsx')
            }
        });
    }

    function pagination(response) {

        $("#pagination").html("");

        var primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == '1') {
            $("#primeira_pagina").attr('disabled', true);
            $("#primeira_pagina").addClass('nav-btn');
            $("#primeira_pagina").addClass('active');
        }

        $('#primeira_pagina').on("click", function () {
            atualizar('?page=1');
        });

        for (x = 3; x > 0; x--) {

            if (response.meta.current_page - x <= 1) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

            $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                atualizar('?page=' + $(this).html());
            });

        }

        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            var pagina_atual = "<button id='pagina_atual' disabled class='btn nav-btn active'>" + (response.meta.current_page) + "</button>";

            $("#pagination").append(pagina_atual);
        }

        for (x = 1; x < 4; x++) {

            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

            $('#pagina_' + (response.meta.current_page + x)).on("click", function () {
                atualizar('?page=' + $(this).html());
            });

        }

        if (response.meta.last_page != '1') {
            var ultima_pagina = "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";

            $("#pagination").append(ultima_pagina);

            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr('disabled', true);
            }

            $('#ultima_pagina').on("click", function () {
                atualizar('?page=' + response.meta.last_page);
            });
        }

    }

});
