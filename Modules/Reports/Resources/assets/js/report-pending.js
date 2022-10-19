var currentPage = null;
//var atualizar = null;
let hasSale = false;
var has_api_integration = false;

$('.company-navbar').change(function () {
    if (verifyIfCompanyIsDefault($(this).val())) return;
    $("#project").find('option').not(':first').remove();
    loadOnTable('#body-table-pending', '.table-pending');
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

    $("#project").html('');
    sessionStorage.removeItem('info');

    updateCompanyDefault().done(function (data1) {
        getCompaniesAndProjects().done(function (data2) {
            getProjects(data2, 'company-navbar');
        });
    });
});

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

function atualizar(link = null) {

    currentPage = link;
    let updateResume = true;

    loadOnTable("#body-table-pending", ".table-pending");

    if (link == null) {
        link = "/api/reports/pending-balance?" + getFilters(true).substr(1);
    } else {
        link = "/api/reports/pending-balance" + link + getFilters(true);
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
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $("#body-table-pending").html("");
            $(".table-pending").addClass("table-striped");

            if (!isEmpty(response.data)) {
                $.each(response.data, function (index, value) {
                    let start_date = "";
                    if (value.start_date) {
                        start_date = value.start_date.split(/\s/g); //data inicial
                        start_date = start_date[0] + "<br> <small class='gray" + start_date[1] + " </small>";
                    }

                    let end_date = "";
                    if (value.end_date) {
                        end_date = value.end_date.split(/\s/g); //data final
                        end_date = end_date[0] + " <br> <small class='gray font-size-12'>" + end_date[1] + " </small>";
                    }
                    let is_security_reserve = "";
                    if (value.is_security_reserve) {
                        is_security_reserve = `<br><div data-toggle="tooltip" title="Reserva de Segurança">
                                                   <img class="ml-30" width="12px" src="/build/global/img/money_lock.svg" alt="Reserva de Segurança">
                                               </div>`;
                    }

                    dados = `  <tr>
                                <td class="text-center">
                                    ${value.sale_code}
                                    ${is_security_reserve}
                                </td>
                                <td class="text-left">${value.project}</td>
                                <td class="text-left">${value.client}</td>
                                <td class="display-sm-none display-m-none display-lg-none">
                                    <img src='/build/global/img/cartoes/${value.brand}.png' alt="${value.brand}"  style='width: 45px'>
                                </td>
                                <td class="display-sm-none display-m-none display-lg-none text-left">${start_date}</td>
                                <td class="text-left">${end_date}</td>
                                <td><b class="font-md-size-20">${value.total_paid}</b></td>
                                <td>
                                    <a role='button' class='detalhes_venda pointer' venda='${value.id}'><span>
                                    <img src="/build/global/img/icon-eye.svg"></span></button></a>
                                </td>
                            </tr>`;

                    $("#body-table-pending").append(dados);
                });

                $("#date").val(
                    moment(new Date()).add(3, "days").format("YYYY-MM-DD")
                );
                $("#date").attr("min", moment(new Date()).format("YYYY-MM-DD"));
            } else {
                $("#body-table-pending").html(
                    "<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img style='width:124px;margin-right:12px;' src='" +
                    $("#body-table-pending").attr("img-empty") +
                    "'> Nenhuma venda encontrada </td></tr>"
                );
            }
            pagination(response, "pending", atualizar);
        },
        complete: (response) => {
            unlockSearch($("#bt_filtro"));
        },
    });

    if (updateResume) {
        resumePending();
    }
}

function getFilters(urlParams = false) {
    let data = {
        company: $(".company-navbar").val(),
        project: $("#project").val(),
        client: $("#client").val(),
        customer_document: $("#customer_document").val(),
        payment_method: $("#payment_method").val(),
        sale_code: $("#sale_code").val().replace("#", ""),
        date_type: $("#date_type").val(),
        date_range: $("#date-filter").val(),
        statement:
            hasSale == false
                ? "automatic_liquidation"
                : $("#type_statement").val(),
        acquirer: $("#acquirer").val(),
        is_security_reserve: $("#is-security-reserve").is(":checked") ? 1 : 0,
    };

    if (urlParams) {
        let params = "";
        for (let param in data) {
            params += "&" + param + "=" + data[param];
        }
        return encodeURI(params);
    }

    return data;
}

function resumePending() {
    $("#total_sales").html(skeLoadMini);

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
        url: "/api/reports/resume-pending-balance",
        data: getFilters(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            //loadOnAny('.number', true);
            $("#total-pending, #total").html(
                'R$ <strong class="font-size-30">0,00</strong>'
            );
            errorAjaxResponse(response);
        },
        success: function success(response) {
            //loadOnAny('.number', true);
            //$('#total_sales').text('0');

            if (response.total_sales) {
                $("#total_sales, #total-pending, #total").text("");
                $("#total_sales").text(response.total_sales);
                var comission = response.commission.split(/\s/g);
                $("#total-pending").html(
                    comission[0] +
                    ' <span class="font-size-30 bold">' +
                    comission[1] +
                    "</span>"
                );
            } else {
                $("#total-pending, #total").html(
                    'R$ <strong class="font-size-30">0,00</strong>'
                );
            }
        },
    });
}

$(document).ready(function () {
    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        $("#pagination-container-pending").removeClass("d-flex").addClass("d-none")
        loadData();
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

    let startDate = moment().subtract(30, 'days').format('YYYY-MM-DD');
    let endDate = moment().format('YYYY-MM-DD');

    getCompaniesAndProjects().done(function (data) {
        getProjects(data);
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
                console.log('erro')
                console.log(response)
            },
            success: function success(response) {
                return response;
            }
        });
    }

    window.getCompanies = function (data, loading = 'y') {
        if (loading == 'y') {
            loadingOnScreen();
            window.fillProjectsSelect(data.companies)
        }
        $.ajax({
            method: "GET",
            url: '/api/core/companies?select=true',
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
                    $.each(response.data, function (index, company) {
                        if (company.company_has_sale_before_getnet) {
                            hasSale = true;
                        }
                        $("#company").append(
                            '<option value="' +
                            company.id +
                            '">' +
                            company.name +
                            "</option>"
                        );
                    });

                    if (hasSale) {
                        $("#select-statement-div").show();
                    }
                }
                getProjects(data);
                getAcquirer();
            },
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
                console.log('erro')
                console.log(response)
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
                    getAcquirer();
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
                        getAcquirer();
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

    function getAcquirer() {
        $.ajax({
            method: "GET",
            url: '/api/finances/acquirers/' + $('.company-navbar').val(),
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
                $.each(response.data, function (i, acquirer) {
                    $("#acquirer").append(
                        $("<option>", {
                            value: acquirer,
                            text: acquirer,
                        })
                    );
                });

                atualizar();

                loadingOnScreenRemove();
            },
        });
    }


    // function resumePending() {

    //     //$("#total_sales").html(skeLoadMini);

    //     $.ajax({
    //         method: "GET",
    //         url: '/api/reports/resume-pending-balance',
    //         data: getFilters(),
    //         dataType: "json",
    //         headers: {
    //             'Authorization': $('meta[name="access-token"]').attr('content'),
    //             'Accept': 'application/json',
    //         },
    //         error: function error(response) {
    //             //loadOnAny('.number', true);
    //             $('#total-pending, #total').html('R$ <strong class="font-size-30">0,00</strong>');
    //             errorAjaxResponse(response);
    //         },
    //         success: function success(response) {
    //             //loadOnAny('.number', true);
    //             //$('#total_sales').text('0');

    //             if (response.total_sales) {
    //                 $('#total_sales, #total-pending, #total').text('');
    //                 $('#total_sales').text(response.total_sales);
    //                 var comission=response.commission.split(/\s/g);
    //                 $('#total-pending').html(comission[0]+' <span class="font-size-30 bold">'+comission[1]+'</span>');
    //             } else {
    //                 $('#total-pending, #total').html('R$ <strong class="font-size-30">0,00</strong>');
    //             }
    //         }
    //     });
    // }


    function resumePending() {
        $("#total-pending, #total_sales").html(skeLoadMini).width("100%");
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
            url: "/api/reports/resume-pending-balance",
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
                    $("#total_sales").text(response.total_sales);
                    var comission = response.commission.split(/\s/g);
                    $("#total-pending").html(
                        '<small class="font-size-16 small gray-1">R$</small> <strong class="font-size-24 orange bold">' +
                        comission[1] +
                        "</strong>"
                    );
                    //var total=response.total.split(/\s/g);
                    //$('#total').html(total[0]+' <span class="font-size-24 orange bold">'+total[1]+'</span>');
                } else {
                    $("#total-pending, #total").html(
                        '<small class="font-size-16 small gray-1">R$</small> <strong class="font-size-24 orange">0,00</strong>'
                    );
                    $("#total_sales").html(
                        '<strong class="font-size-24 orange">0</strong>'
                    );
                }
            },
        });
    }

    atualizar = function (link = null) {
        $("#pagination-pending").children().attr("disabled", "disabled");

        currentPage = link;
        let updateResume = true;

        loadOnTable("#body-table-pending", ".table-pending");
        //$('#body-table-pending').html(skeLoad);

        if (link == null) {
            link = "/api/reports/pending-balance?" + getFilters(true).substr(1);
        } else {
            link = "/api/reports/pending-balance" + link + getFilters(true);
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
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#body-table-pending").html("");
                $(".table-pending").addClass("table-striped");

                if (!isEmpty(response.data)) {
                    $("#pagination-container-pending").removeClass("d-none").addClass("d-flex")
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
                        let is_security_reserve = "";
                        if (value.is_security_reserve) {
                            is_security_reserve = `<br><div data-toggle="tooltip" title="Reserva de Segurança">
                                                       <img class="ml-30 "width="12px" src="/build/global/img/money_lock.svg" alt="Reserva de Segurança">
                                                   </div>`;
                        }

                        dados = `  <tr>

                                    <td class="">
                                        ${value.sale_code}
                                        ${is_security_reserve}
                                    </td>

                                    <td class="text-left">

                                        <div class="fullInformation-pending ellipsis-text" data-toggle="tooltip" data-placement="top" title="${value.project}">
                                            ${value.project}
                                        </div>

                                        <div class="container-tooltips-pending"></div>
                                    </td>

                                    <td class="text-left">

                                        <div class="fullInformation-pending ellipsis-text" data-toggle="tooltip" data-placement="top" title="${value.client}">
                                            ${value.client}
                                        </div>

                                    </td>

                                    <td class="display-sm-none display-m-none display-lg-none">
                                        <img src='/build/global/img/cartoes/${value.brand}.png' alt="${value.brand}"  style='width: 45px'>
                                    </td>

                                    <td class="display-sm-none display-m-none display-lg-none text-left">${start_date}</td>

                                    <td class="text-left">${end_date}</td>

                                    <td>
                                        <b class="font-md-size-20">${value.total_paid}</b>
                                    </td>

                                    <td>
                                        <a role='button' class='detalhes_venda pointer' venda='${value.id}'>
                                            <span>
                                                <img src="/build/global/img/icon-eye.svg">
                                            </span>
                                            </button>
                                        </a>
                                    </td>

                                </tr>`;

                        $("#body-table-pending").append(dados);
                    });
                    $(".fullInformation-pending").tooltip({ container: '.container-tooltips-pending' });


                    $("#date").val(
                        moment(new Date()).add(3, "days").format("YYYY-MM-DD")
                    );
                    $("#date").attr(
                        "min",
                        moment(new Date()).format("YYYY-MM-DD")
                    );
                } else {
                    $("#pagination-container-pending").removeClass("d-flex").addClass("d-none")
                    $("#body-table-pending").html(
                        "<tr class='text-center'><td colspan='10' style='vertical-align: middle;height:257px;'><img class='no-data-table' style='width:124px;' src='" +
                        $("#body-table-pending").attr("img-empty") +
                        "'> Nenhuma venda encontrada </td></tr>"
                    );
                }
                pagination(response, "pending", atualizar);
            },
            complete: (response) => {
                unlockSearch($("#bt_filtro"));
            },
        });

        if (updateResume) {
            resumePending();
        }
    };

    $(document).on("keypress", function (e) {
        if (e.keyCode == 13) {
            atualizar();
        }
    });

    $('.company_name').val($('.company-navbar').find('option:selected').text());

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
    $("#project").on("change", function () {
        updateStorage({
            company: $(this).val(),
            companyName: $(this).find('option:selected').text(),
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
