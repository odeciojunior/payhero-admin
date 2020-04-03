let statusNotification = {
    1: "success",
    0: "danger",
};

$(function () {

    var globalPosition = 0;
    var globalInput = '';
    $(document).on('keyup', '#modal-edit-project-notification .project-notification-field', function (event) {
        globalPosition = this.selectionStart;
        globalInput = this.id;
    })
    $(document).on('click', '#modal-edit-project-notification .project-notification-field', function (event) {
        globalPosition = this.selectionStart;
        globalInput = this.id;
    })

    $(document).on('click', '#modal-edit-project-notification  .inc-param', function (event) {
        var param = $(this).data('value');
        var input = document.getElementById(globalInput);
        // var input = document.getElementById('txt-project-notification');
        var inputVal = input.value;
        input.value = inputVal.slice(0, globalPosition) + param + inputVal.slice(globalPosition);
        input.focus();
        $(input).prop('selectionEnd', (globalPosition + param.length));
    })

    let projectId = $(window.location.pathname.split('/')).get(-1);

    $('#tab_sms').on('click', function () {
        atualizarProjectNotification();
    });

    //carrega os itens na tabela
    atualizarProjectNotification();

    // carregar modal de edicao
    $(document).on('click', '.edit-project-notification', function () {
        let projectNotification = $(this).attr('project-notification');
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/projectnotification/" + projectNotification + "/edit",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                $('#modal-edit-project-notification .project-notification-id').val(projectNotification);
                if (response.data.type_enum == 1) {
                    $('#modal-edit-project-notification .project-notification-type').prop("selectedIndex", 0).change();
                } else {
                    $('#modal-edit-project-notification .project-notification-type').prop("selectedIndex", 1).change();
                }
                if (response.data.status == 1) {
                    $('#modal-edit-project-notification .project-notification-status').prop("selectedIndex", 0).change();
                } else {
                    $('#modal-edit-project-notification .project-notification-status').prop("selectedIndex", 1).change();
                }
                $('#modal-edit-project-notification .project-notification-time').val(response.data.time);
                $('#modal-edit-project-notification .project-notification-message').val(response.data.message);

                $('#modal-edit-project-notification .project-notification-title').val(response.data.title);
                $('#modal-edit-project-notification .project-notification-subject').val(response.data.subject);

                if (response.data.event_enum == 1) {
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 0).change();
                } else if (response.data.event_enum == 2) {
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 1).change();
                } else if (response.data.event_enum == 3) {
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 2).change();
                } else if (response.data.event_enum == 4) {
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 3).change();
                } else if (response.data.event_enum == 5) {
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 4).change();
                } else if (response.data.event_enum == 6) {
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 5).change();
                }

                if (response.data.type_enum == 1) {
                    $('#modal-edit-project-notification .project-notification-field-email').show();
                    $('#modal-edit-project-notification .project-notification-message').attr('maxlength', 10000);
                } else {
                    $('#modal-edit-project-notification .project-notification-message').attr('maxlength', 160);
                    $('#modal-edit-project-notification .project-notification-field-email').hide();
                }

                if ((response.data.event_enum == 1 || response.data.event_enum == 2 || response.data.event_enum == 5) && response.data.type_enum == 2) {
                    $('.param-billet-url').show();
                } else {
                    $('.param-billet-url').hide();
                }

                if (response.data.event_enum == 6) {
                    $('.param-tracking-code').show();
                    $('.param-tracking-url').show();
                } else {
                    $('.param-tracking-code').hide();
                    $('.param-tracking-url').hide();
                }

                if (response.data.event_enum == 4) {
                    $('.param-abandoned-cart').show();
                    $('.param-sale-code').hide();
                } else {
                    $('.param-abandoned-cart').hide();
                    $('.param-sale-code').show();
                }

                $('#modal-edit-project-notification').modal('show');
            }
        });
    });

    //atualizar cupom
    $("#modal-edit-project-notification .btn-update").on('click', function () {
        let formData = new FormData(document.getElementById('form-update-project-notification'));
        let projectNotification = $('#modal-edit-project-notification .project-notification-id').val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/project/" + projectId + "/projectnotification/" + projectNotification,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(data) {
                loadingOnScreenRemove();
                alertCustom("success", "Notificação atualizada com sucesso");
                atualizarProjectNotification();
            }
        });
    });

    // carregar modal de detalhes
    $(document).on('click', '.details-project-notification', function () {
        let projectNotification = $(this).attr('project-notification');
        $("#btn-modal").hide();
        $.ajax({
            method: "GET",
            url: "/api/project/" + projectId + "/projectnotification/" + projectNotification,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            }, success: function success(response) {
                $('#modal-detail-project-notification .projectn-type').html(response.data.type);
                $('#modal-detail-project-notification .projectn-time').html(response.data.time);
                $('#modal-detail-project-notification .projectn-event').html(response.data.event);
                $('#modal-detail-project-notification .projectn-message').html(response.data.message);
                $('#modal-detail-project-notification .projectn-subject').html(response.data.subject);
                $('#modal-detail-project-notification .projectn-title').html(response.data.title);
                $('#modal-detail-project-notification .projectn-status').html(response.data.status == '1'
                    ? '<span class="badge badge-success text-left">Ativo</span>'
                    : '<span class="badge badge-danger">Inativo</span>');

                if (response.data.type_enum == 2) {
                    $('.include-templates-email').hide();
                    $('.tr-project-message').show();
                } else {
                    $('.tr-project-message').hide();
                    $('.include-templates-email').show();
                    $('.templates-email').hide();

                    $('#modal-detail-project-notification .p_text_message').html(response.data.message);
                    $('#modal-detail-project-notification .p_project_name').html(response.data.project_name);
                    $('#modal-detail-project-notification .p_project_contact').html(response.data.project_contact);
                    $('#modal-detail-project-notification .p_text_notification').html(response.data.title);
                    if (response.data.project_image != '') {
                        $('#modal-detail-project-notification .p_image_project').attr('src', response.data.project_image);
                    }

                    if (response.data.notification_enum == 5) {
                        $('.template-billet-generated').show();
                    } else if (response.data.notification_enum == 6) {
                        $('.template-billet-next-day').show();
                    } else if (response.data.notification_enum == 7) {
                        $('.template-billet-next-day').show();
                    } else if (response.data.notification_enum == 8) {
                        $('.template-billet-next-day').show();
                    } else if (response.data.notification_enum == 9) {
                        $('.template-abandoned-cart').show();
                    } else if (response.data.notification_enum == 10) {
                        $('.template-abandoned-cart-nextday').show();
                    } else if (response.data.notification_enum == 12) {
                        $('.template-card-paid').show();
                    } else if (response.data.notification_enum == 13) {
                        $('.template-billet-paid').show();
                    } else if (response.data.notification_enum == 14) {
                        $('.template-tracking').show();
                    }
                }
                $('#modal-detail-project-notification').modal('show');
            }
        });
    });

    $(document).on('change', '.project_notification_status', function () {
        let status = 0;
        if (this.checked) {
            status = 1;
        } else {
            status = 0;
        }
        let projectNotification = this.getAttribute('data-id');
        loadingOnScreen();
        $.ajax({
            method: "PUT",
            url: "/api/project/" + projectId + "/projectnotification/" + projectNotification,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {status: status},
            cache: false,
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(data) {
                loadingOnScreenRemove();
                alertCustom("success", "Notificação atualizada com sucesso");
                if (data.status == 1) {
                    $('.notification-status-' + projectNotification + ' span').removeClass('badge-danger');
                    $('.notification-status-' + projectNotification + ' span').addClass('badge-success');
                    $('.notification-status-' + projectNotification + ' span').html('Ativo');
                } else {
                    $('.notification-status-' + projectNotification + ' span').removeClass('badge-success');
                    $('.notification-status-' + projectNotification + ' span').addClass('badge-danger');
                    $('.notification-status-' + projectNotification + ' span').html('Inativo');
                }
                // atualizarProjectNotification();
            }
        });
    });

    function atualizarProjectNotification() {

        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/project/' + projectId + '/projectnotification';
        } else {
            link = '/api/project/' + projectId + '/projectnotification' + link;
        }

        loadOnTable('#data-table-sms', '#tabela-sms');
        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                $("#data-table-sms").html('');
                if (response.data == '') {
                    $("#data-table-sms").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        let check = (value.status == 1) ? 'checked' : '';
                        let data = `<tr>
                            <td class="project-notification-id">${value.type}</td>
                            <td class="project-notification-type">${value.event}</td>
                            <td class="project-notification-value">${value.time}</td>
                            <td class="project-notification-zip-code-origin">${value.message}</td>
                            <td class="project-notification-status notification-status-${value.id}" style="vertical-align: middle">
                                <span class="badge badge-${statusNotification[value.status]}">${value.status_translated}</span>
                            </td>
                            <td style="text-align:center">
                                <a role="button" title='Visualizar' class="mg-responsive details-project-notification pointer" project-notification="${value.id}">
                                    <i class="material-icons gradient">remove_red_eye</i>
                                </a>
                                 ${value.notification_enum != 11 ?
                            `<a role="button" title="Editar" class="mg-responsive edit-project-notification pointer" project-notification='${value.id}'>
                                    <i class="material-icons gradient">edit</i>
                                 </a>` :
                            `<button style="background-color: transparent;" role="button" class="btn pointer" disabled="">
                                     <i class="material-icons gradient">edit</i>
                                 </button>`}
                                <div class="switch-holder d-inline" ${value.notification_enum == 11 || value.notification_enum == 12 || value.notification_enum == 13 ? 'style=" opacity: 0.5;"' : ''}>
                                   <label class="switch" ${value.notification_enum == 11 || value.notification_enum == 12 || value.notification_enum == 13 ? 'style="cursor: not-allowed"' : ''}>
                                       <input type="checkbox" class="project_notification_status" data-id="${value.id}" ${check}
                                        ${value.notification_enum == 11 || value.notification_enum == 12 || value.notification_enum == 13 ? 'disabled' : ''}>
                                       <span class="slider round" ${value.notification_enum == 11 || value.notification_enum == 12 || value.notification_enum == 13 ? 'style="cursor: not-allowed"' : ''}></span>
                                   </label>
                               </div>
                            </td>
                        </tr>`;
                        $("#data-table-sms").append(data);
                    });
                    pagination(response, 'project-notification', atualizarProjectNotification);
                }
            }
        });
    }
});
