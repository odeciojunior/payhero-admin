$(document).ready(function () {

    atualizar();

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function () {
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
                    dados += "<td>" + value.id + "</td>";
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
                            $('#modal_venda_body').html(response);
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

    function pagination(response) {

        $("#pagination").html("");

        var primeira_pagina = "<button id='primeira_pagina' class='btn' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>1</button>";

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == '1') {
            $("#primeira_pagina").attr('disabled', true);
        }

        $('#primeira_pagina').on("click", function () {
            atualizar('?page=1');
        });

        for (x = 3; x > 0; x--) {

            if (response.meta.current_page - x <= 1) {
                continue;
            }

            $("#pagination").append("<button id='pagina_" + (response.meta.current_page - x) + "' class='btn' style='margin-right:5px;background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>" + (response.meta.current_page - x) + "</button>");

            $('#pagina_' + (response.meta.current_page - x)).on("click", function () {
                atualizar('?page=' + $(this).html());
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
                atualizar('?page=' + $(this).html());
            });

        }

        if (response.meta.last_page != '1') {
            var ultima_pagina = "<button id='ultima_pagina' class='btn' style='background-image: linear-gradient(to right, #e6774c, #f92278);border-radius: 40px;color:white'>" + response.meta.last_page + "</button>";

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
