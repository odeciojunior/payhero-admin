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
                    "/api/apps/astronmembers?company=" +
                    $(".company-navbar").val(),
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
        $(":text").val("");
        $(":checkbox").prop("checked", true).val(1);
        $("#project_id").prop("selectedIndex", 0).change();
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
        $(".modal-title").html("Adicionar nova Integração com Astron Members");
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
        $(".modal-title").html("Editar Integração com Astron Members");
        $("#bt_integration").addClass("btn-update");
        $("#bt_integration").removeClass("btn-save");
        $("#bt_integration").text("Atualizar");
        $("#form_update_integration").show();
        $("#form_add_integration").hide();
        $("#modal_add_integracao").modal("show");

        $.ajax({
            method: "GET",
            url: "/api/apps/astronmembers/" + $(this).attr("project"),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                $("#select_projects_edit").val(response.data.project_id);
                $("#integration_id").val(response.data.id);
                $("#link_edit").val(response.data.link);
                $("#token_edit").val(response.data.token);

                $("#boleto_generated_edit").val(response.data.boleto_generated);
                $("#boleto_generated_edit").prop(
                    "checked",
                    $("#boleto_generated_edit").val() == "1"
                );

                $("#boleto_paid_edit").val(response.data.boleto_paid);
                $("#boleto_paid_edit").prop(
                    "checked",
                    $("#boleto_paid_edit").val() == "1"
                );

                $("#credit_card_refused_edit").val(
                    response.data.credit_card_refused
                );
                $("#credit_card_refused_edit").prop(
                    "checked",
                    $("#credit_card_refused_edit").val() == "1"
                );

                $("#credit_card_paid_edit").val(response.data.credit_card_paid);
                $("#credit_card_paid_edit").prop(
                    "checked",
                    $("#credit_card_paid_edit").val() == "1"
                );

                $("#abandoned_cart_edit").val(response.data.abandoned_cart);
                $("#abandoned_cart_edit").prop(
                    "checked",
                    $("#abandoned_cart_edit").val() == "1"
                );

                $("#pix_generated_edit").val(response.data.pix_generated);
                $("#pix_generated_edit").prop(
                    "checked",
                    $("#pix_generated_edit").val() == "1"
                );
                $("#pix_paid_edit").val(response.data.pix_paid);
                $("#pix_paid_edit").prop(
                    "checked",
                    $("#pix_paid_edit").val() == "1"
                );
                $("#pix_expired_edit").val(response.data.pix_expired);
                $("#pix_expired_edit").prop(
                    "checked",
                    $("#pix_expired_edit").val() == "1"
                );
            },
        });
    });

    //store
    $(document).on("click", ".btn-save", function () {
        if ($("#link").val() == "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }
        let form_data = new FormData(
            document.getElementById("form_add_integration")
        );

        $.ajax({
            method: "POST",
            url: "/api/apps/astronmembers",
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
        let form_data = new FormData(
            document.getElementById("form_update_integration")
        );

        $.ajax({
            method: "POST",
            url: "/api/apps/astronmembers/" + integrationId,
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
        $("#modal-delete-integration .btn-delete").attr("project", project);
        $("#modal-delete-integration").modal("show");
    });
    //destroy
    $(document).on(
        "click",
        "#modal-delete-integration .btn-delete",
        function (e) {
            e.stopPropagation();
            let project = $(this).attr("project");
            $.ajax({
                method: "DELETE",
                url: "/api/apps/astronmembers/" + project,
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
