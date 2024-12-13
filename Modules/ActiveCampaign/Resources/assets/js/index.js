$(document).ready(function () {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#integration-actions").hide();
        $("#no-integration-found").hide();
        $("#project-empty").hide();
        loadOnAny("#content");
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects().done(function (data2) {
                companiesAndProjects = data2;
                index("n");
            });
        });
    });

    let companiesAndProjects = "";

    getCompaniesAndProjects().done(function (data) {
        companiesAndProjects = data;
        index();
    });

    function index(loading = "y") {
        if (loading == "y") loadingOnScreen();
        else {
            $("#content").html("");
            loadOnAny("#content");
        }

        $hasProjects = false;
        if (companiesAndProjects.company_default_projects) {
            $.each(companiesAndProjects.company_default_projects, function (i, project) {
                if (project.status == 1) $hasProjects = true;
            });
        }

        if (!$hasProjects) {
            $("#integration-actions").hide();
            $("#no-integration-found").hide();
            $("#project-empty").show();
            loadingOnScreenRemove();
            loadOnAny("#content", true);
        } else {
            $.ajax({
                method: "GET",
                url: "/api/apps/activecampaign?company=" + $(".company-navbar").val(),
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: (response) => {
                    loadOnAny("#content", true);
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    $("#project_id, #select_projects_edit").html("");
                    fillSelectProject(companiesAndProjects, "#project_id, #select_projects_edit");

                    if (isEmpty(response.integrations)) {
                        $("#no-integration-found").show();
                    } else {
                        $("#content").html("");
                        let integrations = response.integrations;
                        for (let i = 0; i < integrations.length; i++) {
                            renderIntegration(integrations[i]);
                        }
                        $("#no-integration-found").hide();
                    }
                    $("#project-empty").hide();
                    $("#integration-actions").show();
                    // }
                    if (loading == "y") loadingOnScreenRemove();
                    loadOnAny("#content", true);
                },
            });
        }
    }

    //checkbox
    $(".check").on("click", function () {
        if ($(this).is(":checked")) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
    });

    //reset the intergation modal
    function clearForm() {
        $(":text").val("");
        $(":checkbox").prop("checked", true).val(1);
        $("#project_id, #select_projects_edit").prop("selectedIndex", 0).change();
    }

    //draw the integration cards
    function renderIntegration(data) {
        $("#content").append(
            `




                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project=` +
                data.id +
                ` style='cursor:pointer;'>
                                    <a href="/apps/activecampaign/${data.id}" class="activecampaign-link">

                                    <div class="d-flex align-items-center justify-content-center" style="" >
                                        <img class="card-img-top img-fluid w-full" src=` +
                data.project_photo +
                ` onerror="this.onerror=null;this.src='/build/global/img/produto.svg';" alt="` +
                data.project_name +
                `"/>
                </div>

                                    </a>
                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-10'>
                                                <a href="/apps/activecampaign/${data.id}" class="activecampaign-link">
                                                    <h4 class="card-title">` +
                data.project_name +
                `</h4>
                                                    <p class="card-text sm">Criado em ` +
                data.created_at +
                `</p>
                                                </a>
                                            </div>
                                            <div class='col-md-2'>
                                                <div role='button' title='Excluir' class='delete-integration pointer float-right mt-35' project=` +
                data.id +
                `>

                                                    <img src="/build/global/img/icon-trash-new.svg" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `
        );
    }

    //create
    $("#btn-add-integration").on("click", function () {
        $(".modal-title").html("Adicionar nova Integração com ActiveCampaign");
        $("#bt_integration_add").addClass("btn-save");
        $("#bt_integration_add").removeClass("btn-update");
        $("#bt_integration_add").text("Adicionar integração");
        $("#modal_add_integracao").modal("show");
        $("#form_update_integration").hide();
        $("#form_add_integration").show();
        clearForm();
    });

    //store
    $(document).on("click", ".btn-save", function () {
        if ($("#link").val() == "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }
        let form_data = new FormData(document.getElementById("form_add_integration"));

        $.ajax({
            method: "POST",
            url: "/api/apps/activecampaign",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                index();
                alertCustom("success", response.message);
            },
        });
    });

    //update
    $(document).on("click", ".btn-update", function () {
        if ($("#link_edit").val() == "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }
        let integrationId = $("#integration_id").val();
        let form_data = new FormData(document.getElementById("form_update_integration"));

        $.ajax({
            method: "POST",
            url: "/api/apps/activecampaign/" + integrationId,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                index();
                alertCustom("success", response.message);
            },
        });
    });

    // load delete modal
    $(document).on("click", ".delete-integration", function (e) {
        e.stopPropagation();
        let project = $(this).attr("project");
        let card = $(this).parent().parent().parent().parent().parent();
        card.find(".card-edit").unbind("click");
        $("#modal-delete-integration .btn-delete").attr("project", project);
        $("#modal-delete-integration").modal("show");
    });
    //destroy
    $(document).on("click", "#modal-delete-integration .btn-delete", function (e) {
        e.stopPropagation();
        let project = $(this).attr("project");

        $.ajax({
            method: "DELETE",
            url: "/api/apps/activecampaign/" + project,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                index();
                alertCustom("success", response.message);
            },
        });
    });
});
