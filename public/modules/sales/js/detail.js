$(() => {
    // COMPORTAMENTOS DA JANELA

    $("#discount_value").mask("00%", {reverse: true});

    $("#apply_discount").on("click", function () {
        if ($("#div_discount").is(":visible")) {
            $("#div_discount").hide();
            $("#discount_value").val("");
        } else {
            $("#div_discount").show();

            $("#discount_type").on("change", function () {
                if ($("#discount_type").val() == "value") {
                    $("#discount_value")
                        .mask("#.###,#0", {reverse: true})
                        .removeAttr("maxlength");
                    $("#label_discount_value").html("Valor (ex: 20,00)");
                } else {
                    $("#discount_value").mask("00%", {reverse: true});
                    $("#label_discount_value").html("Valor (ex: 20%)");
                }
            });
        }
    });

    $(document).on("click", "#boleto-link .copy_link", function () {
        let temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr("link")).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom("success", "Link copiado!");
    });

    $(document).on("click", "#boleto-digitable-line .copy_link", function () {
        let temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr("digitable-line")).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom("success", "Linha Digitável copiado!");
    });

    $(document).on("click", ".btn-copy-thank-page-url", function () {
        let temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr("link")).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom("success", "Link copiado!");
    });

    $(document).on("click", ".btn-copy-custom-text", function () {
        let temp = $("<input>");
        $("#nav-tabContent").append(temp);
        temp.val($(this).attr("link")).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom("success", "Link copiado!");
    });

    $(".btn-edit-client").on("click", function () {
        let container = $(this).parent();
        container
            .find("input")
            .removeClass("fake-label")
            .prop("readonly", false);
        $(this).hide();
        container.find(".btn-save-client").show();
        container.find(".btn-close-client").show();
    });

    $(".btn-close-client").on("click", function () {
        let container = $(this).parent();
        container.find("input").addClass("fake-label").prop("readonly", true);
        $(this).hide();
        container.find(".btn-save-client").hide();
        container.find(".btn-edit-client").show();
    });

    //atualiza códigos de rastreio
    $(".btn-save-client").on("click", function () {
        let container = $(this).parent();
        let input = container.find("input");

        let data = {
            id: input.attr("client"),
            name: input.attr("name"),
            value: input.val(),
            _method: "PUT",
        };

        $.ajax({
            method: "POST",
            url: "/api/customers/update",
            dataType: "json",
            data: data,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                input.addClass("fake-label").prop("readonly", true);
                $(this).hide();
                container.find(".btn-close-client").hide();
                container.find(".btn-edit-client").show();
                alertCustom(
                    "success",
                    "Dados do cliente alterados com successo!"
                );
            },
        });
    });

    // FIM - COMPORTAMENTOS DA JANELA

    $(document).on("click", ".btn-edit-observation", function () {
        let container = $(this).parent();
        container
            .find("input")
            .removeClass("fake-label")
            .prop("readonly", false);
        $(this).hide();
        container.find(".btn-save-observation").show();
        container.find(".btn-close-observation").show();
    });
    $(document).on("click", ".btn-close-observation", function () {
        let container = $(this).parent();
        container.find("input").addClass("fake-label").prop("readonly", true);
        $(this).hide();
        container.find(".btn-save-observation").hide();
        container.find(".btn-edit-observation").show();
    });
    $(document).on("click", ".btn-save-observation", function () {
        if ($("#refund-observation").val() == "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }
        let container = $(this).parent();
        let input = container.find("input");

        let data = {
            id: input.attr("sale"),
            name: input.attr("name"),
            value: input.val(),
            // _method: 'PUT',
        };
        $.ajax({
            method: "POST",
            url: "/api/sales/updaterefundobservation/" + input.attr("sale"),
            dataType: "json",
            data: data,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                input.addClass("fake-label").prop("readonly", true);
                $(this).hide();
                container.find(".btn-close-observation").hide();
                container.find(".btn-edit-observation").show();
                alertCustom(
                    "success",
                    "Causa do estorno alterado com successo!"
                );
            },
        });
    });

    // MODAL DETALHES DA VENDA
    $(document).on("click", ".detalhes_venda", function () {
        let sale = $(this).attr("venda");
        loadOnAny("#modal-saleDetails");

        $("#nav-home-tab").addClass("active");
        $("#nav-home-tab").addClass("show");
        $("#nav-home").addClass("active");
        $("#nav-home").addClass("show");

        $("#nav-profile-tab").removeClass("active");
        $("#nav-profile-tab").removeClass("show");
        $("#nav-profile").removeClass("active");
        $("#nav-profile").removeClass("show");

        $("#nav-woo-tab").removeClass("active");
        $("#nav-woo").removeClass("active");
        $("#nav-woo").removeClass("show");

        $("#modal_detalhes").modal("show");
        $("#refundAmount").mask("##.###,#0", {reverse: true});
        $("#refundBilletAmount").mask("##.###,#0", {reverse: true});

        $.ajax({
            method: "GET",
            url: "/api/sales/" + sale,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                $("#modal_detalhes").modal("hide");
                errorAjaxResponse(response);
            },
            success: (response) => {
                getSale(response.data);
                
                $("#refundAmount").val(response.data.total);
                $("#refundBilletAmount").text(response.data.total);
                $(".btn_refund_transaction").unbind("click");
                $(".btn_refund_transaction").on("click", function () {
                    $('#refund_observation').val('');

                    let sale = $(this).attr("sale");
                    $("#modal_detalhes").modal("hide");
                    $("#modal-refund-transaction").modal("show");

                    $('#asaas_message').html('');
                    if(response.data.asaas_amount_refund!= ''){
                        $('#asaas_message').html(`<p class="gray"> Esta venda já foi antecipada, o valor a ser debitado no extrato será de <strong>${response.data.asaas_amount_refund}</strong></p>`)   
                    }

                    $("#radioTotalRefund").on("click", function () {
                        $(".value-partial-refund").hide();
                    });

                    $("#radioPartialRefund").on("click", function () {
                        $(".value-partial-refund").show();
                    });

                    $(".btn-confirm-refund-transaction").unbind("click");
                    $(".btn-confirm-refund-transaction").on("click", function () {
                        let partial = 0;
                        if (document.getElementById("radioPartialRefund").checked) {
                            partial = 1;
                        }

                        let refunded_value = $("#refundAmount").val();
                        refundedClick(
                            sale,
                            refunded_value,
                            partial,
                            $("#refund_observation").val()
                        );
                    });
                });

                $(".btn_refund_billet").unbind("click");
                $(".btn_refund_billet").on("click", function () {
                    var sale = $(this).attr("sale");
                    var refunded_value = response.data.total;
                    $(".billet-refunded-tax-value").text(
                        response.data.taxaReal ? response.data.taxaReal : ""
                    );
                    $("#modal-refund-billet").modal("show");
                    $("#modal_detalhes").modal("hide");
                    // var refunded_value = $('#refundBilletAmount').val();
                    $(".btn-confirm-refund-billet").unbind("click");
                    $(".btn-confirm-refund-billet").on("click", function () {
                        refundedBilletClick(sale, refunded_value);
                    });
                });
            },
        });
    });

    $("#update-sale-observation").click(function () {
        let sale = $("#sale-code").text().substring(0, 8);

        $.ajax({
            method: "POST",
            url: "/api/sales/set-observation/" + sale,
            data: {
                observation: $("#observation").val(),
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                alertCustom("success", response.message);
                atualizar(currentPage);
            },
        });
    });

    function getSale(sale) {
        renderSale(sale);

        getProducts(sale);

        getClient(sale.client_id, sale.id);

        if (sale.delivery_id != "") {
            getDelivery(sale.delivery_id);
        }

        if (!sale.api_flag) {
            $(".dados-checkout").css("display", "block");

            getCheckout(sale.checkout_id);
        } else {
            $(".dados-checkout").css("display", "none");
        }

        getNotazz(sale.invoices);
    }

    function renderSale(sale) {
        //Dados da venda
        let paymentMethod = '';

        if (sale.payment_method == 1) {
            paymentMethod = 'Cartão';
        } else if (sale.payment_method == 3) {
            paymentMethod = 'Debito';
        } else if (sale.payment_method == 4) {
            paymentMethod = 'PIX';
        } else {
            paymentMethod = 'Boleto';
        }

        $('#comission-details').css('display','flex');

        $('#sale-code').text(sale.id);
        if (!!sale.upsell) {
            $("#sale-code").append(
                `<span class="text-muted font-size-16 d-block mt-1"> Upsell → ${sale.upsell}</span>`
            );
        }
        if (sale.has_order_bump) {
            $("#sale-code").append(
                `<span class="text-muted font-size-16 d-block mt-1"> Order Bump </span>`
            );
        }
        $('#payment-type').text('Pagamento via ' + paymentMethod + ' em ' + sale.start_date + ' às ' + sale.hours);
        if (sale.release_date != '') {
            $('#release-date').text('Data de liberação: ' + sale.release_date);
        } else {
            $("#release-date").text("");
        }

        $("#card-company").text(
            "Empresa responsável pelo faturamento: " + sale.company_name
        );

        if (!isEmpty(sale.observation)) {
            $("#sale-observation").removeClass("collapse");
        } else {
            $("#sale-observation").addClass("collapse");
        }
        $("#observation").val(sale.observation);

        //Status
        let status = $(".modal-body #status");
        status.html("");
        status.append(
            '<img style="width: 50px;" src="/modules/global/img/cartoes/' +
            sale.flag +
            '.png">'
        );

        switch (sale.status) {
            case 1:
                status.append(
                    "<span class='ml-2 badge badge-success'>Aprovada</span>"
                );
                break;
            case 2:
                status.append(
                    "<span class='ml-2 badge badge-pendente'>Pendente</span>"
                );
                break;
            case 3:
                status.append(
                    "<span class='ml-2 badge badge-danger'>Recusada</span>"
                );
                break;
            case 4:
                status.append(
                    "<span class='ml-2 badge badge-danger'>Chargeback</span>"
                );
                break;
            case 6:
                status.append(
                    "<span class='ml-2 badge badge-primary'>Em análise</span>"
                );
                break;
            case 7:
                status.append(
                    "<span class='ml-2 badge badge-danger'>Estornado</span>"
                );
                break;
            case 8:
                status.append(
                    "<span class='ml-2 badge badge-danger'>Estorno Parcial</span>"
                );
                break;
            case 20:
                status.append(
                    "<span class='ml-2 badge badge-antifraude'>Revisão Antifraude</span>"
                );
                break;
            case 21:
                status.append(
                    "<span class='ml-2 badge badge-danger'>Cancelado Antifraude</span>"
                );
                break;
            case 22:
                status.append(
                    "<span class='ml-2 badge badge-danger'>Estornado</span>"
                );
                break;
            case 23:
                status.append(
                    "<span class='ml-2 badge badge-warning'>Recuperado</span>"
                );
                break;
            case 24:
                status.append(
                    "<span class='ml-2 badge badge-antifraude'>Em disputa</span>"
                );
                break;
            default:
                status.append(
                    "<span class='ml-2 badge badge-primary'>" +
                    sale.status +
                    "</span>"
                );
                break;
        }
        if (sale.is_chargeback_recovered && sale.status == 1) {
            $("#chargeback-recovered").show();
        } else {
            $("#chargeback-recovered").hide();
        }
        //Valores
        $("#subtotal-value").html(sale.subTotal);
        $("#shipment-value").html(sale.shipment_value);

        $("#iof-label, #iof-value, #cambio-label, #cambio-value").hide();
        if (sale.dolar_quotation) {
            $("#cambio-label span").text(
                "Câmbio (1 $ = R$ " + sale.dolar_quotation + "): "
            );
            $("#cambio-value span").text("US$ " + sale.taxa);
            $("#cambio-label, #cambio-value").show();
        }

        $("#taxas-installment-free-label, #taxa-installment-value").hide();
        if (
            parseFloat(sale.installment_tax_value.replace(/[^\d]/g, "")) !== 0 &&
            sale.user_sale_type == "producer"
        ) {
            $("#taxa-installment-value").html(sale.installment_tax_value);
            $("#taxas-installment-free-label").show();
            $("#taxa-installment-value").show();
        }

        if (parseFloat(sale.discount.replace(/[^\d]/g, "")) > 0) {
            $("#discount-title").show();
            $("#discount-data").show();
            $("#desconto-value").html(sale.discount);

        } else {
            $("#discount-title").hide();
            $("#discount-data").hide();
            $("#automatic-discount-value").hide();
        }
        if (!!sale.cupom_code) {
            $("#cupom-code").html(sale.cupom_code);
            $(".cupom-info").show();
        } else {
            $(".cupom-info").hide();
        }
        $(".text-discount").html("");
        $("#automatic-discount-value").html("");

        if (parseInt(sale.automatic_discount.replace(/[^\d]/g, "")) > 0) {
            switch (sale.payment_method) {
                case 2:
                    $(".text-discount").html("Desconto automático boleto");
                break;
                case 4:
                    $(".text-discount").html("Desconto automático pix");
                break;
                default:
                    $(".text-discount").html("Desconto automático cartão");
                break;
            }

            $(".automatic-discount-value").show();
            $(".text-discount").show();
            $("#automatic-discount-value").html(sale.automatic_discount).show();
        } else {
            $(".automatic-discount-value").hide();
            $(".text-discount").hide();
        }

        if (sale.status_name != 'refunded') {
            $("#total-value").html(sale.total);
        } else {
            $("#total-value").html(` <del style="color: #F41C1C !important;">${sale.total}</del>`);
        }

        if (sale.refund_value != "0,00" && sale.status == 8) {
            $(".text-partial-refund").show();
            $("#partial-refund-value").html(sale.refund_value);
            $("#partial-refund-value").show();
        } else {
            $(".text-partial-refund").hide();
            $("#partial-refund-value").hide();
        }

        if (sale.status_name == 'refunded') {
            $('#comission-details').css('display','none');
        }

        // Taxas detalhadas
        $("#taxas-label").html(
            sale.percentage_rate
                ? "Taxas (" +
                sale.percentage_rate +
                "% + " +
                sale.transaction_rate +
                "): "
                : "Taxas"
        );
        $("#taxareal-value").html(sale.taxaReal ? sale.taxaReal : "");

        $("#tax-value-total").html(`Valor total: `);

        if (sale.status_name != 'refunded') {
            $("#tax-value-total-value").html(sale.total_parcial);
        } else {
            $("#tax-value-total-value").html(` <del style="color: #F41C1C !important;">${sale.total_parcial}</del>`);
        }


        $("#tax-percentage").html(`Taxa (${sale.percentage_rate}%)`);
        $("#tax-percentage-value").html(`${sale.taxaDiscount}`);

        $("#tax-fixed").html("Taxa fixa: ");
        $("#tax-fixed-value").html(`${sale.transaction_rate}`);

        $("#tax-total").html(`Valor total das taxas: `);
        $("#tax-total-value").html(`- ${sale.totalTax}`);

        $("#tax-comission").html("Valor recebido");
        $("#tax-comission-value").html(`<b>${sale.comission}</b>`);

        $("#convertax-label, #convertax-value").hide();
        if (sale.convertax_value !== "0,00") {
            $("#convertax-value").text(
                sale.convertax_value ? sale.convertax_value : ""
            );
            $("#convertax-label, #convertax-value").show();
        }

        // valor antecipavel
        if (sale.value_anticipable != "0,00") {
            $(".div-anticipated").show();
            $(".div-value-anticipated")
                .html("")
                .append(
                    `<span class='text-muted ft-12'> ${sale.value_anticipable}</span>`
                )
                .show();
        }

        // valor cashback
        if (sale.has_cashback) {
            $("#cashback-label").removeClass("d-none");
            $("#cashback-value")
                .removeClass("d-none")
                .html("")
                .append(
                    `<span class='ft-12' style="color: #5EE2A1;"> + ${sale.cashback_value}</span>`
                )
                .show();

            // Taxas detalhadas
            $("#tax-subtotal").html("Subtotal:").parent().removeClass("d-none");
            $("#tax-subtotal-value")
                .html(sale.total)
                .parent()
                .removeClass("d-none");
            $("#tax-cashback").html("Cashback:").parent().removeClass("d-none");
            $("#tax-cashback-value")
                .html(`+ ${sale.cashback_value}`)
                .parent()
                .removeClass("d-none")
                .show();
        } else {
            $("#cashback-label").addClass("d-none");
            $("#cashback-value").addClass("d-none").html("");

            // Taxas detalhadas
            $("#tax-subtotal").parent().addClass("d-none");
            $("#tax-subtotal-value").parent().addClass("d-none");
            $("#tax-cashback").parent().addClass("d-none");
            $("#tax-cashback-value").parent().addClass("d-none");
        }

        //comissao afiliado
        if (sale.user_sale_type == "affiliate") {
            $(".div-main-comission-value").html(
                "<h4 id='comission-value' class='table-title'></h4>"
            );
            $(".div-main-comission").html(
                "<h4 class='table-title'>Comissão: </h4>"
            );

            if (sale.affiliate != null) {
                $(".div-user-type-comission-value")
                    .show()
                    .html(
                        "<span id='user-type-comission-value' class='text-muted ft-12'></span>"
                    );
                $(".div-user-type-comission")
                    .show()
                    .html(
                        "<span class='text-muted ft-12'>Comissão do produtor: </span>"
                    );
                $("#user-type-comission-value").html(sale.comission);
            } else {
                $(".div-user-type-comission-value").hide();
                $(".div-user-type-comission").hide();
            }
        } else {
            $(".div-main-comission-value").html(
                "<h4 id='comission-value' class='table-title'></h4>"
            );
            $(".div-main-comission").html(
                "<h4 class='table-title'>Comissão: </h4>"
            );

            if (sale.affiliate != null) {
                $(".div-sale-by-affiliate")
                    .show()
                    .html(
                        "<h4 class='table-title'>Venda realizada pelo afiliado " +
                        sale.affiliate +
                        "</h4>"
                    );
                $(".div-user-type-comission-value")
                    .show()
                    .html(
                        "<span id='user-type-comission-value' class='text-muted ft-12'></span>"
                    );
                $(".div-user-type-comission")
                    .show()
                    .html(
                        "<span class='text-muted ft-12'>Comissão do afiliado: </span>"
                    );
                $("#user-type-comission-value").html(sale.affiliate_comission);
            } else {
                $(".div-sale-by-affiliate").hide();
                $(".div-affiliate-name").hide().html("");
                $(".div-user-type-comission-value").hide();
                $(".div-user-type-comission").hide();
            }

            $("#comission-value").text(sale.comission ? sale.comission : "");
        }

        if (
            sale.affiliate_comission != "" &&
            sale.user_sale_type == "affiliate"
        ) {
            $("#comission-value").text(sale.affiliate_comission);
        }
        
        //Ordem Woocommerce
        if (sale.has_woocommerce_integration) {
            $('#nav-woo-tab').show()
            if(sale.woocommerce_order){
                var order = 'Status: <strong>'+sale.woocommerce_order.status+'</strong>'
                // console.log(sale)
                $('#woo_order').html(order)

                $("#resendWoocommerceOrder").addClass("d-none");
                $("#resendWoocommerceOrder").removeClass("d-block");
                $("#resendWoocommerceOrderButton").attr("sale", "");
            }else{
                $('#woo_order').html('Ordem não encontrada!')

                if(sale.woocommerce_retry_order){
                    
                    $("#resendWoocommerceOrder").removeClass("d-none");
                    $("#resendWoocommerceOrder").addClass("d-block");
                    $("#resendWoocommerceOrderButton").attr("sale", sale.id);
                    $(".btn_new_order_woocommerce").unbind("click");
                    $(".btn_new_order_woocommerce").on("click", function () {
                        var sale = $(this).attr("sale");
                        $("#modal-new-order-woocommerce").modal("show");
                        $("#modal_detalhes").modal("hide");
                        $(".btn-confirm-new-order-woocommerce").unbind("click");
                        $(".btn-confirm-new-order-woocommerce").on(
                            "click",
                            function () {
                                newOrderWooClick(sale);
                            }
                        );
                    });
                }
            }
        }

        //Detalhes do shopify
        if (sale.has_shopify_integration) {
            if (sale.shopify_order || sale.status == 20) {
                // $('#shopify-order').text('TEM ORDER');
                $("#resendShopfyOrder").addClass("d-none");
                $("#resendShopfyOrder").removeClass("d-block");
                $("#resendeShopifyOrderButton").attr("sale", "");
            } else {
                /*
                $('#shopify-order').text('Ordem não foi gerada no shopify!');
                $('#div_details_shopify').html('<button class="btn btn-secondary btn-sm btn_new_order_shopify" sale=' + sale.id + '>Gerar ordem Shopify</button>');*/
                $("#resendShopfyOrder").removeClass("d-none");
                $("#resendShopfyOrder").addClass("d-block");
                $("#resendeShopifyOrderButton").attr("sale", sale.id);
                $(".btn_new_order_shopify").unbind("click");
                $(".btn_new_order_shopify").on("click", function () {
                    var sale = $(this).attr("sale");
                    $("#modal-new-order-shopify").modal("show");
                    $("#modal_detalhes").modal("hide");
                    $(".btn-confirm-new-order-shopify").unbind("click");
                    $(".btn-confirm-new-order-shopify").on(
                        "click",
                        function () {
                            newOrderClick(sale);
                        }
                    );
                });
            }
            $("#details-shopify").show();
        } else {
            $("#resendShopfyOrder").addClass("d-none");
            $("#resendShopfyOrder").removeClass("d-block");
            $("#resendeShopifyOrderButton").attr("sale", "");
        }
        //Detalhes da venda
        $("#nav-profile #card-copany").text("Empresa: Nome da Empresa"); // + sale.company_name);
        if (sale.payment_method === 1) {
            $("#details-card #card-flag").text("Bandeira: " + sale.flag);
            $("#details-card #card-installments").text(
                "Quantidade de parcelas: " + sale.installments_amount
            );
            $("#details-card").show();
            $("#details-boleto").hide();
        }

        if (sale.payment_method === 2) {
            $("#details-boleto #boleto-link a").attr("link", sale.boleto_link);
            $("#details-boleto #boleto-digitable-line a").attr(
                "digitable-line",
                sale.boleto_digitable_line
            );
            $("#details-boleto #boleto-due").text(
                "Vencimento: " + sale.boleto_due_date
            );
            $("#details-card").hide();
            $("#details-boleto").show();
        }

        if (sale.payment_method === 4) {
            $("#details-card").hide();
            $("#details-boleto").hide();
        }

        $("#checkout-attempts").hide();
        if (sale.payment_method === 1) {
            $("#checkout-attempts").text("Quantidade de tentativas: " + sale.attempts).show();
        }

        if (
            (sale.payment_method == 1 || sale.payment_method == 3 || (sale.payment_method == 4 && !sale.has_withdrawal)) &&
            (sale.status == 1 || sale.status == 8 || sale.status == 24) &&
            sale.userPermissionRefunded
        ) {
            if (sale.has_contestation) {
                $("#div_refund_transaction").html(
                    '<button disabled class="btn btn-danger btn-sm">Estorno desabilitado, venda está em disputa</button>'
                );
            } else {
                $("#div_refund_transaction").html(
                    '<button class="btn btn-danger btn-sm btn_refund_transaction" sale=' +
                    sale.id +
                    ">Estornar transação</button>"
                );
            }
        } else {
            $("#div_refund_transaction").html("");
        }

        if (sale.status == 7) {
            $("#div_refund_receipt").html(
                `<a class="btn btn-sm btn-primary" target="_blank" href="/sales/${sale.id}/refundreceipt">Comprovante de estorno</a>`
            );
        } else {
            $("#div_refund_receipt").html("");
        }

        if (sale.status == 2 || sale.status == 1) {
            if ( !sale.api_flag ) {
                $("#saleReSendEmail").show();
            } else {
                $("#saleReSendEmail").hide();
            }
        } else {
            $("#saleReSendEmail").hide();
        }

        if ( !sale.api_flag ) {
            $("#details-api").hide();
        } else {
            $("#details-api").show();
        }

//        if (
//            sale.payment_method == 2 &&
//            sale.status == 1 &&
//            sale.userPermissionRefunded
//        ) {
//            $("#div_refund_billet").html(
//                '<button class="btn btn-danger btn-sm btn_refund_billet" sale=' +
//                    sale.id +
//                    ">Estornar boleto</button>"
//            );
//        } else {
            $("#div_refund_billet").html("");
//        }
        if (sale.refund_observation != null) {
            $(".div-refund-observation").show();
            $("#refund-observation")
                .val(sale.refund_observation)
                .attr("sale", sale.id);
            if (sale.user_changed_observation) {
                $(".btn-edit-observation").show();
            } else {
                $(".btn-edit-observation").hide();
            }
        } else {
            $(".div-refund-observation").hide();
        }
        if (sale.thank_page_url != "") {
            $("#thank-page-url").text(sale.thank_label_text).show();
            $(".btn-copy-thank-page-url").attr("link", sale.thank_page_url);
            $(".btn-copy-thank-page-url").show();
        } else {
            $("#thank-page-url").hide();
            $(".btn-copy-thank-page-url").hide();
        }
        if (sale.delivery_id != "") {
            $("#div_delivery").css("display", "block");
        } else {
            $("#div_delivery").css("display", "none");
        }
        if (verifyAccountFrozen() == true) {
            $(".btn-edit-client").hide();
            $("#update-sale-observation").hide();
            $("#saleReSendEmail").hide();
            $("#div_refund_transaction").hide();
            $("#div_refund_billet").hide();
        } else {
            $(".btn-edit-client").show();
            $("#update-sale-observation").show();
            $("#div_refund_transaction").show();
            $("#div_refund_billet").show();
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
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
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
                let return_message =
                    invoice.return_message == null
                        ? "Sucesso"
                        : invoice.return_message;

                let status = invoice.return_message
                    ? "Erro ao enviar para Notazz"
                    : "Enviado para Notazz";
                let link = invoice.pdf
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
                let status = invoice.return_message
                    ? "Erro ao enviar para Notazz"
                    : "Enviado para Notazz";

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
                let postback_message =
                    invoice.postback_message == null
                        ? "Rejeitado"
                        : invoice.postback_message;

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
                let link = invoice.pdf
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
                $("#div_notazz_schedule").html(
                    "Próxima tentativa de envio em " + invoice.schedule
                );
            }

            $("#div_notazz_invoice").show();
        } else {
            //not exist
            $("#div_notazz_invoice").hide();
        }
    }

    function getClient(client, sale) {
        $.ajax({
            method: "GET",
            url: "/api/customers/" + client + '/' + sale,
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
        $("#client-telephone")
            .val(client.telephone)
            .attr("client", client.code)
            .mask('+00 (00) 00000-0000');
        $("#client-email").val(client.email).attr("client", client.code);
        $("#client-document").text("CPF: " + client.document);
        if(client.fraudster) {
            $("#client-whatsapp-container").hide();
            $('.btn-edit-client').hide();
        }else{
            $('.btn-edit-client').show();
            $("#client-whatsapp-container").show();
            $("#client-whatsapp").attr("href", client.whatsapp_link);
        }
    }

    function getProducts(sale) {
        $("#table-product").html("");
        $("#data-tracking-products").html("");

        $.ajax({
            method: "GET",
            url: "/api/products/saleproducts/" + sale.id,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (!sale.api_flag) {
                    renderProducts(response.data, sale);
                } else {
                    renderProductsApi(response.data);
                }
            },
        });
    }

    function renderProductsApi(products) {
        let div = "";
        $.each(products, function (index, value) {
            div += '<div class="row align-items-baseline justify-content-between mb-15">';
                div += '<div class="col-lg-2">';
                    div += '<img src="/modules/global/img/produto.svg" width="50px" style="border-radius: 6px;">';
                div += '</div>';
                div += '<div class="col-md-5 col-lg-6">';
                    div += '<h4 class="table-title mb-0">' + value.name + '</h4>';
                    div += '<small>' + value.description + '</small>';
                div += '</div>';
                div += '<div class="col-md-3 col-lg-2 text-right">';
                    div += '<p class="sm-text text-muted">' + value.amount + 'x</p>';
                div += '</div>';
            div += '</div>';

            $("#table-product").html(div);
        });

        loadOnAny("#modal-saleDetails", true);
    }

    function renderProducts(products, sale) {
        let div = "";
        let photo = "/modules/global/img/produto.svg";
        $.each(products, function (index, value) {
            if (!value.photo) {
                value.photo = photo;
            }
            div += `<div class="row align-items-baseline justify-content-between mb-15">
                        <div class="col-lg-2">
                            <img src='${value.photo}' onerror=this.src='/modules/global/img/produto.png' width='50px' style='border-radius: 6px;'>
                        </div>
                        <div class="col-md-5 col-lg-6">
                            <h4 class="table-title mb-0">${value.name}</h4>
                            <small>${value.description}</small>
                        </div>
                        <div class="col-md-3 col-lg-2 text-right">
                            <p class="sm-text text-muted">${value.amount}x</p>
                        </div>
                    </div>`;

            if (typeof value.custom_products != 'undefined' && value.custom_products.length > 0) {
                div += `<!-- Customer additional information -->
                    <div class="panel-group my-30" aria-multiselectable="true" role="tablist">
                        <div class="panel panel-custom-product">
                            <div class="panel-heading" id="sale-custom-product-accordion-${value.sale_id}${value.id}${index}" role="tab">
                                <a class="panel-title" data-toggle="collapse" href="#sale-custom-product-${value.sale_id}${value.id}${index}"
                                data-parent="#custom-product-accordion${value.id}" aria-expanded="true"
                                aria-controls="exampleCollapseDefaultOne">
                                    <strong>Personalizações enviadas pelo cliente</strong>
                                </a>
                            </div>
                            <div class="panel-collapse collapse" id="sale-custom-product-${value.sale_id}${value.id}${index}"
                                aria-labelledby="sale-custom-product-accordion-${value.sale_id}${value.id}${index}" role="tabpanel" style="">
                                <div class="panel-body">`;
                var file_name = null;
                line_temp = 0;
                $.each(value.custom_products, function (index2, custom) {
                    if (line_temp != custom.line) {
                        div += `<hr/>`;
                        line_temp = custom.line;
                    }
                    div += `<div class="row mt-2">`;

                    if (typeof custom.type_enum != 'undefined') {
                        if (custom.type_enum != 'Text') {
                            file_name = custom.file_name.substr(-20);
                        }
                        switch (custom.type_enum) {
                            case 'Text':
                                div += `
                                                        <div class="col-md-3">
                                                            <img src="/modules/global/img/custom-product/icon_text.svg" class="img-fluid border-icon">
                                                        </div>
                                                        <div class="col-md-6 px-0 py-13">
                                                            <h5>${custom.value}</h5>
                                                        </div>
                                                        <div class="col-md-3 pl-0 py-11" align="right">
                                                            <a role="button" class="copy_link btn-copy-custom-text" style="cursor: pointer;"  link="${custom.value}" title="Copiar link">
                                                                <span class="material-icons icon-copy-1"> content_copy </span>
                                                            </a>
                                                        </div>`;
                                break;
                            case 'File':
                                div += `
                                                    <div class="col-md-3">
                                                        <img src="/modules/global/img/custom-product/icon_attachment.svg" class="img-fluid border-icon" />
                                                    </div>
                                                    <div class="col-md-6 px-0 py-13">
                                                        <h5>${file_name}</h5>
                                                    </div>
                                                    <div class="col-md-3 pl-0 py-11" align="right">
                                                        <a href="${custom.value}" style="cursor: pointer;" download="${file_name}" title="Baixar Arquivo" target="_blank">
                                                            <img src="/modules/global/img/custom-product/icon_download.png" class="img-fluid" />
                                                        </a>
                                                    </div>`;
                                break;
                            case 'Image':
                                div += `
                                                    <div class="col-md-3">
                                                        <img src="/modules/global/img/custom-product/icon_image.svg" class="img-fluid border-icon">
                                                    </div>
                                                    <div class="col-md-6 px-0 py-13">
                                                        <h5>${file_name}</h5>
                                                    </div>
                                                    <div class="col-md-3 pl-0 py-11" align="right">
                                                        <a href="${custom.value}" style="cursor: pointer;" download="${file_name}" title="Baixar Imagem"  target="_blank">
                                                            <img src="/modules/global/img/custom-product/icon_download.png" class="img-fluid" />
                                                        </a>
                                                    </div>`;
                                break;
                        }

                    }

                    div += `</div>`;

                });

                div += `
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Customer additional information -->
                `;
            }

            $("#table-product").html(div);

            //Tabela de produtos Tracking Code
            if (
                (value.sale_status == 1 || value.sale_status == 4) &&
                sale.delivery_id != ""
            ) {
                let data = `<tr>
                                <td>
                                    <img src='${value.photo}'  width='35px;' style='border-radius:6px;'><br>
                                    <span class='small ellipsis'>${value.name}</span>
                                </td>
                                <td>
                                    <span class="small font-weight-bold">${value.tracking_code}</span>
                                </td>
                                <td>
                                    <span class='tracking-status-span small'>${value.tracking_status_enum}</span>
                                </td>
                                <td>
                                    <span class='small'>${value.tracking_created_at}</span>
                                </td>
                            </tr>`;
                $("#div_tracking_code").css("display", "block");
                $("#data-tracking-products").append(data);
            } else {
                $("#div_tracking_code").css("display", "none");
            }
        });

        loadOnAny("#modal-saleDetails", true);
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
        let deliveryAddress =
            "Endereço: " + delivery.street + ", " + delivery.number;
        if (!isEmpty(delivery.complement)) {
            deliveryAddress += ", " + delivery.complement;
        }
        $("#delivery-address").text(deliveryAddress);
        $("#delivery-neighborhood").text("Bairro: " + delivery.neighborhood);
        $("#delivery-zipcode").text("CEP: " + delivery.zip_code);
        $("#delivery-city").text(
            "Cidade: " + delivery.city + "/" + delivery.state
        );
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
        $("#checkout-operational-system").text(
            "Dispositivo: " + checkout.operational_system
        );
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

    //Estornar venda
    function refundedClick(
        sale,
        refunded_value = 0,
        partial = 0,
        refundObservation
    ) {
        loadingOnChart("#modal-refund");
        $.ajax({
            method: "POST",
            url: "/api/sales/refund/" + sale,
            data: {
                refunded_value: refunded_value,
                partial: partial,
                refund_observation: refundObservation,
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnChartRemove(".sirius-loading");
                $("#modal-refund-transaction").modal('toggle')
                errorAjaxResponse(response);
                atualizar(currentPage);
            },
            success: (response) => {
                loadingOnChartRemove(".sirius-loading");
                $("#modal-refund-transaction").modal('toggle')
                alertCustom("success", response.message);
                $("#refund_observation").val("");
                atualizar(currentPage);
            },
        });
    }

    //Estornar boleto

    function refundedBilletClick(sale, refunded_value) {
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/sales/refund/billet/" + sale,
            data: {refunded_value: refunded_value},
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
                atualizar(currentPage);
            },
            success: (response) => {
                loadingOnScreenRemove();
                alertCustom("success", response.message);
                atualizar(currentPage);
            },
        });
    }

    //Gera ordem shopify
    function newOrderClick(sale) {
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/sales/newordershopify/" + sale,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
                atualizar(currentPage);
            },
            success: (response) => {
                loadingOnScreenRemove();
                alertCustom("success", response.message);
                atualizar(currentPage);
            },
        });
    }

    //Gera ordem woocommerce
    function newOrderWooClick(sale) {
        
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/sales/neworderwoocommerce/" + sale,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
                atualizar(currentPage);
            },
            success: (response) => {
                loadingOnScreenRemove();
                alertCustom("success", response.message);
                atualizar(currentPage);
            },
        });
    }

    $(document).on("click", "#btnSaleReSendEmail", function () {
        saleReSendEmail($("#sale-code").text());
    });

    // reenvia email da venda para o cliente
    function saleReSendEmail(sale) {
        let btnSaleReSendEmail = $("#btnSaleReSendEmail");
        if (!btnSaleReSendEmail.hasClass("sending")) {
            btnSaleReSendEmail.css("opacity", ".5").addClass("sending");
            $.ajax({
                method: "POST",
                url: "/api/sales/saleresendemail",
                dataType: "json",
                data: {sale: sale},
                headers: {
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                error: (response) => {
                    btnSaleReSendEmail
                        .css("opacity", "1")
                        .removeClass("sending");
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    btnSaleReSendEmail
                        .css("opacity", "1")
                        .removeClass("sending");
                    alertCustom("success", response.message);
                },
            });
        }
    }

    /* $('#').on('submit', function (e) {
         // validation code here
        if (!valid) {
            e.preventDefault();
        }
    });*/
});
