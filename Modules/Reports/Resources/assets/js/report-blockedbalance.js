var currentPage = null;
var has_api_integration = false;
//var atualizar = null;

$(function () {
    changeCalendar();
    changeCompany();
});

function searchIsLocked(elementButton) {
    return elementButton.attr('block_search');
}

function lockSearch(elementButton) {
    elementButton.attr('block_search', 'true');
    //set layout do button block
}

function unlockSearch(elementButton) {
    elementButton.attr('block_search', 'false');
    //layout do button block
}

function loadData() {
    elementButton = $("#bt_filtro");
    if (searchIsLocked(elementButton) != "true") {
        lockSearch(elementButton);
        atualizar();
    }
}

$('.company-navbar').change(function () {
    if (verifyIfCompanyIsDefault($(this).val())) return;
    $("#project").find('option').not(':first').remove();
    loadOnTable('#dados_tabela', '#tabela_vendas');
    loadOnAny('.number', false, {
        styles: {
            container: {
                minHeight: '32px',
                height: 'auto'
            },
            loader: {
                width: '20px',
                height: '20px',
                borderWidth: '4px'
            },
        }
    });
    updateCompanyDefault().done(function (data1) {
        getCompaniesAndProjects().done(function (data2) {
            window.getProjects(data2, 'company-navbar');
        });
    });
});

window.atualizar = function (link = null) {
    $("#pagination-sales").children().attr("disabled", "disabled");
    currentPage = link;
    let updateResume = true;

    loadOnTable("#dados_tabela", "#tabela_vendas");
    //$('#dados_tabela').html(skeLoad);

    loadOnAny('.number', false, {
        styles: {
            container: {
                minHeight: '32px',
                height: 'auto'
            },
            loader: {
                width: '20px',
                height: '20px',
                borderWidth: '4px'
            },
        }
    });

    if (link == null) {
        link = "/api/reports/blocked-balance?" + getFilters(true).substr(1);
    } else {
        link = "/api/reports/blocked-balance" + link + getFilters(true);
        updateResume = false;
    }

    $.ajax({
        method: "GET",
        url: link,
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#dados_tabela").html("");
            $("#tabela_vendas").addClass("table-striped");
            $("#dados_tabela").html(
                "<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                $("#dados_tabela").attr("img-empty") +
                "'>Nenhuma venda encontrada</td></tr>"
            );
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $("#dados_tabela").html("");
            $("#tabela_vendas").addClass("table-striped");

            let statusArray = {
                1: "success",
                6: "primary",
                7: "disable", //ESTORNADO
                8: "warning",
                4: "dispute", //CHARGEBACK
                3: "danger",
                2: "pendente",
                12: "success",
                20: "antifraude",
                22: "disable", //ESTORNADO
                23: "warning",
                24: "antifraude",
            };

            if (!isEmpty(response.data)) {
                $("#pagination-container").removeClass("d-none").addClass("d-flex")

                $.each(response.data, function (index, value) {

                    let start_date = "";
                    if (value.start_date) {
                        start_date = value.start_date.split(/\s/g); //data inicial
                        start_date = start_date[0] + " <br> <span class='subdescription font-size-12'>" + start_date[1] + " </span>";
                    }
                    let end_date = "";
                    if (value.end_date) {
                        end_date = value.end_date.split(/\s/g); //data final
                        end_date = end_date[0] + " <br> <span class='subdescription font-size-12'>" + end_date[1] + " </span>";
                    }
                    dados = `
                        <tr>


                            <td class='display-sm-none display-m-none display-lg-none text-center text-left'>

                                <div class="">
                                    ${value.sale_code}
                                </div>
                                ${value.upsell ? '<span class="text-muted font-size-10">(Upsell)</span>' : ""}
                                <div class="container-tooltips-blocked"></div>
                            </td>



                            <td class="text-left">
                                <div class="fullInformation-blocked ellipsis-text">
                                    ${value.project}
                                </div>

                            </td>




                            <td class="text-left">

                                <div class="fullInformation-blocked ellipsis-text">
                                    ${value.product}${value.affiliate != null && value.user_sale_type == "producer" ? `<br><small>(Afiliado: ${value.affiliate})</small>` : ""}
                                </div>

                            </td>



                            <td class='display-sm-none display-m-none display-lg-none text-left'>
                                <div class="fullInformation-blocked ellipsis-text">
                                    ${value.client}
                                </div>
                            </td>




                            <td>
                                <img src='/build/global/img/cartoes/${value.brand}.png'  style='width: 45px'>
                            </td>

                            <td>
                                <div class="d-flex justify-content-center">

                                    <span class="badge badge-${statusArray[value.status]} ${value.status_translate === "Pendente" ? "boleto-pending" : ""}" ${value.status_translate === "Pendente" ? 'status="' + value.status_translate + '" sale="' + value.id_default + '"' : ""}>
                                        ${value.status_translate}
                                    </span>

                                    ${value.is_chargeback_recovered && value.status_translate === "Aprovado" ? `

                                    <img class="orange-gradient ml-10" width="20px" src="/build/global/img/svg/chargeback.svg" title="Chargeback recuperado">` : ""}
                                </div>
                            </td>

                                <td class='display-sm-none text-left display-m-none'>${start_date}</td>

                                <td class='display-sm-none text-left'>${end_date}</td>

                                <td style='white-space: nowrap' class="text-left">
                                    <b>${value.total_paid}</b>
                                </td>

                                <td class="text-left ellipsis-text">
                                    <div class="fullInformation-blocked ellipsis-text">
                                        ${value.reason_blocked}
                                    </div>
                                </td>

                            </tr>`
                        ;

                    $("#dados_tabela").append(dados);
                });

                $('.fullInformation-blocked').bind('mouseover', function () {
                    var $this = $(this);

                    if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
                        $this.attr({
                            'data-toggle': "tooltip",
                            'data-placement': "top",
                            'data-title': $this.text()
                        }).tooltip({ container: ".container-tooltips-blocked" })
                        $this.tooltip("show")
                    }
                });

                $("#date").val(
                    moment(new Date()).add(3, "days").format("YYYY-MM-DD")
                );
                $("#date").attr("min", moment(new Date()).format("YYYY-MM-DD"));
            } else {
                $("#pagination-container").removeClass("d-flex").addClass("d-none")
                $("#dados_tabela").html(
                    "<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img class='no-data-table' style='width:124px;' src='" +
                    $("#dados_tabela").attr("img-empty") +
                    "'>Nenhuma venda encontrada</td></tr>"
                );
            }
            pagination(response, "sales", atualizar);
        },
        complete: (response) => {
            unlockSearch($("#bt_filtro"));
        },
    });

    if (updateResume) {
        blockedResume();
    }
}

function getFilters(urlParams = false) {
    let data = {
        'company': $('.company-navbar').val(),
        'project': $("#project").val(),
        'payment_method': $("#payment_method").val(),
        'status': $("#status").val(),
        'client': $("#client").val(),
        'customer_document': $("#customer_document").val(),
        'date_type': $("#date_type").val(),
        'date_range': $("#date-filter").val(),
        'transaction': $("#transaction").val().replace('#', ''),
        'reason': $('#reason').val(),
        'plan': $('#plan').val(),
    };
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

function blockedResume() {
    $("#commission_blocked, #total_sales").html(skeLoadMini).width("100%");
    // loadOnAny('.number', false, {
    //     styles: {
    //         container: {
    //             minHeight: '32px',
    //             height: 'auto'
    //         },
    //         loader: {
    //             width: '20px',
    //             height: '20px',
    //             borderWidth: '4px'
    //         },
    //     }
    // });

    $.ajax({
        method: "GET",
        url: "/api/reports/resume-blocked-balance",
        data: getFilters(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            //loadOnAny('.number', true);
            errorAjaxResponse(response);
        },
        success: function success(response) {
            loadOnAny('.number', true);
            //$('#total_sales').text('0');

            if (response.total_sales) {
                $("#total_sales, #commission_blocked, #total").text("");
                $("#total_sales").html(response.total_sales);
                $("#commission_blocked").html(
                    `<small class="font-size-16 small gray-1">R$</small> <strong class="font-size-24 bold">${response.commission}</strong>`
                );
                $(".blocked-balance-icon")
                    .attr(
                        "title",
                        "Saldo retido de convites: R$ " +
                        response.commission_invite
                    )
                    .tooltip({ placement: "bottom" });
                $(".blocked-balance-icon")
                    .attr(
                        "data-original-title",
                        "Saldo retido de convites: R$ " +
                        response.commission_invite
                    )
                    .tooltip({ placement: "bottom" });
                $("#total").html(
                    `R$ <span class="font-size-24 bold">${response.total}</span>`
                );
            } else {
                $("#commission_blocked, #total").html(
                    '<small class="font-size-16 small gray-1">R$</small> <span class="font-size-24 bold">0,00</span>'
                );
                $("#total_sales").html(
                    '<strong class="font-size-24 orange">0</strong>'
                );
            }
        },
    });
}

$(document).ready(function () {
    //checkbox
    $(".check").on("click", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });
    // COMPORTAMENTOS DA JANELA

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        $("#pagination-container").removeClass("d-flex").addClass("d-none")

        window.atualizar();
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

    let startDate = moment().subtract(30, "days").format("YYYY-MM-DD");
    let endDate = moment().format("YYYY-MM-DD");
    // $('#date_range').daterangepicker({
    //     startDate: moment('2018-01-01 00:00:00'),
    //     endDate: moment(),
    //     opens: 'center',
    //     maxDate: moment().endOf("day"),
    //     alwaysShowCalendar: true,
    //     showCustomRangeLabel: 'Customizado',
    //     autoUpdateInput: true,
    //     locale: {
    //         locale: 'pt-br',
    //         format: 'DD/MM/YYYY',
    //         applyLabel: "Aplicar",
    //         cancelLabel: "Limpar",
    //         fromLabel: 'De',
    //         toLabel: 'Até',
    //         customRangeLabel: 'Customizado',
    //         weekLabel: 'W',
    //         daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
    //         monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
    //         firstDay: 0
    //     },
    //     ranges: {
    //         'Hoje': [moment(), moment()],
    //         'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    //         'Últimos 7 dias': [moment().subtract(6, 'days'), moment()],
    //         'Últimos 30 dias': [moment().subtract(29, 'days'), moment()],
    //         'Este mês': [moment().startOf('month'), moment().endOf('month')],
    //         'Mês passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
    //         'Vitalício': [moment('2018-01-01 00:00:00'), moment()]
    //     }
    // }, function (start, end) {
    //     startDate = start.format('YYYY-MM-DD');
    //     endDate = end.format('YYYY-MM-DD');
    // });

    // FIM - COMPORTAMENTOS DA JANELA

    getBlockReasons();

    function getBlockReasons() {
        $.ajax({
            url: "/api/reports/block-reasons",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
            },
            error: (resp) => {
                errorAjaxResponse(resp);
            },
            success: (resp) => {
                for (const item of resp) {
                    const option = `<option value="${item.id}" data-toggle="tooltip" title="${item.reason}">
                                        ${item.reason}
                                    </option>`;
                    $("#reason").append(option);
                }
            },
        });
    }

    getCompaniesAndProjects().done(function (data) {
        window.getProjects(data);
    });

    window.fillProjectsSelect = function () {
        return $.ajax({
            method: "GET",
            url: "/api/projects?select=true",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
            },
            success: function success(response) {
                return response;
                /*
                if (!isEmpty(response.data)) {
                    $.each(response.data, function (index, company) {
                        if (company.company_has_sale_before_getnet) {
                            hasSale = true;
                        }
                        const document =
                            (company.document.replace(/\D/g, "").length > 11
                                ? "CNPJ: "
                                : "CPF: ") + company.document;
                        $("#company").append(
                            `<option value="${company.id}" data-toggle="tooltip" title="${document}">${company.name}</option>`
                        );
                    });

                    // if (hasSale) {
                    //     $("#select-statement-div").show();
                    // }
                }
*/
                // getProjects();
                // getAcquirer();
            }
        });
    }

    window.getProjects = function (data, origin = '') {

        loadingOnScreen();

        $.ajax({
            method: "GET",
            url: "/api/sales/projects-with-sales",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadingOnScreenRemove();
            },
            success: function success(response) {
                if (!isEmpty(response) || data.has_api_integration) {
                    $(".div-filters").hide();
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $("#export-excel > div >").show();
                    $.each(response, function (c, project) {
                        $("#project").append($("<option>", { value: project.project_id, text: project.name, }));
                    });
                    if (data.has_api_integration)
                        $("#project").append($("<option>", { value: 'API-TOKEN', text: 'Vendas por API' }));
                    $("#project option:first").attr('selected', 'selected');
                    if (sessionStorage.info) {
                        $("#project").val(JSON.parse(sessionStorage.getItem('info')).company);
                        $("#project").find('option:selected').text(JSON.parse(sessionStorage.getItem('info')).companyName);
                    }
                    company = $("#project").val();
                    atualizar();
                    $(".div-filters").show();
                    loadingOnScreenRemove();
                }
                else {
                    if (!isEmpty(data.company_default_projects)) {
                        $(".div-filters").hide();
                        $("#project-empty").hide();
                        $("#project-not-empty").show();
                        $("#export-excel > div >").show();
                        // $.each(data.company_default_projects, function (i, project) {
                        //     $("#project").append($("<option>", {value: project.project_id,text: project.name,}));
                        // });
                        if (data.has_api_integration)
                            $("#project").append($("<option>", { value: 'API-TOKEN', text: 'Vendas por API' }));
                        $("#project option:first").attr('selected', 'selected');
                        if ($('#select_projects option').length == 0)
                            $('#select_projects').next().css('display', 'none')
                        atualizar();
                        $(".div-filters").show();
                        loadingOnScreenRemove();
                    }
                    else {
                        loadingOnScreenRemove();
                        $(".div-filters").hide();
                        $("#project-empty").show();
                        $("#project-not-empty").hide();
                    }
                }
            }
        })
        loadingOnScreenRemove();
    }

    $("#project").on("change", function () {
        let value = $(this).val();
        $("#plan").val(null).trigger("change");
    });

    //Search plan
    $("#plan").select2({
        placeholder: "Nome do plano",
        // multiple: true,
        allowClear: true,
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
                return {
                    results: $.map(res.data, function (obj) {
                        return {
                            id: obj.id,
                            text:
                                obj.name +
                                (obj.description
                                    ? " - " + obj.description
                                    : ""),
                        };
                    }),
                };
            },
        },
    });

    $(document).on("keypress", function (e) {
        if (e.keyCode == 13) {
            window.atualizar();
        }
    });

    function blockedResume() {
        loadOnAny(".number", false, {
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
            url: "/api/reports/blockedresume",
            data: getFilters(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadOnAny(".number", true);
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadOnAny(".number", true);
                $("#total_sales").text("0");
                $("#commission_blocked, #total").html(
                    'R$ <span class="font-size-30 bold">0,00</span>'
                );
                if (response.total_sales) {
                    $("#total_sales, #commission_blocked, #total").text("");
                    $("#total_sales").html(response.total_sales);
                    $("#commission_blocked").html(
                        `R$ <span class="font-size-30 bold">${response.commission}</span>`
                    );
                    $(".blocked-balance-icon")
                        .attr(
                            "title",
                            "Saldo bloqueado de convites: R$ " +
                            response.commission_invite
                        )
                        .tooltip({ placement: "bottom" });
                    $(".blocked-balance-icon")
                        .attr(
                            "data-original-title",
                            "Saldo bloqueado de convites: R$ " +
                            response.commission_invite
                        )
                        .tooltip({ placement: "bottom" });
                    $("#total").html(
                        `R$ <span class="font-size-30 bold">${response.total}</span>`
                    );
                }
            },
        });
    }
});

function changeCalendar() {
    var startDate = moment().subtract(30, "days").format("DD/MM/YYYY");
    var endDate = moment().format("DD/MM/YYYY");

    $('input[name="daterange"]').attr("value", `${startDate}-${endDate}`);
    $('input[name="daterange"]')
        .dateRangePicker({
            setValue: function (s) {
                if (s) {
                    let normalize = s.replace(
                        /(\d{2}\/\d{2}\/)(\d{2}) à (\d{2}\/\d{2}\/)(\d{2})/,
                        "$120$2-$320$4"
                    );
                    $(this).html(s).data("value", normalize);
                    $('input[name="daterange"]').attr("value", normalize);
                    $('input[name="daterange"]').val(normalize);
                } else {
                    $('input[name="daterange"]').attr(
                        "value",
                        `${startDate}-${endDate}`
                    );
                    $('input[name="daterange"]').val(`${startDate}-${endDate}`);
                }
            },
        })
        .on("datepicker-change", function () { })
        .on("datepicker-open", function () {
            $(".filter-badge-input").removeClass("show");
        })
        .on("datepicker-close", function () {
            $(this).removeClass("focused");
            if ($(this).data("value")) {
                $(this).addClass("active");
            }
        });
}
function changeCompany() {
    $("#select_projects").on("change", function () {
        updateStorage({
            company: $(this).val(),
            companyName: $(this).find("option:selected").text(),
        });
    });
}

function updateStorage(v) {
    var existing = sessionStorage.getItem("info");
    existing = existing ? JSON.parse(existing) : {};
    Object.keys(v).forEach(function (val, key) {
        existing[val] = v[val];
    });
    sessionStorage.setItem("info", JSON.stringify(existing));
}

let skeLoad = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
        </div>
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>
    </div>
`;

let skeLoadMini = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
        </div>
    </div>
`;

