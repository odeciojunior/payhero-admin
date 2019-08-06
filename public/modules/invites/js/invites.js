$(document).ready(function () {
    updateInvites();

    $("#copy-link").on("click", function () {
        var copyText = document.getElementById("invite-link");
        copyText.select();
        document.execCommand("copy");

        alertCustom('success', 'Link copiado!');
    });

    $("#company").on("change", function () {

        $("#invite-link").val('https://app.cloudfox.net/register/' + $("#company option:selected").attr('invite-parameter'));
    });

    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    function updateInvites(link = null) {
        let cont = 0;

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
            error: function (response) {
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', response.message);
                }
            }, success: function (response) {
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
            error: function (response) {
                $('#companies_table_data').html("<tr class='text-center'><td colspan='11'>Error</td></tr>");
                if (response.status == '422') {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {

                    alertCustom('error', response.responseJSON.message);

                }
            },
            success: function (response) {
                if (isEmpty(response.data)) {
                    $("#modal-then-companies").css('display', 'none');
                    $("#modal-not-companies").css('display', 'block');
                } else {
                    $("#modal-not-companies").css('display', 'none');
                    $("#modal-then-companies").css('display', 'block');

                    $("#modal-reverse-title").html('Novo Convite');
                    let selCompany = '';
                    selCompany = '<select class="select-company-list">';
                    let option = '';

                    $.each(response.data, function (index, value) {
                        option += '<option value=' + value.id_code + ' >' + value.fantasy_name + '</option>';
                        selCompany += '<option value=' + value.id_code + ' >' + value.fantasy_name + '</option>';
                    });
                    selCompany += '</select>';

                    $("#company-list").html('').append(selCompany);

                    let linkInvite = '';
                    let companyId = $(".select-company-list option:selected").val();
                    linkInvite = 'https://app.cloudfox.net/register/' + $(".select-company-list option:selected").val();

                    $("#invite-link").val(linkInvite);

                    $(".select-company-list").on('change', function () {
                        linkInvite = 'https://app.cloudfox.net/register/' + $(this).val();
                        $("#invite-link").val(linkInvite);
                        companyId = $(this).val();
                    });

                    $("#btn-send-invite").unbind();
                    $("#btn-send-invite").on('click', function () {
                        let email = $("#email").val();

                        if (email == '') {
                            alertCustom('error', 'O campo Email do convidado é obrigatório');
                        } else if (companyId == '') {
                            alertCustom('error', 'O campo Empresa para receber é obrigatório');
                        } else {
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
            error: function (response) {
                console.log(response);
                if (response.status == '422') {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {

                    alertCustom('error', response.responseJSON.message);

                }
            },
            success: function (response) {

                $(".close").click();
                alertCustom('success', response.message);

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
                var current_page = "<button id='current_page' class='btn nav-btn active'>" + (response.meta.current_page) + "</button>";

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

});
