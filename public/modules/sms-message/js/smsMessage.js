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
                } else if (response.data.event_enum == 2){
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 1).change();
                } else if (response.data.event_enum == 3){
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 2).change();
                } else if (response.data.event_enum == 4){
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 3).change();
                } else if (response.data.event_enum == 5){
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 4).change();
                } else if (response.data.event_enum == 6){
                    $('#modal-edit-project-notification .project-notification-event').prop("selectedIndex", 5).change();
                }

                if(response.data.type_enum == 1) {
                    $('#modal-edit-project-notification .project-notification-field-email').show();
                    $('#modal-edit-project-notification .project-notification-message').attr('maxlength', 10000);
                } else {
                    $('#modal-edit-project-notification .project-notification-message').attr('maxlength', 160);
                    $('#modal-edit-project-notification .project-notification-field-email').hide();
                }

                if((response.data.event_enum == 1 || response.data.event_enum == 2 || response.data.event_enum == 5) && response.data.type_enum == 2) {
                    $('.param-billet-url').show();
                } else {
                    $('.param-billet-url').hide();
                }

                if(response.data.event_enum == 6) {
                    $('.param-tracking-code').show();
                    $('.param-tracking-url').show();
                } else {
                    $('.param-tracking-code').hide();
                    $('.param-tracking-url').hide();
                }

                if(response.data.event_enum == 4) {
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
                $('#modal-detail-project-notification').modal('show');
            }
        });
    });

    
    $(document).on('change', '.project_notification_status', function () {
        let status = 0;
        if(this.checked) {
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
                if(data.status == 1) {
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
                                <a role="button" title='Visualizar' class="mg-responsive details-project-notification pointer" project-notification="${value.id}"><i class="material-icons gradient">remove_red_eye</i> </a>
                                <a role="button" title='Editar' class="mg-responsive edit-project-notification pointer" project-notification="${value.id}"><i class="material-icons gradient">edit</i> </a>
                                <div class="switch-holder d-inline">
                                   <label class="switch">
                                       <input type="checkbox" class="project_notification_status" data-id="${value.id}" ${check}>
                                       <span class="slider round"></span>
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

// $(function () {

//     $(".page-notification").on('click', function () {

//         $(".page-notification").removeClass('active').prop('disabled', false);
//         $(this).addClass('active').prop('disabled', true);
//         let page = $(this).html();
//         $('#data-table-sms tr').hide();
//         $('.page-' + page).show();
//     });
// });

/*
$(function () {

    var projectId = $("#project-id").val();
    $('#tab_sms').on('click', function () {
        atualizarSms();
    });
    atualizarSms();
    $("#add-sms").on('click', function () {
        $("#modal-title").html('Adicionar SMS <br><hr class="my-0">');
        $("#modal_add_size").addClass('modal_simples');
        $("#modal_add_size").addClass('modal-lg');

        /!*$("#modal-add-body").html("<div style='text-align:center;'>Carregando...</div>");*!/
        loadOnModal('#modal-add-body');
        $('#btn-modal').attr('data-dismiss','modal');

        $.ajax({
            method: "GET",
            url: "/sms/create",
            data: {project: projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                loadingOnScreenRemove();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function (data) {
                loadingOnScreenRemove();
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").text('Salvar');
                $("#btn-modal").show();
                $('#modal-add-body').html(data);
                $(".btn-save").unbind('click');
                $(".btn-save").on('click', function () {

                    var formData = new FormData(document.getElementById('form-register-sms'));
                    formData.append("project", projectId);

                    $.ajax({
                        method: "POST",
                        url: "/sms",
                        headers: {
                            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        error: function (data) {
                            $("#modal_add_produto").hide();
                            $(".loading").css("visibility", "hidden");
                            if (data.status == '422') {
                                for (error in data.responseJSON.errors) {
                                    alertCustom('error', String(data.responseJSON.errors[error]));
                                }
                            }
                        }, success: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom("success", "SMS Adicionado!");
                            atualizarSms();
                        }
                    });
                });

            }
        });

    });

    function atualizarSms() {
        loadOnTable('#data-table-sms','#tabela_sms');

        $("#data-table-sms").html('');
        data = '';
        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">SMS</td>';
        data += '<td class= style="vertical-align: middle;">Boleto gerado</td>';
        data += '<td class= style="vertical-align: middle;">Imediato</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, não esqueça de pagar seu boleto para enviarmos seu pedido! {url_boleto}</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">SMS</td>';
        data += '<td class= style="vertical-align: middle;">Boleto vencendo</td>';
        data += '<td class= style="vertical-align: middle;">10:00 horas</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! {url_boleto}</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">SMS</td>';
        data += '<td class= style="vertical-align: middle;">Carrinho abandonado</td>';
        data += '<td class= style="vertical-align: middle;">4 horas depois</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">SMS</td>';
        data += '<td class= style="vertical-align: middle;">Carrinho abandonado</td>';
        data += '<td class= style="vertical-align: middle;">10:00 horas próximo dia</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">Email</td>';
        data += '<td class= style="vertical-align: middle;">Boleto gerado</td>';
        data += '<td class= style="vertical-align: middle;">Imediato</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, aqui está seu boleto. Como você optou por Boleto Bancário, estamos enviando por aqui para você não se esquecer. O boleto deve ser pago até a data de vencimento para enviarmos seu(s) pedido(s)! {url_boleto}</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">Email</td>';
        data += '<td class= style="vertical-align: middle;">Boleto gerado</td>';
        data += '<td class= style="vertical-align: middle;">10:00 horas próximo dia</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, estamos enviando esse email só para avisar que já empacotamos sua encomenda e estamos prontos para enviar para você. Assim que o boleto for pago e recebermos a confirmação sua encomenda será enviada!</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">Email</td>';
        data += '<td class= style="vertical-align: middle;">Boleto gerado</td>';
        data += '<td class= style="vertical-align: middle;">10:00 horas 2 dias após</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, por falta de pagamento vamos ter que liberar sua mercadoria para o estoque novamente. Isso siginigfica que se você não efetuar o pagamento, cancelaremos seu pedido!</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">Email</td>';
        data += '<td class= style="vertical-align: middle;">Boleto vencendo</td>';
        data += '<td class= style="vertical-align: middle;">10:00 horas</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, seu boleto vencerá hoje, ainda dá tempo de pagar! Não se esqueça, só enviaremos o seu pedido (que já está separado) se você efetuar o pagamento! {url_boleto}</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">Email</td>';
        data += '<td class= style="vertical-align: middle;">Carrinho abandonado</td>';
        data += '<td class= style="vertical-align: middle;">4 horas depois</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, nossos produtos estão com preços especiais e o estoque é bem limitado. Recomendamos que você finalize a compra ainda hoje para garantir a promoção e economizar dinheiro! {link_carrinho_abandonado}</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';

        data += '<tr>';
        data += '<td class= style="vertical-align: middle;">Email</td>';
        data += '<td class= style="vertical-align: middle;">Carrinho abandonado</td>';
        data += '<td class= style="vertical-align: middle;">10:00 horas próximo dia</td>';
        data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, vimos que você não aproveitou a promoção de ontem. O seu pedido ainda está separado aguardando a finalização da compra, mas não podemos segurar por muito tempo! {link_carrinho_abandonado}</td>';
        data += '<td class="shipping-status " style="vertical-align: middle;">';
        data += '<span class="badge badge-success mb-1">Ativo</span>';
        data += '<span class="badge badge-primary">Grátis</span>';
        data += '</td>';
        data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
        data += '</td>';
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
        data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
        data += '</tr>';


        $("#data-table-sms").append(data);


        // $.ajax({
        //     method: "GET",
        //     url: '/sms',
        //     data: {project: projectId},
        //     headers: {
        //         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        //     },
        //     error: function () {
        //         $("#data-table-sms").html('Erro ao encontrar dados');
        //     },
        //     success: function (response) {

        //         // $.each(response.data, function (index, value) {
        //         //     data = '';
        //         //     data += '<tr>';
        //         //     data += '<td class="shipping-id text-center" style="vertical-align: middle;">' + value.event + '</td>';
        //         //     data += '<td class="shipping-type text-center" style="vertical-align: middle;">' + value.time + '</td>';
        //         //     data += '<td class="shipping-value text-center" style="vertical-align: middle;">' + value.period + '</td>';
        //         //     data += '<td class="shipping-zip-code-origin text-center" style="vertical-align:">' + value.message + '</td>';
        //         //     data += '<td class="shipping-status text-center" style="vertical-align: middle;">';
        //         //     if (value.status === 1) {
        //         //         data += '<span class="badge badge-success">Ativo</span>';
        //         //     } else {
        //         //         data += '<span class="badge badge-danger">Desativado</span>';
        //         //     }
        //         //
        //         //     data += '</td>';
        //         //
        //         //     data += '<td class="shipping-pre-selected text-center" style="vertical-align: middle;">';
        //         //
        //         //     data += '</td>';
        //         //
        //         //     data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger details-sms'  sms='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
        //         //     data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger edit-sms'  sms='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
        //         //     data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger delete-sms'  sms='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";
        //         //
        //         //     data += '</tr>';
        //         //     $("#data-table-sms").append(data);
        //         // });
        //         // if (response.data == '') {
        //         //     $("#data-table-sms").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>")
        //         // }
        //         // $(".details-sms").unbind('click');
        //         // $(".details-sms").on('click', function () {
        //         //     var sms = $(this).attr('sms');
        //         //     $("#modal-title").html('Detalhes do SMS <br><hr>');
        //         //     $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");
        //         //     var data = {smsId: sms};
        //         //     $("#btn-modal").hide();
        //         //     $.ajax({
        //         //         method: "GET",
        //         //         url: "/sms/" + sms,
        //         //         data: data,
        //         //         headers: {
        //         //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         //         },
        //         //         error: function () {
        //         //             //
        //         //         }, success: function (response) {
        //         //             $("#modal-add-body").html(response);

        //         //         }
        //         //     });
        //         // });

        //         // $(".edit-sms").unbind('click');
        //         // $(".edit-sms").on('click', function () {
        //         //     $("#modal-add-body").html("");
        //         //     var sms = $(this).attr('sms');
        //         //     $("#modal-title").html("Editar SMS<br><hr>");
        //         //     $("#modal_add_size").addClass('modal-lg');
        //         //     $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
        //         //     var data = {smsId: sms};
        //         //     $.ajax({
        //         //         method: "GET",
        //         //         url: "/sms/" + sms + "/edit",
        //         //         data: data,
        //         //         headers: {
        //         //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         //         },
        //         //         error: function () {
        //         //             //
        //         //         }, success: function (response) {
        //         //             $("#btn-modal").addClass('btn-update');
        //         //             $("#btn-modal").text('Atualizar');
        //         //             $("#btn-modal").show();
        //         //             $("#modal-add-body").html(response);

        //         //             $(".btn-update").unbind('click');
        //         //             $(".btn-update").on('click', function () {

        //         //                 $.ajax({
        //         //                     method: "PUT",
        //         //                     url: "/sms/" + sms,
        //         //                     headers: {
        //         //                         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
        //         //                     },
        //         //                     data: {
        //         //                         event: $("#event").val(),
        //         //                         time: $("#time").val(),
        //         //                         period: $("#period").val(),
        //         //                         message: $("#message").val(),
        //         //                         status: $("#status").val(),
        //         //                     },
        //         //                     error: function (response) {
        //         //                         if (response.status == '422') {
        //         //                             for (error in response.responseJSON.errors) {
        //         //                                 alertCustom('error', String(response.responseJSON.errors[error]));
        //         //                             }
        //         //                         }
        //         //                     },
        //         //                     success: function (data) {
        //         //                         alertCustom("success", "Notificação atualizada com sucesso");
        //         //                         atualizarSms();
        //         //                     }
        //         //                 });

        //         //             });
        //         //         }
        //         //     });

        //         // });
        //         // $('.delete-sms').on('click', function (event) {
        //         //     event.preventDefault();
        //         //     var sms = $(this).attr('sms');
        //         //     $("#modal_excluir_titulo").html("Remover Cupom?");
        //         //     $("#bt_excluir").unbind('click');
        //         //     $("#bt_excluir").on('click', function () {
        //         //         $("#fechar_modal_excluir").click();

        //         //         $.ajax({
        //         //             method: "DELETE",
        //         //             url: "/sms/" + sms,
        //         //             headers: {
        //         //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         //             },
        //         //             error: function () {
        //         //                 if (response.status == '422') {
        //         //                     for (error in response.responseJSON.errors) {
        //         //                         alertCustom('error', String(response.responseJSON.errors[error]));
        //         //                     }
        //         //                 }
        //         //             },
        //         //             success: function (data) {
        //         //                 alertCustom("success", "Notificação Removida com sucesso");
        //         //                 atualizarSms();
        //         //             }

        //         //         })
        //         //     });

        //         // });

        //     }
        // });
    }

});
*/
