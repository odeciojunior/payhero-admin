$(document).ready(function () {
    var badgeAffiliateRequest = {
        1: "primary",
        2: "warning",
        3: "success",
        4: "danger",
    };
    var badgeAffiliates = {
        1: "success",
        2: "danger",
    };
    getAffiliates();
    getAffiliatesRequest();

    $('#tab-affiliates').on('click', function () {
        getAffiliates();
    });

    $('#tab-affiliates-request').on('click', function () {
        getAffiliatesRequest();
    });

    function getAffiliates() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/affiliates/getaffiliates';
        } else {
            link = '/api/affiliates/getaffiliates' + link;
        }

        loadOnTable('.body-table-affiliates', '.table-affiliate');

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {
                errorAjaxResponse(response);

            }, success: function (response) {
                $(".body-table-affiliates").html('');
                if (response.data == '') {
                    $(".body-table-affiliates").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum afiliado encontrado</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        data = '';
                        data += '<tr>';
                        data += '<td class="" style="vertical-align: middle;">' + value.name + '</td>';
                        // data += '<td class="" style="vertical-align: middle;">' + value.email + '</td>';
                        data += '<td class="" style="vertical-align: middle;">' + value.project_name + '</td>';
                        data += '<td class="" style="vertical-align: middle;">' + value.date + '</td>';
                        data += '<td class="text-center" style="vertical-align: middle;">' + value.percentage + '</td>';
                        data += '<td class="text-center" ><span class="badge badge-' + badgeAffiliates[value.status] + '">' + value.status_translated + '</span></td>';
                        data += "<td class='text-center'>";
                        data += "<a title='Visualizar' class='mg-responsive pointer details-affiliate' affiliate='" + value.id + "'><i class='material-icons gradient'>remove_red_eye</i></a>";
                        data += "<a title='Editar' class='mg-responsive pointer edit-affiliate'    affiliate='" + value.id + "'><i class='material-icons gradient'>edit</i></a>"
                        data += "<a title='Excluir' class='mg-responsive pointer delete-affiliate' affiliate='" + value.id + "'><i class='material-icons gradient'>delete_outline</i></a>";
                        data += "</td>";
                        data += '</tr>';
                        $(".body-table-affiliates").append(data);
                    });
                }
                $('.table-affiliate').addClass('table-striped');
                pagination(response, 'affiliates', getAffiliates);

                $('.delete-affiliate').on('click', function (event) {
                    event.preventDefault();
                    let affiliate = $(this).attr('affiliate');
                    $('#modal-delete-affiliate').modal('show');

                    $("#modal-delete-affiliate .btn-delete").unbind('click');
                    $("#modal-delete-affiliate .btn-delete").on('click', function () {
                        $("#modal-delete").modal('hide');
                        loadingOnScreen()
                        $.ajax({
                            method: "DELETE",
                            url: "/api/affiliates/" + affiliate,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function (response) {
                                errorAjaxResponse(response);

                                alertCustom('error', 'Ocorreu algum erro');
                                loadingOnScreenRemove()
                            },
                            success: function (data) {
                                loadingOnScreenRemove();
                                getAffiliates();
                            }
                        });
                    });

                });

                $(document).on('click', '.edit-affiliate', function () {
                    let affiliate = $(this).attr('affiliate');
                    $.ajax({
                        method: "GET",
                        url: "/api/affiliates/" + affiliate + "/edit",
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function error(response) {
                            errorAjaxResponse(response);
                        }, success: function success(response) {
                            $('#modal-edit-affiliate .affiliate-id').val(affiliate);
                            $('#modal-edit-affiliate .affiliate-name').val(response.data.name);
                            // $('#modal-edit-affiliate .affiliate-email').val(response.data.email);
                            // $('#modal-edit-affiliate .affiliate-company').val(response.data.company);
                            $('#modal-edit-affiliate .affiliate-percentage').val(response.data.percentage);
                            if (response.data.status == 1) {
                                $('#modal-edit-affiliate .affiliate-status').prop("selectedIndex", 0).change();
                            } else if (response.data.status == 2) {
                                $('#modal-edit-affiliate .affiliate-status').prop("selectedIndex", 1).change();
                            }
                            $('#modal-edit-affiliate').modal('show');
                        }
                    });
                });

                $(document).on('click', '.details-affiliate', function () {
                    let affiliate = $(this).attr('affiliate');
                    $.ajax({
                        method: "GET",
                        url: "/api/affiliates/" + affiliate + "/edit",
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function error(response) {
                            errorAjaxResponse(response);
                        }, success: function success(response) {
                            $('#modal-show-affiliate .affiliate-name').text(response.data.name);
                            $('#modal-show-affiliate .affiliate-email').text(response.data.email);
                            $('#modal-show-affiliate .affiliate-company').text(response.data.company);
                            $('#modal-show-affiliate .affiliate-project').text(response.data.project_name);
                            $('#modal-show-affiliate .affiliate-phone').text(response.data.cellphone);
                            $('#modal-show-affiliate .affiliate-percent').text(response.data.percentage);
                            $('#modal-show-affiliate .affiliate-date').text(response.data.date);
                            $('#modal-show-affiliate .affiliate-status').html('<span class="badge badge-' + badgeAffiliates[response.data.status] + '">' + response.data.status_translated + '</span>');

                            $('#modal-show-affiliate .affiliate-percentage').text(response.data.percentage);
                            $('#modal-show-affiliate .affiliate-percentage').text(response.data.percentage);

                            $('#modal-show-affiliate').modal('show');
                        }
                    });
                });

                $("#modal-edit-affiliate .btn-update").unbind('click');
                $("#modal-edit-affiliate .btn-update").on('click', function () {
                    let formData = new FormData(document.getElementById('form-update-affiliate'));
                    console.log(formData);
                    // formData.append("integration_id", integrationId);

                    let affiliate = $('#modal-edit-affiliate .affiliate-id').val();
                    loadingOnScreen();
                    $.ajax({
                        method: "POST",
                        url: "/api/affiliates/" + affiliate,
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
                            alertCustom("success", "Afiliado atualizado com sucesso");
                            getAffiliates();
                        }
                    });
                });

            }
        });
    }
    function getAffiliatesRequest() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/api/affiliates/getaffiliaterequests';
        } else {
            link = '/api/affiliates/getaffiliaterequests' + link;
        }

        loadOnTable('.body-table-affiliate-requests', '.table-affiliate-request');

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {
                errorAjaxResponse(response);

            }, success: function (response) {
                $(".body-table-affiliate-requests").html('');
                if (response.data == '') {
                    $(".body-table-affiliate-requests").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhuma solicitação de afiliação encontrada</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        data = '';
                        data += '<tr>';
                        data += '<td class="" style="vertical-align: middle;">' + value.name + '</td>';
                        data += '<td class="" style="vertical-align: middle;">' + value.email + '</td>';
                        data += '<td class="" style="vertical-align: middle;">' + value.project_name + '</td>';
                        data += '<td class="" style="vertical-align: middle;">' + value.date + '</td>';
                        // data += '<td class="text-center" ><span class="badge badge-' + badgeAffiliateRequest[value.status] + '">' + value.status_translated + '</span></td>';
                        data += "<td class='text-center'>";
                        if (value.status != 3) {
                            data += "<a title='Aprovar' class='text-white ml-2 badge badge-success pointer evaluate-affiliate' affiliate='" + value.id + "' status='3'>Aprovar</a>";
                            if (value.status != 4) {
                                data += "<a title='Recusar' class='text-white ml-2 badge badge-danger pointer evaluate-affiliate' affiliate='" + value.id + "' status='4'>Recusar</a>";
                            }
                        }

                        data += "</td>";
                        data += '</tr>';
                        $(".body-table-affiliate-requests").append(data);
                    });
                }
                $('.table-affiliate-request').addClass('table-striped');
                pagination(response, 'affiliates-request', getAffiliatesRequest);

                $(".evaluate-affiliate").on('click', function () {
                    let affiliate = $(this).attr('affiliate');
                    let status = $(this).attr('status');

                    loadingOnScreen();
                    $.ajax({
                        method: "POST",
                        url: "/api/affiliates/evaluateaffiliaterequest",
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        data: {status: status, affiliate: affiliate},
                        error: function (response) {
                            loadingOnScreenRemove();
                            errorAjaxResponse(response);
                        },
                        success: function success(data) {
                            loadingOnScreenRemove();
                            alertCustom("success", "Solicitação de afiliação atualizada com sucesso");
                            getAffiliatesRequest();
                        }
                    });
                });

            }
        });
    }
});
