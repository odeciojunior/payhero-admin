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
                    "/api/apps/whatsapp2?company=" + $(".company-navbar").val(),
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
                    $("#inputTokenWhats2").val(response.token_whatsapp2);
                    if (isEmpty(response.integrations)) {
                        $("#no-integration-found").show();
                    } else {
                        $("#content").html("");
                        for (let integration of response.integrations) {
                            renderIntegration(integration);
                        }
                        $("#no-integration-found").hide();
                    }
                    $("#project-empty").hide();
                    $("#integration-actions").show();
                    // }
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

    $("#btnCopyTokenWhats2").on("click", function () {
        var copyText = document.getElementById("inputTokenWhats2");
        copyText.select();
        document.execCommand("copy");
        alertCustom("success", "Token copiado!");
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
        $("#url_checkout").val("");
        $("#url_order").val("");
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
        $(".modal-title").html("Adicionar nova Integração com SAK");
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
        $(".modal-title").html("Editar Integração com SAK");
        $("#bt_integration").addClass("btn-update");
        $("#bt_integration").removeClass("btn-save");
        $("#bt_integration").text("Atualizar");
        $("#form_update_integration").show();
        $("#form_add_integration").hide();
        $("#modal_add_integracao").modal("show");

        $.ajax({
            method: "GET",
            url: "/api/apps/whatsapp2/" + $(this).attr("project"),
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
                $("#url_checkout_edit").val(response.data.url_checkout);
                $("#url_order_edit").val(response.data.url_order);

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

                $("#pix_expired_edit").val(response.data.pix_expired);
                $("#pix_expired_edit").prop(
                    "checked",
                    $("#pix_expired_edit").val() == "1"
                );

                $("#pix_paid_edit").val(response.data.pix_paid);
                $("#pix_paid_edit").prop(
                    "checked",
                    $("#pix_paid_edit").val() == "1"
                );
            },
        });
    });

    //store
    $(document).on("click", ".btn-save", function () {
        if (
            $("#url_checkout").val().length < 0 ||
            $("#url_order").val().length < 0
        ) {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }

        $.ajax({
            method: "POST",
            url: "/api/apps/whatsapp2",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: new FormData(document.getElementById("form_add_integration")),
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
        if (
            $("#url_checkout_edit").val().length < 0 ||
            $("#url_order_edit").val().length < 0
        ) {
            alertCustom("error", "Dados informados inválidos");
            return false;
        }

        $.ajax({
            method: "POST",
            url: "/api/apps/whatsapp2/" + $("#integration_id").val(),
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            processData: false,
            contentType: false,
            cache: false,
            data: new FormData(
                document.getElementById("form_update_integration")
            ),
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
            var project = $(this).attr("project");
            var card = $(
                "a[class='delete-integration float-right mt-35'][project='" +
                    project +
                    "']"
            )
                .parent()
                .parent()
                .parent()
                .parent()
                .parent();
            card.find(".card-edit").unbind("click");
            $.ajax({
                method: "DELETE",
                url: "/api/apps/whatsapp2/" + project,
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
