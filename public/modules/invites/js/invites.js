$(document).ready(function () {
    updateInvites();

    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    function updateInvites() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        var cont = 0;

        if (link == null) {
            link = '/api/invitations';
        } else {
            link = '/api/invitations' + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (_error) {
                function error(_x2) {
                    return _error.apply(this, arguments);
                }

                error.toString = function () {
                    return _error.toString();
                };

                return error;
            }(function (response) {
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', response.message);
                }
            }), success: function success(response) {
                if (isEmpty(response.data)) {
                    $("#content-error").css('display', 'block');
                } else {
                    $("#content-error").hide();
                    $("#card-table-invite").css('display', 'block');

                    $("#text-info").css('display', 'block');
                    $("#card-table-invite").css('display', 'block');
                    $("#table-body-invites").html('');

                    $.each(response.data, function (index, value) {

                        dados = '';
                        dados += '<tr>';

                        dados += '<td class="text-left" style="vertical-align: middle;"><button class="btn btn-floating btn-primary btn-sm" disabled>' + (cont += 1) + '</button></td>';
                        dados += '<td class="text-center" style="vertical-align: middle;">' + value.email_invited + '</td>';
                        dados += '<td class="text-center" style="vertical-align: middle;">';
                        if (value.status === 'pending') {
                            dados += '<span class="badge badge-primary text-center">Pendente</span>';
                        } else {
                            dados += '<span class="badge badge-success text-center">Aceito</span>';
                        }
                        dados += '</td>';
                        dados += '<td class="text-center" style="vertical-align: middle;">' + value.register_date + '</td>';
                        dados += '<td class="text-center" style="vertical-align: middle;">' + value.expiration_date + '</td>';

                        dados += '</tr>';
                        $("#table-body-invites").append(dados);
                    });

                    pagination(response, 'invites');
                }
            }
        });
    }

    $("#store-invite").unbind();
    $("#store-invite").on('click', function () {

        $.ajax({
            method: "GET",
            url: "/api/companies",
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (_error2) {
                function error(_x3) {
                    return _error2.apply(this, arguments);
                }

                error.toString = function () {
                    return _error2.toString();
                };

                return error;
            }(function (response) {
                $('#companies_table_data').html("<tr class='text-center'><td colspan='11'>Error</td></tr>");
                if (response.status == '422') {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', response.responseJSON.message);
                }
            }),
            success: function success(response) {
                if (isEmpty(response.data)) {
                    loadingOnScreenRemove();
                    modalNotCompanies();
                } else {
                    loadingOnScreenRemove();
                    modalThenCompanies();

                    $("#modal-reverse-title").html('Novo Convite');
                    var selCompany = '';
                    selCompany = '<select class="select-company-list">';
                    var option = '';

                    $.each(response.data, function (index, value) {
                        option += '<option value=' + value.id_code + ' >' + value.fantasy_name + '</option>';
                        selCompany += '<option value=' + value.id_code + ' >' + value.fantasy_name + '</option>';
                    });
                    selCompany += '</select>';

                    $("#company-list").html('').append(selCompany);

                    var linkInvite = '';
                    var companyId = $(".select-company-list option:selected").val();
                    linkInvite = 'https://app.cloudfox.net/register/' + $(".select-company-list option:selected").val();

                    $("#invite-link").val(linkInvite);

                    $(".select-company-list").on('change', function () {
                        linkInvite = 'https://app.cloudfox.net/register/' + $(this).val();
                        $("#invite-link").val(linkInvite);
                        companyId = $(this).val();
                    });

                    $("#copy-link").on("click", function () {
                        var copyText = document.getElementById("invite-link");
                        copyText.select();
                        document.execCommand("copy");

                        alertCustom('success', 'Link copiado!');
                    });

                    $("#btn-send-invite").unbind();
                    $("#btn-send-invite").on('click', function () {
                        var email = $("#email").val();

                        if (email == '') {
                            alertCustom('error', 'O campo Email do convidado é obrigatório');
                        } else if (companyId == '') {
                            alertCustom('error', 'O campo Empresa para receber é obrigatório');
                        } else {
                            loadingOnScreen();
                            sendInviteAjax(email, companyId);
                        }
                    });
                }
            }
        });

        $("#modal-invite").modal('show');
    });

    function sendInviteAjax(email, companyId) {
        $.ajax({
            method: "POST",
            url: "/api/invitations",
            dataType: "json",
            data: {
                email: email,
                company: companyId
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (_error3) {
                function error(_x4) {
                    return _error3.apply(this, arguments);
                }

                error.toString = function () {
                    return _error3.toString();
                };

                return error;
            }(function (response) {
                console.log(response);
                if (response.status == '422') {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {

                    alertCustom('error', response.responseJSON.message);
                }
                modalThenCompanies();
                loadingOnScreenRemove();
            }),
            success: function success(response) {
                $(".close").click();
                alertCustom('success', response.message);
                loadingOnScreenRemove();
                updateInvites();
            }
        });
    }

    function pagination(response, model) {
        if (response.meta.last_page == 1) {
            $("#primeira_pagina_" + model).hide();
            $("#ultima_pagina_" + model).hide();
        } else {
            $("#pagination-" + model).html("");

            var first_page = "<button id='first_page' class='btn nav-btn'>1</button>";

            $("#pagination-" + model).append(first_page);

            if (response.meta.current_page == '1') {
                $("#first_page").attr('disabled', true).addClass('nav-btn').addClass('active');
            }

            $('#first_page').on("click", function () {
                updateInvites('?page=1');
            });

            for (x = 3; x > 0; x--) {

                if (response.meta.current_page - x <= 1) {
                    continue;
                }

                $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page - x) + "' class='btn nav-btn'>" + (response.meta.current_page - x) + "</button>");

                $('#page_' + (response.meta.current_page - x)).on("click", function () {
                    updateInvites('?page=' + $(this).html());
                });
            }

            if (response.meta.current_page != 1 && response.meta.current_page != response.meta.last_page) {
                var current_page = "<button id='current_page' class='btn nav-btn active'>" + response.meta.current_page + "</button>";

                $("#pagination-" + model).append(current_page);

                $("#current_page").attr('disabled', true).addClass('nav-btn').addClass('active');
            }
            for (x = 1; x < 4; x++) {

                if (response.meta.current_page + x >= response.meta.last_page) {
                    continue;
                }

                $("#pagination-" + model).append("<button id='page_" + (response.meta.current_page + x) + "' class='btn nav-btn'>" + (response.meta.current_page + x) + "</button>");

                $('#page_' + (response.meta.current_page + x)).on("click", function () {
                    updateInvites('?page=' + $(this).html());
                });
            }

            if (response.meta.last_page != '1') {
                var last_page = "<button id='last_page' class='btn nav-btn'>" + response.meta.last_page + "</button>";

                $("#pagination-" + model).append(last_page);

                if (response.meta.current_page == response.meta.last_page) {
                    $("#last_page").attr('disabled', true).addClass('nav-btn').addClass('active');
                }

                $('#last_page').on("click", function () {
                    updateInvites('?page=' + response.meta.last_page);
                });
            }
        }
    }
    //ALTERAÇÃO DE HTML

    function modalNotCompanies() {
        $('#mainModalBody').html('<div id="modal-not-companies" class="modal-content p-10">' + '<div class="header-modal simple-border-bottom">' + '<h2 id="modal-title" class="modal-title">Ooooppsssss!</h2></div>' + '<div class="modal-body simple-border-bottom" style="padding-bottom:1%; padding-top:1%;">' + '<div class="swal2-icon swal2-error swal2-animate-error-icon" style="display:flex;">' + '<span class="swal2-x-mark">' + '<span class="swal2-x-mark-line-left"></span>' + '<span class="swal2-x-mark-line-right"></span></span></div>' + '<h3 align="center">Você não cadastrou nenhuma empresa</h3>' + '<h5 align="center">Deseja cadastrar uma empresa?' + '<a class="red pointer" href="/companies" target="_blank">clique aqui</a></h5></div>' + '<div style="width:100%; text-align:center; padding-top:3%;">' + '<span class="btn btn-danger" data-dismiss="modal" style="font-size: 25px;">Retornar</span>' + '</div></div>');
    }

    function modalThenCompanies() {
        $('#mainModalBody').html('<div id="modal-then-companies" class="modal-content"><div class="modal-header">' + '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>' + '<h4 id="modal-reverse-title" class="modal-title" style="width: 100%; text-align:center"></h4></div>' + '<div id="modal-reverse-body" class="modal-body"><div id="body-modal"><div class="row">' + '<div class="form-group col-12"><label for="email">Email do convidado</label>' + '<input name="email_invited" type="text" class="form-control" id="email" placeholder="Email">' + '</div></div><div class="row"><div class="form-group col-12">' + '<label for="company">Empresa para receber</label><div id="company-list"></div></div></div>' + '<div class="row"><div class="col-12"><label for="email">Link de convite</label>' + '</div><div id="invite-link-select" class="input-group col-12"><input type="text" class="form-control" id="invite-link" value="" readonly>' + '<span class="input-group-btn"><button id="copy-link" class="btn btn-default" type="button">Copiar</button>' + '</span></div></div><div class="row" style="margin-top: 35px"><div class="form-group col-12">' + '<input id="btn-send-invite" type="button" class="form-control btn" value="Enviar Convite" style="color:white;width: 30%;background-image: linear-gradient(to right, #e6774c, #f92278);position:relative; float:right">' + '</div></div></div></div></div>');
    }
});
