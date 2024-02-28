let statusNotification = {
    1: "success",
    0: "disable",
};

$(function () {
    var globalPosition = 0;
    var globalInput = "";
    $(document).on("keyup", "#modal-edit-project-notification .project-notification-field", function (event) {
        globalPosition = this.selectionStart;
        globalInput = this.id;
    });
    $(document).on("click", "#modal-edit-project-notification .project-notification-field", function (event) {
        globalPosition = this.selectionStart;
        globalInput = this.id;
    });

    $(document).on("click", "#modal-edit-project-notification  .inc-param", function (event) {
        var param = $(this).data("value");
        var input = document.getElementById(globalInput);
        // var input = document.getElementById('txt-project-notification');
        var inputVal = input.value;
        input.value = inputVal.slice(0, globalPosition) + param + inputVal.slice(globalPosition);
        input.focus();
        $(input).prop("selectionEnd", globalPosition + param.length);
    });

    let projectId = $(window.location.pathname.split("/")).get(-1);

    $(".tab_sms").on("click", function () {
        atualizarProjectNotification();
        $(this).off();
    });

    // carregar modal de edicao
    $(document).on("click", ".edit-project-notification", function () {
        let projectNotification = $(this).attr("project-notification");
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/projectnotification/" + projectNotification + "/edit",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#modal-edit-project-notification .project-notification-id").val(projectNotification);

                $("#modal-edit-project-notification .project-notification-type")
                    .find("select")
                    .val(response.data.type_enum)
                    .trigger("change");

                $("#modal-edit-project-notification .project-notification-status")
                    .find("select")
                    .val(response.data.status)
                    .trigger("change");

                $("#modal-edit-project-notification .project-notification-time").val(response.data.time);
                $("#modal-edit-project-notification .project-notification-message").val(response.data.message);

                $("#modal-edit-project-notification .project-notification-title").val(response.data.title);
                $("#modal-edit-project-notification .project-notification-subject").val(response.data.subject);

                if (response.data.event_enum > 0) {
                    $("#modal-edit-project-notification .project-notification-event")
                        .find("select")
                        .val(response.data.event_enum)
                        .trigger("change");
                }

                if (response.data.type_enum == 1) {
                    $("#modal-edit-project-notification .project-notification-field-email").show();
                    $("#modal-edit-project-notification .project-notification-message").attr("maxlength", 10000);
                } else {
                    $("#modal-edit-project-notification .project-notification-message").attr("maxlength", 160);
                    $("#modal-edit-project-notification .project-notification-field-email").hide();
                }

                if (
                    (response.data.event_enum == 1 || response.data.event_enum == 2 || response.data.event_enum == 5) &&
                    response.data.type_enum == 2
                ) {
                    $(".param-billet-url").show();
                } else {
                    $(".param-billet-url").hide();
                }

                if (response.data.event_enum == 6) {
                    $(".param-tracking-code").show();
                    $(".param-tracking-url").show();
                } else {
                    $(".param-tracking-code").hide();
                    $(".param-tracking-url").hide();
                }

                if (response.data.event_enum == 4) {
                    $(".param-abandoned-cart").show();
                    $(".param-sale-code").hide();
                } else {
                    $(".param-abandoned-cart").hide();
                    $(".param-sale-code").show();
                }

                $("#modal-edit-project-notification").modal("show");
            },
        });
    });

    //atualizar cupom
    $("#modal-edit-project-notification .btn-update").on("click", function () {
        let formData = new FormData(document.getElementById("form-update-project-notification"));
        let projectNotification = $("#modal-edit-project-notification .project-notification-id").val();

        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/projectnotification/" + projectNotification,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(data) {
                alertCustom("success", "Notificação atualizada com sucesso");
                atualizarProjectNotification();
            },
        });
    });

    // carregar modal de detalhes
    $(document).on("click", ".details-project-notification", function () {
        let projectNotification = $(this).attr("project-notification");
        $("#btn-modal").hide();
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/projectnotification/" + projectNotification,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#modal-detail-project-notification .projectn-type").html(response.data.type);
                $("#modal-detail-project-notification .projectn-time").html(response.data.time);
                $("#modal-detail-project-notification .projectn-event").html(response.data.event);
                $("#modal-detail-project-notification .projectn-message").html(response.data.message);
                $("#modal-detail-project-notification .projectn-subject").html(response.data.subject);
                $("#modal-detail-project-notification .projectn-title").html(response.data.title);
                $("#modal-detail-project-notification .projectn-status").html(
                    response.data.status == "1"
                        ? '<span class="badge badge-success text-left">Ativo</span>'
                        : '<span class="badge badge-disable">Inativo</span>'
                );

                if (response.data.type_enum == 2) {
                    $(".include-templates-email").hide();
                    $(".tr-project-message").show();
                } else {
                    $(".tr-project-message").hide();
                    $(".include-templates-email").show();
                    $(".templates-email").hide();

                    $("#modal-detail-project-notification .p_text_message").html(response.data.message_html);
                    $("#modal-detail-project-notification .p_project_name").html(response.data.project_name);
                    $("#modal-detail-project-notification .p_text_notification").html(response.data.title);
                    if (response.data.project_image != "") {
                        $("#modal-detail-project-notification .p_image_project").attr(
                            "src",
                            response.data.project_image
                        );
                    }

                    if (response.data.notification_enum == 5) {
                        $(".template-billet-generated").show();
                    } else if (response.data.notification_enum == 6) {
                        $(".template-billet-next-day").show();
                    } else if (response.data.notification_enum == 7) {
                        $(".template-billet-next-day").show();
                    } else if (response.data.notification_enum == 8) {
                        $(".template-billet-next-day").show();
                    } else if (response.data.notification_enum == 9) {
                        $(".template-abandoned-cart").show();
                    } else if (response.data.notification_enum == 10) {
                        $(".template-abandoned-cart-nextday").show();
                    } else if (response.data.notification_enum == 12) {
                        $(".template-card-paid").show();
                    } else if (response.data.notification_enum == 13) {
                        $(".template-billet-paid").show();
                    } else if (response.data.notification_enum == 14) {
                        $(".template-tracking").show();
                    }
                }
                $("#modal-detail-project-notification").modal("show");
            },
        });
    });

    $(document).on("change", ".project_notification_status", function () {
        let status = 0;
        if (this.checked) {
            status = 1;
        } else {
            status = 0;
        }
        let projectNotification = this.getAttribute("data-id");

        $.ajax({
            method: "PUT",
            url: "/api/project/" + projectId + "/projectnotification/" + projectNotification,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            data: { status: status },
            cache: false,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(data) {
                alertCustom("success", "Notificação atualizada com sucesso");
                if (data.status == 1) {
                    $(".notification-status-" + projectNotification + " span").removeClass("badge-disable");
                    $(".notification-status-" + projectNotification + " span").addClass("badge-success");
                    $(".notification-status-" + projectNotification + " span").html("Ativo");
                } else {
                    $(".notification-status-" + projectNotification + " span").removeClass("badge-success");
                    $(".notification-status-" + projectNotification + " span").addClass("badge-disable");
                    $(".notification-status-" + projectNotification + " span").html("Inativo");
                }
                // atualizarProjectNotification();
            },
        });
    });

    function atualizarProjectNotification() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = "/api/project/" + projectId + "/projectnotification";
        } else {
            link = "/api/project/" + projectId + "/projectnotification" + link;
        }

        loadOnTable("#data-table-sms", "#tabela-sms");
        $("#pagination-project-notification").children().attr("disabled", "disabled");

        $("#tab_sms-panel").find(".no-gutters").css("display", "none");
        $("#tabela-sms").find("thead").css("display", "none");

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#data-table-sms").html("");
                if (response.data == "") {
                    $("#pagination-container-sms").removeClass("d-flex").addClass("d-none")

                    $("#data-table-sms").html(`
                        <tr class='text-center'>
                            <td colspan='8' style='height: 70px; vertical-align: middle;'>
                                <div class='d-flex justify-content-center align-items-center'>
                                    <img src='/build/global/img/empty-state-table.svg' style='margin-right: 60px;'>
                                    <div class='text-left'>
                                        <h1 style='font-size: 24px; font-weight: normal; line-height: 30px; margin: 0; color: #636363;'>Nenhuma notificação configurada</h1>
                                        <p style='font-style: normal; font-weight: normal; font-size: 16px; line-height: 20px; color: #9A9A9A;'>Cadastre a sua primeiro notificação para poder
                                        <br>gerenciá-las nesse painel.</p>
                                        <button type='button' style='width: auto; height: auto; padding: .429rem 1rem !important;' class='btn btn-primary add-review' data-toggle="modal" data-target="#modal_review">Adicionar notificação</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `);
                } else {
                    $("#tab_sms-panel").find(".no-gutters").css("display", "flex");
                    $("#tabela-sms").find("thead").css("display", "contents");

                    $("#count-notifications").html(response.meta.total);

                    $.each(response.data, function (index, value) {
                        let check = value.status == 1 ? "checked" : "";
                        let data = `
                            <tr>
                                <td class="project-notification-id">
                                    ${value.type}
                                </td>

                                <td class="project-notification-type">

                                    <div class="fullInformation-sms ellipsis-text">
                                        ${value.event}
                                    </div>

                                    <div class="container-tooltips-sms"></div>

                                </td>

                                <td class="project-notification-value">

                                    <div class="fullInformation-sms ellipsis-text">
                                        ${value.time}
                                    </div>

                                </td>

                                <td class="project-notification-zip-code-origin">

                                    <div class="fullInformation-sms ellipsis-text">
                                        ${value.message}
                                    </div>

                                </td>

                                <td class="text-center project-notification-status notification-status-${value.id}" style="vertical-align: middle">

                                    <span class="badge badge-${statusNotification[value.status]}">
                                        ${value.status_translated}
                                    </span>

                                </td>

                                <td style="text-align:center" class="justify-content-between align-items-center mb-0">

                                    <div class='d-flex justify-content-end align-items-center'>

                                        <a role="button" title='Visualizar' class="details-project-notification mg-responsive pointer" project-notification="${value.id}">
                                            <span class="">
                                                <img src='/build/global/img/icon-eye.svg'/>
                                            </span>
                                        </a>

                                        ${value.notification_enum == 12 || value.notification_enum == 13 || value.notification_enum == 17 ? `
                                            <button style="background-color: transparent;" role="button" class="px-0 pb-0 btn  disabled="">
                                                <span class="">
                                                    <img src="/build/global/img/pencil-icon.svg">
                                                </span>
                                            </button>` : `

                                            <a role="button" title="Editar" class="edit-project-notification mg-responsive pointer" project-notification='${value.id}'>
                                                <span class="">
                                                    <img src='/build/global/img/pencil-icon.svg'/>
                                                </span>

                                            </a>
                                        `}

                                        <div class="switch-holder d-inline mg-responsive pointer mr-0" ${value.notification_enum == 11 || value.notification_enum == 12 || value.notification_enum == 13 || value.notification_enum == 17 ? 'style=" opacity: 0.5;"' : ""}>

                                            <label class="switch mr-0" ${value.notification_enum == 11 || value.notification_enum == 12 || value.notification_enum == 13 || value.notification_enum == 17 ? 'style="cursor: not-allowed"' : ""}>

                                                <input type="checkbox" class="project_notification_status" data-id="${value.id}" ${check} ${value.notification_enum == 11 || value.notification_enum == 12 || value.notification_enum == 13 || value.notification_enum == 17 ? "disabled" : ""}>

                                                <span class="slider round" ${value.notification_enum == 11 || value.notification_enum == 12 || value.notification_enum == 13 || value.notification_enum == 17 ? 'style="cursor: not-allowed"' : ""}>

                                                </span>

                                            </label>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        `;

                        $("#data-table-sms").append(data);
                    });
                    $("#pagination-container-sms").removeClass("d-none").addClass("d-flex")

                    $('.fullInformation-sms').bind('mouseover', function () {
                        var $this = $(this);

                        if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
                            $this.attr({
                                'data-toggle': "tooltip",
                                'data-placement': "top",
                                'data-title': $this.text()
                            }).tooltip({ container: ".container-tooltips-sms" })
                            $this.tooltip("show")
                        }
                    });

                    pagination(response, "project-notification", atualizarProjectNotification);
                }
            },
        });
    }
});
