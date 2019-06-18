$(function () {

    var projectId = $("#project-id").val();

    ///// UDPATE CONFIGURAÇÃO Tela Project
    function updateConfiguracoes() {
        $.ajax({
            method: "GET",
            url: "/projects/" + projectId + '/edit',
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            }, error: function () {
                alertCustom('error', 'Ocorreu algum error');
            }, success: function (data) {
                $("#configuracoes_projeto").html(data);

                $("#porcentagem_afiliados").mask("0#");

                var p = $("#previewimage");
                $("#foto_projeto").on("change", function () {
                    var imageReader = new FileReader();
                    imageReader.readAsDataURL(document.getElementById("foto_projeto").files[0]);

                    imageReader.onload = function (oFREvent) {
                        p.attr('src', oFREvent.target.result).fadeIn();
                        p.on('load', function () {
                            var img = document.getElementById('previewimage');
                            var x1, x2, y1, y2;

                            if (img.naturalWidth > img.naturalHeight) {
                                y1 = Math.floor(img.naturalHeight / 100 * 10);
                                y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                x2 = x1 + (y2 - y1);
                            } else {
                                if (img.naturalWidth < img.naturalHeight) {
                                    x1 = Math.floor(img.naturalWidth / 100 * 10);
                                    x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                    y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                    y2 = y1 + (x2 - x1);
                                } else {
                                    x1 = Math.floor(img.naturalWidth / 100 * 10);
                                    x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                }
                            }

                            $('input[name="project_photo_x1"]').val(x1);
                            $('input[name="project_photo_y1"]').val(y1);
                            $('input[name="project_photo_w1"]').val(x2 - x1);
                            $('input[name="project_photo_h1"]').val(y2 - y1);

                            $("#previewimage").imgAreaSelect({remove: true});
                            $("#previewimage").imgAreaSelect({
                                x1: x1, y1: y1, x2: x2, y2: y2,
                                aspectRatio: '1:1',
                                handles: true,
                                imageHeight: this.naturalHeight,
                                imageWidth: this.naturalWidth,
                                onSelectEnd: function (img, selection) {
                                    $('input[name="project_photo_x1"]').val(selection.x1);
                                    $('input[name="project_photo_y1"]').val(selection.y1);
                                    $('input[name="project_photo_w"]').val(selection.width);
                                    $('input[name="project_photo_h"]').val(selection.height);
                                }
                            });
                        });
                    };
                });

                $("#selecionar_foto").on('click', function () {
                    $("#foto_projeto").click();
                });

                $("#frete_projeto").on('change', function () {
                    if ($(this).val() == 0) {
                        $("#div_frete_fixo_projeto").hide();
                        $("#div_valor_frete_fixo_projeto").hide();
                        $("#div_transportadora_projeto").hide();
                        $("#div_responsavel_frete_projeto").hide();
                        $("#div_id_plano_transportadora_projeto").hide();
                    } else {
                        $("#div_frete_fixo_projeto").show();
                        if ($("#div_frete_fixo_projeto").val() == '1') {
                            $("#div_valor_frete_fixo_projeto").show();
                        }
                        $("#div_transportadora_projeto").show();
                        $("#div_responsavel_frete_projeto").show();
                        if ($("#transportadora_projeto").val() != '2') {
                            $("#div_id_plano_transportadora_projeto_projeto").show();
                        }
                    }
                });

                //////// frete fixo
                $("#frete_fixo_projeto").on('change', function () {
                    if ($(this).val() == '1') {
                        $("#div_valor_frete_fixo_projeto").show();
                    } else {
                        $("#div_valor_frete_fixo_projeto").hide();
                    }
                });

                ///////// tipo de envio /////////////
                $("#shipping_type").on("change", function () {
                    if ($(this).val() == 'static') {
                        $("#zip_code_origin_shipping_row").css('display', 'none');
                        $("#value_shipping_row").css('display', 'block');
                    } else {
                        $("#zip_code_origin_shipping_row").css('display', 'block');
                        $("#value_shipping_row").css('display', 'none');
                    }
                });

                $("#shipping_zip_code_origin").mask("0#");
                $("#shipping_value").mask('#.###,#0', {reverse: true});

                //// click Botão salvar form tipos de frete no projeto
                $("#bt_add_shipping").unbind("click");
                $("#bt_add_shipping").on("click", function () {
                    if ($("#shipping_type").val() === '' || $("#shipping_name").val() === '' || $("#shipping_information").val() === '') {
                        alertCustom('error', 'Dados informados inválidos');
                        return false;
                    }
                    if (($("#shipping_type").val() === 'static' && $("#shipping_value").val() === '') || ($("#shipping_type").val() !== 'static' && $("#shipping_zip_code_origin").val() === '')) {
                        alertCustom('error', 'Dados informados inválidos');
                        return false;
                    }

                    $(".loading").css("visibility", "visible");

                    ///// tipos de frete no projeto
                    var form_data = new FormData(document.getElementById("form_add_shipping"));
                    form_data.append('projeto', projectId);

                    $.ajax({
                        method: "POST",
                        url: "/shipping/store",
                        data: form_data,
                        processData: false,
                        contentType: false,
                        cache: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            $(".loading").css("visivbility", "hidden");
                            alertCustom("error", "Ocorreu algum erro");
                        },
                        success: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom('success', 'Frete cadastrado com sucesso!');
                            updateConfiguracoes();
                        }
                    });
                });

                ////// Botão Editar da tela Project/edit
                $(".edit_shipping").unbind("click");
                $(".edit_shipping").on('click', function () {
                    var shipping_id = $(this).closest('tr').find('.shipping_id').html();
                    var shipping_type = $(this).closest('tr').find('.shipping_type').html();
                    var shipping_name = $(this).closest('tr').find('.shipping_name').html();
                    var shipping_information = $(this).closest('tr').find('.shipping_information').html();
                    var shipping_value = $(this).closest('tr').find('.shipping_value').html();
                    var shipping_zip_code_origin = $(this).closest('tr').find('.shipping_zip_code_origin').html();
                    var shipping_status = $(this).closest('tr').find('.shipping_status').html();
                    var shipping_pre_selected = $(this).closest('tr').find('.shipping_pre_selected').html();

                    if (shipping_type == 'static') {
                        $("#zip_code_origin_shipping_row_edit").css('display', 'none');
                        $("#value_shipping_row_edit").css('display', 'block');
                    } else {
                        $("#zip_code_origin_shipping_row_edit").css('display', 'block');
                        $("#value_shipping_row_edit").css('display', 'none');
                    }

                    $("#shipping_type_edit").val(shipping_type);
                    $("#shipping_name_edit").val(shipping_name);
                    $("#shipping_information_edit").val(shipping_information);
                    $("#shipping_value_edit").val(shipping_value);
                    $("#shipping_zip_code_origin_edit").val(shipping_zip_code_origin);

                    ///////// verifico status da remessa
                    if (shipping_status == 'Ativado')
                        $("#shipping_status_edit").val('1');
                    else
                        $("#shipping_status_edit").val('0');

                    ///// verifico envio pre selecionado
                    if (shipping_pre_selected == 'Sim')
                        $("#shipping_pre_selected_edit").val('1');
                    else
                        $("#shipping_pre_selected_edit").val('0');


                    // botão salvar tela project/project
                    $("#bt_update_shipping").unbind("click");
                    $("#bt_update_shipping").on("click", function () {

                        if ($("#shipping_type_edit").val() == '' || $("#shipping_name_edit").val() == '' || $("#shipping_information_edit").val() == '') {
                            alertPersonalizado('error', 'dados informados inválidos');
                            return false;
                        }
                        if (($("#shipping_type_edit").val() == 'static' && $("#shipping_value_edit").val() == '') || ($("#shipping_type_edit").val() != 'static' && $("#shipping_zip_code_origin_edit").val() == '')) {
                            alertPersonalizado('error', 'dados informados inválidos');
                            return false;
                        }

                        $('.loading').css("visibility", "visible");

                        ///// form atualizar tela project/project
                        var form_data = new FormData(document.getElementById('form_update_shipping'));
                        form_data.append('id', shipping_id);

                        $.ajax({
                            method: "POST",
                            url: "/shipping/update",
                            data: form_data,
                            processData: false,
                            contentType: false,
                            cache: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function () {
                                $('.loading').css("visibility", "hidden");
                                alertPersonalizado('error', 'Ocorreu algum erro');
                            },
                            success: function (data) {
                                $('.loading').css("visibility", "hidden");
                                alertPersonalizado('success', 'Frete cadastrado com sucesso!');
                                updateConfiguracoes();
                            },
                        });

                    });
                });

                /// Botão deletar tela project/edit
                $('.delete_shipping').unbind('click');
                $('.delete_shipping').on('click', function () {

                    var shipping_id = $(this).closest('tr').find('.shipping_id').html();

                    $('#modal_excluir_titulo').html('Remover frete do projeto ?');

                    $('#bt_excluir').unbind('click');
                    $('#bt_excluir').on('click', function () {

                        $('.loading').css("visibility", "visible");
                        $('#fechar_modal_excluir').click();

                        $.ajax({
                            method: "POST",
                            url: "/shipping/delete",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {id: shipping_id},
                            error: function () {
                                $('.loading').css("visibility", "hidden");
                                alertPersonalizado('error', 'Ocorreu algum erro');
                            },
                            success: function (data) {
                                $('.loading').css("visibility", "hidden");
                                updateConfiguracoes();
                            }
                        });
                    });
                });

                $("#transportadora_projeto").on("change", function () {
                    $("#responsavel_frete_projeto option[value='Kapsula']").remove();
                    $("#responsavel_frete_projeto option[value='Lift Gold']").remove();
                    if ($(this).val() != '2') {
                        $("#div_id_plano_transportadora_projeto").show();
                        $("#responsavel_frete_projeto").append(new Option($(this).find("option:selected").text(), $(this).find("option:selected").text()));
                    } else {
                        $("#div_id_plano_transportadora_projeto").hide();
                    }
                });

                $('#bt_atualizar_configuracoes').on('click', function () {

                    $('.loading').css("visibility", "visible");

                    var form_data = new FormData(document.getElementById('atualizar_configuracoes'));
                    form_data.append('projeto', $("#project_id").val());

                    $.ajax({
                        method: "POST",
                        url: "/projects/" + $("#project_id").val(),
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: form_data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('error', 'Ocorreu algum erro');
                            $('#previewimage').imgAreaSelect({remove: true});
                        },
                        success: function (data) {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('success', 'Dados do projeto alterados!');
                            $('#previewimage').imgAreaSelect({remove: true});
                            updateConfiguracoes();
                        },
                    });

                });

                $('#bt_deletar_projeto').on('click', function () {

                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Excluir o projeto ?');

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function () {

                        $('.loading').css("visibility", "visible");

                        $.ajax({
                            method: "POST",
                            url: "/projetos/deletarprojeto",
                            data: {projeto: $("#project_id").val()},
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function () {
                                $('.loading').css("visibility", "hidden");
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error', 'Ocorreu algum erro');
                            },
                            success: function (data) {
                                $('.loading').css("visibility", "hidden");
                                if (data != 'sucesso') {
                                    alertPersonalizado('error', data);
                                } else {
                                    window.location = "/projetos";
                                }
                            }
                        });

                    });
                });

                /// Excluir material
                $(".excluir_material_extra").on("click", function () {

                    $('.loading').css("visibility", "visible");
                    var idMaterialExtra = $(this).attr('material-extra');

                    $.ajax({
                        method: "POST",
                        url: "/projects/deletematerialextra",
                        data: {idMaterialExtra: idMaterialExtra},
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('error', 'Ocorreu algum erro');
                        },
                        success: function (data) {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('success', 'Material extra removido!');
                            updateConfiguracoes();
                        }
                    });

                });

            }
        });
    }

    updateConfiguracoes();

});
