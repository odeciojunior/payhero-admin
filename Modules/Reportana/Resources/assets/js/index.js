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

    var companiesAndProjects = "";

    function index(loading = "y") {
        if (loading == "y") loadingOnScreen();
        else loadOnAny("#content");

        $hasProjects = false;
        if (companiesAndProjects.company_default_projects) {
            $.each(
                companiesAndProjects.company_default_projects,
                function (i, project) {
                    if (project.status == 1) $hasProjects = true;
                }
            );
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
                url:
                    "/api/apps/reportana?company=" + $(".company-navbar").val(),
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                error: (response) => {
                    loadOnAny("#content", true);
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: (response) => {
                    $("#project_id, #select_projects_edit").html("");
                    fillSelectProject(
                        companiesAndProjects,
                        "#project_id, #select_projects_edit"
                    );
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
                    if (loading == "y") loadOnAny("#content", true);
                    loadingOnScreenRemove();
                },
            });
        }
    }

    getCompaniesAndProjects().done(function (data) {
        companiesAndProjects = data;
        index();
    });

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
        $("#client_id").val("");
        $("#client_secret").val("");
        $(":checkbox").prop("checked", true).val(1);
        $("#project_id, #select_projects_edit")
            .prop("selectedIndex", 0)
            .change();
    }

    //draw the integration cards
    function renderIntegration(data) {
        $("#content").append(
            `
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project=` +
                data.id +
                ` style='cursor:pointer;'>

                <div class="d-flex align-items-center justify-content-center"   >
                <img class="card-img-top img-fluid w-full" src=` +
                data.project_photo +
                ` onerror="this.onerror=null;this.src='/build/global/img/produto.svg';" alt="` +
                data.project_name +
                `"/>
                </div>


                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-10'>
                                                <h4 class="card-title">` +
                data.project_name +
                `</h4>
                                                <p class="card-text sm">Criado em ` +
                data.created_at +
                `</p>
                                            </div>
                                            <div class='col-md-2'>
                                                <a role='button' title='Excluir' class='delete-integration float-right mt-35' project=` +
                data.id +
                `>
                                                    <img src="/build/global/img/icon-trash-new.svg" />
                                                </a>
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
        $(".modal-title").html("Adicionar nova Integração com Reportana");
        $("#bt_integration").addClass("btn-save");
        $("#bt_integration").removeClass("btn-update");
        $("#bt_integration").text("Adicionar integração");
        $("#modal_add_integracao").modal("show");
        $("#form_update_integration").hide();
        $("#form_add_integration").show();
        clearForm();
    });

    //edit
    $(document).on("click", ".card-edit", function () {
        $(".modal-title").html("Editar Integração com Reportana");
        $("#bt_integration").addClass("btn-update");
        $("#bt_integration").removeClass("btn-save");
        $("#bt_integration").text("Atualizar");
        $("#form_update_integration").show();
        $("#form_add_integration").hide();
        $("#modal_add_integracao").modal("show");

        $.ajax({
            method: "GET",
            url: "/api/apps/reportana/" + $(this).attr("project"),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                const integration = response.data;

                $("#select_projects_edit").val(integration.project_id);
                $("#integration_id").val(integration.id);
                $("#client_id_edit").val(integration.client_id);
                $("#client_secret_edit").val(integration.client_secret);

                $("#boleto_generated_edit")
                    .val(integration.boleto_generated)
                    .prop("checked", integration.boleto_generated === 1);

                $("#boleto_paid_edit")
                    .val(integration.boleto_paid)
                    .prop("checked", integration.boleto_paid === 1);

                $("#boleto_expired_edit")
                    .val(integration.boleto_expired)
                    .prop("checked", integration.boleto_expired === 1);

                $("#pix_generated_edit")
                    .val(integration.pix_generated)
                    .prop("checked", integration.pix_generated === 1);

                $("#pix_paid_edit")
                    .val(integration.pix_paid)
                    .prop("checked", integration.pix_paid === 1);

                $("#pix_expired_edit")
                    .val(integration.pix_expired)
                    .prop("checked", integration.pix_expired === 1);

                $("#credit_card_refused_edit")
                    .val(integration.credit_card_refused)
                    .prop("checked", integration.credit_card_refused === 1);

                $("#credit_card_paid_edit")
                    .val(integration.credit_card_paid)
                    .prop("checked", integration.credit_card_paid === 1);

                $("#abandoned_cart_edit")
                    .val(integration.abandoned_cart)
                    .prop("checked", integration.abandoned_cart === 1);
            },
        });
    });

    //store
    $(document).on("click", ".btn-save", function () {
        if ($("#client_id").val() == "") {
            alertCustom("error", "Dados informados inválidos 1");
            return false;
        }
        if ($("#client_secret").val() == "") {
            alertCustom("error", "Dados informados inválidos 2");
            return false;
        }
        var form_data = new FormData(
            document.getElementById("form_add_integration")
        );

        $.ajax({
            method: "POST",
            url: "/api/apps/reportana",
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
        if ($("#client_id_edit").val() == "") {
            alertCustom("error", "Dados informados inválidos 3");
            return false;
        }
        if ($("#client_secret_edit").val() == "") {
            alertCustom("error", "Dados informados inválidos 4");
            return false;
        }
        var integrationId = $("#integration_id").val();
        var form_data = new FormData(
            document.getElementById("form_update_integration")
        );

        $.ajax({
            method: "POST",
            url: "/api/apps/reportana/" + integrationId,
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
        var project = $(this).attr("project");
        $("#modal-delete-integration .btn-delete").attr("project", project);
        $("#modal-delete-integration").modal("show");
    });
    //destroy
    $(document).on(
        "click",
        "#modal-delete-integration .btn-delete",
        function (e) {
            e.stopPropagation();
            var project = $(this).attr("project");

            $.ajax({
                method: "DELETE",
                url: "/api/apps/reportana/" + project,
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr(
                        "content"
                    ),
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
        }
    );
});
