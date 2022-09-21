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

        var hasProjects = false;
        if (companiesAndProjects.company_default_projects) {
            $.each(companiesAndProjects.company_default_projects, function (i, project) {
                if (project.status == 1) hasProjects = true;
            });
        }

        if (!hasProjects) {
            $("#integration-actions").hide();
            $("#no-integration-found").hide();
            $("#project-empty").show();
            loadingOnScreenRemove();
            loadOnAny("#content", true);
        } else {
            $.ajax({
                method: "GET",
                url: "/api/apps/notazz?company=" + $(".company-navbar").val(),
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
                    if (isEmpty(response.data)) {
                        $("#no-integration-found").show();
                    } else {
                        $("#content").html("");
                        $(response.data).each(function (index, data) {
                            $("#content").append(
                                `
                                <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                    <div class="card shadow show-integration" integration=` +
                                    data.id +
                                    `>
                                        <a href='/apps/notazz/${data.id}' class=''>
                                            <img class="card-img-top img-fluid w-full" src="${
                                                !data.project_photo
                                                    ? "/build/global/img/produto.png"
                                                    : data.project_photo
                                            }" style='cursor:pointer'/>
                                        </a>
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
                                                    <a role='button' class='edit-integration pointer float-right' integration=` +
                                    data.id +
                                    ` data-toggle='modal' data-target='#modal-edit' type='a'>
                                                        <span class="o-edit-1"></span>
                                                    </a>
                                                    <a role='button' class='delete-integration pointer float-right mt-10' integration=` +
                                    data.id +
                                    ` data-toggle='modal' data-target='#modal-delete' type='a'>
                                                        <span class='o-bin-1'></span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `
                            );
                            $("#active_flag_" + data.id).val(data.active_flag);
                            $("#active_flag_" + data.id).prop("checked", $("#active_flag_" + data.id).val() == "1");
                        });
                        $("#no-integration-found").hide();
                        $(".delete-integration").unbind("click");
                        // load delete modal
                        $(document).on("click", ".delete-integration", function (e) {
                            e.stopPropagation();
                            let integration_id = $(this).attr("integration");
                            $("#modal-delete-integration .btn-delete").attr("integration", integration_id);
                            $("#modal-delete-integration").modal("show");
                        });
                        $("#modal-delete-integration .btn-delete").on("click", function (e) {
                            e.preventDefault();
                            var integration_id = $(this).attr("integration");
                            var card = $(
                                "a[class='delete-integration pointer float-right mt-10'][integration='" +
                                    integration_id +
                                    "']"
                            )
                                .parent()
                                .parent()
                                .parent()
                                .parent()
                                .parent();
                            card.find(".edit-integration").unbind("click");
                            $.ajax({
                                method: "DELETE",
                                url: "/api/apps/notazz/" + integration_id,
                                headers: {
                                    Authorization: $('meta[name="access-token"]').attr("content"),
                                    Accept: "application/json",
                                },
                                dataType: "json",
                                error: (function (_error2) {
                                    function error(_x2) {
                                        return _error2.apply(this, arguments);
                                    }

                                    error.toString = function () {
                                        return _error2.toString();
                                    };

                                    return error;
                                })(function (response) {
                                    if (response.status === 422) {
                                        for (error in response.responseJSON.errors) {
                                            alertCustom("error", String(response.responseJSON.errors[error]));
                                        }
                                    } else {
                                        alertCustom("error", String(response.responseJSON.message));
                                    }
                                }),
                                success: function success(response) {
                                    index("n");
                                    alertCustom("success", response.message);
                                },
                            });
                        });

                        $(".edit-integration").unbind("click");
                        $(".edit-integration").on("click", function () {
                            var integration_id = $(this).attr("integration");
                            fillSelectProject(companiesAndProjects, "#select_projects_edit");

                            $(".modal-title").html("Editar Integração com Notazz");
                            $.ajax({
                                method: "GET",
                                url: "/api/apps/notazz/" + integration_id,
                                headers: {
                                    Authorization: $('meta[name="access-token"]').attr("content"),
                                    Accept: "application/json",
                                },
                                dataType: "json",
                                error: function error(response) {
                                    //
                                },
                                success: function success(response) {
                                    $("#select_projects_edit").val(response.data.project_id);
                                    $("#select_invoice_type_edit").val(response.data.invoice_type);
                                    $("#integration_id").val(response.data.id);
                                    $("#token_api_edit").val(response.data.token_api);
                                    $("#token_webhook_edit").val(response.data.token_webhook);
                                    $("#token_logistics_edit").val(response.data.token_logistics);
                                    $("#start_date_edit").val(response.data.start_date);
                                    $("#select_pending_days_edit").val(response.data.pending_days);

                                    $("#emit_zero_edit").val(response.data.emit_zero);
                                    $("#emit_zero_edit").prop("checked", $("#emit_zero_edit").val() == "1");

                                    $("#remove_tax_edit").val(response.data.remove_tax);
                                    $("#remove_tax_edit").prop("checked", $("#remove_tax_edit").val() == "1");

                                    $("#active_flag").val(response.data.active_flag);
                                    $("#active_flag").prop("checked", $("#active_flag").val() == "1");

                                    $("#modal_add_integracao").modal("show");
                                    $("#form_add_integration").hide();
                                    $("#form_update_integration").show();
                                    $("#bt_integration").addClass("btn-update");
                                    $("#bt_integration").removeClass("btn-save");
                                    $("#bt_integration").text("Atualizar");
                                    $("#btn-modal").show();

                                    $(".btn-update").unbind("click");
                                    $(".btn-update").on("click", function () {
                                        var integrationId = $("#integration_id").val();
                                        var form_data = new FormData(
                                            document.getElementById("form_update_integration")
                                        );

                                        $.ajax({
                                            method: "POST",
                                            url: "/api/apps/notazz/" + integrationId,
                                            headers: {
                                                Authorization: $('meta[name="access-token"]').attr("content"),
                                                Accept: "application/json",
                                            },
                                            dataType: "json",
                                            processData: false,
                                            contentType: false,
                                            cache: false,
                                            data: form_data,
                                            error: (function (_error) {
                                                function error(_x) {
                                                    return _error.apply(this, arguments);
                                                }

                                                error.toString = function () {
                                                    return _error.toString();
                                                };

                                                return error;
                                            })(function (response) {
                                                if (response.status === 422) {
                                                    for (error in response.responseJSON.errors) {
                                                        alertCustom(
                                                            "error",
                                                            String(response.responseJSON.errors[error])
                                                        );
                                                    }
                                                } else {
                                                    if (response.status === 403) {
                                                        alertCustom("error", response.responseJSON.message);
                                                    } else {
                                                        alertCustom("error", response.message);
                                                    }
                                                }
                                            }),
                                            success: function success(response) {
                                                index();
                                                alertCustom("success", response.message);
                                            },
                                        });
                                    });
                                },
                            });
                        });
                    }
                    $("#project-empty").hide();
                    $("#integration-actions").show();
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

    function create() {
        clearForm();
        if (companiesAndProjects.company_default_projects.length === 0) {
            var route = "/projects/create";
            $("#modal-project").modal("show");
            $("#modal-project-title").text("Oooppsssss!");
            $("#modal_project_body").html(
                '<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display: flex;"><span class="swal2-x-mark"><span class="swal2-x-mark-line-left"></span><span class="swal2-x-mark-line-right"></span></span></div>' +
                    '<h3 align="center"><strong>Você não possui lojas para realizar integração</strong></h3>' +
                    '<h5 align="center">Deseja criar sua primeira loja? <a class="red pointer" href="' +
                    route +
                    '">clique aqui</a></h5>'
            );
            $("#modal-withdraw-footer").html(
                '<div style="width:100%;text-align:center;padding-top:3%"><span class="btn btn-success" data-dismiss="modal" style="font-size: 25px">Retornar</span></div>'
            );
        } else {
            $("#select_projects_create").html("");
            fillSelectProject(companiesAndProjects, "#select_projects_create");
            $(".modal-title").html("Adicionar nova Integração com Notazz");
            $("#bt_integration").addClass("btn-save");
            $("#bt_integration").text("Adicionar integração");
            $("#modal_add_integracao").modal("show");
            $("#form_update_integration").hide();
            $("#form_add_integration").show();

            $(".btn-save").unbind("click");
            $(".btn-save").on("click", function () {
                if ($("#token_api_create").val() == "") {
                    alertCustom("error", "Datos invalidos, o campo Token Api é obrigatorio.");
                    return false;
                }

                var select_projects_create = $("#select_projects_create").val();
                var select_invoice_type_create = $("#select_invoice_type_create").val();
                var token_api_create = $("#token_api_create").val();
                var token_webhook_create = $("#token_webhook_create").val();
                var token_logistics_create = $("#token_logistics_create").val();
                var start_date_create = $("#start_date_create").val();
                var select_pending_days_create = $("#select_pending_days_create").val();
                var emit_zero = $("#emit_zero").is(":checked");
                var remove_tax = $("#remove_tax").is(":checked");

                if ($("#start_date_create").val() != "") {
                    swal({
                        title: "Data inicial de geração de notas fiscais foi definida.",
                        type: "warning",
                        text: "Uma data inicial para geração de notas fiscais foi selecionada, será gerada as notas fiscais de todas as vendas aprovadas apartir da data selecionada, deseja continuar?",
                        showCancelButton: true,
                        confirmButtonColor: "#3085D6",
                        cancelButtonColor: "#DD3333",
                        cancelButtonText: "Cancelar",
                        confirmButtonText: "Continuar",
                    }).then(function (data) {
                        if (data.value) {
                            //ok

                            $.ajax({
                                method: "POST",
                                url: "/api/apps/notazz",
                                headers: {
                                    Authorization: $('meta[name="access-token"]').attr("content"),
                                    Accept: "application/json",
                                },
                                dataType: "json",
                                data: {
                                    select_projects_create: select_projects_create,
                                    select_invoice_type_create: select_invoice_type_create,
                                    token_api_create: token_api_create,
                                    token_webhook_create: token_webhook_create,
                                    token_logistics_create: token_logistics_create,
                                    start_date_create: start_date_create,
                                    select_pending_days_create: select_pending_days_create,
                                    remove_tax: remove_tax,
                                    emit_zero: emit_zero,
                                },
                                error: function error(response) {
                                    if (response.status === 422) {
                                        alertCustom("error", response.responseJSON.message);
                                    } else {
                                        alertCustom("error", response.responseJSON.message);
                                    }
                                },
                                success: function success(response) {
                                    $("#no-integration-found").hide();
                                    index();
                                    alertCustom("success", response.message);
                                },
                            });
                        } else {
                            //cancel
                        }
                    });
                } else {
                    $.ajax({
                        method: "POST",
                        url: "/api/apps/notazz",
                        headers: {
                            Authorization: $('meta[name="access-token"]').attr("content"),
                            Accept: "application/json",
                        },
                        dataType: "json",
                        data: {
                            select_projects_create: select_projects_create,
                            select_invoice_type_create: select_invoice_type_create,
                            token_api_create: token_api_create,
                            token_webhook_create: token_webhook_create,
                            token_logistics_create: token_logistics_create,
                            start_date_create: start_date_create,
                            select_pending_days_create: select_pending_days_create,
                            remove_tax: remove_tax,
                            emit_zero: emit_zero,
                        },
                        error: function error(response) {
                            if (response.status === 422) {
                                for (let i in response.responseJSON.errors) {
                                    alertCustom("error", String(response.responseJSON.errors[i]));
                                }
                            } else {
                                alertCustom("error", response.responseJSON.message);
                            }
                        },
                        success: function success(response) {
                            $("#no-integration-found").hide();
                            index();
                            alertCustom("success", response.message);
                        },
                    });
                }
            });
        }
    }

    //reset the intergation modal
    function clearForm() {
        $("#integration_id").val("");
        $("#start_date_create").val(moment().format("YYYY-MM-DD"));
        $("#token_api_create").val("");
        $("#token_webhook_create").val("");
        $("#token_logistics_create").val("");
        $(":checkbox").prop("checked", false).val(0);
        $("#select_projects_create").prop("selectedIndex", 0).change();
        $("#select_invoice_type_create").prop("selectedIndex", 0).change();
        $("#select_pending_days_create").prop("selectedIndex", 0).change();
    }

    $("#btn-add-integration").on("click", function () {
        create();
    });
});
