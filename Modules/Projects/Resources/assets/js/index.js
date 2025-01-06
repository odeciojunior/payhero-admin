$(function () {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#subtitle_drag_drop").addClass("d-none");
        $("#data-table-projects").empty();
        $("#project-empty").hide();
        $("#new-store-button").hide();
        loadingSkeletonCards($("#data-table-projects"));
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects(removeLoadingSkeletonCards).done(function (data2) {
                companiesAndProjects = data2;
                index("n");
            });
        });
    });

    loadingSkeletonCards($("#data-table-projects"));

    var companiesAndProjects = "";

    getCompaniesAndProjects().done(function (data) {
        companiesAndProjects = data;
        index();
    });

    // Funcao Responsavel por gerar cards de cada projeto
    index = function (loading = "y") {
        $.ajax({
            url: "/api/projects",
            data: {
                company: $(".company-navbar").val(),
                status: "active",
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                removeLoadingSkeletonCards();
                let deleteProjectsShowOrHidde = $("#deleted_project_filter");
                if (deleteProjectsShowOrHidde.val() === "1") {
                    $("#deleted_project_filter").prop("checked", true);
                }

                if (response.data.length) {
                    $("#project-empty").hide();
                    $("#company-empty").hide();

                    $.each(response.data, (key, project) => {
                        if (verifyAccountFrozen()) {
                            linkProject = "";
                        } else {
                            linkProject = `<a href="/projects/${project.id}${
                                project.affiliated ? "/" + project.affiliate_id : ""
                            }" class="stretched-link"></a>`;
                        }

                        let data = `
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 name_project" data-id="${project.id}">
                                <div class="card">
                                    ${
                                        project.woocommerce_id != null
                                            ? `<div class="ribbon ribbon-woo">
                                            <span>WooCommerce
                                                <a class="ribbon-woocommerce-default"></a>
                                            </span>
                                        </div>`
                                            : ""
                                    }
                                    ${
                                        project.shopify_id != null && !project.affiliated
                                            ? `<div class="ribbon">
                                            <span>Shopify
                                                <a class="ribbon-shopify-default"></a>
                                            </span>
                                        </div>`
                                            : ""
                                    }
                                    ${
                                        project.nuvemshop_id != null
                                            ? `<div class="ribbon ribbon-nuvem">
                                                    <span>Nuvemshop</span>
                                                </div>`
                                            : ""
                                    }
                                    ${
                                        project.affiliated
                                            ? `<div class="ribbon-left">
                                            <span>Afiliado</span>
                                        </div>`
                                            : ""
                                    }
                                    <img class="card-img-top" onerror="this.src = 'build/global/img/produto.svg'" src="${
                                        project.photo ? project.photo : "build/global/img/produto.svg"
                                    }" alt="${project.name}">
                                    <div class="card-body">
                                        <h5 class="card-title text-truncate">${project.name}</h5>
                                        <div class="d-flex align-item-center justify-content-between">
                                            <p class="card-text sm mb-0">Criado em ${project.created_at}</p>
                                            <img src="build/layouts/projects/img/dragItem.svg" class="drag-drop-icon p-5"/>
                                        </div>
                                        ${linkProject}
                                    </div>
                                </div>
                            </div>`;

                        $("#data-table-projects").append(data);
                        if (verifyAccountFrozen()) {
                            $("#new-store-button").hide();
                        } else {
                            $("#new-store-button").show();
                        }
                    });
                    verifyHasOnlyOne();
                } else {
                    $("#subtitle_drag_drop").addClass("d-none");
                    $("#btn-config").css({ visibility: "hidden" });
                    if (response.no_company) {
                        $("#company-empty").show();
                        $("#project-empty").hide();
                    } else {
                        $("#project-empty").show();
                        $("#company-empty").hide();
                    }
                }
                loadingOnScreenRemove();
            },
        });
    };

    // Funcao responsavel pelo Arrastar e soltar(DRAG e DROP)
    const sortableElement = $("#data-table-projects");
    sortableElement.sortable({
        containment: "window",
        opacity: 1,
        revert: true,
        tolerance: "pointer",
        cursor: "move",
        disabled: "",
        update: function (event, ui) {
            let projectOrder = $(this).sortable("toArray", {
                attribute: "data-id",
            });
            $.ajax({
                method: "POST",
                url: "/api/projects/updateorder",
                dataType: "json",
                data: { order: projectOrder },
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: function (response) {
                    errorAjaxResponse(response);
                },
                success: function success(data) {
                    alertCustom("success", data.message);
                },
            });
        },
        start: function (event, ui) {
            ui.helper.css({
                "margin-top": $("body").scrollTop(),
                top: "0px",
            });
        },
        beforeStop: function (event, ui) {
            ui.helper.css("margin-top", 0);
        },
    });

    // Se existir apenas um prjeto esconder todo conteudo drag e drop
    function verifyHasOnlyOne() {
        let hasOnlyOne = $("#data-table-projects").children().length <= 1;
        if (hasOnlyOne) {
            $("img.drag-drop-icon").hide();
            $("#subtitle_drag_drop").addClass("d-none");
            sortableElement.sortable({
                disabled: true,
            });
        } else $("#subtitle_drag_drop").removeClass("d-none");
    }

    // Seta valor do filtro toggle(ALTERNANCIA) para exibir/esconder projetos
    $(".check").on("change", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    // Exibi / Esconde projetos Excluidos
    $("#deleted_project_filter").on("change", function () {
        let showProjectsDeleteds = $("#deleted_project_filter").val();
        $.ajax({
            method: "POST",
            url: "/api/projects/updateconfig",
            dataType: "json",
            data: {
                deleted_project_filter: showProjectsDeleteds,
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                $("#modal_config").modal("hide");
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#modal_config").modal("hide");
                //location.reload(true);
                //index();
                alertCustom("success", response.message);
            },
        });
    });
});
