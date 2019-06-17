$(function () {
    var projectId = $("#project-id").val();

    $("#adicionar_cupom").on('click', function () {
        $("#modal_add_tamanho").addClass("modal-simple");
        $("#modal_add_tamanho").addClass("modal-lg");

        $("#modal_add_body").html("<div style='text-align'>Carregando...</div>");

        $.ajax({
            method: "GET",
            url: '/couponsdiscounts/create',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                $("#modal_add").hide();
                alertCustom('error', 'Ocorreu algum erro');
            }, success: function (data) {
                $("#modal_add_body").html(data);
                $("#valor_cupom_cadastrar").mask("0#");
                $("#cadastrar").unbind('click');
                $("#cadastrar").on('click', function () {
                    if ($("#nome_cupom").val() === '' || $("#tipo_cupom").val() === '' || $("#valor_cupom_cadastrar").val() === ''
                        || $("#code").val() === '' || $("#status_cupom").val() === '') {
                        alertCustom('error', 'Dados informados inválidos');
                        return false;
                    }

                    $(".loading").css('visibility', 'visible');

                    var formData = new FormData(document.getElementById('cadastrar_cupom'));
                    formData.append("project", projectId);

                    $.ajax({
                        method: "POST",
                        url: '/couponsdiscounts',
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom('error', 'Ocorreu algum erro ao tentar salvar dados');
                        },
                        success: function (data) {
                            $('.loading').css('visibility', 'hidden');
                            alertCustom("success", "Cupom de desconto adicionado com sucesso!");
                            $("#modal_add").hide();
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                        },
                    });
                });
            }
        });

    });

    $("#tabela_cuponsdesconto").DataTable({
        bLengthChange: false,
        ordering: false,
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: '/couponsdiscounts',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            data: {projeto: projectId}
        },
        columns: [
            {data: 'name', name: 'name'},
            {data: 'type', name: 'type'},
            {data: 'value', name: 'value'},
            {data: 'code', name: 'code'},
            {data: 'status', name: 'status'},
            {data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
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
            "sInfoThousands": ",",
            "sLoadingRecords": "Carregando...",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sLast": "Último",
                "sNext": "Próximo",
                "sPrevious": "Anterior",
            },
        },
        "drawCallback": function () {
            $(".detalhes_cupom").on("click", function () {
                var cupom = $(this).attr("cupom");
                $("#modal_detalhes_titulo").html("Detalhes do cupom");
                $("#modal_detalhes_body").html("<h5 style='width::100%; text-align: center;'>Carregando</h5>");
                $.ajax({
                    method: "GET",
                    url: "/couponsdiscounts/" + cupom,
                    data: {id_cupom: cupom},
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
            });

            var id_cupom = '';

            $(".excluir_cupom").on("click", function () {
                id_cupom = $(this).attr('cupom');
                var name = $(this).closest("tr").find("td:first-child").text();
                $("#modal_excluir_titulo").html("Remover do projeto o cupom " + name + " ?");

                $("#bt_excluir").unbind('click');
                $("#bt_excluir").on('click', function () {
                    $(".loading").css("visibility", "visible");
                    $("#fechar_modal_excluir").click();

                    $.ajax({
                        method: "DELETE",
                        url: "/couponsdiscounts/" + id_cupom,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id: id_cupom},
                        error: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom('error', 'Ocorreu algum erro!');
                        },
                        success: function (data) {
                            $(".loading").css("visibility", "hidden");
                            alertCustom('success', 'Cupom removido');
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                        }
                    });
                });
            });

            $(".editar_cupom").on('click', function () {
                $("#modal_editar_tipo").addClass('modal-simple');
                $("#modal_editar_tipó").removeClass('modal-lg');

                id_cupom = $(this).attr('cupom');

                $("#modal_editar_body").html("<div style='text-align:center'>Carregando</div>");
                $.ajax({
                    method: "GET",
                    url: "/couponsdiscounts/" + id_cupom + "/edit",
                    data: {idCupom: id_cupom},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function () {
                        $("#modal_editar").hide();
                        alertCustom('error', 'Ocorreu algum erro');
                    },
                    success: function (data) {
                        $("#modal_editar_body").html(data);
                        $("#valor_cupom_editar").mask("0#");
                        $("#editar").unbind('click');
                        $("#editar").on('click', function () {
                            $(".loading").css("visibility", "visible");
                            /*  var formData = new FormData(document.getElementById("editar_cupom"));
                              formData.append("projeto", projectId);*/
                            var paramObj = {};
                            $.each($('#editar_cupom').serializeArray(), function (_, kv) {
                                if (paramObj.hasOwnProperty(kv.name)) {
                                    paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
                                    paramObj[kv.name].push(kv.value);
                                } else {
                                    paramObj[kv.name] = kv.value;
                                }
                            });
                            paramObj['id'] = id_cupom;

                            $.ajax({
                                method: "PUT",
                                url: "/couponsdiscounts/" + id_cupom,
                                /* processData: false,
                                 contentType: false,
                                 cache: false,*/
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {coupomData: paramObj},
                                error: function () {
                                    $(".loading").css("visibility", 'hidden');
                                    alertCustom("error", "Ocorreu algum error");
                                }, success: function (data) {
                                    $(".loading").css("visibility", 'hidden');
                                    alertCustom('success', 'Cupom atualizado com sucesso');
                                    $("#modal.add").hide();
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
