$('#adicionar_dominio').on('click', function () {

    $('#modal_add_tamanho').addClass('modal-lg');
    $('#modal_add_tamanho').removeClass('modal-simple');

    $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

    $.ajax({
        method: "POST",
        url: "/dominios/getformadddominio",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {projeto: id_projeto},
        error: function () {
            alertPersonalizado('error', 'Ocorreu algum erro');
        },
        success: function (data) {
            $('#modal_add_body').html(data);

            $('#ip_dominio_cadastrar').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                translation: {
                    'Z': {
                        pattern: /[0-9]/,
                        optional: true
                    }
                }
            });

            $('#cadastrar').unbind('click');

            $('#cadastrar').on('click', function () {

                if ($('#dominio').val() == '') {
                    alertPersonalizado('error', 'Dados informados inválidos');
                    return false;
                }

                if ($('#ip_dominio').val() == '') {
                    alertPersonalizado('error', 'Dados informados inválidos');
                    return false;
                }

                $('.loading').css("visibility", "visible");

                var form_data = new FormData(document.getElementById('cadastrar_dominio'));
                form_data.append('projeto', id_projeto);

                $.ajax({
                    method: "POST",
                    url: "/dominios/cadastrardominio",
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
                    },
                    success: function (data) {
                        if (data != 'sucesso') {
                            alertPersonalizado('error', data);
                        } else {
                            alertPersonalizado('success', 'Domínio adicionado!');
                        }
                        $('.loading').css("visibility", "hidden");
                        $('#modal_add').hide();
                        $($.fn.dataTable.tables(true)).css('width', '100%');
                        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                    },
                });
            });
        }
    });

});


$("#tabela_dominios").DataTable({
    bLengthChange: false,
    ordering: false,
    processing: true,
    responsive: true,
    serverSide: true,
    ajax: {
        url: '/dominios/data-source',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        data: {projeto: id_projeto}
    },
    columns: [
        {data: 'name', name: 'name'},
        {data: 'domain_ip', name: 'domain_ip'},
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

        var id_dominio = '';

        $("#excluir_dominio").unbind("click");
        $('.excluir_dominio').on('click', function () {

            id_dominio = $(this).attr('dominio');
            var name = $(this).closest("tr").find("td:first-child").text();
            $('#modal_excluir_titulo').html('Remover do projeto o dominio ' + name + '?');

            $('#bt_excluir').unbind('click');

            $('#bt_excluir').on('click', function () {

                $('.loading').css("visibility", "visible");

                $.ajax({
                    method: "POST",
                    url: "/dominios/deletardominio",
                    data: {id: id_dominio, projeto: id_projeto},
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
                        }
                        alertPersonalizado('success', 'Domínio removido!');
                        $('#fechar_modal_excluir').click();
                        $($.fn.dataTable.tables(true)).css('width', '100%');
                        $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                    }
                });

            });

        });

        $('#editar').unbind('click');

        $('.editar_dominio').on('click', function () {

            $('#modal_editar_tipo').addClass('modal-lg');
            $('#modal_editar_tipo').removeClass('modal-simple');

            id_dominio = $(this).attr('dominio');

            $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

            $.ajax({
                method: "POST",
                url: "/dominios/getformeditardominio",
                data: {id: id_dominio, projeto: id_projeto},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function () {
                    alertPersonalizado('error', 'Ocorreu algum erro');
                },
                success: function (data) {
                    $('#modal_editar_body').html(data);

                    var qtd_novos_registros = 1;

                    $('#ip_dominio_editar').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                        translation: {
                            'Z': {
                                pattern: /[0-9]/,
                                optional: true
                            }
                        }
                    });

                    $("#bt_adicionar_entrada").on("click", function () {

                        $("#novos_registros").after("<tr registro='" + qtd_novos_registros + "'><td>" + $("#tipo_registro").val() + "</td><td>" + $("#nome_registro").val() + "</td><td>" + $("#valor_registro").val() + "</td><td><button type='button' class='btn btn-danger remover_entrada'>Remover</button></td></tr>");

                        $('#editar_dominio').append('<input type="hidden" name="tipo_registro_' + qtd_novos_registros + '" id="tipo_registro_' + qtd_novos_registros + '" value="' + $("#tipo_registro").val() + '" />');
                        $('#editar_dominio').append('<input type="hidden" name="nome_registro_' + qtd_novos_registros + '" id="nome_registro_' + qtd_novos_registros + '" value="' + $("#nome_registro").val() + '" />');
                        $('#editar_dominio').append('<input type="hidden" name="valor_registro_' + qtd_novos_registros + '" id="valor_registro_' + (qtd_novos_registros++) + '" value="' + $("#valor_registro").val() + '" />');

                        $(".remover_entrada").unbind("click");

                        $(".remover_entrada").on("click", function () {

                            var novo_registro = $(this).parent().parent();
                            var id_registro = novo_registro.attr('registro');
                            novo_registro.remove();
                            alert(id_registro);
                            $("#tipo_registro_" + id_registro).remove();
                            $("#nome_registro_" + id_registro).remove();
                            $("#valor_registro_" + id_registro).remove();
                        });

                        $("#tipo_registro").val("A");
                        $("#nome_registro").val("");
                        $("#valor_registro").val("");
                    });

                    $(".remover_registro").on("click", function () {

                        var id_registro = $(this).attr('id-registro');

                        var row = $(this).parent().parent();

                        $.ajax({
                            method: "POST",
                            url: "/dominios/removerregistrodns",
                            data: {
                                id_registro: id_registro,
                                id_dominio: $("#id_dominio").val()
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function () {
                                alert('error', 'Ocorreu algum erro');
                            },
                            success: function (data) {
                                if (data == 'sucesso') {
                                    row.remove();
                                    alertPersonalizado('success', 'Registro removido!');
                                } else {
                                    alertPersonalizado('error', data);
                                }
                            },
                        });

                    });

                    $('#editar').unbind('click');

                    $('#editar').on('click', function () {

                        $('.loading').css("visibility", "visible");

                        var form_data = new FormData(document.getElementById('editar_dominio'));
                        form_data.append('projeto', id_projeto);

                        $.ajax({
                            method: "POST",
                            url: "/dominios/editardominio",
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
                            },
                            success: function (data) {
                                $('.loading').css("visibility", "hidden");
                                if (data == 'sucesso')
                                    alertPersonalizado('success', 'Domínio atualizado!');
                                else
                                    alertPersonalizado('error', data);

                                $('#modal_add').hide();
                                $($.fn.dataTable.tables(true)).css('width', '100%');
                                $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                            },
                        });
                    });
                }
            });
        });

        $('.detalhes_dominio').unbind('click');

        $('.detalhes_dominio').on('click', function () {
            var id_dominio = $(this).attr('dominio');

            $('#modal_detalhes_titulo').html('Detalhes do domínio');
            $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
            $.ajax({
                method: "POST",
                url: "/dominios/detalhesdominio",
                data: {dominio: id_dominio},
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function () {
                    alertPersonalizado('error', 'Ocorreu algum erro');
                },
                success: function (response) {
                    $('#modal_detalhes_body').html(response);
                }
            });
        });

    }

});


