$(function () {

    var projectId = $("#project-id").val();

    $("#tab-partners").on('click', function () {
        $("#previewimage").imgAreaSelect({ remove: true });
        updatePartners();
    });

    updatePartners();

    function maskPercent() {
        $("#value-remuneration").mask("#0,00%", { reverse: true });
    }

    $("#add-partners").click(function () {
        $("#modal-title").html("Cadastrar Parceiro <br><hr>");
        $("#modal-add-body").html("<h5 style='width: 100%; tex-align: center;'>Carregando...</h5>");

        $.ajax({
            method: "GET",
            url: "/partners/create",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                $("#modal-add-body").html("Erro ao tentar acessar pagina");
            },
            success: function success(response) {
                $("#btn-modal").addClass('btn-save').text('Salvar').show();
                $("#modal-add-body").html(response);

                maskPercent();

                $(".btn-save").unbind('click');
                $(".btn-save").click(function () {
                    var formData = new FormData(document.getElementById("form-add-partner"));
                    formData.append('project', projectId);

                    $.ajax({
                        method: "POST",
                        url: "/partners",
                        processData: false,
                        contentType: false,
                        cache: false,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name= "csrf-token"]').attr('content')
                        },
                        data: formData,

                        error: function (_error) {
                            function error(_x) {
                                return _error.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error.toString();
                            };

                            return error;
                        }(function (response) {
                            if (response.status === 422) {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.errors[error]));
                                }
                            }
                        }),
                        success: function success(response) {
                            alertCustom("success", response.message);
                            updatePartners();
                        }
                    });
                });
            }
        });
    });

    function updatePartners() {
        $("#data-table-partners").html("<tr class='text-center'><td colspan='11'>Carregando...</td></tr>");

        $.ajax({
            method: "GET",
            url: "/partners",
            data: { project: projectId },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function error() {
                $("#data-table-partners").html("Erro ao encontrar dados");
            },
            success: function success(response) {
                $("#data-table-partners").html('');
                $.each(response.data, function (index, value) {
                    dados = '';
                    dados += '<tr>';
                    dados += '<td class="shipping-id text-center" style="vertical-align: middle;">' + value.name + '</td>';
                    dados += '<td class="shipping-name text-center" style="vertical-align: middle;">' + value.type + '</td>';
                    dados += '<td class="shipping-type text-center" style="vertical-align: middle;">' + value.status + '</td>';
                    dados += '<div class="btn-group">';
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger details-partners'  partners='" + value.partnersId + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger editar-partnes'  partners='" + value.partnersId + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger excluir-partners'  partners='" + value.partnersId + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";
                    dados += '</div>';
                    dados += '</tr>';

                    $("#data-table-partners").append(dados);
                });

                if (response.data == '') {
                    $("#data-table-partners").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align:middle;'>Nenhum registro encontrado </td></tr>");
                }

                $(".details-partners").unbind('click');
                $(".details-partners").on('click', function () {
                    var partners = $(this).attr('partners');

                    $("#modal-title").html('Detalhes do Parceiro<br><hr>');
                    $("#modal-add-body").html("<h5 style='width: 100%; text-align: center;'>Carregando...</h5>");

                    $.ajax({
                        method: "GET",
                        url: "/partners/" + partners,
                        data: { data: partners },
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            //
                        },
                        success: function success(response) {
                            $("#btn-modal").hide();
                            $("#modal-add-body").html(response);
                        }
                    });
                });

                $(".editar-partners").unbind('click');
                $(".editar-partners").on('click', function () {
                    $("#modal-add-body").html("");
                    var partners = $(this).attr('partners');

                    $("#modal-title").html("Editar parceiro<br><hr>");
                    $("#modal-add-body").html("<h5 style='width:100%; text-align:center;'>Carregando...</h5>");

                    var formData = new FormData(document.getElementById("form-edit-partners"));
                    formData.append('project', projectId);

                    var data = { partner: partners };

                    $.ajax({
                        method: "GET",
                        url: "/partners/" + partners + '/edit',
                        data: data,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            //
                        },
                        success: function success(response) {
                            $("#btn-modal").addClass('btn-save');
                            $("#btn-modal").text('Salvar');
                            $("#btn-modal").show();
                            $("#modal-add-body").html(response);

                            $(".btn-save").unbind('click');
                            $(".btn-save").on('click', function () {

                                var formData = new FormData(document.getElementById("form-add-partner"));
                                formData.append('project', projectId);
                                $.ajax({
                                    method: 'PUT',
                                    url: "/partners/" + partners,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: formData,
                                    error: function (_error2) {
                                        function error(_x2) {
                                            return _error2.apply(this, arguments);
                                        }

                                        error.toString = function () {
                                            return _error2.toString();
                                        };

                                        return error;
                                    }(function (response) {
                                        if (response.status === 422) {
                                            for (error in response.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                    }),
                                    success: function success(response) {
                                        alertCustom('success', 'Parceiro atualizado com sucesso');
                                        updatePartners();
                                    }
                                });
                            });
                        }
                    });
                });

                $(".excluir-partners").on('click', function (event) {
                    event.preventDefault();
                    var partners = $(this).attr('partners');

                    $("#modal_excluir_titulo").html('Remover parceiro?');

                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on('click', function () {
                        $('#fechar_modal_excluir').click();

                        $.ajax({
                            method: 'DELETE',
                            url: '/partners/' + partners,
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function (_error3) {
                                function error() {
                                    return _error3.apply(this, arguments);
                                }

                                error.toString = function () {
                                    return _error3.toString();
                                };

                                return error;
                            }(function () {
                                if (response.status === 422) {
                                    for (error in response.response.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                }
                            }),
                            success: function success(data) {
                                alertCustom('success', 'Parceiro removido com sucesso');
                                updatePartners();
                            }
                        });
                    });
                });
            }
        });
    }
});
