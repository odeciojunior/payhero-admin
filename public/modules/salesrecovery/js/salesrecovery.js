var statusRecovery = {
    'Recuperado': 'success',
    'Não recuperado': 'danger',
    'Recusado': 'danger',
    'Expirado': 'danger',

};

function setSend(sendNumber) {
    if (sendNumber == 1) {
        return 'enviado';
    } else if (sendNumber > 1) {
        return 'enviados';
    } else {
        return '';
    }
}

function isEmpty(obj) {
    return Object.keys(obj).length === 0;
}

$(document).ready(function () {

    getProjects();

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        atualizar();
    });

    function getProjects() {
        $.ajax({
            method: "GET",
            url: "/api/recovery",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function (response) {
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', response.message);
                }
            },
            success: function (response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $.each(response.data, function (i, project) {
                        $("#project").append($('<option>', {
                            value: project.id,
                            text: project.name
                        }));
                    });

                    atualizar();

                } else {
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }
            }
        });
    }

    function atualizar() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#table_data', '#carrinhoAbandonado');

        /*$('#table_data').html("<tr class='text-center'><td colspan='11'> Carregando...</td></tr>");*/
        if (link == null) {
            link = 'api/recovery/getrecoverydata?project=' + $("#project").val() + '&type=' + $("#type_recovery option:selected").val() + '&start_date=' + $("#start_date").val() + '&end_date=' + $("#end_date").val() + '&client_name=' + $("#client-name").val();
        } else {
            link = 'api/recovery/getrecoverydata' + link + '&project=' + $("#project").val() + '&type=' + $("#type_recovery option:selected").val() + '&start_date=' + $("#start_date").val() + '&end_date=' + $("#end_date").val() + '&client_name=' + $("#client-name").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', response.responseJSON.message);
                }

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
                    dados += "<td>" + value.email_status + " " + setSend(value.email_status) + "</td>";
                    dados += "<td>" + value.sms_status + " " + setSend(value.sms_status) + "</td>";
                    dados += "<td><span class='badge badge-" + statusRecovery[value.recovery_status] + "'>" + value.recovery_status + "</span></td>";
                    dados += "<td>" + value.value + "</td>";
                    dados += "<td class='display-sm-none' align='center'> <a href='" + value.whatsapp_link + "', '', $client['telephone']); !!}' target='_blank'><img style='height:24px' src='https://logodownload.org/wp-content/uploads/2015/04/whatsapp-logo-4-1.png'></a></td>";
                    dados += "<td class='display-sm-none' align='center'> <a role='button' class='copy_link' style='cursor:pointer;' link='" + value.link + "'><i class='material-icons gradient'>file_copy</i></a></td>";
                    dados += "<td class='display-sm-none' align='center'> <a role='button' class='details-cart-recovery' style='cursor:pointer;' data-venda='" + value.id + "' ><i class='material-icons gradient'>remove_red_eye</i></button></td>";

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
                pagination(response, 'salesRecovery', atualizar);

                $('.details-cart-recovery').unbind('click');

                $('.details-cart-recovery').on('click', function () {

                    var sale = $(this).data('venda');

                    $('#modal-title').html('Detalhes Carrinho Abandonado' + '<br><hr>');

                    $.ajax({
                        method: "POST",
                        url: '/api/recovery/details',
                        data: {checkout: sale},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            if (response.status === 422) {
                                for (error in response.errors) {
                                    alertCustom('error', String(response.errors[error]));
                                }
                            } else {
                                alertCustom('error', response.message);
                            }
                        },
                        success: function success(response) {
                            $("#table-product").html('');

                            if (!isEmpty(response.data)) {

                                $("#date-as-hours").html(`${response.data.checkout.date} às ${response.data.checkout.hours}`);
                                $("#status-checkout").addClass('badge-' + statusRecovery[response.data.status]).html(response.data.status);

                                /**
                                 * Produtos
                                 */
                                let div = '';
                                let photo = 'public/modules/global/img/produto.png';
                                $.each(response.data.products, function (index, value) {
                                    if (!isEmpty(value.photo)) {
                                        photo = value.photo;
                                    }

                                    div += '<div class="row align-items-baseline justify-content-between mb-15">' +
                                        '<div class="col-lg-2">' +
                                        "<img src='" + value.photo + "' width='50px' style='border-radius: 6px;'>" +
                                        '</div>' +
                                        '<div class="col-lg-5">' +
                                        '<h4 class="table-title">' + value.name + '</h4>\n' +
                                        '</div>' +
                                        '<div class="col-lg-3 text-right">' +
                                        '<p class="sm-text text-muted">' + value.amount + 'x</p>' +
                                        '</div>' +
                                        '</div>';

                                    $("#table-product").html(div);
                                });
                                $("#total-value").html("R$ " + response.data.checkout.total);
                                /**
                                 * Fim Produtos
                                 */

                                /**
                                 * Dados do Cliente e dados da entrega quando for cartao recusado ou boleto expirado
                                 */
                                $("#client-name").html('Nome: ' + (response.data.client.name.length === 0 ? '' : response.data.client.name));
                                $("#client-telephone").html('Nome: ' + (response.data.client.telephone === 0 ? '' : response.data.client.telephone));
                                $("#client-whatsapp").attr('href', (response.data.client.whatsapp_link === 0 ? '' : response.data.client.whatsapp_link));
                                $("#client-email").html('E-mail: ' + (response.data.client.email === 0 ? '' : response.data.client.email));
                                $("#client-document").html('CPF: ' + (response.data.client.document === 0 ? '' : response.data.client.document));
                                if (response.data.method === 'boletoCartao') {
                                    $("#client-street").html('Endereço: ' + response.data.delivery.street);
                                    $("#client-zip-code").html('CEP: ' + response.data.delivery.zip_code);
                                    $("#client-city-state").html('Cidade: ' + response.data.delivery.city + '/' + response.data.delivery.state);
                                    $("#sale-motive").html('Motivo: ' + (response.data.client.error === 0 ? '' : response.data.client.error));

                                }

                                if (!isEmpty(response.data.link)) {
                                    $("#link-sale").html('Link: <a role="button" class="copy_link" style="cursor:pointer;" link="' + response.data.link + '"><i class="material-icons gradient" style="font-size:17px;">file_copy</i> </a> ');
                                } else {
                                    $("#link-sale").html('Link: ' + response.data.link);
                                }

                                $("#checkout-ip").html('IP: ' + response.data.checkout.ip);

                                $("#checkout-is-mobile").html(response.data.checkout.is_mobile);
                                /**
                                 * Fim dados do Cliente
                                 */

                                /**
                                 * Dados do checkout - UTM
                                 */
                                $("#checkout-operational-system").html('Sistema: ' + response.data.checkout.operational_system);
                                $("#checkout-browser").html('Navegador: ' + response.data.checkout.browser);
                                $("#checkout-src").html('SRC: ' + response.data.checkout.src);
                                $("#checkout-utm-source").html('UTM Source: ' + response.data.checkout.utm_source);
                                $("#checkout-utm-medium").html('UTM Medium: ' + response.data.checkout.utm_medium);
                                $("#checkout-utm-campaign").html('UTM Campaign: ' + response.data.checkout.utm_campaign);
                                $("#checkout-utm-term").html('UTM Term: ' + response.data.checkout.utm_term);
                                $("#checkout-utm-content").html('UTM Content: ' + response.data.checkout.utm_content);
                                /**
                                 * Fim dados do checkout
                                 */




                                $('#modal_detalhes').modal('show');

                                $(".copy_link").on("click", function () {
                                    var temp = $("<input>");
                                    $("#nav-tabContent").append(temp);
                                    temp.val($(this).attr('link')).select();
                                    document.execCommand("copy");
                                    temp.remove();
                                    alertCustom('success', 'Link copiado!');
                                });

                            } else {

                            }

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
