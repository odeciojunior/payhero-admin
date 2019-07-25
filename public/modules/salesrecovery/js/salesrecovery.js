$(document).ready(function () {

    atualizar();

    $("#bt_filtro").on("click", function (event) {
        console.log('oi');
        event.preventDefault();
        atualizar();
    });

    function atualizar(link = null) {

        loadOnTable('#table_data', '#carrinhoAbandonado');

        /*$('#table_data').html("<tr class='text-center'><td colspan='11'> Carregando...</td></tr>");*/

        if (link == null) {
            link = '/recoverycart/getabandonatedcarts?project=' + $("#project").val() + '&type=' + $("#type_recovery option:selected").val() + '&start_date=' + $("#start_date").val() + '&end_date=' + $("#end_date").val();
        } else {
            link = '/recoverycart/getabandonatedcarts' + link + '&project=' + $("#project").val() + '&type=' + $("#type_recovery option:selected").val() + '&start_date=' + $("#start_date").val() + '&end_date=' + $("#end_date").val();
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

                $('#table_data').html('');
                $('#carrinhoAbandonado').addClass('table-striped')

                $.each(response.data, function (index, value) {

                    dados = '';
                    dados += '<tr>';
                    dados += "<td>" + value.date + "</td>";
                    dados += "<td>" + value.project + "</td>";
                    dados += "<td>" + value.client + "</td>";
                    dados += "<td>" + value.email_status + "</td>";
                    dados += "<td>" + value.sms_status + "</td>";
                    if (value.recovery_status == 'Recuperado') {
                        dados += "<td><span class='badge badge-success'>" + value.recovery_status + "</span></td>";
                    } else {
                        dados += "<td><span class='badge badge-danger'>" + value.recovery_status + "</span></td>";
                    }
                    dados += "<td>" + value.value + "</td>";
                    dados += "<td><a href='" + value.whatsapp_link + "', '', $client['telephone']); !!}' target='_blank'><img style='height:24px' src='https://logodownload.org/wp-content/uploads/2015/04/whatsapp-logo-4-1.png'></a></td>";
                    dados += "<td> <a role='button' class='copy_link' style='cursor:pointer;' link='" + value.link + "'><i class='material-icons gradient'>file_copy</i></a></td>";
                    dados += "<td><a  role='button' class='details-cart-recovery' style='cursor:pointer;' venda='" + value.id + "' data-target='#modal_detalhes' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i></button></td>";
                    dados += '</tr>';
                    $("#table_data").append(dados);

                    $(".copy_link").on("click", function () {
                        var temp = $("<input>");
                        $("body").append(temp);
                        temp.val($(this).attr('link')).select();
                        document.execCommand("copy");
                        temp.remove();
                        alertCustom('success', 'Link copiado!');
                    });

                });
                if (response.data == '') {
                    $('#table_data').html("<tr><td colspan='11' class='text-center' style='height: 70px;vertical-align: middle'> Nenhum carrinho abandonado até o momento</td></tr>");
                }
                pagination(response);

                var id_venda = '';

                $('.details-cart-recovery').unbind('click');

                $('.details-cart-recovery').on('click', function () {

                    var venda = $(this).attr('venda');

                    $('#modal-title').html('Detalhes Carrinho Abandonado' + '<br><hr>');

                    /*$('.modal-body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");*/

                    var data = {sale_id: venda};

                    $.ajax({
                        method: "POST",
                        url: '/recoverycart/details',
                        data: {checkout: venda},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        },
                        success: function (response) {
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
            var pagina_atual = "<button id='pagina_atual' class='btn nav-btn active'>" + (response.meta.current_page) + "</button>";

            $("#pagination").append(pagina_atual);

            $("#pagina_atual").attr('disabled', true);
            $("#pagina_atual").addClass('nav-btn');
            $("#pagina_atual").addClass('active');
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
                $("#ultima_pagina").addClass('nav-btn');
                $("#ultima_pagina").addClass('active');
            }

            $('#ultima_pagina').on("click", function () {
                atualizar('?page=' + response.meta.last_page);
            });
        }

    }

});
