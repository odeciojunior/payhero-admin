function updateAfterChangeCompany(){
    $("#select_projects, #select_projects_edit").find('option').remove();
    let companies = JSON.parse(sessionStorage.getItem('companies'));
    $.each(companies, function (c, company) {
        if( sessionStorage.getItem('company_default') == company.id){
            $.each(company.projects, function (i, project) {console.log(project)
                if(project.status == 1){
                    $("#select_projects, #select_projects_edit").append($('<option>', {
                        value: project.id,
                        text: project.name
                    }));
                }
            });
        }
    });
    window.index('n')
}

$(document).ready(function () {
    create();

    window.index = function(loading='y') {
        if(loading=='y')
            loadingOnScreen();
        else
            loadOnAny('#content');

        $.ajax({
            method: "GET",
            url: "/api/apps/convertax?company="+ sessionStorage.getItem('company_default'),
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadOnAny('#content',true);
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#content").html("");
                if (Object.keys(response.data).length === 0) {
                    $("#no-integration-found").show();
                } else {
                    $("#no-integration-found").hide();
                    $(response.data).each(function (index, data) {
                        $("#content").append(`
                            <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                                <div class="card shadow card-edit" project=${
                                    data.id
                                } style='cursor:pointer;'>
                                    <img class="card-img-top img-fluid w-full" src="${
                                        data.project_photo
                                            ? data.project_photo
                                            : "/build/global/img/produto.png"
                                    }"/>
                                    <div class="card-body">
                                        <div class='row'>
                                            <div class='col-md-10'>
                                                <h4 class="card-title">${
                                                    data.project_name
                                                }</h4>
                                                <p class="card-text sm">Criado em ${
                                                    data.created_at
                                                }</p>
                                            </div>
                                            <div class='col-md-2'>
                                                <a role='button' title='Excluir' class='delete-integration float-right mt-35' project="${
                                                    data.id
                                                }" data-toggle='modal' data-target='#modal-delete' type='a'>
                                                    <span class='o-bin-1 pointer'></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                    $(".card-edit").unbind("click");
                    $(".card-edit").on("click", function () {
                        var project_id = $(this).attr("project");
                        $.ajax({
                            method: "GET",
                            url: "/api/projects/user-projects",
                            headers: {
                                Authorization: $(
                                    'meta[name="access-token"]'
                                ).attr("content"),
                                Accept: "application/json",
                            },
                            error: function error(response) {
                                errorAjaxResponse(response);
                            },
                            success: function success(response) {
                                $("#select_projects_edit").html("");
                                $(response.data).each(function (index, data) {
                                    $("#select_projects_edit").append(
                                        "<option value='" +
                                            data.id +
                                            "'>" +
                                            data.name +
                                            "</option>"
                                    );
                                });

                                $(".modal-title").html(
                                    "Editar Integração com ConvertaX"
                                );

                                $.ajax({
                                    method: "GET",
                                    url: "/api/apps/convertax/" + project_id,
                                    headers: {
                                        Authorization: $(
                                            'meta[name="access-token"]'
                                        ).attr("content"),
                                        Accept: "application/json",
                                    },
                                    error: function error() {
                                        //
                                    },
                                    success: function success(response) {
                                        $("#select_projects_edit").val(
                                            response.data.project_id
                                        );
                                        $("#integration_id").val(
                                            response.data.id
                                        );
                                        $("#link_edit").val(response.data.link);
                                        $("#value_edit").val(
                                            response.data.value
                                        );
                                        $("#value_edit").unmask();
                                        $("#value_edit").mask("#.###,#0", {
                                            reverse: true,
                                        });

                                        $("#boleto_generated_edit").val(
                                            response.data.boleto_generated
                                        );
                                        $("#boleto_generated_edit").val() == "1"
                                            ? $("#boleto_generated_edit").attr(
                                                  "checked",
                                                  "checked"
                                              )
                                            : $("#boleto_generated_edit").attr(
                                                  ""
                                              );

                                        $("#boleto_paid_edit").val(
                                            response.data.boleto_paid
                                        );
                                        $("#boleto_paid_edit").val() == "1"
                                            ? $("#boleto_paid_edit").attr(
                                                  "checked",
                                                  "checked"
                                              )
                                            : $("#boleto_paid_edit").attr("");

                                        $("#credit_card_refused_edit").val(
                                            response.data.credit_card_refused
                                        );
                                        $("#credit_card_refused_edit").val() ==
                                        "1"
                                            ? $(
                                                  "#credit_card_refused_edit"
                                              ).attr("checked", "checked")
                                            : $(
                                                  "#credit_card_refused_edit"
                                              ).attr("");

                                        $("#credit_card_paid_edit").val(
                                            response.data.credit_card_paid
                                        );
                                        $("#credit_card_paid_edit").val() == "1"
                                            ? $("#credit_card_paid_edit").attr(
                                                  "checked",
                                                  "checked"
                                              )
                                            : $("#credit_card_paid_edit").attr(
                                                  ""
                                              );

                                        $("#abandoned_cart_edit").val(
                                            response.data.abandoned_cart
                                        );
                                        $("#abandoned_cart_edit").val() == "1"
                                            ? $("#abandoned_cart_edit").attr(
                                                  "checked",
                                                  "checked"
                                              )
                                            : $("#abandoned_cart_edit").attr(
                                                  ""
                                              );

                                        $("#modal_add_integracao").modal(
                                            "show"
                                        );
                                        $("#form_add_integration").hide();
                                        $("#form_update_integration").show();

                                        $("#bt_integration").addClass(
                                            "btn-update"
                                        );
                                        $("#bt_integration").removeClass(
                                            "btn-save"
                                        );
                                        $("#bt_integration").text("Atualizar");
                                        $("#btn-modal").show();

                                        $(".check").on("click", function () {
                                            if ($(this).is(":checked")) {
                                                $(this).val(1);
                                            } else {
                                                $(this).val(0);
                                            }
                                        });

                                        $(".btn-update").unbind("click");
                                        $(".btn-update").on(
                                            "click",
                                            function () {
                                                if (
                                                    $("#link_edit").val() ==
                                                        "" ||
                                                    $("#value_edit").val() == ""
                                                ) {
                                                    alertCustom(
                                                        "error",
                                                        "Dados informados inválidos"
                                                    );
                                                    return false;
                                                }

                                                var integrationId = $(
                                                    "#integration_id"
                                                ).val();
                                                var form_data = new FormData(
                                                    document.getElementById(
                                                        "form_update_integration"
                                                    )
                                                );

                                                $.ajax({
                                                    method: "POST",
                                                    url:
                                                        "/api/apps/convertax/" +
                                                        integrationId,
                                                    headers: {
                                                        Authorization: $(
                                                            'meta[name="access-token"]'
                                                        ).attr("content"),
                                                        Accept:
                                                            "application/json",
                                                    },
                                                    processData: false,
                                                    contentType: false,
                                                    cache: false,
                                                    data: form_data,
                                                    error: function (response) {
                                                        errorAjaxResponse(
                                                            response
                                                        );
                                                    },
                                                    success: function success(
                                                        response
                                                    ) {
                                                        window.index();
                                                        alertCustom(
                                                            "success",
                                                            response.message
                                                        );
                                                    },
                                                });
                                            }
                                        );
                                    },
                                });
                            },
                        });
                    });
                    $(".delete-integration").unbind("click");
                    // load delete modal
                    $(document).on(
                        "click",
                        ".delete-integration",
                        function (e) {
                            e.preventDefault();
                            let project = $(this).attr("project");
                            var card = $(
                                'a[class="delete-integration float-right mt-35"][ project="' +
                                    project +
                                    '"]'
                            )
                                .parent()
                                .parent()
                                .parent()
                                .parent()
                                .parent();
                            card.find(".card-edit").unbind("click");
                            $("#modal-delete-integration .btn-delete").attr(
                                "project",
                                project
                            );
                            $("#modal-delete-integration").modal("show");
                        }
                    );
                    $("#modal-delete-integration .btn-delete").on(
                        "click",
                        function (e) {
                            e.stopPropagation();
                            var project_id = $(this).attr("project");
                            var card = $(
                                'a[class="delete-integration float-right mt-35"][ project="' +
                                    project_id +
                                    '"]'
                            )
                                .parent()
                                .parent()
                                .parent()
                                .parent()
                                .parent();
                            card.find(".card-edit").unbind("click");
                            $.ajax({
                                method: "DELETE",
                                url: "/api/apps/convertax/" + project_id,
                                headers: {
                                    Authorization: $(
                                        'meta[name="access-token"]'
                                    ).attr("content"),
                                    Accept: "application/json",
                                },
                                error: function (response) {
                                    errorAjaxResponse(response);
                                },
                                success: function success(response) {
                                    window.index();
                                    alertCustom("success", response.message);
                                },
                            });
                        }
                    );
                    loadOnAny('#content',true);
                }
            },
        });
    }

    function create() {
        loadingOnScreen();
        $.ajax({
            method: "GET",
            url: "/api/projects?select=true&company="+ sessionStorage.getItem('company_default'),
            data: {
                status: "active",
            },
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadOnAny('#content',true);
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadOnAny('#content',true);
                loadingOnScreenRemove();

                if (isEmpty(response.data)) {
                    $("#project-empty").show();
                    $("#integration-actions").hide();
                    return;
                }

                $("#select_projects").html("");
                $(response.data).each(function (index, data) {
                    if(data.status===1){
                        $("#select_projects").append(
                            "<option value='" +
                                data.id +
                                "'>" +
                                data.name +
                                "</option>"
                        );
                    }
                });
                $(".modal-title").html(
                    "Adicionar nova Integração com ConvertaX"
                );
                $("#bt_integration")
                    .addClass("btn-save")
                    .text("Adicionar integração");
                $("#form_update_integration").hide();
                $("#form_add_integration").show();

                $("#value").mask("#.###,#0", { reverse: true });

                $(".check").on("click", function () {
                    if ($(this).is(":checked")) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                });

                if ($(":checkbox").is(":checked")) {
                    $(":checkbox").val(1);
                } else {
                    $(":checkbox").val(0);
                }

                $(".btn-save").unbind("click");
                $(".btn-save").on("click", function () {
                    if ($("#link").val() == "" || $("#value").val() == "") {
                        alertCustom("error", "Dados informados inválidos");
                        return false;
                    }
                    var form_data = new FormData(
                        document.getElementById("form_add_integration")
                    );

                    $.ajax({
                        method: "POST",
                        url: "/api/apps/convertax",
                        headers: {
                            Authorization: $('meta[name="access-token"]').attr(
                                "content"
                            ),
                            Accept: "application/json",
                        },
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: form_data,
                        error: function error(response) {
                            if (response.status === 422) {
                                for (error in response.errors) {
                                    alertCustom(
                                        "error",
                                        String(response.errors[error])
                                    );
                                }
                            } else {
                                alertCustom(
                                    "error",
                                    response.responseJSON.message
                                );
                            }
                        },
                        success: function success(response) {
                            $("#no-integration-found").hide();
                            window.index();
                            alertCustom("success", response.message);
                        },
                    });
                });

                $("#project-empty").hide();
                $("#integration-actions").show();

                window.index();
            },
        });
    }

    $("#btn-add-integration").on("click", function () {
        $("#modal_add_integracao").modal("show");
    });
});
