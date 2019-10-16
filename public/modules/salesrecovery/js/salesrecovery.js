$(document).ready(function () {

    getProjects();

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        updateSalesRecovery();
    });

    let startDate = moment().subtract(30, 'days').format('YYYY-MM-DD');
    let endDate = moment().format('YYYY-MM-DD');
    $("#date-range-sales-recovery").daterangepicker({
        startDate: moment().subtract(30, 'days'),
        endDate: moment(),
        opens: 'center',
        maxDate: moment().endOf("day"),
        alwaysShowCalendar: true,
        showCustomRangeLabel: 'Customizado',
        autoUpdateInput: true,
        locale: {
            locale: 'pt-br',
            format: 'DD/MM/YYYY',
            applyLabel: "Aplicar",
            cancelLabel: "Limpar",
            fromLabel: 'De',
            toLabel: 'Até',
            customRangeLabel: 'Customizado',
            weekLabel: 'W',
            daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            firstDay: 0
        },
        ranges: {
            'Hoje': [moment(), moment()],
            'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
            'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
            'Este mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function (start, end) {
        startDate = start.format('YYYY-MM-DD');
        endDate = end.format('YYYY-MM-DD');
    });

    /**
     * Busca os projetos para montar o select
     */
    function getProjects() {
        $.ajax({
            method: "GET",
            url: "/api/projects/?select=true",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
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

                    updateSalesRecovery();

                } else {
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }
            }
        });
    }

    /**
     * Formata url
     * @param link
     */
    function urlDataFormatted(link) {
        let url = '';
        if (link == null) {
            url = `?project=${$("#project option:selected").val()}&status=${$("#type_recovery option:selected").val()}&date_range=${$("#date-range-sales-recovery").val()}&client=${$("#client-name").val()}&date_type=created_at`;
        } else {
            url = `${link}&project=${$("#project option:selected").val()}&status=${$("#type_recovery option:selected").val()}&date_range=${$("#date-range-sales-recovery").val()}&client=${$("#client-name").val()}&date_type=created_at`;
        }

        if ($("#type_recovery option:selected").val() == 1) {
            return `/api/checkout${url}`;
        } else if ($("#type_recovery option:selected").val() == 3) {
            return `/api/recovery/getrefusedcart${url}`;
        } else if ($("#type_recovery option:selected").val() == 5) {
            return `/api/recovery/getboleto${url}`;
        } else {
            return `/api/sales${url}`;

        }
    }

    /**
     * Atualiza tabela de recuperação de vendas
     * @param link
     */
    function updateSalesRecovery(link = null) {

        loadOnTable('#table_data', '#carrinhoAbandonado');

        // Formata a url
        link = urlDataFormatted(link);

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {

                $('#table_data').html('');
                $('#carrinhoAbandonado').addClass('table-striped');

                if (response.data == '' && $('#type_recovery').val() == 1) {
                    $("#pagination-salesRecovery").hide();
                    $('#table_data').html("<tr><td colspan='11' class='text-center' style='height: 70px;vertical-align: middle'> Nenhum carrinho abandonado até o momento</td></tr>");
                } else if (response.data == '' && $('#type_recovery').val() == 5) {
                    $("#pagination-salesRecovery").hide();
                    $('#table_data').html("<tr><td colspan='11' class='text-center' style='height: 70px;vertical-align: middle'> Nenhum boleto vencido até o momento</td></tr>");
                } else if (response.data == '' && $('#type_recovery').val() == 3) {
                    $("#pagination-salesRecovery").hide();
                    $('#table_data').html("<tr><td colspan='11' class='text-center' style='height: 70px;vertical-align: middle'> Nenhum cartão recusado até o momento</td></tr>");
                } else {

                    createHTMLTable(response);
                    $("#pagination-salesRecovery").show();
                    pagination(response, 'salesRecovery', updateSalesRecovery);

                    $(".copy_link").on("click", function () {
                        var temp = $("<input>");
                        $("body").append(temp);
                        temp.val($(this).attr('link')).select();
                        document.execCommand("copy");
                        temp.remove();
                        alertCustom('success', 'Link copiado!');
                    });

                    if ($("#type_recovery").val() == '5') {
                        $(".sale_status").hover(
                            function () {
                                $(this).css('cursor', 'pointer').text('Regerar');
                                $(this).css("background", "#545B62");
                            }, function () {
                                var status = $(this).attr('status');
                                $(this).removeAttr("style");
                                $(this).text(status);
                            }
                        );

                        $("#date").val(moment(new Date()).add(3, "days").format("YYYY-MM-DD"));
                        $("#date").attr('min', moment(new Date()).format("YYYY-MM-DD"));

                        $('.sale_status').on('click', function () {
                            $('#saleId').val('');
                            let saleId = $(this).attr('sale_id');
                            $('#saleId').val(saleId);
                            $('#modal_regerar_boleto').modal('show');

                            $('#bt_send').on('click', function () {
                                loadingOnScreen();

                                regenerateBoleto(saleId);

                            });
                        });
                    }
                    $('.details-cart-recovery').unbind('click');
                    $('.details-cart-recovery').on('click', function () {

                        ajaxDetails($(this).data('venda'));

                    });

                    $('.estornar_venda').unbind('click');
                    $('.estornar_venda').on('click', function () {

                        id_venda = $(this).attr('venda');

                        $('#modal_estornar_titulo').html('Estornar venda #' + id_venda + ' ?');
                        $('#modal_estornar_body').html('');
                    });
                }
            }
        });
    }

    /**
     * @param saleId
     */
    function regenerateBoleto(saleId) {
        $.ajax({
            method: "POST",
            url: "/api/recovery/regenerateboleto",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                saleId: saleId,
                date: $('#date').val(),
                discountType: $("#discount_type").val(),
                discountValue: $("#discount_value").val()
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success() {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                window.location = '/sales';
            }
        });
    }

    /**
     *
     * @param response
     */
    function createHTMLTable(response) {
        let html = '';
        $.each(response.data, function (index, value) {
            if (value.type === 'cart_refundend') {
                html += createHtmlOthers(value);
            } else if (value.type === 'expired') {
                html += createHtmlOthers(value);
            } else if (typeof value.sale_code === 'undefined') {
                html += createHtmlCartAbandoned(value);
            } else {
                html += createHtmlOthers(value);
            }
        });

        $("#table_data").append(html);
    }

    /**
     * Cria html quando e carrinho abandonado
     * @param value
     */
    function createHtmlCartAbandoned(value) {

        let data = '';
        data += '<tr>';
        data += "<td class='display-sm-none display-m-none display-lg-none'>" + value.date + "</td>";
        data += "<td>" + value.project + "</td>";
        data += "<td class='display-sm-none display-m-none'>" + value.client + "</td>";
        data += "<td>" + value.email_status + " " + setSend(value.email_status) + "</td>";
        data += "<td>" + value.sms_status + " " + setSend(value.sms_status) + "</td>";
        data += "<td><span class='sale_status badge badge-" + statusRecovery[value.status_translate] + "' status='" + value.status_translate + "' sale_id='" + value.id + "'>" + value.status_translate + "</span></td>";
        data += "<td>" + value.value + "</td>";
        data += "<td class='display-sm-none' align='center'> <a href='" + value.whatsapp_link + "' target='_blank' title='Enviar mensagem pelo whatsapp'><img style='height:24px' src='https://logodownload.org/wp-content/uploads/2015/04/whatsapp-logo-4-1.png'></a></td>";
        data += "<td class='display-sm-none' align='center'> <a role='button' class='copy_link' style='cursor:pointer;' link='" + value.link + "' title='Copiar link'><i class='material-icons gradient'>file_copy</i></a></td>";
        data += "<td class='display-sm-none' align='center'> <a role='button' class='details-cart-recovery' style='cursor:pointer;' data-venda='" + value.id + "' ><i class='material-icons gradient'>remove_red_eye</i></button></td>";
        data += "</tr>";

        return data;

    }

    /**
     * Cria html quando for boleto vencido ou cartão recusado
     * @param value
     * @returns {string}
     */
    function createHtmlOthers(value) {

        let data = '';
        data += '<tr>';
        data += "<td class='display-sm-none display-m-none display-lg-none'>" + value.start_date + "</td>";
        data += "<td>" + value.project + "</td>";
        data += "<td class='display-sm-none display-m-none'>" + value.client + "</td>";
        data += "<td>" + value.email_status + " " + setSend(value.email_status) + "</td>";
        data += "<td>" + value.sms_status + " " + setSend(value.sms_status) + "</td>";
        data += "<td><span class='sale_status badge badge-" + statusRecovery[value.recovery_status] + "' sale_id='" + value.id_default + "'>" + value.recovery_status + "</span></td>";
        data += "<td>" + value.total_paid + "</td>";
        data += "<td class='display-sm-none' align='center'> <a href='" + value.whatsapp_link + "' target='_blank' title='Enviar mensagem pelo whatsapp'><img style='height:24px' src='https://logodownload.org/wp-content/uploads/2015/04/whatsapp-logo-4-1.png'></a></td>";
        data += "<td class='display-sm-none' align='center'> <a role='button' class='copy_link' style='cursor:pointer;' link='" + value.link + "' title='Copiar link'><i class='material-icons gradient'>file_copy</i></a></td>";
        data += "<td class='display-sm-none' align='center'> <a role='button' class='details-cart-recovery' style='cursor:pointer;' data-venda='" + value.id_default + "' ><i class='material-icons gradient'>remove_red_eye</i></button></td>";
        data += "</tr>";

        return data;

    }

// ajax modal details
    function ajaxDetails(sale) {
        $.ajax({
            method: "POST",
            url: '/api/recovery/details',
            data: {checkout: sale},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);

            },
            success: function success(response) {
                $("#table-product").html('');

                if (!isEmpty(response.data)) {

                    createHtmlDetails(response.data)
                } else {

                }

            }
        });
    }

    /**
     * Monta html da modal
     * @param data
     */
    function createHtmlDetails(data) {
        clearFields();

        $('#modal-title').html('Detalhes Carrinho Abandonado' + '<br><hr>');
        $("#date-as-hours").html(`${data.checkout.date} às ${data.checkout.hours}`);
        $("#status-checkout").addClass('badge-' + statusRecovery[data.status]).html(data.status);

        /**
         * Produtos
         */
        let div = '';
        let photo = 'public/modules/global/img/produto.png';
        $.each(data.products, function (index, value) {
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
        $("#total-value").html("R$ " + data.checkout.total);
        /**
         * Fim Produtos
         */

        /**
         * Dados do Cliente e dados da entrega quando for cartao recusado ou boleto expirado
         */
        $("#client-name-details").html('Nome: ' + data.client.name);
        $("#client-telephone").html('Telefone: ' + data.client.telephone);
        $("#client-whatsapp").attr('href', data.client.whatsapp_link);
        $("#client-email").html('E-mail: ' + data.client.email);
        $("#client-document").html('CPF: ' + data.client.document);
        $("#client-street").html('Endereço: ' + data.delivery.street);
        $("#client-zip-code").html('CEP: ' + data.delivery.zip_code);
        $("#client-city-state").html('Cidade: ' + data.delivery.city + '/' + data.delivery.state);
        $("#sale-motive").html('Motivo: ' + data.client.error);

        if (!isEmpty(data.link)) {
            $("#link-sale").html('Link: <a role="button" class="copy_link" style="cursor:pointer;" link="' + data.link + '" title="Copiar link"><i class="material-icons gradient" style="font-size:17px;">file_copy</i> </a> ');
        } else {
            $("#link-sale").html('Link: ' + data.link);
        }

        $("#checkout-ip").html('IP: ' + data.checkout.ip);

        $("#checkout-is-mobile").html(data.checkout.is_mobile);
        /**
         * Fim dados do Cliente
         */

        /**
         * Dados do checkout - UTM
         */
        $("#checkout-operational-system").html('Sistema: ' + data.checkout.operational_system);
        $("#checkout-browser").html('Navegador: ' + data.checkout.browser);
        $("#checkout-src").html('SRC: ' + data.checkout.src);
        $("#checkout-utm-source").html('UTM Source: ' + data.checkout.utm_source);
        $("#checkout-utm-medium").html('UTM Medium: ' + data.checkout.utm_medium);
        $("#checkout-utm-campaign").html('UTM Campaign: ' + data.checkout.utm_campaign);
        $("#checkout-utm-term").html('UTM Term: ' + data.checkout.utm_term);
        $("#checkout-utm-content").html('UTM Content: ' + data.checkout.utm_content);
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
    }

    $('#discount_value').mask('00%', {reverse: true});

    $("#apply_discount").on("click", function () {
        if ($("#div_discount").is(":visible")) {
            $("#div_discount").hide();
            $("#discount_value").val("");
        } else {
            $("#div_discount").show();

            $("#discount_type").on('change', function () {
                if ($("#discount_type").val() == 'value') {
                    $("#discount_value").mask('#.###,#0', {reverse: true}).removeAttr('maxlength');
                    $("#label_discount_value").html("Valor (ex: 20,00)");
                } else {
                    $('#discount_value').mask('00%', {reverse: true});
                    $("#label_discount_value").html("Valor (ex: 20%)");
                }
            });
        }
    });

    /**
     * Adiciona class ao badge da modal e da tabela
     * @type {{Recuperado: string, Recusado: string, "Não recuperado": string, Expirado: string}}
     */
    var statusRecovery = {
        'Recuperado': 'success',
        'Não recuperado': 'danger',
        'Recusado': 'danger',
        'Expirado': 'danger',

    };

    var statusRecoverySale = {
        "Cancelado": 'danger',
        "Recusado": 'danger',
    }
    /**
     * @param sendNumber
     * @returns {string}
     */
    function setSend(sendNumber) {
        if (sendNumber === 1) {
            return 'enviado';
        } else if (sendNumber > 1) {
            return 'enviados';
        } else {
            return '';
        }
    }

    function clearFields() {
        $("#status-checkout").removeClass('badge-success badge-danger');
        $("#client-whatsapp").attr('href', '');
        $(".clear-fields").empty();
        // $("#date-as-hours, #table-product, #total-value, #client-name-details, #client-telephone, #client-email, #client-document, #client-street, #client-zip-code, #client-city-state, #sale-motive, #link-sale, #checkout-ip, #checkout-is-mobile, #checkout-operational-system, #checkout-browser, #checkout-src, #checkout-utm-source, #checkout-utm-medium, #checkout-utm-campaign, #checkout-utm-term, #checkout-utm-content").html('');
    }

});
