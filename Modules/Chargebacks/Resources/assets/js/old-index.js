$(document).ready(function () {
    datePicker();
    updateChagebacksTable();
    getTotalValues();

    $("#btn-filter").on("click", function (event) {
        event.preventDefault();
        updateChagebacksTable();
        getTotalValues();
    });

    function updateChagebacksTable(link = null) {
        loadOnTable("#chargebacks-table-data", "#chargebacks-table");

        if (link == null) {
            link =
                "/old-chargebacks/getchargebacks?" +
                "fantasy_name=" +
                $("#fantasy_name").val() +
                "&user_name=" +
                $("#user_name").val() +
                "&date=" +
                $("#date_range").val();
        } else {
            link =
                "/old-chargebacks/getchargebacks" +
                link +
                "&fantasy_name=" +
                $("#fantasy_name").val() +
                "&user_name=" +
                $("#user_name").val() +
                "&date=" +
                $("#date_range").val();
        }
        $.ajax({
            method: "GET",
            url: link,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            error: function () {
                //
            },
            success: function (response) {
                $("#chargebacks-table-data").html("");
                $("#chargebacks-table").addClass("table-striped");

                $.each(response.data, function (index, value) {
                    dados = "";
                    dados += "<tr>";
                    dados += "<td>" + value.fantasy_name + "</td>";
                    dados += "<td>" + value.name + "</td>";
                    dados +=
                        "<td>" +
                        value.chargeback_tax +
                        " (" +
                        value.count_sales_chargeback +
                        " de " +
                        value.count_sales_approved +
                        " aprovadas" +
                        ")" +
                        "</td>";
                    dados += "<td>";
                    dados +=
                        "<div><a role='button' class='chargeback_details pointer' company='" +
                        value.id +
                        "' data-target='#details-modal' data-toggle='modal' title='Visualizar' style='margin-right:10px;'><i class='material-icons gradient'>remove_red_eye</i></button></a></div>";
                    dados += "</div></td>";
                    dados += "</tr>";
                    $("#chargebacks-table-data").append(dados);
                });

                if (response.data == "") {
                    $("#chargebacks-table-data").html(
                        "<tr class='text-center'><td colspan='7' style='height: 70px;vertical-align: middle'> Nenhum chargeback encontrado nesse período</td></tr>"
                    );
                }
                pagination(response);
                chargebackDetails();
            },
        });
    }
    function getTotalValues() {
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
        $.ajax({
            method: "GET",
            url: "old-chargebacks/gettotalvalues",
            data: { date: $("#date_range").val() },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            error: function (response) {
                loadOnAny(".total-number", true);
                errorAjaxResponse(response);
            },
            success: function (response) {
                loadOnAny(".total-number", true);
                $("#total-credit-card-sales").html(response.total_credit_card_sale);
                $("#total-chargeback").html(response.total_chargeback);
                $("#total-chargeback-tax").html(
                    response.total_chargeback_tax +
                        " (" +
                        response.total_chargeback +
                        " de " +
                        response.total_credit_card_approved +
                        " aprovadas" +
                        ")"
                );
            },
        });
    }
    function datePicker() {
        //DatePicker
        let startDate = moment().subtract(30, "days").format("YYYY-MM-DD");
        let endDate = moment().format("YYYY-MM-DD");
        $("#date_range").daterangepicker(
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
                    Ontem: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
                    "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                    "Este mês": [moment().startOf("month"), moment().endOf("month")],
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
    }

    function chargebackDetails() {
        $(".chargeback_details").unbind("click");
        $(".chargeback_details").on("click", function (event) {
            event.preventDefault();
            var company = $(this).attr("company");
            loadOnAny("#details-modal .modal-body");

            $.ajax({
                method: "GET",
                url: "old-chargebacks/" + company,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                error: function (response) {
                    loadOnAny("#details-modal .modal-body", true);
                    errorAjaxResponse(response);
                },
                success: function (response) {
                    $(".modal-body").html("");
                    loadOnAny("#details-modal .modal-body", true);
                    $(".modal-body").html(response);
                },
            });
        });
    }
    function pagination(response) {
        $("#pagination").html("");

        var primeira_pagina = "<button id='primeira_pagina' class='btn nav-btn'>1</button>";

        $("#pagination").append(primeira_pagina);

        if (response.meta.current_page == "1") {
            $("#primeira_pagina").attr("disabled", true);
            $("#primeira_pagina").addClass("nav-btn");
            $("#primeira_pagina").addClass("active");
        }

        $("#primeira_pagina").on("click", function () {
            updateChagebacksTable("?page=1");
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

            $("#pagina_" + (response.meta.current_page - x)).on("click", function () {
                updateChagebacksTable("?page=" + $(this).html());
            });
        }

        if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
            var pagina_atual =
                "<button id='pagina_atual' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

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

            $("#pagina_" + (response.meta.current_page + x)).on("click", function () {
                updateChagebacksTable("?page=" + $(this).html());
            });
        }

        if (response.meta.last_page != "1") {
            var ultima_pagina =
                "<button id='ultima_pagina' class='btn nav-btn'>" + response.meta.last_page + "</button>";

            $("#pagination").append(ultima_pagina);

            if (response.meta.current_page == response.meta.last_page) {
                $("#ultima_pagina").attr("disabled", true);
                $("#ultima_pagina").addClass("nav-btn");
                $("#ultima_pagina").addClass("active");
            }

            $("#ultima_pagina").on("click", function () {
                updateChagebacksTable("?page=" + response.meta.last_page);
            });
        }
    }
    $(document).on("keypress", function (e) {
        if (e.keyCode == 13) {
            updateChagebacksTable();
            getTotalValues();
        }
    });
});
