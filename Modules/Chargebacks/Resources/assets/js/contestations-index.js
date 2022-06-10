function updateAfterChangeCompany(){
    $("#project").find('option').not(':first').remove();
    //$("#project option:first").attr('selected','selected');
    //$("#project-select").next().next().empty();
    let companies = JSON.parse(sessionStorage.getItem('companies'));
    $.each(companies, function (c, company) {
        if( sessionStorage.getItem('company_default') == company.id){
            $.each(company.projects, function (i, project) {
                $("#project").append(
                    $("<option>", {
                        value: project.id,
                        text: project.name,
                    })
                );
            });
        }
    });
    window.atualizar();
    window.getTotalValues();
}

$(document).ready(function () {
    let statusObject = {
        1: "Aprovada",
        2: "Pendente",
        3: "Recusado",
        4: "ChargeBack",
        5: "Cancelada",
        6: "Em processo",
        7: "Estornada",
        8: "Estorno Parcial",
        10: "BlackList",
        20: "Revisão Antifraude",
        21: "Cancelada Antifraude",
        22: "Estornado",
        24: "Em disputa",
        99: "Erro Sistema",
        null: "Em Processo",
    };

    let badgeObject = {
        1: "badge-success",
        2: "badge-pendente",
        3: "badge-danger",
        4: "badge-danger",
        5: "badge-danger",
        6: "badge-secondary",
        7: "badge-danger",
        8: "badge-warning",
        10: "badge-dark",
        20: "badge-antifraude",
        21: "badge-danger",
        22: "badge-danger",
        24: "badge-antifraude",
        99: "badge-danger",
        null: "badge-primary",
    };

    $('#date_range').val(moment().subtract(29, 'days').format('DD/MM/YYYY') + ' - ' + moment().format('DD/MM/YYYY')).
    dateRangePicker({
        format: 'DD/MM/YYYY',
        endDate: moment().add(30, 'days'),
        customShortcuts: [
            {
                name: 'Hoje',
                dates: () => [moment().startOf('day').toDate(), new Date()]
            },
            {
                name: '7 dias',
                dates: () => [moment().subtract(6, 'days').toDate(), new Date()]
            },
            {
                name: '15 dias',
                dates: () => [moment().subtract(14, 'days').toDate(), new Date()]
            },
            {
                name: 'Último mês',
                dates: () => [moment().subtract(30, 'days').toDate(), new Date()]
            },
            {
                name: 'Próximo 30 dias',
                dates: () => [moment().add(30, 'days').toDate(), new Date()]
            },
            {
                name: 'Desde o início',
                dates: () => [moment('2018-01-01').toDate(), new Date()]
            }
        ]
    });

    function getFilters(urlParams = true) {
        let current_url = window.location.href;
        let vazio = current_url.includes("vazio") ? "true" : "";

        let data = {
            transaction: $("#transaction").val().split("#").join(""),
            project: $("#project").val() ?? "",
            customer: $("#customer").val() ?? "",
            customer_document: $("#customer_document").val() ?? "",
            date_range: $('#date_range').val().replace(" à ", " - ") ?? "",
            date_type: $("#date_type").val() ?? "",
            order_by_expiration_date: $("#expiration_date").is(":checked") ? 1 : 0,
            contestation_situation: $("#contestation_situation").val() ?? "",
            is_contested: $("#is_contested").val() ?? "",
            is_expired: $("#is_expired").val() ?? "",
            sale_approve: $("#sale_approve").is(":checked") ? 1 : 0,
            company: sessionStorage.getItem('company_default')
        };
        if (urlParams) {
            let params = "";
            let isFirst = true;
            for (let param in data) {
                params += `${isFirst ? "" : "&"}${param}=${data[param]}`;
                isFirst = false;
            }
            return encodeURI(params);
        }
        return data;
    }

    const addZeroLeft = (value) => value > 0 && value < 10 ? String(value).padStart(2, '0') : value

    function pagination(response) {
        $("#pagination").html("");

        if (response.meta.total <= response.meta.per_page) {
            return;
        }

        var primeira_pagina =
            "<button id='primeira_pagina' class='btn nav-btn'>1</button>";

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == "1") {
            $("#primeira_pagina").attr("disabled", true);
            $("#primeira_pagina").addClass("nav-btn");
            $("#primeira_pagina").addClass("active");
        }

        $("#primeira_pagina").on("click", function () {
            window.atualizar("?page=1");
        });

        for (x = 3; x > 0; x--) {
            if (response.meta.current_page - x <= 1) {
                continue;
            }

            $("#pagination").append(
                "<button id='pagina_" +
                    (response.meta.current_page - x) +
                    "' class='btn nav-btn'>" +
                    (response.meta.current_page - x) +
                    "</button>"
            );

            $("#pagina_" + (response.meta.current_page - x)).on(
                "click",
                function () {
                    window.atualizar("?page=" + $(this).html());
                }
            );
        }

        if (
            response.meta.current_page != 1 &&
            response.meta.current_page != response.meta.last_page
        ) {
            var pagina_atual =
                "<button id='pagina_atual' class='btn nav-btn active'>" +
                response.meta.current_page +
                "</button>";

            $("#pagination").append(pagina_atual);

            $("#pagina_atual").attr("disabled", true);
            $("#pagina_atual").addClass("nav-btn");
            $("#pagina_atual").addClass("active");
        }
        for (x = 1; x < 4; x++) {
            if (response.meta.current_page + x >= response.meta.last_page) {
                continue;
            }

            $("#pagination").append(
                "<button id='pagina_" +
                    (response.meta.current_page + x) +
                    "' class='btn nav-btn'>" +
                    (response.meta.current_page + x) +
                    "</button>"
            );

            $("#pagina_" + (response.meta.current_page + x)).on(
                "click",
                function () {
                    window.atualizar("?page=" + $(this).html());
                }
            );
        }

        if (response.meta.last_page != "1") {
            var ultima_pagina =
                "<button id='ultima_pagina' class='btn nav-btn'>" +
                response.meta.last_page +
                "</button>";

            $("#pagination").append(ultima_pagina);

            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr("disabled", true);
                $("#ultima_pagina").addClass("nav-btn");
                $("#ultima_pagina").addClass("active");
            }

            $("#ultima_pagina").on("click", function () {
                window.atualizar("?page=" + response.meta.last_page);
            });
        }
    }

    function contestationDetails() {
        $(".detalhes_ckargeback").unbind("click");
        $(".detalhes_ckargeback").on("click", function (event) {
            event.preventDefault();
            var ckargeback = $(this).attr("ckargeback");
            $("#modal_titulo").html("Detalhes da contestação");
            loadOnAny("#modal-details .modal-body");

            $.ajax({
                method: "GET",
                url: "api/contestations/" + ckargeback,
                headers: {
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                error: function (response) {
                    loadOnAny("#modal-details .modal-body", true);
                    errorAjaxResponse(response);
                },
                success: function (response) {
                    $(".modal-body").html("");
                    loadOnAny("#modal-details .modal-body", true);
                    $(".modal-body").html(response);
                },
            });
        });
    }

    window.atualizar = function(link = null) {
        loadOnTable("#chargebacks-table-data", "#chargebacks-table");

        if (link == null) {
            link = "/api/contestations/getcontestations?" + getFilters();
        } else {
            link = "/api/contestations/getcontestations" + link + "&" + getFilters();
        }
        $.ajax({
            method: "GET",
            url: link,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function () {
                //
            },
            success: function (response) {
                $("#chargebacks-table-data").html("");
                $("#chargebacks-table").addClass("table-striped");

                $.each(response.data, function (index, value) {
                    const objectArray = Object.entries(value.sale_blacklist);
                    let valuesObject = ``;

                    objectArray.forEach(([key, value]) => {
                        valuesObject += `${Object.keys(
                            value
                        )} - ${Object.values(value)}`;
                    });

                    dados = "";
                    dados += `
                        <tr ${value.expiration_user == "Ganha" ? "class='won-contestation'" : ""}>
                            <td id='${value.id}'>
                                <span>${value.sale_code}</span>
                            </td>

                            <td class="line-overflow" title="${value.company}">
                                ${value.company_limit}
                                <br>
                                <small class="text-muted">
                                    ${value.project}
                                </small>
                            </td>

                            <td class="line-overflow" title="${value.customer}">
                                ${value.customer}
                                <br>
                                <small class="text-muted">
                                    Pagamento em ${value.adjustment_date}
                                </small>
                            </td>`;

                            if (value.sale_status in statusObject) {
                                dados +=`
                                    <td class='copy_link'>
                                        <div class="d-flex justify-content-center align-items-center text-center" >
                                            <span class='badge ${badgeObject[value.sale_status]} ${value.sale_status === 10 ? "pointer" : "cursor-default"} font-size-14' data-toggle="tooltip" data-html="true" data-placement="top" title="${valuesObject}">
                                                ${statusObject[value.sale_status]}
                                            </span>
                                            ${value.sale_has_valid_tracking ? "" +'<span class="o-truck-1 font-size-20 text-success cursor-default ml-5" data-toggle="tooltip" title="Rastreamento válido"></span>' : value.sale_only_digital_products
                                                ? '<i class="material-icons font-size-20 text-info cursor-default ml-5" data-toggle="tooltip" title="A venda não tem produtos físicos">computer</i>'
                                                : '<span class="o-truck-1 font-size-20 text-danger cursor-default ml-5" data-toggle="tooltip" title="Rastreamento inválido ou não informado"></span>'}
                                            ${value.sale_is_chargeback_recovered ? '<img class="orange-gradient ml-5" src="/global/img/svg/chargeback.svg" width="20px" title="Chargeback recuperado">' : ""}
                                        </div>
                                    </td>`
                                ;
                            } else {
                                dados +=`
                                    <td>
                                        <span class='badge badge-danger'>
                                            Vazio
                                        </span>
                                    </td>`
                                ;
                            }
                            dados+=`
                                    <td class="bold">${value.expiration_user} ${value.expiration_user.includes("dia") ? '<br><span class="font-size-12 text-muted"> para expirar</span>' : ""}</td>
                                `;

                            dados +=`
                                <td class="font-size-12 bold line-overflow" style="white-space: normal;">
                                    ${value.reason}
                                </td>
                                <!-- <td style='white-space: nowrap'> <b>${value.amount}</b> </td>-->
                                    <td>
                                        ${
                                            value.is_file_user_completed
                                                ? '<a  role="button" class="contetation_file pointer  ' +
                                                  (value.has_expired
                                                      ? "disabled"
                                                      : "") +
                                                  '" title="' +
                                                  (value.has_expired
                                                      ? "Prazo para recurso encerrado"
                                                      : "Enviar arquivo") +
                                                  '"   style="margin-right:5px" contestation="' +
                                                  value.id +
                                                  '"><span class="material-icons" id="check-status-text-icon" data-toggle="tooltip" title="Envio completo">done</span></a>'
                                                : '<a  role="button" class="contetation_file pointer  ' +
                                                  (value.has_expired
                                                      ? "disabled"
                                                      : value.has_files
                                                      ? "text-success"
                                                      : "") +
                                                  '" title="' +
                                                  (value.has_expired
                                                      ? "Prazo para recurso encerrado"
                                                      : "Enviar arquivo") +
                                                  '"   style="margin-right:5px" contestation="' +
                                                  value.id +
                                                  '">' +
                                                  '<span class="o-upload-to-cloud-1  ' +
                                                  (value.has_files
                                                      ? "text-success"
                                                      : "") +
                                                  '" id="upload-file_' +
                                                  value.id +
                                                  ' " ></span>' +
                                                  "</a>"
                                        }
                                        <a role='button' class='detalhes_venda pointer' venda='${
                                            value.sale_id
                                        }'>
                                            <span class="o-eye-1"></span>
                                        </a>
                                  </td>
                                </tr>`;

                    $("#chargebacks-table-data").append(dados);
                });

                if (response.data == "") {
                    $("#chargebacks-table-data").html(
                        "<tr class='text-center gray'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                            $("#chargebacks-table-data").attr("img-empty") +
                            "' alt='Sem contestações'>Nenhuma contestação encontrada</td></tr>"
                    );
                }
                pagination(response);

                contestationDetails();

                $(".contestation_pdf").unbind("click");
                $(".contestation_pdf").on("click", function (event) {
                    event.preventDefault();
                    $("#observation").val("");
                    $("#pdf-modal").modal("show");
                    $("#update-contestation-pdf").on("click", function () {
                        let files = new FormData();
                        files.append(
                            "file_contestation",
                            $("#file_contestation")[0].files[0]
                        );

                        loadOnAny("#pdf-modal .modal-user-pdf-body");

                        $.ajax({
                            method: "POST",
                            url: "api/contestations/send-contestation",
                            processData: false,
                            contentType: false,
                            data: files,
                            headers: {
                                Authorization: $(
                                    'meta[name="access-token"]'
                                ).attr("content"),
                                Accept: "application/json",
                            },
                            error: function (response) {
                                errorAjaxResponse(response);
                            },
                            success: function (response) {
                                $("#pdf-modal").modal("hide");
                                alertCustom("success", response.message);
                            },
                            complete: function (data) {
                                loadOnAny(
                                    "#pdf-modal .modal-user-pdf-body",
                                    true
                                );
                            },
                        });

                        $(
                            ".icon-observation-value_" + response.data.id
                        ).addClass("green");
                    });
                });
            },
        });
    }

    window.getTotalValues = function() {
        loadOnAny(".total-number", false, {
            styles: {
                container: {
                    minHeight: "32px",
                    height: "auto",
                },
                loader: {
                    width: "20px",
                    height: "20px",
                    borderWidth: "4px",
                },
            },
        });

        let link = "/api/contestations/gettotalvalues?" + getFilters();
        $.ajax({
            method: "GET",
            url: link,
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function (response) {
                loadOnAny(".total-number", true);
                errorAjaxResponse(response);
            },
            success: function (response) {
                loadOnAny(".total-number", true);

                $("#total-contestation").html(addZeroLeft(response.total_contestation));
                $("#total-contestation-tax").html(" (" + response.total_contestation_tax + " de " + response.total_sale_approved + ")");

                $("#total-chargeback-tax-val").html(addZeroLeft(response.total_chargeback));
                $("#total-chargeback-tax").html(" (" + response.total_chargeback_tax + ")");

                $("#total-contestation-value").html(response.total_contestation_value);
            },
        });
    }

    // Obtem o os campos dos filtros
    function getProjects() {
        loadingOnScreen();
        $.ajax({
            method: "GET",
            url: "/api/projects?select=true&company="+ sessionStorage.getItem('company_default'),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    // $("#export-excel").show();

                    $.each(response.data, function (i, project) {
                        $("#project").append(
                            $("<option>", {
                                value: project.id,
                                text: project.name,
                            })
                        );
                    });

                    window.atualizar();
                } else {
                    // $("#export-excel").hide();
                    $("#project-not-empty").hide();
                    $("#project-empty").show();
                }

                loadingOnScreenRemove();
            },
        });
    }

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        window.atualizar();
        window.getTotalValues();
    });

    $('#transaction').on('change paste keyup select', function () {
        let val = $(this).val();

        if (val === '') {
            $('#date_type').attr('disabled', false).removeClass('disableFields');
            $('#date_range').attr('disabled', false).removeClass('disableFields');
        } else {
            $('#date_range').val(moment("2018-01-01").format("DD/MM/YYYY") + ' - ' + moment().format("DD/MM/YYYY"));
            $('#date_type').attr('disabled', true).addClass('disableFields');
            $('#date_range').attr('disabled', true).addClass('disableFields');
        }
    });

    //Search user
    $("#usuario").select2({
        placeholder: "Nome do usuário",
        allowClear: true,
        language: {
            noResults: function () {
                return "Nenhum usuário encontrado";
            },
            searching: function () {
                return "Procurando...";
            },
        },
        ajax: {
            data: function (params) {
                return {
                    list: "user",
                    search: params.term,
                };
            },
            method: "POST",
            url: "/users/searchuser",
            delay: 300,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processResults: function (res) {
                return {
                    results: $.map(res.data, function (obj) {
                        return { id: obj.id, text: obj.name };
                    }),
                };
            },
        },
    });

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
            window.atualizar();
            window.getTotalValues();
        }
    });

    $("#pagination").css({ marginBottom: "100px" });

    //window.atualizar();
    window.getTotalValues();
    getProjects();
});
