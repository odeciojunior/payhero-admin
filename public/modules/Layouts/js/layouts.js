$(function () {

    $('#adicionar_layout').on('click', function () {

        $('#modal_add_tamanho').addClass('modal-lg');
        $('#modal_add_tamanho').removeClass('modal-simple');

        $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

        $.ajax({
            method: "POST",
            url: "/layouts/getformaddlayout",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                $('#modal_add').hide();
                alertPersonalizado('error', 'Ocorreu algum erro');
            },
            success: function success(data) {

                $('#modal_add_body').html(data);

                atualizarPreView();

                function atualizarPreView() {

                    $('#form-preview').submit();
                }

                $("#atualizar_preview_cadastro").on("click", function () {
                    atualizarPreView();
                });

                $('#cadastrar').unbind('click');

                $('#cadastrar').on('click', function () {

                    if ($('#descricao').val() == '' || $('#logo').val() == '') {
                        alertPersonalizado('error', 'Dados informados inválidos');
                        return false;
                    }

                    $('.loading').css("visibility", "visible");

                    var form_data = new FormData(document.getElementById('cadastrar_layout'));
                    form_data.append('projeto', id_projeto);

                    $.ajax({
                        method: "POST",
                        url: "/layouts/cadastrarlayout",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: form_data,
                        error: function error() {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('error', 'Ocorreu algum erro');
                            $('#previewimage_checkout_cadastrar').imgAreaSelect({ remove: true });
                        },
                        success: function success(data) {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('success', 'Layout adicionado!');
                            $('#modal_add').hide();
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                            $('#previewimage_checkout_cadastrar').imgAreaSelect({ remove: true });
                        }
                    });
                });

                $("#formato_logo_cadastrar").on("change", function () {
                    $("#foto_checkout").val('');
                    $('#previewimage_checkout_cadastrar').imgAreaSelect({ remove: true });
                    $('#previewimage_checkout_cadastrar').attr('src', '#');
                    $("#preview_logo_formato").val($(this).val());
                });

                var p = $("#previewimage_checkout_cadastrar");
                $("#foto_checkout").on("change", function () {

                    var input = $(this).clone();
                    $('#form-preview').append(input);

                    var imageReader = new FileReader();
                    imageReader.readAsDataURL(document.getElementById("foto_checkout").files[0]);

                    imageReader.onload = function (oFREvent) {
                        p.attr('src', oFREvent.target.result).fadeIn();

                        p.on('load', function () {

                            var img = document.getElementById('previewimage_checkout_cadastrar');
                            var x1, x2, y1, y2;

                            if ($("#formato_logo_cadastrar").val() == 'quadrado') {
                                if (img.naturalWidth > img.naturalHeight) {
                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                    x2 = x1 + (y2 - y1);
                                } else {
                                    if (img.naturalWidth < img.naturalHeight) {
                                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                                        ;
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
                            } else {
                                if (img.naturalWidth > img.naturalHeight) {
                                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor(y2 - y1);
                                    if (x1 < 0) x1 = 2;
                                    x2 = x1 + (y2 - y1) * 2;
                                    if (x2 > img.naturalWidth) {
                                        x2 = img.naturalWidth - 2;
                                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 4);
                                        y2 = y1 + Math.floor((x2 - x1) / 2);
                                    }
                                } else {
                                    x1 = 2;
                                    x2 = img.naturalWidth - 2;
                                    y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 4);
                                    y2 = y1 + Math.floor((x2 - x1) / 2);
                                }
                            }

                            $('input[name="foto_checkout_cadastrar_x1"]').val(x1);
                            $('input[name="foto_checkout_cadastrar_y1"]').val(y1);
                            $('input[name="foto_checkout_cadastrar_w"]').val(x2 - x1);
                            $('input[name="foto_checkout_cadastrar_h"]').val(y2 - y1);
                            $('input[name="preview_logo_x1"]').val(x1);
                            $('input[name="preview_logo_y1"]').val(y1);
                            $('input[name="preview_logo_w"]').val(x2 - x1);
                            $('input[name="preview_logo_h"]').val(y2 - y1);

                            var formato = '';
                            if ($("#formato_logo_cadastrar").val() == 'quadrado') {
                                formato = '1:1';
                            } else {
                                formato = '2:1';
                            }

                            $('#modal_editar').on('hidden.bs.modal', function () {
                                $('#previewimage_checkout_cadastrar').imgAreaSelect({ remove: true });
                            });
                            $('#previewimage_checkout_cadastrar').imgAreaSelect({ remove: true });

                            $('#previewimage_checkout_cadastrar').imgAreaSelect({
                                x1: x1, y1: y1, x2: x2, y2: y2,
                                aspectRatio: formato,
                                handles: true,
                                imageHeight: this.naturalHeight,
                                imageWidth: this.naturalWidth,
                                onSelectEnd: function onSelectEnd(img, selection) {
                                    $('input[name="foto_checkout_cadastrar_x1"]').val(selection.x1);
                                    $('input[name="foto_checkout_cadastrar_y1"]').val(selection.y1);
                                    $('input[name="foto_checkout_cadastrar_w"]').val(selection.width);
                                    $('input[name="foto_checkout_cadastrar_h"]').val(selection.height);
                                    $('input[name="preview_logo_x1"]').val(selection.x1);
                                    $('input[name="preview_logo_y1"]').val(selection.y1);
                                    $('input[name="preview_logo_w"]').val(selection.width);
                                    $('input[name="preview_logo_h"]').val(selection.height);
                                },
                                parent: $('#conteudo_modal_add')
                            });
                        });
                    };
                });

                $("#selecionar_foto_checkout_cadastrar").on("click", function () {
                    $("#foto_checkout").click();
                });
            }
        });
    });

    $("#tabela_layouts").DataTable({
        bLengthChange: false,
        ordering: false,
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '/layouts/data-source',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            data: { projeto: id_projeto }
        },
        columns: [{ data: 'description', name: 'description' }, { data: 'status', name: 'status' }, { data: 'detalhes', name: 'detalhes', orderable: false, searchable: false }],
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
                "sPrevious": "Anterior"
            }
        },
        "drawCallback": function drawCallback() {

            $('.editar_layout').on('click', function () {

                $('#modal_editar_tipo').addClass('modal-lg');
                $('#modal_editar_tipo').removeClass('modal-simple');

                id_layout = $(this).attr('layout');

                $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                $.ajax({
                    method: "POST",
                    url: "/layouts/getformeditarlayout",
                    data: { id: id_layout, projeto: id_projeto },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    error: function error() {
                        $('#modal_editar').hide();
                        alertPersonalizado('error', 'Ocorreu algum erro');
                    },
                    success: function success(data) {
                        $('#modal_editar_body').html(data);

                        atualizarPreView();

                        function atualizarPreView() {

                            $('#form_preview_editar').submit();
                        }

                        $("#formato_logo_editar").on("change", function () {
                            $("#foto_checkout").val('');
                            $('#previewimage_checkout_editar').imgAreaSelect({ remove: true });
                            $('#previewimage_checkout_editar').attr('src', '#');
                            $("#preview_logo_formato").val($(this).val());
                        });

                        var p = $("#previewimage_checkout_editar");
                        $("#foto_checkout").on("change", function () {

                            var input = $(this).clone();
                            $('#form_preview_editar').append(input);

                            var imageReader = new FileReader();
                            imageReader.readAsDataURL(document.getElementById("foto_checkout").files[0]);

                            imageReader.onload = function (oFREvent) {
                                p.attr('src', oFREvent.target.result).fadeIn();

                                p.on('load', function () {

                                    var img = document.getElementById('previewimage_checkout_editar');
                                    var x1, x2, y1, y2;

                                    if ($("#formato_logo_editar").val() == 'quadrado') {
                                        if (img.naturalWidth > img.naturalHeight) {
                                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                            x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                                            x2 = x1 + (y2 - y1);
                                        } else {
                                            if (img.naturalWidth < img.naturalHeight) {
                                                x1 = Math.floor(img.naturalWidth / 100 * 10);
                                                ;
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
                                    } else {
                                        if (img.naturalWidth > img.naturalHeight) {
                                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                                            x1 = Math.floor(img.naturalWidth / 2) - Math.floor(y2 - y1);
                                            if (x1 < 0) x1 = 2;
                                            x2 = x1 + (y2 - y1) * 2;
                                            if (x2 > img.naturalWidth) {
                                                x2 = img.naturalWidth - 2;
                                                y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 4);
                                                y2 = y1 + Math.floor((x2 - x1) / 2);
                                            }
                                        } else {
                                            x1 = 2;
                                            x2 = img.naturalWidth - 2;
                                            y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 4);
                                            y2 = y1 + Math.floor((x2 - x1) / 2);
                                        }
                                    }

                                    $('input[name="foto_checkout_editar_x1"]').val(x1);
                                    $('input[name="foto_checkout_editar_y1"]').val(y1);
                                    $('input[name="foto_checkout_editar_w"]').val(x2 - x1);
                                    $('input[name="foto_checkout_editar_h"]').val(y2 - y1);
                                    $('input[name="preview_logo_x1"]').val(x1);
                                    $('input[name="preview_logo_y1"]').val(y1);
                                    $('input[name="preview_logo_w"]').val(x2 - x1);
                                    $('input[name="preview_logo_h"]').val(y2 - y1);

                                    var formato = '';
                                    if ($("#formato_logo_editar").val() == 'quadrado') {
                                        formato = '1:1';
                                    } else {
                                        formato = '2:1';
                                    }

                                    $('#previewimage_checkout_editar').imgAreaSelect({ remove: true });
                                    $('#modal_editar').on('hidden.bs.modal', function () {
                                        $('#previewimage_checkout_editar').imgAreaSelect({ remove: true });
                                    });
                                    $('#previewimage_checkout_editar').imgAreaSelect({
                                        x1: x1, y1: y1, x2: x2, y2: y2,
                                        aspectRatio: formato,
                                        handles: true,
                                        imageHeight: this.naturalHeight,
                                        imageWidth: this.naturalWidth,
                                        onSelectEnd: function onSelectEnd(img, selection) {
                                            $('input[name="foto_checkout_editar_x1"]').val(selection.x1);
                                            $('input[name="foto_checkout_editar_y1"]').val(selection.y1);
                                            $('input[name="foto_checkout_editar_w"]').val(selection.width);
                                            $('input[name="foto_checkout_editar_h"]').val(selection.height);
                                            $('input[name="preview_logo_x1"]').val(selection.x1);
                                            $('input[name="preview_logo_y1"]').val(selection.y1);
                                            $('input[name="preview_logo_w"]').val(selection.width);
                                            $('input[name="preview_logo_h"]').val(selection.height);
                                        },
                                        parent: $('#conteudo_modal_editar')
                                    });
                                });
                            };
                        });

                        $("#selecionar_foto_checkout_editar").on("click", function () {
                            $("#foto_checkout").click();
                        });

                        $("#atualizar_preview_editar").on("click", function () {
                            atualizarPreView();
                        });

                        $('#editar').unbind('click');

                        $('#editar').on('click', function () {

                            $('.loading').css("visibility", "visible");

                            var form_data = new FormData(document.getElementById('editar_layout'));
                            form_data.append('projeto', id_projeto);

                            $.ajax({
                                method: "POST",
                                url: "/layouts/editarlayout",
                                processData: false,
                                contentType: false,
                                cache: false,
                                data: form_data,
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                error: function error() {
                                    $('.loading').css("visibility", "hidden");
                                    alertPersonalizado('error', 'Ocorreu algum erro');
                                    $('#previewimage_checkout_editar').imgAreaSelect({ remove: true });
                                },
                                success: function success(data) {
                                    $('.loading').css("visibility", "hidden");
                                    alertPersonalizado('success', 'Layout atualizado!');
                                    $('#modal_add').hide();
                                    $($.fn.dataTable.tables(true)).css('width', '100%');
                                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                    $('#previewimage_checkout_editar').imgAreaSelect({ remove: true });
                                }
                            });
                        });
                    }
                });
            });

            $('.excluir_layout').on('click', function () {

                id_layout = $(this).attr('layout');

                $('#modal_excluir_titulo').html('Remover layout do projeto ?');

                $('#bt_excluir').unbind('click');

                $('#bt_excluir').on('click', function () {

                    $('.loading').css("visibility", "visible");
                    $('#fechar_modal_excluir').click();

                    $.ajax({
                        method: "POST",
                        url: "/layouts/removerlayout",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: { id: id_layout },
                        error: function error() {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('error', 'Ocorreu algum erro');
                        },
                        success: function success(data) {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('success', 'Layout removido!');
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                        }
                    });
                });
            });
        }

    });
});
