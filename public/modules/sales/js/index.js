$(document).ready(function () {

    atualizar();

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").hide(700);
        } else {
            $("#div_filtros").show(700);
        }
    });

    $("#bt_filtro").on("click", function () {
        atualizar();
    });

    function atualizar(link = null) {

        $('#dados_tabela').html("<tr class='text-center'><td colspan='11'> Carregando...</td></tr>");

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
                    dados += "<td class='text-center' style='vertical-align: middle'>" + value.id + "</td>";
                    dados += "<td style='vertical-align: middle' class='text-center'>" + value.project + "</td>";
                    dados += "<td style='vertical-align: middle' class='text-center'>" + value.product + "</td>";
                    dados += "<td style='vertical-align: middle' class='text-center'>" + value.client + "</td>";

                    if (value.method == '2') {
                        dados += "<td style='vertical-align: middle' class='text-center'><img src='/modules/global/assets/img/boleto.jpeg' style='width: 60px'></td>";
                    } else {
                        if (value.brand == 'mastercard') {
                            dados += "<td style='vertical-align: middle' class='text-center'><img src='/modules/global/assets/img/master.1.svg' style='width: 60px'></td>";
                        } else if (value.brand == 'visa') {
                            dados += "<td style='vertical-align: middle' class='text-center'><img src='/modules/global/assets/img/visa.svg' style='width: 60px'></td>";
                        } else {
                            dados += "<td style='vertical-align: middle' class='text-center'><img src='/modules/global/assets/img/cartao.jpg' style='width: 60px'></td>";
                        }
                    }

                    if (value.status == 'CO' || value.status == 'paid') {
                        dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-success'>Aprovada</span></td>";
                    } else if (value.status == 'CA' || value.status == 'refused') {
                        dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-danger'>Recusada</span></td>";
                    } else if (value.status == 'chargedback' || value.status == 'refunded') {
                        dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-secondary'>Estornada</span></td>";
                    } else if (value.status == 'PE' || value.status == 'waiting_payment') {
                        dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-primary'>Pendente</span></td>";
                    } else {
                        dados += "<td style='vertical-align: middle' class='text-center'><span class='badge badge-primary'>" + value.status + "</span></td>";
                    }

                    dados += "<td style='vertical-align: middle' class='text-center'>" + value.start_date + "</td>";
                    dados += "<td style='vertical-align: middle' class='text-center'>" + value.end_date + "</td>";
                    dados += "<td style='vertical-align: middle;white-space: nowrap' class='text-center'><b>" + value.total_paid + "</b></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger detalhes_venda' venda='" + value.id + "' data-target='#modal_detalhes' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    dados += '</tr>';
                    $("#dados_tabela").append(dados);

                });
                if (response.data == '') {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                }
                pagination(response);

                var id_venda = '';

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
