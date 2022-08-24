var exportFormat = null;

$(document).ready(function () {

    $('.company-navbar').change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#project").find('option').not(':first').remove();
        $("#plan").find('option').not(':first').remove();
        $("#project").val($("#project option:first").val());
        $("#plan").val($("#plan option:first").val());
        $('#plan').data('select2').results.clear();
        loadOnTable("#table_data", "#carrinhoAbandonado");
        updateCompanyDefault().done(function(data){
            getCompaniesAndProjects().done(function(data2){
                if(!isEmpty(data2.company_default_projects)){
                    $('#export-excel').show();
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    fillProjectsSelect(data2.companies)
                    updateSalesRecovery();
                }
                else{
                    $('#export-excel').hide();
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                }
            });
        });
    });

    function fillProjectsSelect(data){
        $.ajax({
            method: "GET",
            url: "/api/recovery/projects-with-recovery",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                console.log('erro')
                console.log(response)
            },
            success: function success(response) {
                return response;
            }
        }).done(function(dataSales){
            $.each(data, function (c, company) {
                //if( data2.company_default == company.id){
                    $.each(company.projects, function (i, project) {
                        if( dataSales.includes(project.id) )
                            $("#project").append($("<option>", {value: project.id,text: project.name,}));
                    });
                //}
            });
        });
    }

    getCompaniesAndProjects().done( function (data){console.log(data)
        if(!isEmpty(data.company_default_projects)){
            getProjects(data);
        }
        else{
            $('#export-excel').hide()
            $("#project-empty").show();
            $("#project-not-empty").hide();
            loadingOnScreenRemove();
        }

    });

    //APLICANDO FILTRO MULTIPLO EM ELEMENTOS COM A CLASS (applySelect2)
    $(".applySelect2").select2({
        width: "100%",
        multiple: true,
        language: {
            noResults: function () {
                return "Nenhum resultado encontrado";
            },
            searching: function () {
                return "Procurando...";
            },
        },
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        loadData();
    });

    $("#bt_get_csv").on("click", function () {
        $("#modal-export-sale").modal("show");
        exportFormat = "csv";
    });

    $("#bt_get_xls").on("click", function () {
        $("#modal-export-sale").modal("show");
        exportFormat = "xls";
    });

    $(".btn-confirm-export-sale").on("click", function () {
        var regexEmail = new RegExp(
            /^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/
        );
        var email = $("#email_export").val();

        if (email == "" || !regexEmail.test(email)) {
            alertCustom("error", "Preencha o email corretamente");
            return false;
        } else {
            salesExport(exportFormat);
            $("#modal-export-sale").modal("hide");
        }
    });

    let startDate = moment().subtract(30, "days").format("YYYY-MM-DD");
    let endDate = moment().format("YYYY-MM-DD");
    $("#date-range-sales-recovery").daterangepicker(
        {
            startDate: moment().subtract(30, "days"),
            endDate: moment(),
            opens: "center",
            maxDate: moment().endOf("day"),
            alwaysShowCalendar: true,
            showCustomRangeLabel: "Customizado",
            autoUpdateInput: true,
            locale: {
                locale: "pt-br",
                format: "DD/MM/YYYY",
                applyLabel: "Aplicar",
                cancelLabel: "Limpar",
                fromLabel: "De",
                toLabel: "Até",
                customRangeLabel: "Customizado",
                weekLabel: "W",
                daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
                monthNames: [
                    "Janeiro",
                    "Fevereiro",
                    "Março",
                    "Abril",
                    "Maio",
                    "Junho",
                    "Julho",
                    "Agosto",
                    "Setembro",
                    "Outubro",
                    "Novembro",
                    "Dezembro",
                ],
                firstDay: 0,
            },
            ranges: {
                Hoje: [moment(), moment()],
                Ontem: [
                    moment().subtract(1, "days"),
                    moment().subtract(1, "days"),
                ],
                "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                "Este mês": [
                    moment().startOf("month"),
                    moment().endOf("month"),
                ],
                "Mês passado": [
                    moment().subtract(1, "month").startOf("month"),
                    moment().subtract(1, "month").endOf("month"),
                ],
                Vitalício: [moment("2018-01-01 00:00:00"), moment()],
            },
        },
        function (start, end) {
            startDate = start.format("YYYY-MM-DD");
            endDate = end.format("YYYY-MM-DD");
        }
    );

    /**
     * Busca os lojas para montar o select
     */
    function getProjects(data) {
        loadingOnScreen();

        $("#project-empty").hide();
        $("#project-not-empty").show();
        $("#export-excel").show();
        fillProjectsSelect(data.companies)
        $("#project").val($("#project option:first").val());
        $("#plan").val($("#plan option:first").val());
        updateSalesRecovery();

        loadingOnScreenRemove();
    }

    /**
     * Formata url
     * @param link
     */
    function urlDataFormatted(link) {
        let url = "";
        if (link == null) {
            url = `?project=${$("#project").val()}&recovery_type=${$(
                "#recovery_type option:selected"
            ).val()}&date_range=${$(
                "#date-range-sales-recovery"
            ).val()}&client=${$(
                "#client-name"
            ).val()}&date_type=created_at&client_document=${$(
                "#client-cpf"
            ).val()}&plan=${$("#plan").val()}`;
        } else {
            url = `${link}&project=${$("#project").val()}

            &recovery_type=${$("#recovery_type option:selected").val()}

            &date_range=${$("#date-range-sales-recovery").val()}

            &client=${$("#client-name").val()}

            &date_type=created_at&client_document=${$("#client-cpf").val()}

            &plan=${$("#plan").val()}`;
        }
        url += "&company="+ $('.company-navbar').val();

        let recoveryTypeSelected = $("#recovery_type option:selected").val();
        if (recoveryTypeSelected == 1) {
            return `/api/checkout${url}`;
        } else if (recoveryTypeSelected == 3) {
            return `/api/recovery/getrefusedcart${url}`;
        } else if (recoveryTypeSelected == 4) {
            return `/api/recovery/get-pix${url}`;
        } else if (recoveryTypeSelected == 5) {
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
        loadOnTable("#table_data", "#carrinhoAbandonado");

        // Formata a url
        link = urlDataFormatted(link);

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {console.log(response)
                const BOLETO_TYPE = '5'

                $("#table_data").html("");
                $("#carrinhoAbandonado").addClass("table-striped");

                let recoveryType = $("#recovery_type")
                    .children("option:selected")
                    .text()
                    .toLowerCase();
                let image = $("#table_data").attr("img-empty");
                if (response.data == "" && recoveryType) {
                    $("#pagination-salesRecovery").hide();
                    $("#table_data").html(
                        `<tr>
                            <td colspan='11' class='text-center' style='vertical-align: middle;height:257px;'>
                                <img style='width:124px;margin-right:12px;' src=${image}>
                                Nenhum ${recoveryType} encontrado
                            </td>
                        </tr>`
                    );
                } else {
                    createHTMLTable(response);
                    $("#pagination-salesRecovery").show();
                    pagination(response, "salesRecovery", updateSalesRecovery);

                    $(".copy_link").on("click", function () {
                        var temp = $("<input>");
                        $("body").append(temp);
                        temp.val($(this).attr("link")).select();
                        document.execCommand("copy");
                        temp.remove();
                        alertCustom("success", "Link copiado!");
                    });

                    if ($("#recovery_type option:selected").val() == "5") {
                        if (verifyAccountFrozen() == false) {
                            $(".sale_status").hover(
                                function () {
                                    $(this).css("cursor", "pointer").text("Regerar");
                                    $(this).css("background", "#3D4456");
                                },
                                function () {
                                    var status = $(this).attr("status");
                                    $(this).removeAttr("style");
                                    $(this).text("Expirado");
                                }
                            );
                        }

                        $("#date").val(
                            moment(new Date())
                                .add(3, "days")
                                .format("YYYY-MM-DD")
                        );
                        $("#date").attr(
                            "min",
                            moment(new Date()).format("YYYY-MM-DD")
                        );

                        $(".sale_status").on("click", function () {
                            if (verifyAccountFrozen() == false) {
                                $("#saleId").val("");
                                let saleId = $(this).attr("sale_id");
                                $("#saleId").val(saleId);
                                $("#modal_regerar_boleto").modal("show");

                                $("#bt_send").unbind("click");
                                $("#bt_send").on("click", function () {
                                    loadingOnScreen();

                                    regenerateBoleto(saleId);
                                });
                            }
                        });
                    }
                    $(".details-cart-recovery").unbind("click");
                    $(".details-cart-recovery").on("click", function () {
                        ajaxDetails($(this).data("venda"));
                    });

                    $(".estornar_venda").unbind("click");
                    $(".estornar_venda").on("click", function () {
                        id_venda = $(this).attr("venda");

                        $("#modal_estornar_titulo").html(
                            "Estornar venda #" + id_venda + " ?"
                        );
                        $("#modal_estornar_body").html("");
                    });
                }
            },
            complete: (response) => {
                unlockSearch($("#bt_filtro"));
            },
        });
    }

    function loadData() {
        elementButton = $("#bt_filtro");
        if (searchIsLocked(elementButton) != "true") {
            lockSearch(elementButton);
            console.log(elementButton.attr("block_search"));
            updateSalesRecovery();
        }
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
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: {
                saleId: saleId,
                date: $("#date").val(),
                discountType: $("#discount_type").val(),
                discountValue: $("#discount_value").val(),
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success() {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                window.location = "/sales";
            },
        });
    }

    /**
     *
     * @param response
     */
    function createHTMLTable(response) {
        let html = "";
        $.each(response.data, function (index, value) {
            if (value.type === "cart_refundend") {
                html += createHtmlOthers(value);
            } else if (value.type === "expired") {
                html += createHtmlOthers(value);
            } else if (typeof value.sale_code === "undefined") {
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
        let data = "";
        data += "<tr>";
        data +=
            "<td class='display-sm-none display-m-none display-lg-none'>" +
            value.date +
            "</td>";
        data += "<td>" + value.project + "</td>";
        data +=
            "<td class='display-sm-none display-m-none'>" +
            value.client +
            "</td>";
        data +=
            "<td>" +
            value.email_status +
            " " +
            setSend(value.email_status) +
            "</td>";
        data +=
            "<td>" +
            value.sms_status +
            " " +
            setSend(value.sms_status) +
            "</td>";
        data +=
            "<td><span class='sale_status badge badge-" +
            statusRecovery[value.status_translate] +
            "' status='" +
            value.status_translate +
            "' sale_id='" +
            value.id +
            "'>" +
            value.status_translate +
            "</span></td>";
        data += "<td>" + value.value + "</td>";
        data +=
            "<td class='display-sm-none' align='center'> <a href='" +
            value.whatsapp_link +
            "' target='_blank' title='Enviar mensagem pelo whatsapp'><span class='o-whatsapp-1'></span></a></td>";
        data +=
            "<td style='padding:0 !important;' class='display-sm-none text-center' align='center'> <a role='button' class='copy_link' style='cursor:pointer;' link='" +
            value.link +
            "' title='Copiar link'><span class=''> <img src='build/global/img/icon-copy-table.svg'/> </span></a></td>";
        data +=
            "<td class='display-sm-none' align='center'> <a role='button' class='details-cart-recovery' style='cursor:pointer;' data-venda='" +
            value.id +
            "' ><span> <img src='/build/global/img/icon-eye.svg'> </span></button></td>";
        data += "</tr>";

        return data;
    }

    /**
     * Cria html quando for boleto vencido ou cartão recusado
     * @param value
     * @returns {string}
     */
    function createHtmlOthers(value) {
        let data = "";
        data += "<tr>";
        data +=
            "<td class='display-sm-none display-m-none display-lg-none'>" +
            value.start_date +
            "</td>";
        data += "<td>" + value.project + "</td>";
        data +=
            "<td class='display-sm-none display-m-none'>" +
            value.client +
            "</td>";
        data +=
            "<td>" +
            value.email_status +
            " " +
            setSend(value.email_status) +
            "</td>";
        data +=
            "<td>" +
            value.sms_status +
            " " +
            setSend(value.sms_status) +
            "</td>";
        data +=
            "<td><span class='sale_status badge badge-" +
            statusRecovery[value.recovery_status] +
            "' sale_id='" +
            value.id_default +
            "'>" +
            value.recovery_status +
            "</span></td>";
        data += "<td>" + value.total_paid + "</td>";
        data +=
            "<td class='display-sm-none' align='center'> <a href='" +
            value.whatsapp_link +
            "' target='_blank' title='Enviar mensagem pelo whatsapp'><span class='o-whatsapp-1'></span></a></td>";
        data +=
            "<td class='display-sm-none text-right' style='padding:0!important' align='center'> <a role='button' class='copy_link' style='cursor:pointer;' link='" +
            value.link +
            "' title='Copiar link'><span class=''><img src='build/global/img/icon-copy-table.svg'/></span></a></td>";
        data +=
            "<td class='display-sm-none' align='center'> <a role='button' class='details-cart-recovery' style='cursor:pointer;' data-venda='" +
            value.id_default +
            "' ><span class='o-eye-1'></span></button></td>";
        data += "</tr>";

        return data;
    }

    // ajax modal details
    function ajaxDetails(sale) {
        $.ajax({
            method: "POST",
            url: "/api/recovery/details",
            data: { checkout: sale },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#table-product").html("");

                if (!isEmpty(response.data)) {
                    createHtmlDetails(response.data);
                } else {
                }
            },
        });
    }

    /**
     * Monta html da modal
     * @param data
     */
    function createHtmlDetails(data) {
        clearFields();

        $("#modal-title").html("Detalhes " + "<br><hr>");
        $("#date-as-hours").html(
            `${data.checkout.date} às ${data.checkout.hours}`
        );
        $("#status-checkout")
            .addClass("badge-" + statusRecovery[data.status])
            .html(data.status);

        /**
         * Produtos
         */
        let div = "";
        let photo = "public/build/global/img/produto.png";
        $.each(data.products, function (index, value) {
            if (!isEmpty(value.photo)) {
                photo = value.photo;
            }

            div +=
                '<div class="row align-items-baseline justify-content-between mb-15">' +
                '<div class="col-lg-2">' +
                "<img onerror=this.src='/build/global/img/produto.png' src='" +
                value.photo +
                "' width='50px' style='border-radius: 6px;'>" +
                "</div>" +
                '<div class="col-lg-5">' +
                '<h4 class="table-title">' +
                value.name +
                "</h4>\n" +
                "</div>" +
                '<div class="col-lg-3 text-right">' +
                '<p class="sm-text text-muted">' +
                value.amount +
                "x</p>" +
                "</div>" +
                "</div>";

            $("#table-product").html(div);
        });
        $("#total-value").html("R$ " + data.checkout.total);
        /**
         * Fim Produtos
         */

        /**
         * Dados do Cliente e dados da entrega quando for cartao recusado ou boleto expirado
         */
        $("#client-name-details").html("Nome: " + data.client.name);
        $("#client-telephone").html("Telefone: " + data.client.telephone);
        $("#client-whatsapp").attr("href", data.client.whatsapp_link);
        $("#client-email").html("E-mail: " + data.client.email);
        $("#client-document").html("CPF: " + data.client.document);
        $("#client-street").html("Endereço: " + data.delivery.street);
        $("#client-zip-code").html("CEP: " + data.delivery.zip_code);
        $("#client-city-state").html(
            "Cidade: " + data.delivery.city + "/" + data.delivery.state
        );
        $("#sale-motive").html("Motivo: " + data.client.error);

        if (
            data.method == "boletoCartao" &&
            data.delivery.street == "" &&
            data.delivery.zip_code == "" &&
            data.delivery.city == "" &&
            data.delivery.state == ""
        ) {
            $("#div_delivery").hide();
        } else {
            $("#div_delivery").show();
        }
        if (!isEmpty(data.link)) {
            $("#link-sale").html(
                'Link: <a role="button" class="copy_link" style="cursor:pointer;" link="' +
                    data.link +
                    '" title="Copiar link"><span class="o-copy-1"></span> </a> '
            );
        } else {
            $("#link-sale").html("Link: " + data.link);
        }

        $("#checkout-ip").html("IP: " + data.checkout.ip);

        $("#checkout-is-mobile").html(data.checkout.is_mobile);
        /**
         * Fim dados do Cliente
         */

        /**
         * Dados do checkout - UTM
         */
        $("#checkout-operational-system").html(
            "Sistema: " + data.checkout.operational_system
        );
        $("#checkout-browser").html("Navegador: " + data.checkout.browser);
        $("#checkout-src").html("SRC: " + data.checkout.src);
        $("#checkout-utm-source").html(
            "UTM Source: " + data.checkout.utm_source
        );
        $("#checkout-utm-medium").html(
            "UTM Medium: " + data.checkout.utm_medium
        );
        $("#checkout-utm-campaign").html(
            "UTM Campaign: " + data.checkout.utm_campaign
        );
        $("#checkout-utm-term").html("UTM Term: " + data.checkout.utm_term);
        $("#checkout-utm-content").html(
            "UTM Content: " + data.checkout.utm_content
        );
        /**
         * Fim dados do checkout
         */

        $("#modal_detalhes").modal("show");

        $(".copy_link").on("click", function () {
            var temp = $("<input>");
            $("#nav-tabContent").append(temp);
            temp.val($(this).attr("link")).select();
            document.execCommand("copy");
            temp.remove();
            alertCustom("success", "Link copiado!");
        });
    }

    $("#discount_value").mask("00%", { reverse: true });

    $("#apply_discount").on("click", function () {
        if ($("#div_discount").is(":visible")) {
            $("#div_discount").hide();
            $("#discount_value").val("");
        } else {
            $("#div_discount").show();

            $("#discount_type").on("change", function () {
                if ($("#discount_type").val() == "value") {
                    $("#discount_value")
                        .mask("#.###,#0", { reverse: true })
                        .removeAttr("maxlength");
                    $("#label_discount_value").html("Valor (ex: 20,00)");
                } else {
                    $("#discount_value").mask("00%", { reverse: true });
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
        Recuperado: "success",
        "Não recuperado": "danger",
        Recusado: "danger",
        Expirado: "danger",
    };

    var statusRecoverySale = {
        Cancelado: "danger",
        Recusado: "danger",
    };
    /**
     * @param sendNumber
     * @returns {string}
     */
    function setSend(sendNumber) {
        if (sendNumber === 1) {
            return "enviado";
        } else if (sendNumber > 1) {
            return "enviados";
        } else {
            return "";
        }
    }
    function getFilters(urlParams = false) {
        let data = {
            project: $("#project").val(),
            recovery_type: $("#recovery_type option:selected").val(),
            date_range: $("#date-range-sales-recovery").val(),
            client: $("#client-name").val(),
            client_document: $("#client-cpf").val(),
            plan: $("#plan").val(),
            date_type: "created_at",
        };

        Object.keys(data).forEach((value) => {
            if (Array.isArray(data[value])) {
                data[value] = data[value].filter((value) => value).join(",");
            }
        });

        if (urlParams) {
            let params = "";
            for (let param in data) {
                params += "&" + param + "=" + data[param];
            }
            return encodeURI(params);
        } else {
            return data;
        }
    }
    // Download do relatorio
    function salesExport(fileFormat) {
        let data = getFilters();
        data["format"] = fileFormat;
        data["email"] = $("#email_export").val();
        $.ajax({
            method: "POST",
            url: "/api/recovery/export",
            data: data,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#export-email").text(response.email);
                $("#alert-export").show().shake();
            },
        });
    }
    //COMPORTAMENTO DO FILTRO MULTIPLO
    function behaviorMultipleFilter(data, selectId) {
        var $select = $(`#${selectId}`);
        var valueToRemove = "all";
        var values = $select.val();

        if (data.id != "all" && data.id != "") {
            if (values) {
                var i = values.indexOf(valueToRemove);

                if (i >= 0) {
                    values.splice(i, 1);
                    $select.val(values).change();
                }
            }
        } else {
            if (values) {
                values.splice(0, values.lenght);
                $select.val(null).change();

                values.push("all");
                $select.val("all").change();
            }
        }
    }

    //NAO PERMITI QUE O FILTRO FIQUE VAZIO
    function deniedEmptyFilter(selectId) {
        let arrayValues = $(`#${selectId}`).val();
        let valueAmount = $(`#${selectId}`).val().length;

        if (valueAmount === 0) {
            arrayValues.push("all");
            arrayValues = $(`#${selectId}`).val("all").trigger("change");
        }
    }

    $(".applySelect2").on("select2:select", function (evt) {
        var data = evt.params.data;
        var selectId = $(this).attr("id");
        behaviorMultipleFilter(data, selectId);

        $(`#${selectId}`).focus().scrollTop(0);
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });

    $(".applySelect2").on("change", function () {
        let idTarget = $(this).attr("id");
        deniedEmptyFilter(idTarget);
    });

    $(document).on("focusout", ".select2-selection__rendered", function () {
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });

    $(document).on("focusin", ".select2-selection__rendered", function () {
        $(".select2-selection.select2-selection--multiple").scrollTop(0);
    });
    // FIM DO COMPORTAMENTO DO FILTRO

    //LISTA PLANOS DE ACORDO COM O PROJETO(S)
    $("#project").on("change", function () {
        let value = $(this).val();
        $("#plan").val(null).trigger("change");
        $('#plan').data('select2').results.clear();
    });

    //Search plan
    $("#plan").select2({
        language: {
            noResults: function () {
                return "Nenhum plano encontrado";
            },
            searching: function () {
                return "Procurando...";
            },
        },
        ajax: {
            data: function (params) {
                return {
                    list: "plan",
                    search: params.term,
                    project_id: $("#project").val(),
                    company: $(".company-navbar").val()
                };
            },
            method: "GET",
            url: "/api/sales/user-plans",
            delay: 300,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processResults: function (res) {
                result = $.map(res.data, function (obj) {
                    return {
                        id: obj.id,
                        text:
                            obj.name +
                            (obj.description ? " - " + obj.description : ""),
                    };
                });

                if (res.data.length > 0) {
                    result.splice(0, 0, {
                        id: "",
                        text: "Todos os Planos",
                    });
                }

                return {
                    results: result,
                };
            },
        },
    });
    function clearFields() {
        $("#status-checkout").removeClass("badge-success badge-danger");
        $("#client-whatsapp").attr("href", "");
        $(".clear-fields").empty();
        // $("#date-as-hours, #table-product, #total-value, #client-name-details, #client-telephone, #client-email, #client-document, #client-street, #client-zip-code, #client-city-state, #sale-motive, #link-sale, #checkout-ip, #checkout-is-mobile, #checkout-operational-system, #checkout-browser, #checkout-src, #checkout-utm-source, #checkout-utm-medium, #checkout-utm-campaign, #checkout-utm-term, #checkout-utm-content").html('');
    }

    $(".btn-light-1").click(function () {
        var collapse = $("#icon-filtro");
        var text = $("#text-filtro");

        text.fadeOut(10);
        if (
            collapse.css("transform") == "matrix(1, 0, 0, 1, 0, 0)" ||
            collapse.css("transform") == "none"
        ) {
            collapse.css("transform", "rotate(180deg)");
            text.text("Minimizar filtros").fadeIn();
        } else {
            collapse.css("transform", "rotate(0deg)");
            text.text("Filtros avançados").fadeIn();
        }
    });

    $(document).on("keypress", function (e) {
        if (e.keyCode == 13) {
            updateSalesRecovery();
        }
    });
});
