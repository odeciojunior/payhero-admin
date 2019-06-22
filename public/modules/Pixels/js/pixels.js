$(function () {

    var projectId = $("#project-id").val();

    $('#tab_pixels').on('click', function () {
        console.log('oi');
        atualizarPixel();
    });
    atualizarPixel();
    //criar novo pixel
    $("#add-pixel").on('click', function () {

        $("#modal_add_size").addClass('modal_simples');
        $("#modal_add_size").removeClass('modal-lg');

        $("#modal-add-body").html("<div style='text-align:center;'>Carregando...</div>");

        $.ajax({
            method: "GET",
            url: "/pixels/create",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal-content").hide();
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function (data) {
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").text('Salvar');
                $("#btn-modal").show();
                $('#modal-add-body').html(data);

                $(".btn-save").unbind('click');
                $(".btn-save").on('click', function () {

                    var formData = new FormData(document.getElementById('form-register-pixel'));
                    formData.append('project', projectId);

                    $.ajax({
                        method: "POST",
                        url: "/pixels",
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
                            alertCustom('error', 'Ocorreu algum erro');
                        }, success: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom("success", "Pixel Adicionado!");
                            $("#modal_add_produto").hide();
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                        }
                    });
                });
            }
        });

    });
    function atualizarPixel() {
        $("#data-table-pixel").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");
        $.ajax({
            method: "GET",
            url: "/pixels",
            data: {project: projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#data-table-pixel").html('Erro ao encontrar dados');
            },
            success: function (response) {
                $("#data-table-pixel").html('');
                $.each(response.data, function (index, value) {
                    data = '';
                    data += '<tr>';
                    data += '<td class="shipping-id text-center" style="vertical-align: middle;">' + value.name + '</td>';
                    data += '<td class="shipping-type text-center" style="vertical-align: middle;">' + value.code + '</td>';
                    data += '<td class="shipping-value text-center" style="vertical-align: middle;">' + value.platform + '</td>';
                    data += '<td class="shipping-status text-center" style="vertical-align: middle;">';
                    if (value.status == 1) {
                        data += '<span class="badge badge-success">Ativo</span>';
                    } else {
                        data += '<span class="badge badge-danger">Desativado</span>';
                    }
                    data += '</td>';

                    data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger details-pixel'  frete='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger edit-pixel'  frete='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger delete-pixel'  frete='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";
                    data += '</tr>';
                    $("#data-table-pixel").append(data);
                });
            }
        });
    }
    /// tabela de pixels
    $("#tabela_pixels").DataTable({
        bLengthChange: false,
        ordering: false,
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: '/pixels',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            data: {
                projeto: projectId,
            },
        },
        columns: [
            {data: 'name', name: 'name'},
            {data: 'code', name: 'code'},
            {data: 'platform', name: 'platform'},
            {
                data: function (data) {
                    if (data.status == 1) {
                        return 'Ativo';
                    } else {
                        return 'Inativo';
                    }
                }, name: 'status'
            },
            {
                data: 'detalhes',
                name: 'detalhes',
                orderable: false,
                searchable: false
            },
        ],
        "language": {
            "sProcessing": "Carregando...",
            "lengthMenu": "Apresentando _MENU_ registros por página",
            "zeroRecords": "Nenhum registro encontrado",
            "info": "Apresentando página _PAGE_ de _PAGES_",
            "infoEmpty": "Nenhum registro encontrado",
            "infoFiltered": "(filtrado por _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Procurar :",
            "sUrl": "",
            "sLoadingRecords": "Carregando...",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sLast": "Último",
                "sNext": "Próximo",
                "sPrevious": "Anterior",
            },
        },
        "drawCallback": function () {
            $(".detalhes_pixel").on('click', function () {
                var pixel = $(this).attr('pixel');
                $('#modal_detalhes_titulo').html('Detalhes do Pixel');
                $("#modal_detalhes_body").html("<h5 style='width: 100%; text-align: center;'>Carregando...</h5>");
                $.ajax({
                    method: "GET",
                    url: "/pixels/" + pixel,
                    data: {pixelId: pixel},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function () {
                        alertCustom('error', 'Ocorreu algum erro');
                    },
                    success: function (response) {
                        $("#modal_detalhes_body").html(response);
                    }
                });

                /*$.get("/pixels/", data).then(function () {
                    $("#modal_detalhes_body").html(response);
                });*/
            });

            //// Excluir pixel
            var id_pixel = '';
            $(".excluir_pixel").on('click', function () {
                id_pixel = $(this).attr('pixel');
                var name = $(this).closest("tr").find("td:first-child").text();
                $("#modal_excluir_titulo").html("Remover do projeto o pixel " + name + "?");

                $("#bt_excluir").unbind('click');
                $("#bt_excluir").on('click', function () {
                    $(".loading").css('visibility', 'visible');
                    $("#fechar_modal_excluir").click();

                    $.ajax({
                        method: "DELETE",
                        url: "/pixels/" + id_pixel,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            $(".loading").css('visibility', 'hidden');
                            alertCustom('error', 'Ocorreu algum erro');
                        },
                        success: function () {
                            $(".loading").css('visibility', 'hidden');
                            alertCustom('success', 'Pixel Removido com sucesso!');
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                        }
                    });
                });
            });

            /// Editar pixel
            $(".editar_pixel").on('click', function () {
                $("#modal_editar_tipo").addClass('modal-simple');
                $("#modal_editar_tipo").removeClass('modal-lg');

                id_pixel = $(this).attr('pixel');

                $("#modal_editar_body").html("<div style='text-align:center'>Carregango...</div>");

                $.ajax({
                    method: "GET",
                    url: "/pixels/" + id_pixel + "/edit",
                    data: {id: id_pixel},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function () {
                        alertPersonalizado('error', 'Ocorreu algum erro');
                    },
                    success: function (data) {
                        $('#modal_editar_body').html(data);

                        $('#editar').unbind('click');

                        $('#editar').on('click', function () {

                            $('.loading').css("visibility", "visible");

                            var paramObj = {};
                            $.each($('#editar_pixel').serializeArray(), function (_, kv) {
                                if (paramObj.hasOwnProperty(kv.name)) {
                                    paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
                                    paramObj[kv.name].push(kv.value);
                                } else {
                                    paramObj[kv.name] = kv.value;
                                }
                            });
                            paramObj['id'] = id_pixel;

                            $.ajax({
                                method: "PUT",
                                url: "/pixels/" + id_pixel,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {pixelData: paramObj},
                                error: function () {
                                    $('.loading').css("visibility", "hidden");
                                    alertCustom('error', 'Ocorreu algum erro');
                                },
                                success: function (data) {
                                    $('.loading').css("visibility", "hidden");
                                    alertPersonalizado('success', 'Pixel atualizado!');
                                    $($.fn.dataTable.tables(true)).css('width', '100%');
                                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                }
                            });

                        });

                    }
                });
            });

        }
    });

});
