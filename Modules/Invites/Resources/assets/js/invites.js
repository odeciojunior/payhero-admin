var statusInvite = {
    1: "success",
    2: "pendente",
    3: "warning",
};

var statusDocumentUser = {
    pending: "Pendente",
    analyzing: "Em análise",
    approved: "Aprovado",
    refused: "Recusado",
};

let disabledCompany = false;
let companyVerification = true;

$(document).ready(function () {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#content-error").hide();
        $("#store-invite").attr("disabled", "disabled");
        loadOnTable("#table-body-invites", "#table_invites");
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
        updateCompanyDefault().done(function (data) {
            getCompaniesAndProjects().done(function (data2) {
                companiesAndProjects = data2;
                $("#store-invite").removeAttr("disabled");
                $(".company_name").val(companiesAndProjects.company_default_fullname);
                getInvitationData();
                updateInvitesAfterChangeCompany();
            });
        });
    });

    var companiesAndProjects = "";

    getCompaniesAndProjects().done(function (data) {
        companiesAndProjects = data;
        $(".company_name").val(companiesAndProjects.company_default_fullname);
        updateInvites();
    });

    var currentPage = 1;

    function updateInvites() {
        loadingOnScreen();
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        var cont = 0;

        if (link == null) {
            link = "/api/invitations" + "?company=" + $(".company-navbar").val();
        } else {
            link = "/api/invitations" + link + "?company=" + $(".company-navbar").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (isEmpty(response.data)) {
                    pagination(response, "invites");
                    $("#content-error").show();
                } else {
                    $("#content-error").hide();
                    $("#card-table-invite").css("display", "block");
                    $("#card-invitation-data").css("display", "block");

                    // $("#text-info").css('display', 'block');
                    $("#card-table-invite").css("display", "block");
                    $("#table-body-invites").html("");

                    $.each(response.data, function (index, value) {
                        dados = "";
                        dados += "<tr>";
                        if (index != 9) {
                            dados +=
                                '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' +
                                (currentPage - 1) +
                                (cont += 1) +
                                "</button></td>";
                        } else {
                            dados +=
                                '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' +
                                response.meta.to +
                                "</button></td>";
                        }
                        dados +=
                            '<td class="text-center ellipsis-text" style="vertical-align: middle;">' +
                            value.email_invited +
                            "</td>";
                        dados +=
                            '<td class="text-center ellipsis-text" style="vertical-align: middle;">' +
                            value.company_name +
                            "</td>";
                        dados += '<td class="text-center" style="vertical-align: middle;">';
                        dados +=
                            '<span class="badge badge-' +
                            statusInvite[value.status] +
                            ' text-center">' +
                            value.status_translated +
                            "</span>";
                        dados += "</td>";
                        dados +=
                            '<td class="text-center" style="vertical-align: middle;">' + value.register_date + "</td>";
                        dados +=
                            '<td class="text-center" style="vertical-align: middle;">' +
                            value.expiration_date +
                            "</td>";
                        if (value.status != "2" || verifyAccountFrozen()) {
                            dados +=
                                "<td><button class='btn pointer resend-invitation' title='Reenviar convite' style='background-color:transparent;' invitation='" +
                                value.id +
                                "' disabled><span class=''><img src='/build/global/img/icons-cashback.svg'/></span></button></td>";
                            dados +=
                                "<td><button class='btn pointer delete-invitation' title='Excluir' style='background-color:transparent;' invitation='" +
                                value.id +
                                "' disabled><span class=''><img src='/build/global/img/icon-trash-tale.svg'/></span></button></td>";
                        } else {
                            dados +=
                                "<td><button class='btn pointer resend-invitation' title='Reenviar convite' style='background-color:transparent;' invitation='" +
                                value.id +
                                "'><span class=''><img src='/build/global/img/icons-cashback.svg'/></span></button></td>";
                            dados +=
                                "<td><button class='btn pointer delete-invitation' title='Excluir' style='background-color:transparent;' invitation='" +
                                value.id +
                                "'><span class=''><img src='/build/global/img/icon-trash-tale.svg'/></span></button></td>";
                        }
                        dados += "</tr>";
                        $("#table-body-invites").append(dados);
                    });

                    pagination(response, "invites");
                }
                getInvitationData();

                // Reenviar convite
                $(".resend-invitation").unbind("click");
                $(".resend-invitation").on("click", function () {
                    let invitationId = $(this).attr("invitation");
                    $("#modal-resend-invitation").modal("show");
                    $("#btn-resend-invitation").unbind("click");
                    $("#btn-resend-invitation").on("click", function () {
                        loadingOnScreen();
                        $.ajax({
                            method: "POST",
                            url: "/api/invitations/resendinvitation",
                            data: { invitationId: invitationId },
                            dataType: "json",
                            headers: {
                                Authorization: $('meta[name="access-token"]').attr("content"),
                                Accept: "application/json",
                            },
                            error: (response) => {
                                loadingOnScreenRemove();
                                errorAjaxResponse(response);
                            },
                            success: (response) => {
                                loadingOnScreenRemove();
                                updateInvites();
                                alertCustom("success", response.message);
                            },
                        });
                    });
                });

                // Excluir convite
                $(".delete-invitation").unbind("click");
                $(".delete-invitation").on("click", function () {
                    let invitationId = $(this).attr("invitation");
                    $("#modal-delete-invitation").modal("show");
                    $("#btn-delete-invitation").unbind("click");
                    $("#btn-delete-invitation").on("click", function () {
                        loadingOnScreen();
                        $.ajax({
                            method: "DELETE",
                            url: "/api/invitations/" + invitationId,
                            data: { invitationId: invitationId },
                            dataType: "json",
                            headers: {
                                Authorization: $('meta[name="access-token"]').attr("content"),
                                Accept: "application/json",
                            },
                            error: (response) => {
                                loadingOnScreenRemove();
                                errorAjaxResponse(response);
                            },
                            success: (response) => {
                                loadingOnScreenRemove();
                                updateInvites();
                                alertCustom("success", response.message);
                            },
                        });
                    });
                    //     var cont = 0;
                    //     $.ajax({
                    //         method: "GET",
                    //         url: '/api/invitations?company='+ $('.company-navbar').val(),
                    //         dataType: "json",
                    //         headers: {
                    //             'Authorization': $('meta[name="access-token"]').attr('content'),
                    //             'Accept': 'application/json',
                    //         },
                    //         error: (response) => {
                    //             loadingOnScreenRemove();
                    //             errorAjaxResponse(response);
                    //         },
                    //         success: (response) => {
                    //             if (isEmpty(response.data)) {
                    //                 $("#card-table-invite").hide();
                    //                 $("#table_invites").hide();
                    //                 $("#content-error").show();
                    //             } else {
                    //                 $("#content-error").hide();
                    //                 $("#table_invites").show();
                    //                 $("#card-table-invite").css('display', 'block');
                    //                 $("#card-invitation-data").css('display', 'block');

                    //                 // $("#text-info").css('display', 'block');
                    //                 $("#card-table-invite").css('display', 'block');
                    //                 $("#table-body-invites").html('');

                    //                 $.each(response.data, function (index, value) {
                    //                     dados = '';
                    //                     dados += '<tr>';
                    //                     if (index != 9) {
                    //                         dados += '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' + (currentPage - 1) + (cont += 1) + '</button></td>';
                    //                     } else {
                    //                         dados += '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' + response.meta.to + '</button></td>';
                    //                     }
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">' + value.email_invited + '</td>';
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">' + value.company_name + '</td>';
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">';
                    //                     dados += '<span class="badge badge-' + statusInvite[value.status] + ' text-center">' + value.status_translated + '</span>';
                    //                     dados += '</td>';
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">' + value.register_date + '</td>';
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">' + value.expiration_date + '</td>';
                    //                     if (value.status != '2' || verifyAccountFrozen()) {
                    //                         dados += "<td><button class='btn pointer resend-invitation' title='Reenviar convite' style='background-color:transparent;' invitation='" + value.id + "' disabled><span class='o-reload-1'></span></button></td>";
                    //                         dados += "<td><button class='btn pointer delete-invitation' title='Excluir' style='background-color:transparent;' invitation='" + value.id + "' disabled><span class='o-bin-1'></span></button></td>";

                    //                     } else {
                    //                         dados += "<td><button class='btn pointer resend-invitation' title='Reenviar convite' style='background-color:transparent;' invitation='" + value.id + "'><span class='o-reload-1'></span></button></td>";
                    //                         dados += "<td><button class='btn pointer delete-invitation' title='Excluir' style='background-color:transparent;' invitation='" + value.id + "'><span class='o-bin-1'></span></button></td>";
                    //                     }
                    //                     dados += '</tr>';
                    //                     $("#table-body-invites").append(dados);
                    //                 });

                    //             loadingOnScreenRemove()
                    //         }
                    //     });
                    // }

                    // function updateInvitesAfterChangeCompany() {
                    //     loadOnTable('#table-body-invites', '#table_invites');
                    //     loadOnAny('.number', false, {
                    //         styles: {
                    //             container: {
                    //                 minHeight: "32px",
                    //                 height: "auto",
                    //             },
                    //             loader: {
                    //                 width: "20px",
                    //                 height: "20px",
                    //                 borderWidth: "4px",
                    //             },
                    //         },
                    //     });
                    //     var cont = 0;
                    //     $.ajax({
                    //         method: "GET",
                    //         url: '/api/invitations?company='+ $('.company-navbar').val(),
                    //         dataType: "json",
                    //         headers: {
                    //             'Authorization': $('meta[name="access-token"]').attr('content'),
                    //             'Accept': 'application/json',
                    //         },
                    //         error: (response) => {
                    //             loadingOnScreenRemove();
                    //             errorAjaxResponse(response);
                    //         },
                    //         success: (response) => {
                    //             if (isEmpty(response.data)) {
                    //                 $("#card-table-invite").hide();
                    //                 $("#table_invites").hide();
                    //                 $("#content-error").show();
                    //             } else {
                    //                 $("#content-error").hide();
                    //                 $("#table_invites").show();
                    //                 $("#card-table-invite").css('display', 'block');
                    //                 $("#card-invitation-data").css('display', 'block');

                    //                 // $("#text-info").css('display', 'block');
                    //                 $("#card-table-invite").css('display', 'block');
                    //                 $("#table-body-invites").html('');

                    //                 $.each(response.data, function (index, value) {
                    //                     dados = '';
                    //                     dados += '<tr>';
                    //                     if (index != 9) {
                    //                         dados += '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' + (currentPage - 1) + (cont += 1) + '</button></td>';
                    //                     } else {
                    //                         dados += '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' + response.meta.to + '</button></td>';
                    //                     }
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">' + value.email_invited + '</td>';
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">' + value.company_name + '</td>';
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">';
                    //                     dados += '<span class="badge badge-' + statusInvite[value.status] + ' text-center">' + value.status_translated + '</span>';
                    //                     dados += '</td>';
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">' + value.register_date + '</td>';
                    //                     dados += '<td class="text-center" style="vertical-align: middle;">' + value.expiration_date + '</td>';
                    //                     if (value.status != '2' || verifyAccountFrozen()) {
                    //                         dados += "<td><button class='btn pointer resend-invitation' title='Reenviar convite' style='background-color:transparent;' invitation='" + value.id + "' disabled><span class='o-reload-1'></span></button></td>";
                    //                         dados += "<td><button class='btn pointer delete-invitation' title='Excluir' style='background-color:transparent;' invitation='" + value.id + "' disabled><span class='o-bin-1'></span></button></td>";

                    //                     } else {
                    //                         dados += "<td><button class='btn pointer resend-invitation' title='Reenviar convite' style='background-color:transparent;' invitation='" + value.id + "'><span class='o-reload-1'></span></button></td>";
                    //                         dados += "<td><button class='btn pointer delete-invitation' title='Excluir' style='background-color:transparent;' invitation='" + value.id + "'><span class='o-bin-1'></span></button></td>";
                    //                     }
                    //                     dados += '</tr>';
                    //                     $("#table-body-invites").append(dados);
                });

                loadingOnScreenRemove();
            },
        });
    }

    function updateInvitesAfterChangeCompany() {
        loadOnTable("#table-body-invites", "#table_invites");
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
        var cont = 0;
        $.ajax({
            method: "GET",
            url: "/api/invitations?company=" + $(".company-navbar").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (isEmpty(response.data)) {
                    $("#card-table-invite").hide();
                    $("#table_invites").hide();
                    $("#content-error").show();
                } else {
                    $("#content-error").hide();
                    $("#table_invites").show();
                    $("#card-table-invite").css("display", "block");
                    $("#card-invitation-data").css("display", "block");

                    // $("#text-info").css('display', 'block');
                    $("#card-table-invite").css("display", "block");
                    $("#table-body-invites").html("");

                    $.each(response.data, function (index, value) {
                        dados = "";
                        dados += "<tr>";
                        if (index != 9) {
                            dados +=
                                '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' +
                                (currentPage - 1) +
                                (cont += 1) +
                                "</button></td>";
                        } else {
                            dados +=
                                '<td class="" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' +
                                response.meta.to +
                                "</button></td>";
                        }
                        dados +=
                            '<td class="text-center" style="vertical-align: middle;">' + value.email_invited + "</td>";
                        dados +=
                            '<td class="text-center" style="vertical-align: middle;">' + value.company_name + "</td>";
                        dados += '<td class="text-center" style="vertical-align: middle;">';
                        dados +=
                            '<span class="badge badge-' +
                            statusInvite[value.status] +
                            ' text-center">' +
                            value.status_translated +
                            "</span>";
                        dados += "</td>";
                        dados +=
                            '<td class="text-center" style="vertical-align: middle;">' + value.register_date + "</td>";
                        dados +=
                            '<td class="text-center" style="vertical-align: middle;">' +
                            value.expiration_date +
                            "</td>";
                        if (value.status != "2" || verifyAccountFrozen()) {
                            dados +=
                                "<td><button class='btn pointer resend-invitation' title='Reenviar convite' style='background-color:transparent;' invitation='" +
                                value.id +
                                "' disabled><span class='o-reload-1'></span></button></td>";
                            dados +=
                                "<td><button class='btn pointer delete-invitation' title='Excluir' style='background-color:transparent;' invitation='" +
                                value.id +
                                "' disabled><span class='o-bin-1'></span></button></td>";
                        } else {
                            dados +=
                                "<td><button class='btn pointer resend-invitation' title='Reenviar convite' style='background-color:transparent;' invitation='" +
                                value.id +
                                "'><span class='o-reload-1'></span></button></td>";
                            dados +=
                                "<td><button class='btn pointer delete-invitation' title='Excluir' style='background-color:transparent;' invitation='" +
                                value.id +
                                "'><span class='o-bin-1'></span></button></td>";
                        }
                        dados += "</tr>";
                        $("#table-body-invites").append(dados);
                    });

                    pagination(response, "invites");
                }

                loadingOnScreenRemove();
            },
        });
    }

    $("#store-invite").unbind();
    $("#store-invite").on("click", function () {
        $.ajax({
            method: "GET",
            url: "/api/core/companies",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (companyVerification) {
                    if (isEmpty(response.data)) {
                        loadingOnScreenRemove();
                        $("#modal-then-companies").hide();
                        $("#modal-not-approved-document-companies").hide();
                        $("#modal-not-companies").show();
                    } else {
                        loadingOnScreenRemove();

                        let contCompanies = 0;
                        let contCompaniesNotApproved = 0;

                        var selCompany = "";
                        selCompany = '<select id="select-company-list" class="sirius-select">';

                        disabledCompany = false;
                        $.each(response.data, function (index, company) {
                            contCompanies++;
                            if (companyIsApproved(company)) {
                                if (company.type_company === "physical person") {
                                    if (
                                        statusDocumentUser[company.user_address_document_status] !== "Aprovado" ||
                                        statusDocumentUser[company.user_personal_document_status] !== "Aprovado"
                                    ) {
                                        disabledCompany = false;
                                        contCompaniesNotApproved++;
                                        selCompany += `<option value=${company.id_code} disabled> ${company.fantasy_name}  </option>`;
                                    } else {
                                        selCompany += `<option value=${company.id_code} >  ${company.fantasy_name}  </option>`;
                                    }
                                } else if (company.type_company === "juridical person") {
                                    if (
                                        company.address_document_translate !== "Aprovado" ||
                                        company.contract_document_translate !== "Aprovado" ||
                                        statusDocumentUser[company.user_address_document_status] !== "Aprovado" ||
                                        statusDocumentUser[company.user_personal_document_status] !== "Aprovado"
                                    ) {
                                        disabledCompany = false;
                                        contCompaniesNotApproved++;
                                        selCompany += `<option value=${company.id_code} disabled> ${company.fantasy_name}  </option>`;
                                    } else {
                                        selCompany += `<option value=${company.id_code} >  ${company.fantasy_name}  </option>`;
                                    }
                                }
                            } else {
                                contCompaniesNotApproved++;
                            }
                        });
                        selCompany += "</select>";

                        if (contCompanies === contCompaniesNotApproved) {
                            $("#modal-then-companies").hide();
                            $("#modal-not-companies").hide();
                            $("#modal-not-approved-document-companies").show();
                        } else {
                            $("#modal-not-companies").hide();
                            $("#modal-not-approved-document-companies").hide();
                            $("#modal-then-companies").show();

                            $("#modal-reverse-title").html("Novo Convite");

                            $("#company-list").html("").append(selCompany);

                            var linkInvite = "";
                            var companyId = $(".company-navbar").val();
                            linkInvite = "https://accounts.azcend.com.br/signup?i=" + companyId;

                            $("#invite-link").val(linkInvite);

                            $("#select-company-list").on("change", function () {
                                linkInvite = "https://accounts.azcend.com.br/signup?i=" + $(this).val();
                                $("#invite-link").val(linkInvite);
                                companyId = $(this).val();
                            });

                            $("#copy-link").on("click", function () {
                                var copyText = document.getElementById("invite-link");
                                copyText.select();
                                document.execCommand("copy");

                                alertCustom("success", "Link copiado!");
                            });

                            $("#btn-send-invite").unbind();
                            $("#btn-send-invite").on("click", function () {
                                var email = $("#email").val();

                                if (email == "") {
                                    alertCustom("error", "O campo Email do convidado é obrigatório");
                                } else if (companyId == "") {
                                    alertCustom("error", "O campo Empresa para receber é obrigatório");
                                } else {
                                    loadingOnScreen();
                                    sendInviteAjax(email, companyId);
                                }
                            });
                        }
                    }
                } else {
                    $("#modal-not-invites-today").show();
                }
            },
        });

        $("#modal-invite").modal("show");
    });

    function sendInviteAjax(email, companyId) {
        $.ajax({
            method: "POST",
            url: "/api/invitations",
            data: {
                email: email,
                company: companyId,
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: (response) => {
                $(".close").click();
                alertCustom("success", response.message);
                loadingOnScreenRemove();
                updateInvites();
            },
        });
    }

    function getInvitationData() {
        $.ajax({
            method: "GET",
            url: "/api/invitations/getinvitationdata" + "?company=" + $(".company-navbar").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                loadOnAny(".number", true);
            },
            success: (response) => {
                $("#invitations_accepted").html("" + response.data.invitation_accepted_count + "");
                $("#invitations_sent").html("" + response.data.invitation_sent_count + "");
                var commission_paid = response.data.commission_paid.split(/\s/g);
                $("#commission_paid").html(
                    commission_paid[0] + ' <strong class="font-size-30">' + commission_paid[1] + "</strong>"
                );
                var commission_pending = response.data.commission_pending.split(/\s/g);
                $("#commission_pending").html(
                    commission_pending[0] + ' <strong class="font-size-30">' + commission_pending[1] + "</strong>"
                );
                $("#invitations_amount").html("" + response.data.invitations_available);
                if (verifyAccountFrozen()) {
                    $("#store-invite").attr("disabled", true);
                }
                loadOnAny(".number", true);
            },
        });
    }

    function pagination(response, model) {
        if (response.meta.last_page == 1) {
            $("#pagination-invites").css({ background: "#f4f4f4" });
            $("#pagination-" + model).html("");
            $("#primeira_pagina_" + model).hide();
            $("#ultima_pagina_" + model).hide();
        } else {
            $("#pagination-" + model).html("");

            var first_page = "<button id='first_page' class='btn nav-btn'>1</button>";

            $("#pagination-" + model).append(first_page);

            if (response.meta.current_page == 1) {
                $("#pagination-invites").css({ background: "#ffffff" });
                $("#first_page").attr("disabled", true).addClass("nav-btn").addClass("active");
            }

            $("#first_page").on("click", function () {
                currentPage = 1;
                updateInvites("?page=1");
            });

            for (x = 3; x > 0; x--) {
                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $("#pagination-" + model).append(
                    "<button id='page_" +
                        (response.meta.current_page - x) +
                        "' class='btn nav-btn'>" +
                        (response.meta.current_page - x) +
                        "</button>"
                );

                $("#page_" + (response.meta.current_page - x)).on("click", function () {
                    currentPage = $(this).html();
                    updateInvites("?page=" + $(this).html());
                });
            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                var current_page =
                    "<button id='current_page' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

                $("#pagination-" + model).append(current_page);

                $("#current_page").attr("disabled", true).addClass("nav-btn").addClass("active");
            }
            for (x = 1; x < 4; x++) {
                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $("#pagination-" + model).append(
                    "<button id='page_" +
                        (response.meta.current_page + x) +
                        "' class='btn nav-btn'>" +
                        (response.meta.current_page + x) +
                        "</button>"
                );

                $("#page_" + (response.meta.current_page + x)).on("click", function () {
                    currentPage = $(this).html();
                    updateInvites("?page=" + $(this).html());
                });
            }

            if (response.meta.last_page != "1") {
                var last_page = "<button id='last_page' class='btn nav-btn'>" + response.meta.last_page + "</button>";

                $("#pagination-" + model).append(last_page);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#last_page").attr("disabled", true).addClass("nav-btn").addClass("active");
                }

                $("#last_page").on("click", function () {
                    currentPage = $(this).html();
                    updateInvites("?page=" + response.meta.last_page);
                });
            }
        }
    }

    //$('.company_name').val( $('.company-navbar').find('option:selected').text() );

    //ALTERAÇÃO DE HTML

    /*  function modalNotCompanies() {
          $('#mainModalBody').html(
              '<div id="modal-not-companies" class="modal-content p-10">' +
              '<div class="header-modal simple-border-bottom">' +
              '<h2 id="modal-title" class="modal-title">Ooooppsssss!</h2>' +
              '</div>' +
              '<div class="modal-body simple-border-bottom" style="padding-bottom:1%; padding-top:1%;">' +
              '<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display:flex;">' +
              '<span class="swal2-x-mark">' +
              '<span class="swal2-x-mark-line-left"></span>' +
              '<span class="swal2-x-mark-line-right"></span>' +
              '</span>' +
              '</div>' +
              '<h3 align="center">Você não cadastrou nenhuma empresa</h3>' +
              '<h5 align="center">Deseja cadastrar uma empresa?' +
              '<a class="red pointer" href="/companies">clique aqui</a>' +
              '</h5>' +
              '</div>' +
              '<div style="width:100%; text-align:center; padding-top:3%;">' +
              '<span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px;">Retornar</span>' +
              '</div>' +
              '</div>');
      }

      function modalThenCompanies() {
          let textModal = '';
          textModal += '<div id="modal-then-companies" class="modal-content">' +
              '<div class="modal-header">' +
              '<button type="button" id="btn-close-invite" class="close" data-dismiss="modal" aria-label="Close">' +
              '<span aria-hidden="true">×</span>' +
              '</button>' +
              '<h4 id="modal-reverse-title" class="modal-title" style="width: 100%; text-align:center"></h4>' +
              '</div>' +
              '<div id="modal-reverse-body" class="modal-body">' +
              '<div id="body-modal">' +
              '<div class="row">' +
              '<div class="form-group col-12">' +
              '<label for="email">Email do convidado</label>' +
              '<input name="email_invited" type="text" class="form-control" id="email" placeholder="Email">' +
              '</div>' +
              '</div>' +
              '<div class="row">' +
              '<div class="form-group col-12">' +
              '<label for="company">Empresa para receber</label>' +
              '<div id="company-list"></div>' +
              '<span>Para enviar convites a empresa tem que estar com os documentos aprovados </span>' +
              '</div>' +
              '</div>';
          if (disabledCompany) {
              textModal += '<div class="row">' +
                  '<div class="col-12">' +
                  '<label for="email">Link de convite</label>' +
                  '</div>' +
                  '<div id="invite-link-select" class="input-group col-12">' +
                  '<input type="text" class="form-control" id="invite-link" value="" readonly>' +
                  '<span class="input-group-btn">' +
                  '<button id="copy-link" class="btn btn-default" type="button">Copiar</button>' +
                  '</span>' +
                  '</div>' +
                  '</div>' +
                  '<div class="row" style="margin-top: 35px">' +
                  '<div class="form-group col-12">' +
                  '<input id="btn-send-invite" type="button" class="form-control btn col-sm-12 col-m-3 col-lg-3" value="Enviar Convite" ' +
                  'style="color:white;background-image: linear-gradient(to right, #e6774c, #f92278);position:relative; float:right">' +
                  '</div>' +
                  '</div>' +
                  '</div>' +
                  '</div>' +
                  '</div>';
          } else {
              textModal += '</div></div></div>';
          }

          $('#mainModalBody').html(textModal);
      }*/
});
