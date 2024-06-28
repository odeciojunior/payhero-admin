$(document).ready(function () {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#integration-actions").hide();
        $("#no-integration-found").hide();
        $("#project-empty").hide();
        loadOnAny("#content");
    });

    function index(loading = true) {
        if (loading) {
            loadingOnScreen();
        } else {
            loadOnAny("#content");
        }

        $.ajax({
            method: "GET",
            url: "/api/apps/utmify?company=" + $(".company-navbar").val(),
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

                $.each(response.projects, function (i, project) {
                    $("#project_id, #select_projects_edit").append(
                        $("<option>", { value: project.id, text: project.name })
                    );
                });

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
                if (loading) loadOnAny("#content", true);
                loadingOnScreenRemove();
            },
        });
    }

    index();

    // Reset the integration modal
    function clearForm() {
        $("#token").val("");
        $("#project_id, #select_projects_edit").prop("selectedIndex", 0).change();
    }

    // Draw the integration cards
    function renderIntegration(data) {
        $("#content").append(`
            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 integration-card" project="${data.id}">
                <div class="card shadow card-edit" style='cursor:pointer;'>
                    <div class="d-flex align-items-center justify-content-center">
                        <img class="card-img-top img-fluid w-full" src="${
                            data.project_photo || "/build/global/img/produto.svg"
                        }"
                             onerror="this.onerror=null;this.src='/build/global/img/produto.svg';"
                             alt="${data.project_name}" />
                    </div>
                    <div class="card-body">
                        <div class='row'>
                            <div class='col-md-10'>
                                <h4 class="card-title">${data.project_name}</h4>
                                <p class="card-text sm">Criado em ${data.created_at}</p>
                            </div>
                            <div class='col-md-2'>
                                <a role='button' title='Excluir' class='delete-integration float-right mt-35' project="${
                                    data.id
                                }">
                                    <img src="/build/global/img/icon-trash-new.svg" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `);
    }

    // Create
    $("#btn-add-integration").on("click", function () {
        $(".modal-title").html("Adicionar nova Integração com Utmify");
        $("#bt_integration").addClass("btn-save");
        $("#bt_integration").removeClass("btn-update");
        $("#bt_integration").text("Adicionar integração");
        $("#modal_add_integracao").modal("show");
        $("#form_update_integration").hide();
        $("#form_add_integration").show();
        clearForm();
    });

    // Edit
    $(document).on("click", ".card-edit", function () {
        const projectId = $(this).closest(".integration-card").attr("project");

        $.ajax({
            method: "GET",
            url: "/api/apps/utmify/" + projectId,
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
                $("#token_edit").val(integration.token);

                $(".modal-title").html("Editar Integração com Utmify");
                $("#bt_integration").addClass("btn-update").removeClass("btn-save").text("Atualizar");
                $("#form_update_integration").show();
                $("#form_add_integration").hide();
                $("#modal_add_integracao").modal("show");
            },
        });
    });

    // Store
    $(document).on("click", ".btn-save", function () {
        if ($("#token").val() === "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }
        var form_data = new FormData(document.getElementById("form_add_integration"));

        loadingOnScreen();
        let description = "UTMIFY";
        let companyHash = $(".company-navbar").val();

        storeIntegration(description, companyHash)
            .then(function (response) {
                $.ajax({
                    method: "POST",
                    url: "/api/apps/utmify",
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
                        loadingOnScreenRemove();
                    },
                    success: (response) => {
                        index();
                        loadingOnScreenRemove();
                        alertCustom("success", response.message);
                    },
                });
            })
            .catch(function (error) {
                loadingOnScreenRemove();
                console.error("Erro ao executar storeIntegration:", error);
            });
    });

    $(document).on("click", ".btnCopiarLinkToken", function () {
        var tmpInput = $("<input>");
        $("body").append(tmpInput);
        var copyText = $("#token_edit").val();
        tmpInput.val(copyText).select();
        document.execCommand("copy");
        tmpInput.remove();
        alertCustom("success", "Token copiado!");
    });

    function storeIntegration(description, companyHash) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                method: "POST",
                url: "/api/integrations",
                data: {
                    description: description,
                    company_id: companyHash,
                },
                dataType: "json",
                headers: {
                    Authorization: $('meta[name="access-token"]').attr("content"),
                    Accept: "application/json",
                },
                error: (response) => {
                    errorAjaxResponse(response);
                    reject(response);
                },
                success: (response) => {
                    resolve(response);
                },
            });
        });
    }

    // Update
    $(document).on("click", ".btn-update", function () {
        if ($("#token_edit").val() === "") {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }
        var integrationId = $("#integration_id").val();
        var form_data = new FormData(document.getElementById("form_update_integration"));
        $.ajax({
            method: "POST",
            url: "/api/apps/utmify/" + integrationId,
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

    // Load delete modal
    $(document).on("click", ".delete-integration", function (e) {
        e.stopPropagation();
        var project = $(this).attr("project");
        $("#modal-delete-integration .btn-delete").attr("project", project);
        $("#modal-delete-integration").modal("show");
    });

    // Destroy
    $(document).on("click", "#modal-delete-integration .btn-delete", function (e) {
        e.stopPropagation();
        var project = $(this).attr("project");

        $.ajax({
            method: "DELETE",
            url: "/api/apps/utmify/" + project,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (response) => {
                // Remove the integration card from the screen
                $(`.integration-card[project="${project}"]`).remove();
                index();
                alertCustom("success", response.message);
            },
        });
    });
});
