let statusShipping = {
    1: "success",
    0: "disable",
};

let activeShipping = {
    1: "success",
    0: "danger",
};

$(document).ready(function () {
    let projectId = $(window.location.pathname.split("/")).get(-1);

    loadMelhorEnvioOptions();

    function loadMelhorEnvioOptions() {
        $.ajax({
            url: "/api/apps/melhorenvio",
            data: {
                completed: 1,
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            success: (resp) => {
                let options = ``;
                for (let integration of resp.data) {
                    options += `<option class="menv${integration.id}" value="melhorenvio-${integration.id}">${integration.name} (integração com a API do Melhor Envio)</option>`;
                }
                $(".shipping-type select").each(function () {
                    $(this).append(options);
                });
            },
            error: (resp) => { },
        });
    }

    //comportamentos da tela
    $(".tab-fretes").on("click", function () {
        $(this).off();
        $("#previewimage").imgAreaSelect({ remove: true });
        atualizarFrete();
    });

    $(document).on("click", ".shipping-regions", function () {
        if ($(this).is(":checked")) {
            $("#shipping-multiple-value").hide();
            $("#shipping-single-value").show();
        } else {
            $("#shipping-multiple-value").show();
            $("#shipping-single-value").hide();
            $(".shipping-value1").trigger("focus");
        }
    });

    $(document).on("click", ".shipping-regions-edit", function () {
        if ($(this).is(":checked")) {
            $("#shipping-multiple-value-edit").hide();
            $("#shipping-single-value-edit").show();
        } else {
            $("#shipping-multiple-value-edit").show();
            $("#shipping-single-value-edit").hide();
            $(".shipping-value1-edit").trigger("focus");
        }
    });

    $(document).on("change", "#shipping-type", function () {
        // altera campo value dependendo do tipo do frete
        let selected = $(this).val();

        if (selected === "static") {
            $(".information-shipping-row").show();
            $(".value-shipping-row").show();
            $(".zip-code-origin-shipping-row").hide();
            $(".shipping-description").attr("placeholder", "Frete grátis");
        } else if (selected === "pac") {
            $(".information-shipping-row").show();
            $(".value-shipping-row").hide();
            $(".shipping-value").val("");
            $(".zip-code-origin-shipping-row").show();
            $(".shipping-description").attr("placeholder", "PAC");
        } else if (selected === "sedex") {
            $(".information-shipping-row").show();
            $(".value-shipping-row").hide();
            $(".shipping-value").val("");
            $(".zip-code-origin-shipping-row").show();
            $(".shipping-description").attr("placeholder", "SEDEX");
        } else if (selected.includes("melhorenvio")) {
            $(".information-shipping-row").hide();
            $(".value-shipping-row").hide();
            $(".shipping-value").val("");
            $(".zip-code-origin-shipping-row").show();
            $(".shipping-description").attr("placeholder", "Melhor Envio");
        }
    });

    $(".shipping-money-format").maskMoney({ thousands: ".", decimal: ",", allowZero: true, prefix: "R$ " });

    setSelect2Plugin("#shipping-plans-add", "#modal-create-shipping");
    setSelect2Plugin("#shipping-plans-edit", "#modal-edit-shipping");
    setSelect2Plugin("#shipping-not-apply-plans-add", "#modal-create-shipping");
    setSelect2Plugin("#shipping-not-apply-plans-edit", "#modal-edit-shipping");

    $(".check").on("click", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    //Limpa campos
    function clearFields() {
        $(".shipping-description").val("");
        $(".shipping-info").val("");
        $(".shipping-value").val("");
        $(".shipping-zipcode").val("");
        $(".rule-shipping-value").val("");
        $(".shipping-use-variants").val(1).prop("checked", true);
        $("#shipping-plans-add").html('<option value="all">Qualquer plano</option>').val("all").trigger("change");
        $("#shipping-not-apply-plans-add").html("");
    }

    clearFields();

    $(".shipping-description").keyup(function () {
        if ($(this).val().length > 60) {
            $(this).parent().children("#shipping-name-error").html("O campo descrição permite apenas 60 caracteres");
            return false;
        } else {
            $(this).parent().children("#shipping-name-error").html("");
        }
    });

    $(".shipping-info").keyup(function () {
        if ($(this).val().length > 100) {
            $(this)
                .parent()
                .children("#shipping-information-error")
                .html("O campo tempo de entrega estimado permite apenas 100 caracteres");
            return false;
        } else {
            $(this).parent().children("#shipping-information-error").html("");
        }
    });

    $(".shipping-value").keyup(function () {
        if ($.trim($(this).val()).length > 8) {
            $(this).parent().children("#shipping-value-error").html("O campo valor permite apenas 6  caracteres");
            return false;
        } else {
            $(this).parent().children("#shipping-value-error").html("");
        }
    });

    $(".shipping-use-variants").on("change", function () {
        const slider = $(this);
        const modal = slider.closest(".modal");

        const offerContainer = modal.find(".shipping-plans-add-container, .shipping-plans-edit-container");
        const notOfferContainer = modal.find(
            ".shipping-not-apply-plans-add-container, .shipping-not-apply-plans-edit-container"
        );

        const offerLabel = offerContainer.find("label");
        const notOfferLabel = notOfferContainer.find("label");

        const offerSelect = offerContainer.find("select");
        const notOfferSelect = notOfferContainer.find("select");

        let targetName = "";
        if (slider.prop("checked")) {
            offerLabel.text("Oferecer o frete para os planos:");
            notOfferLabel.text("Não oferecer o frete para os planos:");
            targetName = "plano";
        } else {
            offerLabel.text("Oferecer o frete para os produtos:");
            notOfferLabel.text("Não oferecer o frete para os produtos:");
            targetName = "produto";
        }

        offerSelect.html(`<option value="all">Qualquer ${targetName}</option>`).val("all").trigger("change");
        notOfferSelect.html("").val("").trigger("change");

        setSelect2Plugin(offerSelect, modal);
        setSelect2Plugin(notOfferSelect, modal);
    });

    // carregar modal de detalhes
    $(document).on("click", ".detalhes-frete", function () {
        let frete = $(this).attr("frete");
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/shippings/" + frete,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                switch (response.type) {
                    case "static":
                        $("#modal-detail-shipping #shipping-type").html("Estático");
                        break;
                    case "pac":
                        $("#modal-detail-shipping #shipping-type").html("PAC - Caculado automaticamente");
                        break;
                    case "sedex":
                        $("#modal-detail-shipping #shipping-type").html("SEDEX - Caculado automaticamente");
                        break;
                    case "melhorenvio":
                        $("#modal-detail-shipping #shipping-type").html("MelhorEnvio - Caculado automaticamente");
                        break;
                }
                $("#modal-detail-shipping .shipping-description").html(response.name);
                if (response.regions_values) {
                    var regions_values = JSON.parse(response.regions_values);
                    response.value = '<div class="row">';
                    for (i in regions_values) {
                        response.value += '<div class="col-7">';
                        response.value +=
                            regions_values[i].name + ': </div><div class="col-5">' + regions_values[i].value + "";
                        response.value += "</div>";
                    }
                    response.value += "</div>";
                }
                $("#modal-detail-shipping .shipping-value").html(response.type != "static" ? "" : response.value);
                $("#modal-detail-shipping .shipping-info").html(response.information);
                $("#modal-detail-shipping .rule-shipping-value").html(response.rule_value);
                $("#modal-detail-shipping .shipping-status").html(
                    response.status == 1
                        ? '<span class="badge badge-success text-left">Ativo</span>'
                        : '<span class="badge badge-danger">Desativado</span>'
                );
                $("#modal-detail-shipping .shipping-pre-selected").html(
                    response.pre_selected == 1
                        ? '<span class="badge badge-success">Sim</span>'
                        : '<span class="badge badge-primary">Não</span>'
                );

                $("#modal-detail-shipping").modal("show");
            },
        });
    });

    // carregar modal de edicao
    $(document).on("click", ".editar-frete", function () {
        let frete = $(this).attr("frete");
        $(this).attr("frete");

        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/shippings/" + frete + "/edit",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#modal-edit-shipping .shipping-id").val(response.id_code).change();

                $(".shipping-regions-edit").prop("checked", true);
                $("#shipping-multiple-value-edit").hide();
                $("#shipping-single-value-edit").show();

                switch (response.type) {
                    case "static":
                        $("#modal-edit-shipping #shipping-type").prop("selectedIndex", 0).change();
                        if (response.regions_values) {
                            $(".shipping-regions-edit").prop("checked", false);
                            $("#shipping-single-value-edit").hide();
                            $("#shipping-multiple-value-edit").show();
                            $(".regions_values").val(response.regions_values);
                            var regions_values = JSON.parse(response.regions_values);
                            for (i in regions_values) {
                                ii = i;
                                ii++;
                                $(".shipping-value" + ii + "-edit").val(regions_values[i].value);
                            }
                        }
                        break;
                    case "pac":
                        $("#modal-edit-shipping #shipping-type").prop("selectedIndex", 1).change();
                        break;
                    case "sedex":
                        $("#modal-edit-shipping #shipping-type").prop("selectedIndex", 2).change();
                        break;
                    case "melhorenvio":
                        $("#modal-edit-shipping .menv" + response.melhorenvio_integration_id).prop("selected", true);
                        $("#modal-edit-shipping #shipping-type").change();
                        break;
                }
                $("#modal-edit-shipping .shipping-description").val(response.name);
                $("#modal-edit-shipping .shipping-info").val(response.information);
                $("#modal-edit-shipping .shipping-value").val(!response.regions_values ? response.value : "0,00");
                $("#modal-edit-shipping .rule-shipping-value").val(response.rule_value).trigger("input");
                $("#modal-edit-shipping .shipping-zipcode").val(response.zip_code_origin);
                $("#modal-edit-shipping .shipping-status").prop("checked", !!response.status).change();
                $("#modal-edit-shipping .shipping-pre-selected").prop("checked", !!response.pre_selected).change();
                $("#modal-edit-shipping .shipping-use-variants").prop("checked", !!response.use_variants).change();

                // Seleciona a opção do select de acordo com o que vem do banco
                var applyOnPlansEl = $("#modal-edit-shipping .shipping-plans-edit");
                applyOnPlansEl.html("");
                var applyOnPlans = [];
                for (let plan of response.apply_on_plans) {
                    applyOnPlans.push(plan.id);
                    applyOnPlansEl.append(
                        `<option value="${plan.id}">${plan.name + (plan.description ? " - " + plan.description : "")
                        }</option>`
                    );
                }
                applyOnPlansEl.val(applyOnPlans).trigger("change");

                var notApplyOnPlansEl = $("#modal-edit-shipping .shipping-not-apply-plans-edit");
                notApplyOnPlansEl.html("");
                var notApplyOnPlans = [];
                for (let plan of response.not_apply_on_plans) {
                    notApplyOnPlans.push(plan.id);
                    notApplyOnPlansEl.append(
                        `<option value="${plan.id}">${plan.name + (plan.description ? " - " + plan.description : "")
                        }</option>`
                    );
                }
                notApplyOnPlansEl.val(notApplyOnPlans).trigger("change");

                $("#modal-edit-shipping").modal("show");
            },
        });
    });

    //carregar modal delecao
    $(document).on("click", ".excluir-frete", function (event) {
        event.preventDefault();

        let frete = $(this).attr("frete");

        //deletar frete
        $("#btn-delete-frete").unbind("click");
        $("#btn-delete-frete").on("click", function () {
            $.ajax({
                method: "DELETE",
                url: "/api/project/" + projectId + "/shippings/" + frete,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: function (response) {
                    errorAjaxResponse(response);
                },
                success: function success(data) {
                    atualizarFrete();

                    alertCustom("success", "Frete Removido com sucesso");
                },
            });
        });
    });

    //cria novo frete
    $("#modal-create-shipping .btn-save").click(function () {
        if ($(".shipping-regions").is(":visible") == true && $(".shipping-regions").is(":checked") == false) {
            var regions_values = [];
            var regions = ["NORTE", "NORDESTE", "CENTRO-OESTE", "SUDESTE", "SUL"];
            for (i in regions) {
                var ii = i;
                ii++;
                regions_values.push({ name: regions[i], value: $(".shipping-value" + ii).val() });
            }

            for (i = 1; i <= 5; i++) {
                //if(isNaN(parseInt($('.shipping-value'+i).val()))) $('.shipping-value'+i).val(0);
                if (!$(".shipping-value" + i).val()) {
                    var ii = i;
                    ii--;
                    alertCustom("error", "Preencha um valor para a regição " + regions[ii] + "");
                    return false;
                }
            }
            $("#regions_values").val(JSON.stringify(regions_values));
        }
        if ($(".shipping-regions").is(":visible") == true && $(".shipping-regions").is(":checked") == true) {
            //if(isNaN(parseInt($('#shipping-single-value > input').val()))) $('#shipping-single-value > input').val(0);

            $("#regions_values").val("");
        }

        let formData = new FormData(document.getElementById("form-add-shipping"));
        formData.set("status", $("#form-add-shipping .shipping-status").is(":checked") ? 1 : 0);
        formData.set("pre_selected", $("#form-add-shipping .shipping-pre-selected").is(":checked") ? 1 : 0);
        formData.set("use_variants", $("#form-add-shipping .shipping-use-variants").is(":checked") ? 1 : 0);

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/shippings",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(data) {
                alertCustom("success", data.message);
                atualizarFrete();
                clearFields();
            },
        });
    });

    //atualizar frete
    $("#modal-edit-shipping .btn-update").on("click", function () {
        if ($(".shipping-regions-edit").is(":visible") == true && $(".shipping-regions-edit").is(":checked") == false) {
            var regions_values = [];
            var regions = ["NORTE", "NORDESTE", "CENTRO-OESTE", "SUDESTE", "SUL"];
            for (i in regions) {
                var ii = i;
                ii++;
                regions_values.push({ name: regions[i], value: $(".shipping-value" + ii + "-edit").val() });
            }

            for (i = 1; i <= 5; i++) {
                //if(isNaN(parseInt($('.shipping-value'+i+'-edit').val()))) $('.shipping-value'+i+'-edit').val(0);
                if (!$(".shipping-value" + i + "-edit").val()) {
                    var ii = i;
                    ii--;
                    alertCustom("error", "Preencha um valor para a regição " + regions[ii] + "");
                    return false;
                }
            }
            var json = JSON.stringify(regions_values);

            $(".regions_values").val(json);
        }

        if ($(".shipping-regions-edit").is(":visible") == true && $(".shipping-regions-edit").is(":checked") == true) {
            $(".regions_values").val("");
        }
        if ($(".shipping-regions-edit").is(":checked") == false) {
            $(".shipping-value").val(0);
        }

        let formData = new FormData(document.querySelector("#modal-edit-shipping #form-update-shipping"));
        formData.set("status", $("#modal-edit-shipping .shipping-status").is(":checked") ? 1 : 0);
        formData.set("pre_selected", $("#modal-edit-shipping .shipping-pre-selected").is(":checked") ? 1 : 0);
        formData.set("use_variants", $("#modal-edit-shipping .shipping-use-variants").is(":checked") ? 1 : 0);
        let frete = $("#modal-edit-shipping .shipping-id").val();

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/shippings/" + frete,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success() {
                alertCustom("success", "Frete atualizado com sucesso");
                atualizarFrete();
            },
        });
    });

    function atualizarFrete() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = "/api/project/" + projectId + "/shippings";
        } else {
            link = "/api/project/" + projectId + "/shippings" + link;
        }

        loadOnTable("#dados-tabela-frete", "#tabela_fretes");
        $("#pagination-shippings").children().attr("disabled", "disabled");

        $("#tab-fretes-panel").find(".no-gutters").css("display", "none");
        $("#tabela-fretes").find("thead").css("display", "none");

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                $("#dados-tabela-frete").html(response.message);
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#dados-tabela-frete").html("");

                if (response.data == "") {
                    $("#pagination-container-shipping").removeClass("d-flex").addClass("d-none")

                    $("#dados-tabela-frete").html(`
                        <tr class='text-center'>
                            <td colspan='8' style='height: 70px; vertical-align: middle;'>
                                <div class='d-flex justify-content-center align-items-center'>
                                    <img src='/build/global/img/empty-state-table.svg' style='margin-right: 60px;'>
                                    <div class='text-left'>
                                        <h1 style='font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;'>Nenhum frete configurado</h1>
                                        <p style='font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;'>Cadastre o seu primeiro frete para poder
                                        <br>gerenciá-los nesse painel.</p>
                                        <button type='button' style='width: auto; height: auto; padding: .429rem 1rem !important;' class='btn btn-primary add-shipping' data-toggle="modal" data-target="#modal-create-shipping">Adicionar frete</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                } else {
                    $("#pagination-container-shipping").removeClass("d-none").addClass("d-flex")

                    $("#tab-fretes-panel").find(".no-gutters").css("display", "flex");
                    $("#tabela-fretes").find("thead").css("display", "contents");
                    $("#count-fretes").html(response.meta.total);

                    $.each(response.data, function (index, value) {
                        let dados =
                            `<tr>
                                <td style="vertical-align: middle; display: none;">
                                    ${value.zip_code_origin}
                                </td>


                                <td class="text-nowrap" style="vertical-align: middle;">

                                    <div class="fullInformation-shipping ellipsis-text">
                                        ${value.type_name}
                                    </div>

                                    <div class="container-tooltips-shipping"></div>

                                </td>


                                <td>

                                    <div class="fullInformation-shipping ellipsis-text">
                                        ${value.name}
                                    </div>

                                </td>


                                <td class="text-nowrap" style="vertical-align: middle;">

                                    <div class="fullInformation-shipping ellipsis-text">
                                        ${value.value}
                                    </div>

                                </td>


                                <td class="text-nowrap" style="vertical-align: middle;">

                                    <div class="fullInformation-shipping ellipsis-text">
                                        ${value.information}
                                    </div>

                                </td>


                                <td class="text-center" style="vertical-align: middle;">
                                    <span class="badge badge-${statusShipping[value.status]}">
                                        ${value.status_translated}
                                    </span>
                                </td>


                                <td class="text-center display-sm-none display-m-none" style="vertical-align: middle;">
                                    <span class="badge badge-${activeShipping[value.pre_selected]}">${value.pre_selected_translated}</span>
                                </td>


                                <td style='text-align:center'>

                                    <div class='d-flex justify-content-end align-items-center'>
                                        <a role='button' title='Visualizar' class='pointer detalhes-frete mg-responsive' frete="${value.shipping_id}">
                                            <span class="">
                                                <img src='/build/global/img/icon-eye.svg'/>
                                            </span>
                                        </a>

                                        <a role='button' title='Editar' class='pointer editar-frete mg-responsive' frete="${value.shipping_id}">
                                            <span class=''>
                                                <img src='/build/global/img/pencil-icon.svg'/>
                                            </span>
                                        </a>

                                        <a role='button' title='Excluir' class='pointer excluir-frete mg-responsive' frete="${value.shipping_id}" data-toggle='modal' data-target='#modal-delete-shipping'>
                                            <span class=''>
                                                <img src='/build/global/img/icon-trash-tale.svg'/>
                                            </span>
                                        </a>

                                    </div>

                                </td>

                            </tr>
                        `;

                        $("#dados-tabela-frete").append(dados);
                    });


                    $('.fullInformation-shipping').bind('mouseover', function () {
                        var $this = $(this);

                        if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
                            $this.attr({
                                'data-toggle': "tooltip",
                                'data-placement': "top",
                                'data-title': $this.text()
                            }).tooltip({ container: ".container-tooltips-shipping" })
                            $this.tooltip("show")
                        }
                    });



                    if ($("#dados-tabela-frete").children("tr:first").children("td:first").css("display") == "none") {
                        $("#dados-tabela-frete").children("tr").children("td:nth-child(2)").css("padding-left", "30px");
                    } else {
                        $("#dados-tabela-frete").children("tr").children("td:nth-child(2)").css("padding-left", "");
                    }

                    pagination(response, "shippings", atualizarFrete);
                }
            },
        });

        if (!["shopify", "woocommerce"].includes($("#project_type").val())) {
            $(".shipping-use-variants").prop("checked", false).val(0).closest(".switch-holder").hide();
        }
    }

    function setSelect2Plugin(el, dropdownParent) {
        el = $(el);

        const useVariants = el.closest("form").find(".shipping-use-variants").prop("checked") ? 1 : 0;
        const targetName = useVariants ? "plano" : "produto";

        el.select2({
            placeholder: `Nome do ${targetName}`,
            multiple: true,
            closeOnSelect: false,
            dropdownParent: $(dropdownParent),
            language: {
                noResults: function () {
                    return `Nenhum ${targetName} encontrado`;
                },
                searching: function () {
                    return "Procurando...";
                },
                loadingMore: function () {
                    return `Carregando mais ${targetName}s...`;
                },
            },
            ajax: {
                data: function (params) {
                    return {
                        list: "plan",
                        search: params.term,
                        project_id: projectId,
                        page: params.page || 1,
                        variants: useVariants,
                        active_flag: 1,
                    };
                },
                method: "GET",
                url: "/api/plans/user-plans",
                delay: 300,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                processResults: function (res) {
                    let elemId = this.$element.attr("id");
                    if (
                        (elemId === "shipping-plans-add" || elemId === "shipping-plans-edit") &&
                        res.meta.current_page === 1
                    ) {
                        let allObject = {
                            id: "all",
                            name: `Qualquer ${targetName}`,
                            description: "",
                        };
                        res.data.unshift(allObject);
                    }

                    return {
                        results: $.map(res.data, function (obj) {
                            return { id: obj.id, text: obj.name + (obj.description ? " - " + obj.description : "") };
                        }),
                        pagination: {
                            more: res.meta.current_page !== res.meta.last_page,
                        },
                    };
                },
            },
        });

        $(document).on("show.bs.modal", "#modal-create-shipping", () => {
            $("#modal-create-shipping #shipping-type").val("static").change();
        });

        el.on("select2:select", function () {
            let selectPlan = $(this);
            if (
                (selectPlan.val().length > 1 && selectPlan.val().includes("all")) ||
                (selectPlan.val().includes("all") && selectPlan.val() !== "all")
            ) {
                selectPlan.val("all").trigger("change");
            }
        });
    }
});
