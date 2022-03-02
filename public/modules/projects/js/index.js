$(function () {
    index();

    function index() {
        loadingOnScreen();
        $.ajax({
            url: "/api/projects",
            data: {
                status: "active",
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                loadOnAny("#data-table-projects", true);
                errorAjaxResponse(response);
            },
            success: (response) => {
                if (verifyAccountFrozen()) {
                    $("#btn-config").css({visibility: "hidden"});
                } else {
                    $("#btn-config").css({visibility: "visible"});
                }
                if (response.data.length) {

                    $("#project-empty").hide();
                    $("#company-empty").hide();

                    $.each(response.data, (key, project) => {
                        if (verifyAccountFrozen()) {
                            linkProject = "";
                        } else {
                            linkProject = `<a href="/projects/${project.id}${
                                project.affiliated
                                    ? "/" + project.affiliate_id
                                    : ""
                            }" class="stretched-link"></a>`;
                        }
                        let data = `<div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 name_project" data-id="${
                            project.id
                        }">
                                        <div class="card">

                                            ${project.woocommerce_id != null ? '<div class="ribbon ribbon-woo"><span >WooCommerce <a class="ribbon-woocommerce-default"></a></span></div>' : ''}


                                            ${
                            project.shopify_id != null &&
                            !project.affiliated
                                ? '<div class="ribbon"><span>Shopify <a class="ribbon-shopify-default"></a></span></div>'
                                : ""
                        }
                                            ${
                            project.affiliated
                                ? '<div class="ribbon-left"><span>Afiliado</span></div>'
                                : ""
                        }
                                            <img class="card-img-top" onerror="this.src = '/modules/global/img/produto.svg'" src="${
                            project.photo
                                ? project.photo
                                : "/modules/global/img/produto.svg"
                        }" alt="${project.name}">
                                            <div class="card-body">
                                                <h5 class="card-title">${
                            project.name
                        }</h5>
                                                <p class="card-text sm">Criado em ${
                            project.created_at
                        }</p>
                                                ${linkProject}
                                            </div>
                                        </div>
                                    </div>`;
                        $("#data-table-projects").append(data);
                        if (verifyAccountFrozen()) {
                            $("#btn-add-project").hide();
                        } else {
                            $("#btn-add-project").show();
                        }
                    });

                    Sortable.create(
                        document.getElementById("data-table-projects"),
                        {
                            onEnd: function (evt) {
                                var orderProjects = [];
                                var listCompanies = $("#data-table-projects");
                                $(listCompanies)
                                    .find(".name_project")
                                    .each(function (index, tr) {
                                        orderProjects.push($(tr).data("id"));
                                    });

                                $.ajax({
                                    method: "POST",
                                    url: "/api/projects/updateorder",
                                    dataType: "json",
                                    data: {order: orderProjects},
                                    headers: {
                                        Authorization: $(
                                            'meta[name="access-token"]'
                                        ).attr("content"),
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
                        }
                    );
                } else {
                    $("#data-table-projects").hide();
                    $("#btn-config").css({visibility: "hidden"});
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
    }

    $("#btn-config").on("click", function () {
        $("#modal_config").modal("show");
        if ($("#deleted_project_filter").val() == 1) {
            $("#deleted_project_filter").attr("checked", "checked");
        } else {
            $("#deleted_project_filter").attr("checked", false);
        }
    });

    $("#btn_save_config").on("click", function () {
        $.ajax({
            method: "POST",
            url: "/api/projects/updateconfig",
            dataType: "json",
            data: {
                deleted_project_filter: $("#deleted_project_filter").val(),
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
                alertCustom("success", response.message);
            },
        });
    });

    $(".check").on("change", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });
});
