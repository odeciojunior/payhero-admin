$(document).ready(function () {
    let projectId = $(window.location.pathname.split("/")).get(-1);
    let countCompanies;
    let countCompanyApproved = 0;

    getProjectData();
    getCompanyData();

    function getProjectData() {
        loadOnAny(".page-content");
        $.ajax({
            method: "GET",
            url: "/api/affiliates/" + projectId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadOnAny(".page-content", true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                loadOnAny(".page-content", true);
                $(".page-content").show();

                if (response.data.status_url_affiliates) {
                    $(".div-project").show();
                    $(".project-header").html(`Afiliação na loja ${response.data.name}`);
                    $(".project-image").prop(
                        "src",
                        `${response.data.photo ? response.data.photo : "/build/global/img/projeto.svg"}`
                    );
                    $("#created_by").html(`Produtor: ${response.data.user_name}`);
                    $(".text-about-project").html(response.data.description);
                    if (response.data.url_page != null && response.data.url_page != "https://") {
                        $(".url_page").html(
                            ` <strong >URL da página principal: <a href='${response.data.url_page}' target='_blank'>${response.data.url_page}</a></strong>`
                        );
                    } else {
                        $(".url_page").html(` <strong >URL da página principal: Não configurado</strong>`);
                    }
                    $(".created_at").html(` <strong >Criado em: ${response.data.created_at}</strong>`);
                    if (!response.data.producer) {
                        if (response.data.affiliatedMessage != "") {
                            $(".div-button").html(
                                `<div class="alert alert-info">${response.data.affiliatedMessage}</div>`
                            );
                            $(".div-button").toggleClass("text-center");
                        } else {
                            if (response.data.automatic_affiliation) {
                                $(".div-button").html(
                                    '<button id="btn-affiliation" class="btn btn-primary" data-type="affiliate">Confimar Afiliação</button>'
                                );
                            } else {
                                $(".div-button").html(
                                    '<button id="btn-affiliation" class="btn btn-primary" data-type="affiliate_request">Solicitar Afiliação</button>'
                                );
                            }
                        }
                    }
                    if (response.data.percentage_affiliates != null) {
                        $(".percentage-affiliate").html(
                            `<strong>Porcentagem de afiliado: <span class='green-gradient'>${response.data.percentage_affiliates}%</span></strong>`
                        );
                    } else {
                        $(".percentage-affiliate").html(
                            `<strong>Porcentagem de afiliado: <span class='green-gradient'>Não configurado</span></strong>`
                        );
                    }
                    if (response.data.cookie_duration != null) {
                        if (response.data.cookie_duration == 0) {
                            $(".cookie_duration").html(
                                ` <strong>Duração do cookie: <span class='green-gradient'>Eterno</span></strong>`
                            );
                        } else if (response.data.cookie_duration >= 1) {
                            $(".cookie_duration").html(
                                ` <strong>Duração do cookie: <span class='green-gradient'>${response.data.cookie_duration} dias</span></strong>`
                            );
                        }
                    } else {
                        $(".cookie_duration").html(
                            ` <strong>Duração do cookie: <span class='green-gradient'>Não configurado</span></strong>`
                        );
                    }
                    if (response.data.terms_affiliates != null) {
                        $(".text-terms").html(response.data.terms_affiliates);
                    } else {
                        $(".text-terms").html("<strong >Não possui termos de afiliação.</strong>");
                    }
                    if (response.data.contact != null) {
                        $(".contact").html(`<strong>E-mail: ${response.data.contact}</strong>`);
                    } else {
                        $(".contact").html(`<strong>E-mail: Não configurado</strong>`);
                    }
                    if (response.data.support_phone != null) {
                        $(".support_phone").html(`<strong>Telefone: ${response.data.support_phone}</strong>`);
                    } else {
                        $(".support_phone").html(`<strong>Telefone: Não configurado</strong>`);
                    }

                    $(".release_days").html(
                        `<strong>Dias para liberar dinheiro: <span class='green-gradient'>${response.data.billet_release_days}</span> dias</strong>`
                    );
                } else {
                    swal({
                        title: "Essa loja não está disponível para afiliação",
                        type: "warning",
                        confirmButtonColor: "#EC6421",
                        confirmButtonClass: "btn btn-warning",
                        confirmButtonText: "OK",
                    }).then((result) => {
                        window.location.replace("/dashboard");
                    });
                }
            },
        });
    }

    function getCompanyData() {
        $.ajax({
            method: "GET",
            url: "/api/core/usercompanies",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {},
            success: (response) => {
                countCompanies = response.data.length;

                $(response.data).each(function (index, company) {
                    if (companyIsApproved(company)) {
                        countCompanyApproved++;
                        $("#companies").append(`
                            <option value='${company.id}'>
                                ${
                                    company.company_document_status == "pending"
                                        ? company.name + " (documentos pendentes)"
                                        : company.name
                                }
                            </option>
                        `);
                    }
                });
            },
        });
    }

    $(document).on("click", "#btn-affiliation", function () {
        if (countCompanies == 0) {
            $("#modal-not-companies").modal("show");
        } else if (countCompanyApproved == 0) {
            $("#modal-not-companies-approved").modal("show");
        } else {
            $("#modal_store_affiliate").modal("show");
        }
    });

    $(document).on("click", "#btn-store-affiliation", function () {
        loadingOnScreen();
        let type = $("#btn-affiliation").data("type");
        $("#btn-affiliation").hide();
        $.ajax({
            method: "POST",
            url: "/api/affiliates",
            dataType: "json",
            data: {
                project_id: projectId,
                type: type,
                company_id: $("#companies").val(),
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                $("#modal_store_affiliate").modal("hide");
                loadingOnScreenRemove();
                $("#btn-affiliation").show();
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#modal_store_affiliate").modal("hide");
                loadingOnScreenRemove();
                alertCustom("success", response.message);
                if (response.type == "affiliate") {
                    window.location = "/projects";
                } else {
                    setTimeout(function () {
                        window.location = "/dashboard";
                    }, 4000);
                }
            },
        });
    });
});
