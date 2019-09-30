$(function () {
    $('#adicionar_brinde').on('click', function () {

        $('#modal_add_tamanho').addClass('modal-lg');
        $('#modal_add_tamanho').removeClass('modal-simple');

        $('#modal_add_body').html("<div style='text-align: center'>Carregando...</div>");

        $.ajax({
            method: "GET",
            url: "/brindes/getformaddbrinde",
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                errorAjaxResponse(response);
            },
            success: (data) => {
                $('#modal_add_body').html(data);

                $('#cadastrar').unbind('click');

                $('#cadastrar').on('click', function () {

                    if ($('#titulo_brinde').val() == '' || $('#descricao_brinde').val() == '' || $('#foto_brinde').val() == '' || $('#tipo_brinde').val() == '') {
                        alertPersonalizado('error', 'Dados informados inválidos');
                        return false;
                    }

                    $('.loading').css("visibility", "visible");

                    var form_data = new FormData(document.getElementById('cadastrar_brinde'));
                    form_data.append('projeto', id_projeto);

                    $.ajax({
                        method: "POST",
                        url: "/brindes/cadastrarbrinde",
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: form_data,
                        error: (response) => {
                            errorAjaxResponse(response);
                        },
                        success: (data) => {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('success', 'Brinde adicionado!');
                            $('#modal_add').hide();
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                            $('#previewimage_brinde_cadastrar').imgAreaSelect({remove: true});
                        }
                    });
                });

                var p = $("#previewimage_brinde_cadastrar");
                $("#foto_brinde_cadastrar").on("change", function () {

                    var imageReader = new FileReader();
                    imageReader.readAsDataURL(document.getElementById("foto_brinde_cadastrar").files[0]);

                    imageReader.onload = function (oFREvent) {
                        p.attr('src', oFREvent.target.result).fadeIn();

                        p.on('load', function () {

                            var img = document.getElementById('previewimage_brinde_cadastrar');
                            var x1, x2, y1, y2;

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

                            $('input[name="foto_brinde_cadastrar_x1"]').val(x1);
                            $('input[name="foto_brinde_cadastrar_y1"]').val(y1);
                            $('input[name="foto_brinde_cadastrar_w"]').val(x2 - x1);
                            $('input[name="foto_brinde_cadastrar_h"]').val(y2 - y1);

                            $('#modal_editar').on('hidden.bs.modal', function () {
                                $('#previewimage_brinde_cadastrar').imgAreaSelect({remove: true});
                            });
                            $('#previewimage_brinde_cadastrar').imgAreaSelect({remove: true});

                            $('#previewimage_brinde_cadastrar').imgAreaSelect({
                                x1: x1, y1: y1, x2: x2, y2: y2,
                                aspectRatio: '1:1',
                                handles: true,
                                imageHeight: this.naturalHeight,
                                imageWidth: this.naturalWidth,
                                onSelectEnd: function onSelectEnd(img, selection) {
                                    $('input[name="foto_brinde_cadastrar_x1"]').val(selection.x1);
                                    $('input[name="foto_brinde_cadastrar_y1"]').val(selection.y1);
                                    $('input[name="foto_brinde_cadastrar_w"]').val(selection.width);
                                    $('input[name="foto_brinde_cadastrar_h"]').val(selection.height);
                                },
                                parent: $('#conteudo_modal_add')
                            });
                        });
                    };
                });

                $("#selecionar_foto_brinde_cadastrar").on("click", function () {
                    $("#foto_brinde_cadastrar").click();
                });

                $('#tipo_brinde').on('change', function () {

                    if ($(this).val() == 1) {
                        $('#div_input_arquivo').show();
                        $('#div_input_link').hide();
                    }
                    if ($(this).val() == 2) {
                        $('#div_input_arquivo').hide();
                        $('#div_input_link').show();
                    }
                });
            }
        });
    });

    $("#tabela_brindes").DataTable({
        bLengthChange: false,
        ordering: false,
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: '/brindes/data-source',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            type: 'POST',
            data: {projeto: id_projeto}
        },
        columns: [{data: 'title', name: 'title'}, {data: 'description', name: 'description'}, {
            data: 'type',
            name: 'type'
        }, {data: 'detalhes', name: 'detalhes', orderable: false, searchable: false}],
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

            $('.detalhes_brinde').on('click', function () {
                var brinde = $(this).attr('brinde');
                $('#modal_detalhes_titulo').html('Detalhes da brinde');
                $('#modal_detalhes_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                var data = {id_brinde: brinde};
                $.post("/brindes/detalhe", data).then(function (response, status) {
                    $('#modal_detalhes_body').html(response);
                });
            });

            var id_brinde = '';

            $('.excluir_brinde').on('click', function () {

                id_brinde = $(this).attr('brinde');
                var name = $(this).closest("tr").find("td:first-child").text();
                $('#modal_excluir_titulo').html('Remover do projeto o brinde ' + name + ' ?');

                $('#bt_excluir').unbind('click');

                $('#bt_excluir').on('click', function () {

                    $('.loading').css("visibility", "visible");

                    $.ajax({
                        method: "POST",
                        url: "/brindes/deletarbrinde",
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        data: {id: id_brinde},
                        error: (response) => {
                            errorAjaxResponse(response);
                        },
                        success: (data) => {
                            $('.loading').css("visibility", "hidden");
                            alertPersonalizado('success', 'Brinde removido!');
                            $('#fechar_modal_excluir').click();
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                        }
                    });
                });
            });

            $('.editar_brinde').on('click', function () {

                $('#modal_editar_tipo').addClass('modal-simple');
                $('#modal_editar_tipo').removeClass('modal-lg');

                id_brinde = $(this).attr('brinde');

                $('#modal_editar_body').html("<div style='text-align: center'>Carregando...</div>");

                $.ajax({
                    method: "POST",
                    url: "/brindes/getformeditarbrinde",
                    dataType: "json",
                    headers: {
                        'Authorization': $('meta[name="access-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    data: {id: id_brinde},
                    error: (response) => {
                        errorAjaxResponse(response);
                    },
                    success: (data) => {
                        $('#modal_editar_body').html(data);

                        $('#editar').unbind('click');

                        $('#editar').on('click', function () {

                            $('.loading').css("visibility", "visible");

                            var form_data = new FormData(document.getElementById('editar_brinde'));
                            form_data.append('projeto', id_projeto);

                            $.ajax({
                                method: "POST",
                                url: "/brindes/editarbrinde",
                                dataType: "json",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                processData: false,
                                contentType: false,
                                cache: false,
                                data: form_data,
                                error: (response) => {
                                    errorAjaxResponse(response);
                                },
                                success: (data) => {
                                    $('.loading').css("visibility", "hidden");
                                    alertPersonalizado('success', 'Brinde atualizado!');
                                    $('#modal_editar').hide();
                                    $($.fn.dataTable.tables(true)).css('width', '100%');
                                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                                    $('#previewimage_brinde_editar').imgAreaSelect({remove: true});
                                }
                            });
                        });

                        var p = $("#previewimage_brinde_editar");
                        $("#foto_brinde_editar").on("change", function () {

                            var imageReader = new FileReader();
                            imageReader.readAsDataURL(document.getElementById("foto_brinde_editar").files[0]);

                            imageReader.onload = function (oFREvent) {
                                p.attr('src', oFREvent.target.result).fadeIn();

                                p.on('load', function () {

                                    var img = document.getElementById('previewimage_brinde_editar');
                                    var x1, x2, y1, y2;

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

                                    $('input[name="foto_brinde_editar_x1"]').val(x1);
                                    $('input[name="foto_brinde_editar_y1"]').val(y1);
                                    $('input[name="foto_brinde_editar_w"]').val(x2 - x1);
                                    $('input[name="foto_brinde_editar_h"]').val(y2 - y1);

                                    $('#modal_editar').on('hidden.bs.modal', function () {
                                        $('#previewimage_brinde_editar').imgAreaSelect({remove: true});
                                    });
                                    $('#previewimage_brinde_editar').imgAreaSelect({remove: true});

                                    $('#previewimage_brinde_editar').imgAreaSelect({
                                        x1: x1, y1: y1, x2: x2, y2: y2,
                                        aspectRatio: '1:1',
                                        handles: true,
                                        imageHeight: this.naturalHeight,
                                        imageWidth: this.naturalWidth,
                                        onSelectEnd: function onSelectEnd(img, selection) {
                                            $('input[name="foto_brinde_editar_x1"]').val(selection.x1);
                                            $('input[name="foto_brinde_editar_y1"]').val(selection.y1);
                                            $('input[name="foto_brinde_editar_w"]').val(selection.width);
                                            $('input[name="foto_brinde_editar_h"]').val(selection.height);
                                        },
                                        parent: $('#conteudo_modal_editar')
                                    });
                                });
                            };
                        });

                        $("#selecionar_foto_brinde_editar").on("click", function () {
                            $("#foto_brinde_editar").click();
                        });

                        $('#tipo_brinde').on('change', function () {

                            if ($(this).val() == 1) {
                                $('#div_input_arquivo').show();
                                $('#div_input_link').hide();
                            }
                            if ($(this).val() == 2) {
                                $('#div_input_arquivo').hide();
                                $('#div_input_link').show();
                            }
                        });

                        var tipo_brinde = '1';

                        if (tipo_brinde == '1') {
                            $('#div_input_arquivo').show();
                        }
                        if (tipo_brinde == '2') {
                            $('#div_input_link').show();
                        }
                    }
                });
            });
        }
    });
});
