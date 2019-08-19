$(document).ready(function () {

    var projectId = $("#project-id").val();

    $("#tab-fretes").on('click', function () {
        $("#previewimage").imgAreaSelect({remove: true});
        atualizarFrete();
    });
    atualizarFrete();

    function changeType() {
        $("#shipping-type").change(function () {
            // altera campo value dependendo do tipo do frete
            var selected = $("#shipping-type").val();
            if (selected === 'static') {
                $('#shipping-name').attr('placeholder', 'Frete grátis');
                $("#value-shipping-row").css('display', 'block');
                $("#zip-code-origin-shipping-row").css('display', 'none');
            } else if (selected == 'pac') {
                $('#shipping-name').attr('placeholder', 'PAC');
                $("#value-shipping-row").css('display', 'none');
                $("#zip-code-origin-shipping-row").css('display', 'block');
            } else if (selected == 'sedex') {
                $('#shipping-name').attr('placeholder', 'SEDEX');
                $("#value-shipping-row").css('display', 'none');
                $("#zip-code-origin-shipping-row").css('display', 'block');
            }

            //mask money
            $('#shipping-value').mask('#.###,#0', {reverse: true});
        });
    }

    //mask money
    $('#shipping-value').mask('#.###,#0', {reverse: true});

    $("#add-shipping").on('click', function () {
        loadOnModal('#modal-add-body');
        $("#modal-title").html('Cadastrar frete');

        $.ajax({
            method: "GET",
            url: "/shippings/create",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function (_error) {
                function error() {
                    return _error.apply(this, arguments);
                }

                error.toString = function () {
                    return _error.toString();
                };

                return error;
            }(function (response) {
                loadingOnScreenRemove();
                if (response.status === 422) {
                    for (error in response.responseJSON.errors) {
                        alertCustom('error', String(response.responseJSON.errors[error]));
                    }
                } else {
                    alertCustom('error', String(response.responseJSON.message));

                }
            }),
            success: function success(response) {
                loadingOnScreenRemove();
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").html('<i class="material-icons btn-fix"> save </i>Salvar');
                $("#btn-modal").show();
                $("#modal-add-body").html(response);
                $('#shipping-zip-code-origin').mask('00000-000');
                $('#shipping-value').mask('#.###,#0', {reverse: true});

                $('.check').on('click', function () {
                    if ($(this).is(':checked')) {
                        $(this).val(1);
                    } else {
                        $(this).val(0);
                    }
                });

                changeType();
                let name = true;
                let information = true;
                let value = true;

                $("#shipping-name").keyup(function () {
                    if ($("#shipping-name").val().length >= 30) {
                        $("#shipping-name-error").html("O campo descrição permite apenas 30 caracteres");
                        name = false;
                    } else {
                        $("#shipping-name-error").html("")
                        name = true;
                    }

                });

                $("#shipping-information").keyup(function () {
                    if ($("#shipping-information").val().length >= 30) {
                        $("#shipping-information-error").html("O campo tempo de entrega estimado permite apenas 30 caracteres");
                        information = false;
                    } else {
                        $("#shipping-information-error").html("")
                        information = true;
                    }

                });

                $("#shipping-value").keyup(function () {
                    if ($.trim($("#shipping-value").val()).length >= 7) {
                        $("#shipping-value-error").html("O campo valor permite apenas 6  caracteres");
                        value = false;
                    } else {
                        $("#shipping-value-error").html("")
                        value = true;
                    }

                });

                $(".btn-save").unbind('click');
                $(".btn-save").click(function () {
                    var formData = new FormData(document.getElementById('form-add-shipping'));
                    formData.append("project", projectId);
                    loadingOnScreen();

                    $.ajax({
                        method: "POST",
                        url: "/shippings",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        error: function (_error2) {
                            function error(_x) {
                                return _error2.apply(this, arguments);
                            }

                            error.toString = function () {
                                return _error2.toString();
                            };

                            return error;
                        }(function (response) {
                            if (response.status === 422) {
                                for (error in response.responseJSON.errors) {
                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                }
                            } else {
                                alertCustom('error', String(response.responseJSON.message));

                            }
                        }),
                        success: function success(data) {
                            loadingOnScreenRemove();
                            alertCustom("success", data.message);
                            atualizarFrete();
                        }
                    });
                });
            }
        });
    });

    function atualizarFrete(link = null) {

        loadOnTable('#dados-tabela-frete', '#tabela_fretes');
        changeType();
        if (link == null) {
            link = '/shippings?' + 'project=' + projectId;
        } else {
            link = '/shippings' + link + '&project=' + projectId;
        }
        $.ajax({
            method: "GET",
            url: link,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function error(response) {
                $("#dados-tabela-frete").html(response.message);
            },
            success: function success(response) {

                $("#dados-tabela-frete").html('');

                if (response.data == '') {
                    $("#dados-tabela-frete").html("<tr class='text-center'><td colspan='8' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        dados = '';
                        dados += '<tr>';
                        dados += '<td class="shipping-id " style="vertical-align: middle; display: none;">' + value.shipping_id + '</td>';
                        dados += '<td class="shipping-type " style="vertical-align: middle; display: none;">' + value.type + '</td>';
                        dados += '<td class="shipping-value " style="vertical-align: middle; display: none;">' + value.value + '</td>';
                        dados += '<td class="shipping-zip-code-origin " style="vertical-align: middle; display: none;">' + value.zip_code_origin + '</td>';
                        dados += '<td class="shipping-id " style="vertical-align: middle;">' + value.type + '</td>';
                        dados += '<td class="shipping-name " style="vertical-align: middle;">' + value.name + '</td>';
                        dados += '<td class="shipping-type " style="vertical-align: middle;">' + value.value + '</td>';
                        dados += '<td class="shipping-information " style="vertical-align: middle;">' + value.information + '</td>';
                        dados += '<td class="shipping-status " style="vertical-align: middle;">';
                        if (value.status === 1) {
                            dados += '<span class="badge badge-success">Ativo</span>';
                        } else {
                            dados += '<span class="badge badge-danger">Desativado</span>';
                        }

                        dados += '</td>';

                        dados += '<td class="shipping-pre-selected text-center display-sm-none display-m-none" style="vertical-align: middle;">';
                        if (value.pre_selected === 1) {
                            dados += '<span class="badge badge-success">Sim</span>';
                        } else {
                            dados += '<span class="badge badge-primary"> Não </span>';
                        }
                        dados += '</td>';
                        dados += "<td style='text-align:center'>"
                        dados += "<a role='button' class='pointer detalhes-frete mg-responsive'  frete='" + value.shipping_id + "' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a>"
                        dados += "<a role='button' class='pointer editar-frete mg-responsive'  frete='" + value.shipping_id + "' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a>"
                        dados += "<a role='button' class='pointer excluir-frete mg-responsive'  frete='" + value.shipping_id + "'  data-toggle='modal' data-target='#modal-delete'> <i class='material-icons gradient'> delete_outline </i></a>";
                        "</td>";
                        dados += '</tr>';
                        $("#dados-tabela-frete").append(dados);
                    });

                    pagination(response, 'shippings', atualizarFrete);

                    $(".detalhes-frete").unbind('click');
                    $(".detalhes-frete").on('click', function () {
                        loadOnModal("#modal-add-body");
                        var frete = $(this).attr('frete')
                        $("#modal-title").html('Detalhes do frete');
                        var data = {freteId: frete};

                        $("#btn-modal").hide();

                        $.ajax({
                            method: "GET",
                            url: "/shippings/" + frete,
                            data: data,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function error() {
                                //
                                loadingOnScreenRemove();
                            }, success: function success(response) {
                                loadingOnScreenRemove();
                                $("#modal-add-body").html(response);
                            }
                        });
                    });

                    $(".editar-frete").unbind('click');
                    $(".editar-frete").on("click", function () {
                        loadOnModal("#modal-add-body");
                        $("#modal-add-body").html("");
                        var frete = $(this).attr('frete');

                        $("#modal-title").html("Editar Frete");

                        var data = {frete: frete};

                        $.ajax({
                            method: "GET",
                            url: "/shippings/" + frete + "/edit",
                            data: data,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function error() {
                                //
                                loadingOnScreenRemove()
                            }, success: function success(response) {
                                loadingOnScreenRemove()
                                $("#btn-modal").addClass('btn-update');
                                $("#btn-modal").text('Atualizar');
                                $("#btn-modal").show();
                                $("#modal-add-body").html(response);
                                $('#shipping-zip-code-origin').mask('00000-000');
                                $('.check').on('click', function () {
                                    if ($(this).is(':checked')) {
                                        $(this).val(1);
                                    } else {
                                        $(this).val(0);
                                    }
                                });
                                var selected = $("#shipping-type").val();
                                if (selected === 'static') {
                                    $("#value-shipping-row").css('display', 'block');
                                    $("#zip-code-origin-shipping-row").css('display', 'none');
                                } else {
                                    $("#value-shipping-row").css('display', 'none');
                                    $("#zip-code-origin-shipping-row").css('display', 'block');
                                }
                                changeType();
                                $('#shipping-value').mask('#.###,#0', {reverse: true});

                                $(".btn-update").unbind('click');
                                $(".btn-update").on('click', function () {
                                    var formData = new FormData(document.getElementById('form-update-shipping'));
                                    formData.append("project", projectId);
                                    loadingOnScreen();
                                    $.ajax({
                                        method: "POST",
                                        url: "/shippings/" + frete,
                                        headers: {
                                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                        },
                                        data: formData,
                                        processData: false,
                                        contentType: false,
                                        cache: false,
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
                                            if (response.status === 422) {
                                                for (error in response.responseJSON.errors) {
                                                    alertCustom('error', String(response.responseJSON.errors[error]));
                                                }
                                            } else {
                                                alertCustom('error', String(response.responseJSON.message));
                                            }
                                        }),
                                        success: function success(data) {
                                            loadingOnScreenRemove();
                                            alertCustom("success", "Frete atualizado com sucesso");
                                            atualizarFrete();
                                        }
                                    });
                                });
                            }
                        });
                    });

                    $(".excluir-frete").on('click', function (event) {
                        event.preventDefault();
                        var frete = $(this).attr('frete');

                        $("#modal_excluir_titulo").html("Remover Frete?");

                        $("#bt_excluir").unbind('click');
                        $("#bt_excluir").on("click", function () {
                            $("#fechar_modal_excluir").click();
                            loadingOnScreen();
                            $.ajax({
                                method: "DELETE",
                                url: "/shippings/" + frete,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                error: function (_error4) {
                                    function error(_x3) {
                                        return _error4.apply(this, arguments);
                                    }

                                    error.toString = function () {
                                        return _error4.toString();
                                    };

                                    return error;
                                }(function (response) {
                                    loadingOnScreenRemove();
                                    if (response.status == '422') {
                                        for (error in response.responseJSON.errors) {
                                            alertCustom('error', String(response.responseJSON.errors[error]));
                                        }
                                    }
                                    if (response.status == '400') {
                                        alertCustom('error', response.responseJSON.message);
                                    }
                                }),
                                success: function success(data) {
                                    loadingOnScreenRemove();
                                    alertCustom("success", "Frete Removido com sucesso");
                                    atualizarFrete();
                                }

                            });
                        });
                    });
                }
            }
        });
    }

    $("#shippement").on('change', function () {
        if ($(this).val() == 0) {
            $("#div-carrier").hide();
            $("#div-shipment-responsible").hide();
        } else {
            $("#div-carrier").show();
            $("#div-shipment-responsible").show();
        }
    });

    $("#bt-add-shipping-config").unbind('click');
    $("#bt-add-shipping-config").on('click', function (event) {
        event.preventDefault();
        var formData = new FormData(document.getElementById('form-config-shipping'));

        $.ajax({
            method: "POST",
            url: "/shipping/config/" + projectId,
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            }, error: function error() {
                //
            }, success: function success() {
                alertCustom('success', 'Configuração atualizadas com sucesso');
                atualizarFrete();
            }
        });
    });

});
