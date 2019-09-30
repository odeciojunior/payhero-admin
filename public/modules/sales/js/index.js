let currentSaleCode;
let currentDeliveryCode;
$(document).ready(function () {

    getSalesData();

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

    function getSalesData() {
        $.ajax({
            method: "GET",
            url: "/api/sales/",
            error: function error(response) {
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.message));
                }
            },
            success: function success(data) {
                if (data.sales_amount) {
                    for (let i = 0; i < data.projetos.length; i++) {
                        $('#projeto').append('<option value="' + data.projetos[i].id + '">' + data.projetos[i].nome + '</option>')
                    }
                    $('#export-excel, .page-content').show();
                    $('.content-error').hide();
                } else {
                    $('#export-excel, .page-content').hide();
                    $('.content-error').show();
                }
            }
        });
    }

    function downloadFile(data, fileName) {
        var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "text/plain";

        // Create an invisible A element
        var a = document.createElement("a");
        a.style.display = "none";
        document.body.appendChild(a);

        // Set the HREF to a Blob representation of the data to be downloaded
        a.href = window.URL.createObjectURL(new Blob([data], {type: type}));

        // Use download attribute to set set desired file name
        a.setAttribute("download", fileName);

        // Trigger the download by simulating click
        a.click();

        // Cleanup
        window.URL.revokeObjectURL(a.href);
        document.body.removeChild(a);
    }

    function atualizar() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#dados_tabela', '#tabela_vendas');

        if (link == null) {
            link = '/api/sales/getsales?' + 'projeto=' + $("#projeto option:selected ").val() + '&transaction=' + $("#transaction").val().replace('#', '') + '&forma=' + $("#forma option:selected").val() + '&status=' + $("#status option:selected").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        } else {
            link = '/api/sales/getsales' + link + '&projeto=' + $("#projeto option:selected").val() + '&transaction=' + $("#transaction").val().replace('#', '') + '&forma=' + $("#forma option:selected").val() + '&status=' + $("#status option:selected").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        }

        function renderDetails(data) {
            //Dados da venda
            $('#sale-code').text(data.sale.code);
            $('#payment-type').text('Pagamento via ' + (data.sale.payment_method === 2 ? 'Boleto' : 'Cartão ' + data.sale.flag) + ' em ' + data.sale.start_date + ' às ' + data.sale.hours);
            var sale_status = data.sale.status;
            //Status
            let status = $('.modal-body #status');
            status.html('');
            status.append('<img style="width: 50px;" src="/modules/global/img/cartoes/' + data.sale.flag + '.png">');

            switch (data.sale.status) {
                case 1:
                    status.append("<span class='badge badge-success'>Aprovada</span></td>");
                    break;
                case 2:
                    status.append("<span class='badge badge-pendente'>Pendente</span>");
                    break;
                case 3:
                    status.append("<span class='badge badge-danger'>Recusada</span>");
                    break;
                case 4:
                    status.append("<span class='badge badge-danger'>Estornada</span>");
                    break;
                case 6:
                    status.append("<span class='badge badge-primary'>Em análise</span>");
                    break;
                default:
                    status.append("<span class='badge badge-primary'>" + data.sale.status + "</span>");
                    break;
            }

            //Produtos
            $("#table-product").html('');
            $('#data-tracking-products').html('');
            let div = '';
            let photo = 'public/modules/global/img/produto.png';
            $.each(data.products, function (index, value) {
                if (!value.photo) {
                    value.photo = photo;
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

                //Tabela de produtos Tracking Code
                if (sale_status == 1) {
                    let data = `<tr>
                                 <td>
                                      <img src='${value.photo}'  width='35px;' style='border-radius:6px;'><br>
                                      <span class='small'>${value.name}</span>
                                  </td>
                                 <td>
                                      <span class='tracking-code-span small'>${value.tracking_code}</span>
                                      <input class='form-control' id='tracking_code' name='tracking_code' value='${value.tracking_code}' style='display:none;'/>
                                 </td>
                                 <td>
                                      <span class='tracking-status-span small'>${value.tracking_status_enum}</span>
                                 </td>
                                 <td>
                                      <a class='pointer btn-edit-trackingcode p-5' title='Editar Código de rastreio' product-code='${value.id_code}'><i class='icon wb-edit' aria-hidden='true' style='color:#f1556f;'></i></a>
                                      <a class='pointer btn-save-trackingcode p-3 mb-15' title='Salvar Código de rastreio' product-code='${value.id_code}' style='display:none;'><i class="material-icons gradient" style="font-size:17px;">save</i></a>
                                      <a class='pointer btn-close-tracking' title='Fechar' style='display:none;'><i class='material-icons gradient mt-5'>close</i></a>
                                 </td>
                                </tr>`;
                    $('#div_tracking_code').css('display', 'block');
                    $('#data-tracking-products').append(data);
                }

            });

            //Valores
            $("#subtotal-value").html("R$ " + data.subTotal);
            $("#shipment-value").html("R$ " + data.shipment_value);
            $("#subtotal-value").html("R$ " + data.subTotal);

            $('#iof-label, #iof-value, #cambio-label, #cambio-value').hide();
            if (data.sale.dolar_quotation) {
                $('#iof-value span').text('R$ ' + data.sale.iof);
                $('#cambio-label span').text('Câmbio (1 $ = R$ ' + data.sale.dolar_quotation + '): ');
                $('#cambio-value span').text('US$ ' + data.taxa);
                $('#iof-label, #iof-value, #cambio-label, #cambio-value').show();
            }

            $("#desconto-value").html("R$ " + data.discount);
            $("#total-value").html("R$ " + data.total);

            $('#taxas-label').text('Taxas (' + data.transaction.percentage_rate + '% + ' + data.transaction.transaction_rate + '): ');
            $('#taxareal-value').text(data.taxaReal ? data.taxaReal : '');

            $('#convertax-label, #convertax-value').hide();
            if (data.convertax_value !== '0,00') {
                $('#convertax-value').text(data.convertax_value ? data.convertax_value : '');
                $('#convertax-label, #convertax-value').show();
            }

            $('#comission-value').text(data.comission ? data.comission : '');

            //Cliente
            $('#client-name').text('Nome: ' + data.client.name);
            $('#client-telephone').text('Telefone: ' + data.client.telephone);
            $('#client-whatsapp').attr('href', data.whatsapp_link);
            $('#client-email').text('Email: ' + data.client.email);
            $('#client-document').text('CPF: ' + data.client.document);

            //Entrega
            $('#tracking-actions').hide();
            if (data.sale.shopify_order && data.sale.status === 1) {
                $('#tracking-actions #btn-edit-trackingcode, #tracking-actions #btn-sent-tracking-user, .btn-save-tracking').attr('data-code', data.sale.code);
                $('#tracking-actions').show();
                if (data.delivery.tracking_code) {
                    $('#tracking-actions #btn-sent-tracking-user').show();
                    $('.tracking-code .tracking-code-value').text(data.delivery.tracking_code);
                    $('.input-value-trackingcode').val(data.delivery.tracking_code);
                } else {
                    $('#tracking-actions #btn-sent-tracking-user').hide();
                    $('.tracking-code .tracking-code-value').text('Nao informado');
                    $('.input-value-trackingcode').val('');
                }
            }
            $('#delivery-address').text('Endereço: ' + data.delivery.street + ', ' + data.delivery.number);
            $('#delivery-zipcode').text('CEP: ' + data.delivery.zip_code);
            $('#delivery-city').text('Cidade: ' + data.delivery.city + '/' + data.delivery.state);

            //Detalhes da venda
            if (data.sale.payment_method === 1) {
                $('#details-card #card-flag').text('Bandeira: ' + data.sale.flag);
                $('#details-card #card-installments').text('Quantidade de parcelas: ' + data.sale.installments_amount);
                $('#details-card').show();
            }

            if (data.sale.payment_method === 2) {
                $('#details-boleto #boleto-link a').attr('link', data.sale.boleto_link);
                $('#details-boleto #boleto-digitable-line a').attr('digitable-line', data.sale.boleto_digitable_line);
                $('#details-boleto #boleto-due').text('Vencimento: ' + data.sale.boleto_due_date);
                $('#details-boleto').show();
            }

            $('#checkout-ip').text('IP: ' + data.checkout.ip);
            $('#checkout-operational-system').text('Dispositivo: ' + data.checkout.operational_system);
            $('#checkout-browser').text('Navegador: ' + data.checkout.browser);

            $('#checkout-attempts').hide();
            if (data.sale.payment_method === 1) {
                $('#checkout-attempts').text('Quantidade de tentativas: ' + data.sale.attempts).show();
            }

            $('#checkout-src').text('SRC: ' + data.checkout.src);
            $('#checkout-source').text('UTM Source: ' + data.checkout.source);
            $('#checkout-medium').text('UTM Medium: ' + data.checkout.utm_medium);
            $('#checkout-campaign').text('UTM Campaign: ' + data.checkout.utm_campaign);
            $('#checkout-term').text('UTM Term: ' + data.checkout.utm_term);
            $('#checkout-content').text('UTM Content: ' + data.checkout.utm_content);

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
                $('#dados_tabela').html('');
                $('#tabela_vendas').addClass('table-striped');

                var statusArray = {
                    1: 'success',
                    6: 'primary',
                    4: 'danger',
                    3: 'danger',
                    2: 'pendente'
                };

                $.each(response.data, function (index, value) {
                    dados = '';
                    dados += '<tr>';
                    dados += "<td class='display-sm-none display-m-none display-lg-none'>" + value.sale_code + "</td>";
                    dados += "<td>" + value.project + "</td>";
                    dados += "<td>" + value.product + "</td>";
                    dados += "<td class='display-sm-none display-m-none display-lg-none'>" + value.client + "</td>";
                    dados += "<td><img src='/modules/global/img/cartoes/" + value.brand + ".png'  style='width: 60px'></td>";
                    if(value.status_translate == 'Pendente'){
                        dados += "<td><span class='boleto-pending badge badge-" + statusArray[value.status] + "' status=" + value.status_translate + " sale=" + value.id_default + ">" + value.status_translate + "</span></td>";
                    }
                    else{
                        dados += "<td><span class='badge badge-" + statusArray[value.status] + "'>" + value.status_translate + "</span></td>";
                    }
                    dados += "<td class='display-sm-none display-m-none'>" + value.start_date + "</td>";
                    dados += "<td class='display-sm-none'>" + value.end_date + "</td>";
                    dados += "<td style='white-space: nowrap'><b>" + value.total_paid + "</b></td>";
                    dados += "<td><a role='button' class='detalhes_venda pointer' venda='" + value.id + "'><i class='material-icons gradient'>remove_red_eye</i></button></a></td>";
                    dados += '</tr>';
                    $("#dados_tabela").append(dados);
                });
                if (response.data == '') {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='10' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                }
                pagination(response, 'sales', atualizar);

                $(".boleto-pending").hover(
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

                $('.boleto-pending').on('click', function () {
                    // $('#saleId').val('');
                    let saleId = $(this).attr('sale');
                    // $('#saleId').val(saleId);
                    $('#modal_regerar_boleto').modal('show');

                    $('#bt_send').unbind("click");
                    $('#bt_send').on('click', function () {
                        loadingOnScreen();
                        $.ajax({
                            method: "POST",
                            url: "/api/recovery/regenerateboleto",
                            headers: {
                                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                            },
                            data: {saleId: saleId, date: $('#date').val(), discountType: $("#discount_type").val(), discountValue: $("#discount_value").val()},
                            error: function error(response) {
                                loadingOnScreenRemove();

                                if (response.status === 422) {
                                    for (error in response.errors) {
                                        alertCustom('error', String(response.errors[error]));
                                    }
                                } else {
                                    alertCustom('error', response.responseJSON.message);
                                }
                            },
                            success: function success(response) {
                                loadingOnScreenRemove();
                                $(".loading").css("visibility", "hidden");
                                window.location = '/sales';
                            }
                        });
                    });
                });

                $('.detalhes_venda').unbind('click');

                $('.detalhes_venda').on('click', function () {
                    let btn_detalhe = $(this);
                    btn_detalhe.hide();
                    btn_detalhe.parent().append('<span class="loaderSpan"></span>');

                    var venda = $(this).attr('venda');

                    $('#modal_venda_titulo').html('Detalhes da venda ' + venda + '<br><hr>');

                    $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
 
                    var data = {sale_id: venda};

                    // $.ajax({
                    //     method: "get",
                    //     url: '/api/client/' + 3800,
                    //     headers: {
                    //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    //     },
                    //     error: function error(response) {
                    //         console.log(response);
                    //     },
                    //     success: function success(response) {
                    //         console.log(response);
                    //
                    //     }
                    // });

                    $.ajax({
                        method: "POST",
                        url: '/api/sales/detail',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            alertCustom('error', 'Erro ao exibir detalhes da venda');
                            btn_detalhe.parent().children('span').remove();
                            btn_detalhe.show();
                        },
                        success: function success(response) {
                            $('.subTotal').mask('#.###,#0', {reverse: true});
                            currentSaleCode = response.sale.code;
                            currentDeliveryCode = response.delivery.code;

                            renderDetails(response);

                            setTrackingCode(response.sale.id_code);

                            $("#boleto-link .copy_link").on("click", function () {
                                var temp = $("<input>");
                                $("#nav-tabContent").append(temp);
                                temp.val($(this).attr('link')).select();
                                document.execCommand("copy");
                                temp.remove();
                                alertCustom('success', 'Link copiado!');
                            });
                            $("#boleto-digitable-line .copy_link").on("click", function () {
                                var temp = $("<input>");
                                $("#nav-tabContent").append(temp);
                                temp.val($(this).attr('digitable-line')).select();
                                document.execCommand("copy");
                                temp.remove();
                                alertCustom('success', 'Linha Digitável copiado!');
                            });

                            $('#modal_detalhes').modal('show');
                            btn_detalhe.parent().children('span').remove();
                            btn_detalhe.show();

                        }
                    });
                });

            }
        });
    }

    //Código de rastreio
    function setTrackingCode(sale_id) {
        $(".btn-edit-trackingcode").unbind('click');

        $('.btn-edit-trackingcode').on('click', function () {
            var trackingInput = $(this).parent().parent().find('#tracking_code');
            var trackingCodeSpan = trackingInput.parent().find('.tracking-code-span');
            var productId = $(this).attr('product-code');
            var btnEdit = $(this);
            var btnSave = btnEdit.parent().find('.btn-save-trackingcode');
            var btnClose = $(this).parent().find('.btn-close-tracking');
            btnEdit.hide('fast');
            btnSave.show('fast');
            btnClose.show('fast');
            trackingCodeSpan.hide('fast');
            trackingInput.show('fast');

            //Botão para salvar Código de Restreio
            btnSave.unbind('click');
            btnSave.on('click', function () {
                var tracking_code = trackingInput.val();
                if (tracking_code == '') {
                    alertCustom('error', 'Dados informados inválidos');
                    return false;
                }

                $.ajax({
                    method: "POST",
                    url: '/api/tracking',
                    data: {tracking_code: tracking_code, sale_id: sale_id, product_id: productId},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function error(response) {
                        // trackingInput.hide('fast');
                        if (response.status == '400') {
                            alertCustom('error', response.message);
                        }
                    },
                    success: function success(response) {
                        var trackingStatusSPan = trackingInput.parents().next('td').find('.tracking-status-span');
                        trackingCodeSpan.html(response.data.tracking_code);
                        trackingStatusSPan.html(response.data.tracking_status);
                        trackingInput.val(response.data.tracking_code);
                        trackingInput.hide('fast');
                        trackingCodeSpan.show('fast');
                        btnSave.hide('fast');
                        btnEdit.show('fast');
                        btnClose.hide('fast');
                        alertCustom('success', response.message);
                    }
                });
            });

            //Botão para ocultar campos
            $('.btn-close-tracking').on('click', function () {
                $(this).hide('fast');
                btnSave.hide('fast');
                trackingInput.hide('fast');
                btnEdit.show('fast');
                trackingCodeSpan.show('fast');
            });

        });
    }
    function csvSalesExport() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/sales/getcsvsales?' + 'projeto=' + $("#projeto").val() + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        } else {
            link = '/api/sales/getcsvsales' + link + '&projeto=' + $("#projeto").val() + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {

            },
            success: function success(response) {
                downloadFile(response, 'export.xlsx');
            }
        });
    }

    $('#discount_value').mask('00%', {reverse: true});

    $("#apply_discount").on("click", function(){
        if($("#div_discount").is(":visible")){
            $("#div_discount").hide();
            $("#discount_value").val("");
        } else{
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

});
