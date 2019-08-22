var statusRecovery = {
    'Recuperado': 'success',
    'Não recuperado': 'danger',
}

$(document).ready(function () {

    atualizar();

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        atualizar();
    });

    function atualizar() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#table_data', '#carrinhoAbandonado');

        /*$('#table_data').html("<tr class='text-center'><td colspan='11'> Carregando...</td></tr>");*/

        if (link == null) {
            link = '/recovery/getrecoverydata?project=' + $("#project").val() + '&type=' + $("#type_recovery option:selected").val() + '&start_date=' + $("#start_date").val() + '&end_date=' + $("#end_date").val();
        } else {
            link = '/recovery/getrecoverydata' + link + '&project=' + $("#project").val() + '&type=' + $("#type_recovery option:selected").val() + '&start_date=' + $("#start_date").val() + '&end_date=' + $("#end_date").val();
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

                $('#table_data').html('');
                $('#carrinhoAbandonado').addClass('table-striped');

                $.each(response.data, function (index, value) {

                    dados = '';
                    dados += '<tr>';
                    dados += "<td class='display-sm-none display-m-none display-lg-none'>" + value.date + "</td>";
                    dados += "<td>" + value.project + "</td>";
                    dados += "<td class='display-sm-none display-m-none'>" + value.client + "</td>";
                    dados += "<td>" + value.email_status + "</td>";
                    dados += "<td>" + value.sms_status + "</td>";
                    dados += "<td><span class='badge badge-" + statusRecovery[value.recovery_status] + "'>" + value.recovery_status + "</span></td>";
                    dados += "<td>" + value.value + "</td>";
                    dados += "<td class='display-sm-none' align='center'> <a href='" + value.whatsapp_link + "', '', $client['telephone']); !!}' target='_blank'><img style='height:24px' src='https://logodownload.org/wp-content/uploads/2015/04/whatsapp-logo-4-1.png'></a></td>";
                    dados += "<td class='display-sm-none' align='center'> <a role='button' class='copy_link' style='cursor:pointer;' link='" + value.link + "'><i class='material-icons gradient'>file_copy</i></a></td>";
                    dados += "<td class='display-sm-none' align='center'> <a role='button' class='details-cart-recovery' style='cursor:pointer;' venda='" + value.id + "' data-target='#modal_detalhes' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i></button></td>";

                    dados += "<td class='display-m-none display-lg-none display-xlg-none'>";
                    dados += "<a role=''       class='mg-responsive'                       style='cursor:pointer;'                href='" + value.whatsapp_link + "', '', $client['telephone']); !!}' target='_blank'><img style='height:24px' src='https://logodownload.org/wp-content/uploads/2015/04/whatsapp-logo-4-1.png'></a>"
                    dados += "<a role='button' class='mg-responsive copy_link'             style='cursor:pointer;' link='" + value.link + "'><i class='material-icons gradient'>file_copy</i></a>"
                    dados += "<a role='button' class='mg-responsive details-cart-recovery' style='cursor:pointer;' venda='" + value.id + "' data-target='#modal_detalhes' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i></button></td>";

                    dados += "</tr>";
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
                if (response.data == '' && $('#type_recovery').val() == 1) {
                    $('#table_data').html("<tr><td colspan='11' class='text-center' style='height: 70px;vertical-align: middle'> Nenhum carrinho abandonado até o momento</td></tr>");
                } else if (response.data == '' && $('#type_recovery').val() == 2) {
                    $('#table_data').html("<tr><td colspan='11' class='text-center' style='height: 70px;vertical-align: middle'> Nenhum boleto vencido até o momento</td></tr>");
                } else if (response.data == '' && $('#type_recovery').val() == 3) {
                    $('#table_data').html("<tr><td colspan='11' class='text-center' style='height: 70px;vertical-align: middle'> Nenhum cartão recusado até o momento</td></tr>");
                }
                pagination(response,'salesRecovery',atualizar);

                var id_venda = '';

                $('.details-cart-recovery').unbind('click');

                $('.details-cart-recovery').on('click', function () {

                    var venda = $(this).attr('venda');

                    $('#modal-title').html('Detalhes Carrinho Abandonado' + '<br><hr>');

                    /*$('.modal-body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");*/

                    var data = {sale_id: venda};

                    $.ajax({
                        method: "POST",
                        url: '/recovery/details',
                        data: {checkout: venda},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            //
                        },
                        success: function success(response) {
                            $('.modal-body').html(response);

                            $(".copy_link").on("click", function () {
                                var temp = $("<input>");
                                $("#nav-tabContent").append(temp);
                                temp.val($(this).attr('link')).select();
                                document.execCommand("copy");
                                temp.remove();
                                alertCustom('success', 'Link copiado!');
                            });
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
});
