$(() => {
    // COMPORTAMENTOS DA JANELA

    $("#discount_value").mask("00%", { reverse: true });

    $("#apply_discount").on("click", function () {
        if ($("#div_discount").is(":visible")) {
            $("#div_discount").hide();
            $("#discount_value").val("");
        } else {
            $("#div_discount").show();

            $("#discount_type").on("change", function () {
                if ($("#discount_type").val() == "value") {
                    $("#discount_value").mask("#.###,#0", { reverse: true }).removeAttr("maxlength");
                    $("#label_discount_value").html("Valor (ex: 20,00)");
                } else {
                    $("#discount_value").mask("00%", { reverse: true });
                    $("#label_discount_value").html("Valor (ex: 20%)");
                }
            });
        }
    });

    $(document).on("click", "#boleto-link .copy_link", function () {
        var temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr("link")).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom("success", "Link copiado!");
    });

    $(document).on("click", "#boleto-digitable-line .copy_link", function () {
        var temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr("digitable-line")).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom("success", "Linha Digitável copiado!");
    });

    //Codigo de rastreio
    $(document).on("click", ".btn-edit-trackingcode", function () {
        var trackingInput = $(this).parent().parent().find("#tracking_code");
        var trackingCodeSpan = trackingInput.parent().find(".tracking-code-span");
        var btnEdit = $(this);
        var btnSave = btnEdit.parent().find(".btn-save-trackingcode");
        var btnClose = $(this).parent().find(".btn-close-tracking");
        btnEdit.hide("fast");
        btnSave.show("fast");
        btnClose.show("fast");
        trackingCodeSpan.hide("fast");
        trackingInput.show("fast");
    });

    //Botão para ocultar campos rastreio
    $(document).on("click", ".btn-close-tracking", function () {
        var trackingInput = $(this).parent().parent().find("#tracking_code");
        var trackingCodeSpan = trackingInput.parent().find(".tracking-code-span");
        var btnEdit = $(this).parent().find(".btn-edit-trackingcode");
        var btnSave = $(this).parent().find(".btn-save-trackingcode");

        $(this).hide("fast");
        btnSave.hide("fast");
        trackingInput.hide("fast");
        btnEdit.show("fast");
        trackingCodeSpan.show("fast");
    });

    // FIM - COMPORTAMENTOS DA JANELA

    // MODAL DETALHES DA VENDA
    $(document).on("click", ".detalhes_venda", function () {
        var sale = $(this).attr("sale");

        loadOnAny("#modal-saleDetails");
        $("#modal_detalhes").modal("show");

        $.ajax({
            method: "GET",
            url: "/api/sales/" + sale,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                getSale(response.data);
            },
        });
    });

    function getSale(sale) {
        renderSale(sale);

        getClient(sale.client_id);

        getProducts(sale.id);

        getDelivery(sale.delivery_id);

        getCheckout(sale.checkout_id);

        getNotazz(sale.invoices);
    }

    function renderSale(sale) {
        //Dados da venda
        $("#sale-code").text(sale.id);
        $("#payment-type").text(
            "Pagamento via " +
                (sale.payment_method === 2 ? "Boleto" : "Cartão " + sale.flag) +
                " em " +
                sale.start_date +
                " às " +
                sale.hours
        );
        if (sale.release_date != "") {
            $("#release-date").text("Data de liberação: " + sale.release_date);
        } else {
            $("#release-date").text("");
        }

        //Status
        let status = $(".modal-body #status");
        status.html("");
        status.append('<img style="width: 50px;" src="/build/global/img/cartoes/' + sale.flag + '.png">');

        switch (sale.status) {
            case 1:
                status.append("<span class='badge badge-success'>Aprovada</span>");
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
                status.append("<span class='badge badge-primary'>" + sale.status + "</span>");
                break;
        }

        //Valores
        $("#subtotal-value").html("R$ " + sale.subTotal);
        $("#shipment-value").html("R$ " + sale.shipment_value);

        $("#iof-label, #iof-value, #cambio-label, #cambio-value").hide();
        if (sale.dolar_quotation) {
            $("#cambio-label span").text("Câmbio (1 $ = R$ " + sale.dolar_quotation + "): ");
            $("#cambio-value span").text("US$ " + sale.taxa);
            $("#cambio-label, #cambio-value").show();
        }

        $("#taxas-installment-free-label, #taxa-installment-value").hide();
        if (sale.installment_tax_value !== "0,00") {
            $("#taxa-installment-value").html("R$ " + sale.installment_tax_value);
            $("#taxas-installment-free-label").show();
            $("#taxa-installment-value").show();
        }

        $("#desconto-value").html("R$ " + sale.discount);
        $("#total-value").html("R$ " + sale.total);

        $("#taxas-label").text(sale.tax ? "Taxas (" + sale.tax + " + " + sale.transaction_tax + "): " : "Taxas");
        $("#taxareal-value").text(sale.taxaReal ? sale.taxaReal : "");

        $("#convertax-label, #convertax-value").hide();
        if (sale.convertax_value !== "0,00") {
            $("#convertax-value").text(sale.convertax_value ? sale.convertax_value : "");
            $("#convertax-label, #convertax-value").show();
        }

        $("#comission-value").text(sale.comission ? sale.comission : "");

        //Detalhes da venda
        if (sale.payment_method === 1) {
            $("#details-card #card-flag").text("Bandeira: " + sale.flag);
            $("#details-card #card-installments").text("Quantidade de parcelas: " + sale.installments_amount);
            $("#details-card").show();
            $("#details-boleto").hide();
        }

        if (sale.payment_method === 2) {
            $("#details-boleto #boleto-link a").attr("link", sale.boleto_link);
            $("#details-boleto #boleto-digitable-line a").attr("digitable-line", sale.boleto_digitable_line);
            $("#details-boleto #boleto-due").text("Vencimento: " + sale.boleto_due_date);
            $("#details-card").hide();
            $("#details-boleto").show();
        }

        $("#checkout-attempts").hide();
        if (sale.payment_method === 1) {
            $("#checkout-attempts")
                .text("Quantidade de tentativas: " + sale.attempts)
                .show();
        }
    }

    function getNotazz(invoices) {
        if (!isEmpty(invoices)) {
            let lastInvoice = invoices[invoices.length - 1];

            $.ajax({
                method: "GET",
                url: "/api/apps/notazz/invoice/" + lastInvoice,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: (response) => {
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    renderNotazz(response.data);
                },
            });
        }
    }

    function renderNotazz(invoice) {
        if (!isEmpty(invoice)) {
            //exist

            $("#data-notazz-invoices").empty();

            if (invoice.date_pending) {
                let data = `<tr>
                                <td>
                                    ${invoice.date_pending}
                                </td>
                                <td>
                                    Pendente de envio
                                </td>
                                <td>
                                    0
                                </td>
                                <td>
                                    Sucesso
                                </td>
                                <td>

                                </td>
                            </tr>`;
                $("#data-notazz-invoices").append(data);
            }

            if (invoice.date_sent) {
                var return_message = invoice.return_message == null ? "Sucesso" : invoice.return_message;

                var status = invoice.return_message ? "Erro ao enviar para Notazz" : "Enviado para Notazz";
                var link = invoice.pdf
                    ? "<a href='" +
                      invoice.pdf +
                      "' class='copy_link' style='cursor:pointer;' target='_blank'><span class='material-icons icon-copy-1'> content_copy </span></a>"
                    : "";
                let data = `<tr>
                                <td>
                                    ${invoice.date_sent}
                                </td>
                                <td>
                                    ${status}
                                </td>
                                <td>
                                    ${invoice.return_http_code}
                                </td>
                                <td>
                                    ${return_message}
                                </td>
                                <td>
                                    ${link}
                                </td>
                            </tr>`;
                $("#data-notazz-invoices").append(data);
            }

            if (invoice.date_error) {
                var status = invoice.return_message ? "Erro ao enviar para Notazz" : "Enviado para Notazz";

                let data = `<tr>
                                <td>
                                    ${invoice.date_error}
                                </td>
                                <td>
                                    ${status}
                                </td>
                                <td>
                                    ${invoice.return_http_code}
                                </td>
                                <td>
                                    ${invoice.return_message}
                                </td>
                                <td>

                                </td>
                            </tr>`;
                $("#data-notazz-invoices").append(data);
            }

            if (invoice.date_rejected) {
                var postback_message = invoice.postback_message == null ? "Rejeitado" : invoice.postback_message;

                let data = `<tr>
                                <td>
                                    ${invoice.date_sent}
                                </td>
                                <td>
                                    Nota fiscal rejeitada
                                </td>
                                <td>
                                    ${invoice.return_http_code}
                                </td>
                                <td>
                                    ${postback_message}
                                </td>
                                <td>

                                </td>
                            </tr>`;
                $("#data-notazz-invoices").append(data);
            }

            if (invoice.date_canceled) {
                var link = invoice.pdf
                    ? "<a href='" +
                      invoice.pdf +
                      "' class='copy_link' style='cursor:pointer;' target='_blank'><span class='material-icons icon-copy-1'> content_copy </span></a>"
                    : "";
                let data = `<tr>
                                <td>
                                    ${invoice.date_sent}
                                </td>
                                <td>
                                    Nota fical cancelada
                                </td>
                                <td>
                                    ${invoice.return_http_code}
                                </td>
                                <td>
                                    Sucesso
                                </td>
                                <td>
                                    ${link}
                                </td>
                            </tr>`;
                $("#data-notazz-invoices").append(data);
            }

            if (invoice.return_message) {
                $("#div_notazz_schedule").html("Próxima tentativa de envio em " + invoice.schedule);
            }

            $("#div_notazz_invoice").show();
        } else {
            //not exist
            $("#div_notazz_invoice").hide();
        }
    }

    function getClient(client) {
        $.ajax({
            method: "GET",
            url: "/api/customers/" + client,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderClient(response.data);
            },
        });
    }

    function renderClient(client) {
        //Cliente
        $("#client-name").text("Nome: " + client.name);
        $("#client-telephone").text("Telefone: " + client.telephone);
        $("#client-whatsapp").attr("href", client.whatsapp_link);
        $("#client-email").text("Email: " + client.email);
        $("#client-document").text("CPF: " + client.document);
    }

    function getProducts(sale) {
        $.ajax({
            method: "GET",
            url: "/api/products/saleproducts/" + sale,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderProducts(response.data, sale);
            },
        });
    }

    function renderProducts(products, sale) {
        $("#table-product").html("");
        $("#data-tracking-products").html("");
        let div = "";
        let photo = "/build/global/img/produto.svg";
        $.each(products, function (index, value) {
            if (!value.photo) {
                value.photo = photo;
            }
            div += `<div class="row align-items-baseline justify-content-between mb-15">
                        <div class="col-lg-2">
                            <img src='${value.photo}' width='50px' style='border-radius: 6px;'>
                        </div>
                        <div class="col-lg-5">
                            <h4 class="table-title">${value.name}</h4>
                        </div>
                        <div class="col-lg-3 text-right">
                            <p class="sm-text text-muted">${value.amount}x</p>
                        </div>
                    </div>`;
            $("#table-product").html(div);

            //Tabela de produtos Tracking Code
            if (value.sale_status == 1) {
                let data = `<tr>
                                <td>
                                    <img src='${value.photo}'  width='35px;' style='border-radius:6px;'><br>
                                    <span class='small' style='display: inline-block; width: 60px;white-space: nowrap;overflow: hidden !important;text-overflow: ellipsis;'>${value.name}</span>
                                </td>
                                <td>
                                    <span class='tracking-code-span small ellipsis'>${value.tracking_code}</span>
                                    <input class='form-control' id='tracking_code' name='tracking_code' value='${value.tracking_code}' style='display:none;'/>
                                </td>
                                <td>
                                    <span class='tracking-status-span small'>${value.tracking_status_enum}</span>
                                </td>
                                <td>
                                    <a class='pointer btn-edit-trackingcode p-5' title='Editar Código de rastreio' product-code='${value.id}'><i class='icon wb-edit' aria-hidden='true' style='color:#f1556f;'></i></a>
                                    <a class='pointer btn-save-trackingcode p-3 mb-15' title='Salvar Código de rastreio' sale='${sale}' product-code='${value.id}' style='display:none;'><i class="material-icons gradient" style="font-size:17px;">save</i></a>
                                    <a class='pointer btn-close-tracking' title='Fechar' style='display:none;'><i class='material-icons gradient mt-5'>close</i></a>
                                </td>
                            </tr>`;
                $("#div_tracking_code").css("display", "block");
                $("#data-tracking-products").append(data);
            } else {
                $("#div_tracking_code").css("display", "none");
            }
        });
    }

    function getDelivery(deliveryId) {
        $.ajax({
            method: "GET",
            url: "/api/delivery/" + deliveryId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderDelivery(response.data);
            },
        });
    }

    function renderDelivery(delivery) {
        $(".btn-save-trackingcode").attr("delivery", delivery.id);
        $("#delivery-address").text("Endereço: " + delivery.street + ", " + delivery.number);
        $("#delivery-zipcode").text("CEP: " + delivery.zip_code);
        $("#delivery-city").text("Cidade: " + delivery.city + "/" + delivery.state);
    }

    function getCheckout(checkoutId) {
        $.ajax({
            method: "GET",
            url: "/api/checkout/" + checkoutId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                renderCheckout(response.data);
            },
        });
    }

    function renderCheckout(checkout) {
        $("#checkout-ip").text("IP: " + checkout.ip);
        $("#checkout-operational-system").text("Dispositivo: " + checkout.operational_system);
        $("#checkout-browser").text("Navegador: " + checkout.browser);
        $("#checkout-src").text("SRC: " + checkout.src);
        $("#checkout-source").text("UTM Source: " + checkout.source);
        $("#checkout-medium").text("UTM Medium: " + checkout.utm_medium);
        $("#checkout-campaign").text("UTM Campaign: " + checkout.utm_campaign);
        $("#checkout-term").text("UTM Term: " + checkout.utm_term);
        $("#checkout-content").text("UTM Content: " + checkout.utm_content);

        //remove o loader depois de tudo carregado
        loadOnAny("#modal-saleDetails", true);
    }

    // FIM - MODAL DETALHES DA VENDA

    //Salvar Código de Rastreio
    $(document).on("click", ".btn-save-trackingcode", function () {
        var btnSave = $(this);
        var trackingInput = $(this).parent().parent().find("#tracking_code");
        var tracking_code = trackingInput.val();
        var productId = $(this).attr("product-code");
        var saleId = $(this).attr("sale");
        if (tracking_code == "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }
        $.ajax({
            method: "POST",
            url: "/api/tracking",
            data: { tracking_code: tracking_code, sale_id: saleId, product_id: productId },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                var trackingStatusSPan = trackingInput.parents().next("td").find(".tracking-status-span");
                var trackingCodeSpan = trackingInput.parent().find(".tracking-code-span");
                var btnEdit = btnSave.parent().find(".btn-edit-trackingcode");
                var btnClose = btnSave.parent().find(".btn-close-tracking");

                trackingCodeSpan.html(response.data.tracking_code);
                trackingStatusSPan.html(response.data.tracking_status);
                trackingInput.val(response.data.tracking_code);
                trackingInput.hide("fast");
                trackingCodeSpan.show("fast");
                btnSave.hide("fast");
                btnEdit.show("fast");
                btnClose.hide("fast");
                alertCustom("success", response.message);
            },
        });
    });
});
