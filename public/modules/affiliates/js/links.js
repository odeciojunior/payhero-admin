$(function () {

    var projectId = $(window.location.pathname.split('/')).get(-2);
    var affiliateId = $(window.location.pathname.split('/')).get(-1);

    var pageCurrent;

    $('#tab_links').on('click', function () {
        index();
    });

    $("#btn-search-link").on('click', function () {
        index();
    });

    //criar novo link
    $("#modal-create-link .btn-save").on('click', function () {
        let formData = new FormData(document.querySelector('#modal-create-link  #form-register-link'));
        formData.append('affiliate', affiliateId);

        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: "/api/affiliatelinks",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: function (_error) {
                function error(_x) {
                    return _error.apply(this, arguments);
                }

                error.toString = function () {
                    return _error.toString();
                };

                return error;
            }(function (response) {
                loadingOnScreenRemove();
                $("#modal_add_link").hide();
                $(".loading").css("visibility", "hidden");
                errorAjaxResponse(response);

            }), success: function success() {
                loadingOnScreenRemove();
                $(".loading").css("visibility", "hidden");
                alertCustom("success", "Link Adicionado!");
                index();
                $('#link-affiliate').val('');
            }
        });
    });

    // carregar modal de edicao
    $(document).on('click', '.edit-link', function () {
        let link = $(this).attr('link');
        $.ajax({
            method: "GET",
            url: "/api/affiliatelinks/" + link + "/edit",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);

            }, success: function success(response) {
                $('#modal-edit-link .link-id').val(response.data.id);
                $('#modal-edit-link #link-affiliate-update').val(response.data.link);
                $('#modal-edit-link').modal('show');
            }
        });
    });

    //atualizar link
    $(document).on('click', '#modal-edit-link .btn-update', function () {
        loadingOnScreen();
        let link = $('#modal-edit-link .link-id').val();
        $.ajax({
            method: "PUT",
            url: "/api/affiliatelinks/" + link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                link: $("#modal-edit-link #link-affiliate-update").val(),
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);

            },
            success: function success() {
                loadingOnScreenRemove();
                alertCustom("success", "Link atualizado com sucesso");
                index();
            }
        });
    });

    // deletar link
    $(document).on('click', '#modal-delete-link .btn-delete', function () {
        loadingOnScreen();
        let link = $(this).attr('link');
        $.ajax({
            method: "DELETE",
            url: "/api/affiliatelinks/" + link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (_error3) {
                function error() {
                    return _error3.apply(this, arguments);
                }

                error.toString = function () {
                    return _error3.toString();
                };

                return error;
            }(function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);

            }),
            success: function success() {
                loadingOnScreenRemove();
                alertCustom("success", "Link removido com sucesso");
                index();
            }
        });
    });

    //carregar modal delecao
    $(document).on('click', '.delete-link', function (event) {
        let link = $(this).attr('link');
        $("#modal-delete-link .btn-delete").attr("link", link);
        $("#modal-delete-link").modal('show');
    });

    $(document).on('click', '.details-link', function () {
        let link = $(this).attr('link');
        $.ajax({
            method: "GET",
            url: "/api/affiliatelinks/" + link,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error() {
                errorAjaxResponse(response);

            }, success: function success(response) {
                $('#modal-detail-link .link-plan').html(response.data.plan_name);
                $('#modal-detail-link .link-description').html(response.data.description);
                $('#modal-detail-link .link-clicks').html(response.data.clicks);
                $('#modal-detail-link .link-price').html(response.data.price);
                $('#modal-detail-link .link-commission').html(response.data.commission);
                $('#modal-detail-link .link-project').html(response.data.link_project);
                $('#modal-detail-link .link-plan-link').html(response.data.link_plan);
                if (response.data.link != null) {
                    $('#modal-detail-link .link-affiliate-link').val(response.data.link);
                } else {
                    $('#modal-detail-link .link-affiliate-link').val(response.data.link_affiliate);
                }
                $('#modal-detail-link').modal('show');
            }
        });
    });

    /**
     * Update Table Link
     */
    function index() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        pageCurrent = link;

        loadOnTable('#data-table-link', '#table-links');
        if (link == null) {
            link = '/api/affiliatelinks';

        } else {
            link = '/api/affiliatelinks' + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            data: {
                plan: $("#plan-name").val(),
                projectId: projectId
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
                $("#data-table-link").html('Erro ao encontrar dados');
                errorAjaxResponse(response);

            }),
            success: function success(response) {

                if (isEmpty(response.data)) {
                    $("#data-table-link").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                    $('#table-links').addClass('table-striped');

                } else {
                    $("#data-table-link").html('');

                    if (response.data[0].document_status == 'approved') {
                        $.each(response.data, function (index, value) {
                            data = '';
                            data += '<tr>';
                            data += '<td class="display-sm-none display-m-none copy_link_plan" title="Copiar Link" style="cursor:pointer;" link="' + value.link + '">';
                            // if (value.plan_name == null) {
                            //     data += limitLink(value.link, 50) + ' <br><small>' + value.project_name + '</small> </td>';
                            // } else {
                            //     data += limitLink(value.link_plan, 50) + ' <br><small>' + value.plan_name + ' <br> ' + value.description + '</small> </td>';
                            // }
                            data += '<small>' + value.plan_name + ' <br> ' + value.description + '</small> </td>';

                            data += '<td class="display-lg-none display-xlg-none" title="Copiar Link"><a class="material-icons pointer gradient copy_link_plan" link="' + value.link + '"> file_copy</a></td>';

                            data += '<td class="display-sm-none display-m-none copy_link" title="Copiar Link" style="cursor:pointer;" link="' + value.link_affiliate + '">' + limitLink(value.link_affiliate, 40) + '</td>';
                            data += '<td class="display-lg-none display-xlg-none" title="Copiar Link"><a class="material-icons pointer gradient copy_link" link="' + value.link_affiliate + '"> file_copy</a></td>';
                            if (value.price != '' && value.commission != '') {
                                data += '<td class="text-center" >' + value.price + '<br><small>(' + value.commission + ' comissão)<small></td>';
                            } else {
                                data += '<td class="text-center" ></td>';
                            }
                            data += '<td style="text-align:center">';
                            data += '<a title="Visualizar" class="mg-responsive details-link pointer" link="' + value.id + '" data-target="#modal-details-link" data-toggle="modal"><i class="material-icons gradient">remove_red_eye</i></a>';

                            if (value.plan_name == null && value.link != null) {
                                data += '<a title="Editar" class="mg-responsive edit-link pointer" link="' + value.id + '" data-toggle="modal"><i class="material-icons gradient">edit</i></a>';
                                data += '<a title="Excluir" class="mg-responsive delete-link pointer" link="' + value.id + '" data-toggle="modal"><i class="material-icons gradient">delete_outline</i></a>';
                            } else {
                                data += '<a title="Editar" class="mg-responsive pointer disabled"><i class="material-icons gradient">edit</i></a>';
                                data += '<a title="Excluir" class="mg-responsive pointer disabled"><i class="material-icons gradient">delete_outline</i></a>';
                            }
                            data += '</td>';
                            data += '</tr>';
                            $("#data-table-link").append(data);
                            $('#table-links').addClass('table-striped');
                            $('.domain-project-link').html(value.domain);
                        });

                        pagination(response, 'links', index);
                    } else {
                        $("#data-table-link").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Link de pagamento só ficará disponível quando seus documentos e da sua empresa estiverem aprovados</td></tr>");
                        $('#table-links').addClass('table-striped');
                    }

                }
            }
        });

        function limitLink(link, length) {
            if (link.length > length) {
                return link.substr(0, length) + '...';
            }
            return link;
        }

        $(".table-links").on("click", ".copy_link", function () {
            var temp = $("<input>");
            $("#table-links").append(temp);
            temp.val($(this).attr('link')).select();
            document.execCommand("copy");
            temp.remove();
            alertCustom('success', 'Link copiado!');
        });

        $(".table-links").on("click", ".copy_link_plan", function () {
            var temp = $("<input>");
            $("#table-links").append(temp);
            temp.val($(this).attr('link')).select();
            document.execCommand("copy");
            temp.remove();
            alertCustom('success', 'Link copiado!');
        });
    }

    $('#link-affiliate').on('keypress', function (e) {
        if (e.keyCode == 13) {
            return false;
        }
    });

})
;
