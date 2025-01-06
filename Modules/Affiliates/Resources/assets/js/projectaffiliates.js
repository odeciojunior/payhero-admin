$(document).ready(function () {

    $('.company-navbar').change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        loadOnAny('.page-content');
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects().done(function (data2) {
                companiesAndProjects = data2
                $("#project-affiliate").find('option').not(':first').remove();
                $("#project-affiliate-request").find('option').not(':first').remove();
                getProjects('n');
            });
        });
    });

    let companiesAndProjects = ''

    getCompaniesAndProjects().done(function (data) {
        companiesAndProjects = data
        getProjects();
    });

    let badgeAffiliateRequest = {
        1: "primary",
        2: "warning",
        3: "success",
        4: "danger",
    };
    let badgeAffiliates = {
        1: "success",
        2: "disable",
    };

    $("#btn-filter-affiliates").on("click", function () {
        loadData($("#btn-filter-affiliates"), 1);
    });

    $("#btn-filter-affiliates-request").on("click", function () {
        loadData($("#btn-filter-affiliates-request"), 2);
    });

    function loadData(elementButton, elementFunction) {
        if (searchIsLocked(elementButton) != "true") {
            lockSearch(elementButton);

            switch (elementFunction) {
                case 1:
                    getAffiliates();
                    break;
                case 2:
                    getAffiliatesRequest();
                    break;
            }
        }
    }

    function getProjects(loading = 'y') {
        if (loading == 'y')
            loadingOnScreen();
        else
            loadOnAny('.page-content');

        $('#tab-affiliates').trigger('click');

        $.ajax({
            method: "GET",
            url: "/api/projects?affiliate=true&status=active&company=" + $('.company-navbar').val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadOnAny('.page-content', true);
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    $.each(response.data, function (i, project) {
                        if (project.affiliated == false) {
                            $("#project-affiliate").append(
                                $("<option>", {
                                    value: project.id,
                                    text: project.name,
                                })
                            );
                            $("#project-affiliate-request").append(
                                $("<option>", {
                                    value: project.id,
                                    text: project.name,
                                })
                            );
                        }
                    });

                    getAffiliates();
                    getAffiliatesRequest();
                } else {
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                    $("#project-affiliate").append(
                        $("<option>", {
                            value: 0,
                            text: "Nenhuma loja encontrado",
                        })
                    );
                    $("#project-affiliate-request").append(
                        $("<option>", {
                            value: 0,
                            text: "Nenhuma loja encontrado",
                        })
                    );
                }
                loadOnAny('.page-content', true);
                loadingOnScreenRemove();
            },
        });
    }

    function getAffiliates() {
        let link =
            arguments.length > 0 && arguments[0] !== undefined
                ? arguments[0]
                : null;

        let project = $("#project-affiliate").val();
        let name = $("#name-affiliate").val();

        project = project ? project : null;
        name = name ? name : null;

        if (link == null) {
            link = "/api/affiliates/getaffiliates?project=" + project + "&name=" + name + "&company=" + $('.company-navbar').val();
        } else {
            link = "/api/affiliates/getaffiliates" + link + "&project=" + project + "&name=" + name + "&company=" + $('.company-navbar').val();
        }

        loadOnTable("#body-table-affiliates", ".table-affiliate");
        $("#pagination-affiliates").children().attr("disabled", "disabled");

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                $("#body-table-affiliates").html("");
                if (response.data == "") {
                    $("#body-table-affiliates").html(
                        "<tr class='text-center'><td colspan='8' style='height: 257px; vertical-align: middle;'><img style='width:124px;margin-right:12px;' src='" +
                        $("#body-table-affiliates").attr("img-empty") +
                        "'> Nenhum afiliado encontrado</td></tr>"
                    );
                } else {
                    $.each(response.data, function (index, value) {

                        let fullData = value.date
                        let dataParse = fullData.split(" ")
                        let dataDay = dataParse[0]
                        let dataTime = dataParse[1]


                        if (value.percentage == "" || value.percentage == "0")
                            value.percentage = "0%";
                        data = "";
                        data += "<tr>";
                        data +=
                            '<td class="ellipsis-text" style="vertical-align: middle;">' +
                            '<span class="fullInformation" data-toggle="tooltip" data-placement="top" title="' + value.name + '">' + value.name + '</span>'
                        "</td>";
                        // data += '<td class="" style="vertical-align: middle;">' + value.email + '</td>';
                        data +=
                            '<td class="ellipsis-text" style="vertical-align: middle;">' +
                            '<span class="fullInformation" data-toggle="tooltip" data-placement="top" title="' + value.project_name + '">' + value.project_name + '</span>'
                        "</td>";
                        data +=
                            '<td class="" style="vertical-align: middle;">' +
                            dataDay + '<br> <span class="subdescription font-size-12">' + dataTime + '</span> ' +
                            "</td>";
                        data +=
                            '<td class="text-center" style="vertical-align: middle;">' +
                            value.percentage +
                            "</td>";
                        data +=
                            '<td class="text-center" ><span class="badge badge-' +
                            badgeAffiliates[value.status] +
                            '">' +
                            value.status_translated +
                            "</span></td>";
                        data += "<td class='text-center text-nowrap'>";
                        data +=
                            "<a title='Visualizar' class='mg-responsive pointer details-affiliate' affiliate='" +
                            value.id +
                            "'><span class=''><img src='/build/global/img/icon-eye.svg'/></span></a>";
                        data +=
                            "<a title='Editar' class='mg-responsive pointer edit-affiliate'    affiliate='" +
                            value.id +
                            "'><span class=''><img src='build/global/img/pencil-icon.svg'/></span></a>";
                        data +=
                            "<a title='Excluir' class='mg-responsive pointer delete-affiliate' affiliate='" +
                            value.id +
                            "'><span class=''><img src='/build/global/img/icon-trash-tale.svg'/></span></a>";
                        data += "</td>";
                        data += "</tr>";
                        $("#body-table-affiliates").append(data);
                    });
                    $(".fullInformation").tooltip();
                    $(".fullInformation").tooltip();
                }
                $(".table-affiliate").addClass("table-striped");
                $(".table-affiliate").addClass("mb-0");
                pagination(response, "affiliates", getAffiliates);

                $(".delete-affiliate").on("click", function (event) {
                    event.preventDefault();
                    if (verifyAccountFrozen()) {
                        return;
                    }
                    let affiliate = $(this).attr("affiliate");
                    $("#modal-delete-affiliate").modal("show");

                    $("#modal-delete-affiliate .btn-delete").unbind("click");
                    $("#modal-delete-affiliate .btn-delete").on(
                        "click",
                        function () {
                            $("#modal-delete").modal("hide");
                            loadingOnScreen();
                            $.ajax({
                                method: "DELETE",
                                url: "/api/affiliates/" + affiliate,
                                dataType: "json",
                                headers: {
                                    Authorization: $(
                                        'meta[name="access-token"]'
                                    ).attr("content"),
                                    Accept: "application/json",
                                },
                                error: (function (_error3) {
                                    function error() {
                                        return _error3.apply(this, arguments);
                                    }

                                    error.toString = function () {
                                        return _error3.toString();
                                    };

                                    return error;
                                })(function (response) {
                                    loadingOnScreenRemove();
                                    errorAjaxResponse(response);
                                }),
                                success: function (data) {
                                    loadingOnScreenRemove();
                                    getAffiliates();
                                },
                            });
                        }
                    );
                });

                $(document).on("click", ".edit-affiliate", function () {
                    let affiliate = $(this).attr("affiliate");
                    if (verifyAccountFrozen()) {
                        return;
                    }
                    $.ajax({
                        method: "GET",
                        url: "/api/affiliates/" + affiliate + "/edit",
                        dataType: "json",
                        headers: {
                            Authorization: $('meta[name="access-token"]').attr(
                                "content"
                            ),
                            Accept: "application/json",
                        },
                        error: function error(response) {
                            errorAjaxResponse(response);
                        },
                        success: function success(response) {
                            if (
                                response.data.percentage == "" ||
                                response.data.percentage == "0"
                            )
                                response.data.percentage = "0%";
                            $("#modal-edit-affiliate .affiliate-id").val(
                                affiliate
                            );
                            $("#modal-edit-affiliate .affiliate-name").val(
                                response.data.name
                            );
                            // $('#modal-edit-affiliate .affiliate-email').val(response.data.email);
                            // $('#modal-edit-affiliate .affiliate-company').val(response.data.company);
                            $(
                                "#modal-edit-affiliate .affiliate-percentage"
                            ).val(response.data.percentage);
                            if (response.data.status == 1) {
                                $("#modal-edit-affiliate .affiliate-status")
                                    .prop("selectedIndex", 0)
                                    .change();
                            } else if (response.data.status == 2) {
                                $("#modal-edit-affiliate .affiliate-status")
                                    .prop("selectedIndex", 1)
                                    .change();
                            }
                            $("#modal-edit-affiliate").modal("show");
                        },
                    });
                });

                $(document).on("click", ".details-affiliate", function () {
                    let affiliate = $(this).attr("affiliate");
                    $.ajax({
                        method: "GET",
                        url: "/api/affiliates/" + affiliate + "/edit",
                        dataType: "json",
                        headers: {
                            Authorization: $('meta[name="access-token"]').attr(
                                "content"
                            ),
                            Accept: "application/json",
                        },
                        error: function error(response) {
                            errorAjaxResponse(response);
                        },
                        success: function success(response) {
                            if (
                                response.data.percentage == "" ||
                                response.data.percentage == "0"
                            )
                                response.data.percentage = "0%";
                            $("#modal-show-affiliate .affiliate-name").text(
                                response.data.name
                            );
                            $("#modal-show-affiliate .affiliate-email").text(
                                response.data.email
                            );
                            $("#modal-show-affiliate .affiliate-company").text(
                                response.data.company
                            );
                            $("#modal-show-affiliate .affiliate-project").text(
                                response.data.project_name
                            );
                            $("#modal-show-affiliate .affiliate-phone").text(
                                response.data.cellphone
                            );
                            $("#modal-show-affiliate .affiliate-percent").text(
                                response.data.percentage
                            );
                            $("#modal-show-affiliate .affiliate-date").text(
                                response.data.date
                            );
                            $("#modal-show-affiliate .affiliate-status").html(
                                '<span class="badge badge-' +
                                badgeAffiliates[response.data.status] +
                                '">' +
                                response.data.status_translated +
                                "</span>"
                            );

                            $(
                                "#modal-show-affiliate .affiliate-percentage"
                            ).text(response.data.percentage);
                            $(
                                "#modal-show-affiliate .affiliate-percentage"
                            ).text(response.data.percentage);

                            $("#modal-show-affiliate").modal("show");
                        },
                    });
                });

                $("#modal-edit-affiliate .btn-update").unbind("click");
                $("#modal-edit-affiliate .btn-update").on("click", function () {
                    let formData = new FormData(document.getElementById("form-update-affiliate"));

                    let affiliate = $(
                        "#modal-edit-affiliate .affiliate-id"
                    ).val();
                    loadingOnScreen();
                    $.ajax({
                        method: "POST",
                        url: "/api/affiliates/" + affiliate,
                        dataType: "json",
                        headers: {
                            Authorization: $('meta[name="access-token"]').attr(
                                "content"
                            ),
                            Accept: "application/json",
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        error: function (response) {
                            loadingOnScreenRemove();
                            errorAjaxResponse(response);
                        },
                        success: function success(data) {
                            loadingOnScreenRemove();
                            alertCustom(
                                "success",
                                "Afiliado atualizado com sucesso"
                            );
                            getAffiliates();
                        },
                    });
                });
            },
            complete: (response) => {
                unlockSearch($("#btn-filter-affiliates"));
            },
        });
    }
    function getAffiliatesRequest() {
        let link =
            arguments.length > 0 && arguments[0] !== undefined
                ? arguments[0]
                : null;

        let project = $("#project-affiliate-request").val();
        let name = $("#name-affiliate-request").val();

        project = project ? project : null;
        name = name ? name : null;

        if (link == null) {
            link = "/api/affiliates/getaffiliaterequests?project=" + project + "&name=" + name + "&company=" + $('.company-navbar').val();
        } else {
            link = "/api/affiliates/getaffiliaterequests" + link + "&project=" + project + "&name=" + name + "&company=" + $('.company-navbar').val();
        }

        loadOnTable("#body-table-affiliate-requests", ".table-affiliate-request");
        $("#pagination-affiliates-request").children().attr("disabled", "disabled");

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                $("#body-table-affiliate-requests").html("");
                if (response.data == "") {
                    $("#body-table-affiliate-requests").html(
                        "<tr class='text-center'><td colspan='8' style='height: 257px; vertical-align: middle;'><img style='width:124px;margin-right:12px;' src='" +
                        $("#body-table-affiliate-requests").attr(
                            "img-empty"
                        ) +
                        "'>Nenhuma solicitação de afiliação encontrada</td></tr>"
                    );
                } else {

                    $.each(response.data, function (index, value) {
                        let fullData = value.date
                        let dataParse = fullData.split(" ")
                        let dataDay = dataParse[0]
                        let dataTime = dataParse[1]

                        let data = "";
                        data += "<tr>";
                        data +=
                            '<td class="ellipsis-text" style="vertical-align: middle;">' +
                            '<span class="fullInformation" data-toggle="tooltip" data-placement="top" title="' + value.name + '">' + value.name + '</span>'
                        "</td>";
                        data +=
                            '<td class="ellipsis-text" style="vertical-align: middle;">' +
                            '<span class="fullInformation" data-toggle="tooltip" data-placement="top" title="' + value.email + '">' + value.email + '</span>'
                        "</td>";
                        data +=
                            '<td class="ellipsis-text" style="vertical-align: middle;">' +
                            '<span class="fullInformation" data-toggle="tooltip" data-placement="top" title="' + value.project_name + '">' + value.project_name + '</span>'
                        "</td>";
                        data +=
                            '<td class="" style="vertical-align: middle;">' +
                            dataDay + '<br> <span class="subdescription font-size-12">' + dataTime + '</span> ' +
                            "</td>";
                        // data += '<td class="text-center" ><span class="badge badge-' + badgeAffiliateRequest[value.status] + '">' + value.status_translated + '</span></td>';
                        data += "<td class='text-center text-nowrap'>";
                        if (
                            value.status != 3 &&
                            verifyAccountFrozen() == false
                        ) {
                            data +=
                                "<a title='Aprovar' class='text-white ml-2 mb-1 mt-1 badge badge-success pointer evaluate-affiliate' affiliate='" +
                                value.id +
                                "' status='3'>Aprovar</a>";
                            if (value.status != 4) {
                                data +=
                                    "<a title='Recusar' class='text-white ml-2 mb-1 mt-1 badge badge-danger pointer evaluate-affiliate' affiliate='" +
                                    value.id +
                                    "' status='4'>Recusar</a>";
                            }
                        }

                        data += "</td>";
                        data += "</tr>";
                        $("#body-table-affiliate-requests").append(data);
                    });
                }
                $(".table-affiliate-request").addClass("table-striped");
                $(".table-affiliate-request").addClass("mb-0");
                pagination(
                    response,
                    "affiliates-request",
                    getAffiliatesRequest
                );

                $(".evaluate-affiliate").on("click", function () {
                    let affiliate = $(this).attr("affiliate");
                    let status = $(this).attr("status");

                    loadingOnScreen();
                    $.ajax({
                        method: "POST",
                        url: "/api/affiliates/evaluateaffiliaterequest",
                        dataType: "json",
                        headers: {
                            Authorization: $('meta[name="access-token"]').attr(
                                "content"
                            ),
                            Accept: "application/json",
                        },
                        data: { status: status, affiliate: affiliate },
                        error: function (response) {
                            loadingOnScreenRemove();
                            errorAjaxResponse(response);
                        },
                        success: function success(data) {
                            loadingOnScreenRemove();
                            alertCustom(
                                "success",
                                "Solicitação de afiliação atualizada com sucesso"
                            );
                            getAffiliates();
                            getAffiliatesRequest();
                        },
                    });
                });
            },
            complete: (response) => {
                unlockSearch($("#btn-filter-affiliates-request"));
            },
        });
    }
});
