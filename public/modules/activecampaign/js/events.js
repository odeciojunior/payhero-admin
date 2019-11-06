$(function () {
    var integrationId = $(window.location.pathname.split('/')).get(-1);
    var form_register_event = $("#form-register-event").html();
    var form_update_event = $("#form-update-event").html();
    $('#tab_events').on('click', function () {
        index();
    });

    /**
     *  Verifica se a array de objetos que retorna do ajax esta vazio
     * @returns {boolean}
     * @param data
     */

    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }
    function clearFields() {
        $("#form-register-event").html('');
        $("#form-register-event").html(form_register_event);
    }

    function create() {
        $.ajax({
            method: "GET",
            url: "/api/apps/activecampaignevent/create",
            data: {integration: integrationId},
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                $("#modal-content").hide();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                    $("#events").html('');
                    $(response.events).each(function (index, data) {
                        $("#events").append("<option value='" + data.id + "'>" + data.name + "</option>");
                    });

                    var lists = response.lists;
                    $("#add_list").html('<option>Selecione a lista</option>');
                    $("#remove_list").html('<option>Selecione a lista</option>');
                    if(lists !== null) {
                        $(lists.lists).each(function (index, data) {
                            $("#add_list").append("<option value='" + data.id + ";" + data.name + "'>" + data.name + "</option>");
                        });
                        
                        $(lists.lists).each(function (index, data) {
                            $("#remove_list").append("<option value='" + data.id + ";" + data.name + "'>" + data.name + "</option>");
                        });
                    }

                    var tags = response.tags;
                    $("#add_tags").html('<option>Selecione a(s) tag(s)</option>');
                    $("#remove_tags").html('<option>Selecione a(s) tag(s)</option>');
                    if(tags !== null) {
                        $(tags.tags).each(function (index, data) {
                            $("#add_tags").append("<option value='" + data.id + ";" + data.tag + "'>" + data.tag + "</option>");
                        });
                        
                        $(tags.tags).each(function (index, data) {
                            $("#remove_tags").append("<option value='" + data.id + ";" + data.tag + "'>" + data.tag + "</option>");
                        });
                    }

                    $('#add_tags').select2({
                        dropdownParent: $('#modal_add_event'),
                        placeholder: 'Selecione a(s) tag(s)'
                    });

                    $('#remove_tags').select2({
                        dropdownParent: $('#modal_add_event'),
                        placeholder: 'Selecione a(s) tag(s)'
                    });

                    $('#add_list').select2({
                        dropdownParent: $('#modal_add_event'),
                        placeholder: 'Selecione a lista'
                    });

                    $('#remove_list').select2({
                        dropdownParent: $('#modal_add_event'),
                        placeholder: 'Selecione a lista'
                    });

                    $("#modal-title-event").html('<span class="ml-15">Adicionar Evento</span>');
                    $("#btn-modal").addClass('btn-save-event');
                    $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar')
                    $("#modal_add_event").modal('show');
                    $("#form-update-event").hide();
                    $("#form-register-event").show();

                    // $(document).on('click', '.btnDelete', function (event) {
                    //     event.preventDefault();
                    //     $(this).parent().parent().remove();
                    // });


                    /**
                     * Save new Event
                     */
                    $(".btn-save-event").unbind('click');
                    $(".btn-save-event").on('click', function () {

                        if ($('#events').val() == '') {
                            alertCustom('error', 'Dados informados inválidos');
                            return false;
                        }

                        var formData = new FormData(document.getElementById('form-register-event'));
                        formData.append("integration_id", integrationId);
                        loadingOnScreen();
                        $.ajax({
                            method: "POST",
                            url: '/api/apps/activecampaignevent',
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            cache: false,
                            error: function error(response) {
                                loadingOnScreenRemove();
                                errorAjaxResponse(response);
                            },
                            success: function success(response) {
                                loadingOnScreenRemove();
                                index();
                                clearFields();
                                alertCustom("success", "Evento Adicionado!");
                            }
                        });
                    });
                // }
            }
        });
    }

    /**
     * Add new Event
     */
    $("#add-event").on('click', function () {
        $('#modal_add_event').attr('data-backdrop', 'static');
        create();
        $('.btn-close-add-event').on('click', function () {
            clearFields();
            $('#modal_add_event').removeAttr('data-backdrop');
        });
    });

    /**
     * Update Table Event
     */
    function index() {

        var link = '/api/apps/activecampaignevent';

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            data: {
                integration: integrationId
            },
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (_error2) {
                function error() {
                    return _error2.apply(this, arguments);
                }

                error.toString = function () {
                    return _error2.toString();
                };

                return error;
            }(function (response) {
                $("#data-table-event").html('Erro ao encontrar dados');
                errorAjaxResponse(response);

            }),
            success: function success(response) {
                if (isEmpty(response.data)) {
                    $("#data-table-event").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {

                    $("#data-table-event").html('');
                    $.each(response.data, function (index, value) {
                        data = '';
                        data += '<tr>';
                        data += '<td id="" class="" style="vertical-align: middle;">' + value.event_text + '</td>';

                        data += '<td id="" class="" style="vertical-align: middle;">';
                        $.each(value.add_tags, function (index2, value2) {
                            data += '<div class="badge badge-success mr-1">' + value2.tag + '</div>';
                        });
                        data += '</td>';

                        data += '<td id="" class="" style="vertical-align: middle;">';
                        $.each(value.remove_tags, function (index2, value2) {
                            data += '<div class="badge badge-danger mr-1">' + value2.tag + '</div>';
                        });
                        data += '</td>';

                        let addList = (value.add_list != null) ? value.add_list.list : '';
                        let rmList = (value.remove_list != null) ? value.remove_list.list : '';
                        data += '<td id="" class="" style="vertical-align: middle;">' + addList + '</td>';
                        data += '<td id="" class="" style="vertical-align: middle;">' + rmList + '</td>';

                        data += "<td style='text-align:center' class='mg-responsive'>"
                        data += "<a title='Visualizar' class='mg-responsive pointer details-event' event='" + value.id + "' role='button'><i class='material-icons gradient'>remove_red_eye</i></a>"
                        data += "<a title='Editar' class='mg-responsive pointer edit-event' event='" + value.id + "' role='button'data-toggle='modal' data-target='#modal-content'><i class='material-icons gradient'>edit</i></a>"
                        data += "<a title='Excluir' class='mg-responsive pointer delete-event' event='" + value.id + "' role='button'data-toggle='modal' data-target='#modal-delete'><i class='material-icons gradient'>delete_outline</i></a>";
                        data += "</td>";
                        data += '</tr>';
                        $("#data-table-event").append(data);
                        $('#table-events').addClass('table-striped');
                    });

                    if(response.data.length < 5) {
                        $('#add-event').removeClass('d-none');
                        $('#add-event').addClass('d-flex');
                    } else {
                        $('#add-event').addClass('d-none');
                        $('#add-event').removeClass('d-flex');
                    }

                }

                /**
                 * Details Event
                 */
                $(".details-event").unbind('click');
                $('.details-event').on('click', function () {
                    var event = $(this).attr('event');

                    $.ajax({
                        method: "GET",
                        url: '/api/apps/activecampaignevent/'+event,
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function (_error3) {
                            function error(_x3) {
                                return _error3.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error3.toString();
                            };

                            return error;
                        }(function (response) {
                            errorAjaxResponse(response);

                            loadingOnScreenRemove();
                        }), success: function success(response) {
                            if (response.message == 'error') {
                                alertCustom('error', 'Ocorreu um erro ao tentar buscar dados do evento!');
                            } else {
                                $("#modal-title-details").html('Detalhes do Evento <br>');

                                let addList = (response.data.add_list != null) ? response.data.add_list.list : '';
                                let rmList = (response.data.remove_list != null) ? response.data.remove_list.list : '';

                                $('#add_list_text').text(addList);
                                $('#remove_list_text').text(rmList);

                                data = '';
                                $.each(response.data.add_tags, function (index, value) {
                                    data += value.tag + ', ';
                                });
                                $("#add_tags_text").text(data);

                                $("#event_sale").text(response.data.event_text);

                                data = '';
                                $.each(response.data.remove_tags, function (index, value) {
                                    data += value.tag + ', ';
                                });
                                $("#remove_tags_text").text(data);

                                $("#modal_details_event").modal('show');
                            }
                        }
                    });
                });

                /**
                 * Edit Event
                 */
                $(".edit-event").unbind('click');
                $(".edit-event").on('click', function () {
                    loadOnModal('#modal-add-body');
                    $("#modal-add-body").html("");
                    var eventId = $(this).attr('event');
                    $("#modal-title-event").html('<span class="ml-15">Editar Evento</span>');

                    $.ajax({
                        method: "GET",
                        url: '/api/apps/activecampaignevent/' + eventId + '/edit',
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function error() {
                            errorAjaxResponse(response);

                            loadingOnScreenRemove()
                        }, success: function success(response) {
                            $("#form-update-event").html('');
                            $("#form-update-event").html(form_update_event);

                            $('#event_id_edit').val(response.event.id);
                            $('#event_name_edit').html(response.event.event_text);
                            
                            var lists = response.lists;
                            $("#add_list_edit").html('<option>Selecione a lista</option>');
                            $("#remove_list_edit").html('<option>Selecione a lista</option>');
                            if(lists !== null) {
                                $(lists.lists).each(function (index, data) {
                                    let selected = '';
                                    let idAddList = getIdAddList(response);
                                    if(data.id == idAddList) {
                                        selected = 'selected';
                                    }
                                    $("#add_list_edit").append("<option value='" + data.id + ";" + data.name + "' " + selected + ">" + data.name + "</option>");
                                });
                                
                                $(lists.lists).each(function (index, data) {
                                    let selected = '';
                                    let idRemoveList = getIdRemoveList(response);
                                    if(data.id == idRemoveList) {
                                        selected = 'selected';
                                    }
                                    $("#remove_list_edit").append("<option value='" + data.id + ";" + data.name + "' " + selected + ">" + data.name + "</option>");
                                });
                            }

                            var tags = response.tags;
                            $("#add_tags_edit").html('');
                            $("#remove_tags_edit").html('');
                            if(tags !== null) {
                                let arrayTagsAdd = [];
                                $(response.event.add_tags).each(function (index, data) {
                                    arrayTagsAdd.push(data.id);
                                });
                                let arrayTagRemove = [];
                                $(response.event.remove_tags).each(function (index, data) {
                                    arrayTagRemove.push(data.id);
                                });

                                $(tags.tags).each(function (index, data) {
                                    let selectedAdd = '';
                                    let selectedRemove = '';
                                    if(arrayTagsAdd.indexOf(data.id) >= 0) {
                                        selectedAdd = 'selected';
                                    }
                                    if(arrayTagRemove.indexOf(data.id) >= 0) {
                                        selectedRemove = 'selected';
                                    }
                                    $("#add_tags_edit").append("<option value='" + data.id + ";" + data.tag + "' " + selectedAdd + ">" + data.tag + "</option>");
                                    $("#remove_tags_edit").append("<option value='" + data.id + ";" + data.tag + "' " + selectedRemove + ">" + data.tag + "</option>");
                                });
                            }

                            $('#add_tags_edit').select2({
                                dropdownParent: $('#modal_add_event'),
                                placeholder: 'Selecione a(s) tag(s)'
                            });

                            $('#remove_tags_edit').select2({
                                dropdownParent: $('#modal_add_event'),
                                placeholder: 'Selecione a(s) tag(s)'
                            });

                            $('#add_list_edit').select2({
                                dropdownParent: $('#modal_add_event'),
                                placeholder: 'Selecione a lista'
                            });

                            $('#remove_list_edit').select2({
                                dropdownParent: $('#modal_add_event'),
                                placeholder: 'Selecione a lista'
                            });

                            $("#modal_add_event").modal('show');
                            $("#form-register-event").hide();
                            $("#form-update-event").show();

                            $("#btn-modal").removeClass('btn-save-event');
                            $("#btn-modal").addClass('btn-update-event');
                            $("#btn-modal").text('Atualizar');
                            $("#btn-modal").show();

                            loadingOnScreenRemove()

                            $(document).on('click', '.btnDelete', function (event) {
                                event.preventDefault();
                                $(this).parent().parent().remove();
                            });

                            /**
                             * Update Event
                             */
                            $(".btn-update-event").unbind('click');
                            $(".btn-update-event").on('click', function () {
                                var formData = new FormData(document.getElementById('form-update-event'));
                                formData.append("integration_id", integrationId);
                                loadingOnScreen();
                                $.ajax({
                                    method: "POST",
                                    url: '/api/apps/activecampaignevent/' + eventId,
                                    dataType: "json",
                                    headers: {
                                        'Authorization': $('meta[name="access-token"]').attr('content'),
                                        'Accept': 'application/json',
                                    },
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    cache: false,
                                    error: function (_error4) {
                                        function error(_x4) {
                                            return _error4.apply(this, arguments);
                                        }

                                        error.toString = function () {
                                            return _error4.toString();
                                        };

                                        return error;
                                    }(function (response) {
                                        loadingOnScreenRemove();
                                        errorAjaxResponse(response);

                                        index();
                                    }),
                                    success: function success(data) {
                                        loadingOnScreenRemove();
                                        alertCustom("success", "Evento atualizado com sucesso");
                                        index();
                                    }
                                });
                            });
                        }
                    });
                });

                /**
                 * Delete Event
                 */
                $('.delete-event').on('click', function (event) {
                    event.preventDefault();
                    var eventSale = $(this).attr('event');
                    $("#modal-delete-event").modal('show');
                    $("#btn-delete-event").unbind('click');
                    $("#btn-delete-event").on('click', function () {
                        $("#modal-delete-event").modal('hide');
                        loadingOnScreen();

                        $.ajax({
                            method: "DELETE",
                            url: '/api/apps/activecampaignevent/' + eventSale,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function (_error5) {
                                function error(_x5) {
                                    return _error5.apply(this, arguments);
                                }

                                error.toString = function () {
                                    return _error5.toString();
                                };

                                return error;
                            }(function (response) {
                                loadingOnScreenRemove();
                                errorAjaxResponse(response);

                            }),
                            success: function success(response) {
                                loadingOnScreenRemove();
                                alertCustom('success', response.message);
                                index();
                            }

                        });
                    });
                });
            }
        });

    }
})
;

function getIdAddList(response) {
    try {
        let id = response.event.add_list.id;
        return id;
    } catch (e) {
        return 0;
    }
}

function getIdRemoveList(response) {
    try {
        let id = response.event.remove_list.id;
        return id;
    } catch (e) {
        return 0;
    }
}