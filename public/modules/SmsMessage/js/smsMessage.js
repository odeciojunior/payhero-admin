$(function () {

    var projectId = $("#project-id").val();
    $('#tab_sms').on('click', function () {
        atualizarSms();
    });
    atualizarSms();
    $("#add-sms").on('click', function () {
        $("#modal_add_tamanho").addClass("modal-simple");
        $("#modal_add_tamanho").removeClass("modal-lg");

        $("#modal_add_body").html("<div style='text-align: center;'>Carregando....</div>");

        $.ajax({
            method: "GET",
            url: "/sms/create",
            data: {project: projectId},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function () {
                alertCustom('error', 'Ocorreu algum erro');
            },
            success: function (data) {
                $("#modal_add_body").html(data);
                $("#tempo_sms_cadastrar").mask("0#");
                $("#cadastrar").unbind("click");
                $("#cadastrar").on("click", function () {
                    if ($("#plano_sms") === "" || $("#evento_sms").val() === "" || $("#tempo_sms_cadastrar").val() === "" || $("#status_sms").val() === ""
                        || $("#mensagem_sms").val() === "") {
                        alertCustom('error', 'Dados informados invalidos');
                        return false;
                    }

                    $(".loading").css("visibility", "visible");

                    var formData = new FormData(document.getElementById('cadastrar_sms'));
                    formData.append("project", projectId);

                    $.ajax({
                        method: "POST",
                        url: "/sms",
                        processData: false,
                        contentType: false,
                        cache: false,
                        data: formData,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom('error', 'Ocorreu algum erro');
                        },
                        success: function (data) {
                            $(".loading").css("visibility", "hidden");
                            alertCustom('success', "SMS Adicionado com sucesso");
                            $("#modal_add").hide();
                            $($.fn.dataTable.tables(true)).css('width', '100%');
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
                        },
                    });
                });
            }
        });

    });

    // $("#tabela_sms").DataTable({
    //     bLengthChange: false,
    //     ordering: false,
    //     processing: false,
    //     responsive: true,
    //     serverSide: true,
    //     ajax: {
    //         url: '/sms',
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         type: "GET",
    //         data: {projectId: projectId},
    //     },
    //     columns: [
    //         {data: 'plan', name: 'plan'},
    //         {
    //             data: function (data) {
    //                 return data.event.replace(new RegExp('_', 'g'), ' ');
    //             }, name: 'event'
    //         },
    //         {
    //             data: function (data) {
    //                 return data.time + ' ' + data.period;
    //             }, name: 'time'
    //         },
    //         {data: 'message', name: 'message'},
    //         {
    //             data: function (data) {
    //                 if (data.status)
    //                     return "Ativo";
    //                 else
    //                     return "Inativo";
    //             }, new: 'status'
    //         },
    //         {data: 'detalhes', name: 'detalhes', orderable: false, searchable: false},
    //     ],
    //     "language": {
    //         "sProcessing": "Carregando...",
    //         "lengthMenu": "Apresentando _MENU_ registros por página",
    //         "zeroRecords": "Nenhum registro encontrado",
    //         "info": "Apresentando página _PAGE_ de _PAGES_",
    //         "infoEmpty": "Nenhum registro encontrado",
    //         "infoFiltered": "(filtrado por _MAX_ registros)",
    //         "sInfoPostFix": "",
    //         "sSearch": "Procurar :",
    //         "sUrl": "",
    //         "sInfoThousands": ",",
    //         "sLoadingRecords": "Carregando...",
    //         "oPaginate": {
    //             "sFirst": "Primeiro",
    //             "sLast": "Último",
    //             "sNext": "Próximo",
    //             "sPrevious": "Anterior",
    //         },
    //     },
    //     "drawCallback": function () {
    //         $("#modal_editar_tipo").addClass('modal-simple');
    //         $("#modal_editar_tipo").addClass('modal-lg');
    //
    //         var id_sms = '';
    //
    //         $(".detalhes_sms").on("click", function () {
    //             var sms = $(this).attr('sms');
    //             $("#modal_detalhes_titulo").html("Detalhes do sms");
    //             $("#modal_detalhes_body").html("<h5 style='width:100%;text-align:center;'>Carregando...</h5>");
    //
    //             $.ajax({
    //                 method: "GET",
    //                 url: "/sms/" + sms,
    //                 data: {smsId: sms},
    //                 headers: {
    //                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 error: function () {
    //                     alertCustom('error', 'Ocorreu algum erro');
    //                 },
    //                 success: function (response) {
    //                     $("#modal_detalhes_body").html(response);
    //                 }
    //             });
    //         });
    //
    //         $(".excluir_sms").on("click", function () {
    //             idSms = $(this).attr('sms');
    //             var name = $(this).closest("tr").find("td:first-child").text();
    //             $("#modal_excluir_titulo").html("Remover do projeto o sms para o plano " + name + " ?");
    //             $("#bt_excluir").unbind('click');
    //             $("#bt_excluir").on('click', function () {
    //                 $(".loading").css("visibility", "visible");
    //                 $("#fechar_modal_excluir").click();
    //
    //                 $.ajax({
    //                     method: "DELETE",
    //                     url: "/sms/" + idSms,
    //                     headers: {
    //                         "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
    //                     },
    //                     error: function () {
    //                         $(".loading").csS("visibility", "hidden");
    //                         alertCustom("error", "Ocorreu algum erro");
    //                     },
    //                     success: function (data) {
    //                         $(".loading").css("visibility", "hidden");
    //                         alertCustom("success", "SMS removido!");
    //                         $($.fn.dataTable.tables(true)).css('width', '100%');
    //                         $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
    //                     }
    //                 });
    //             });
    //         });
    //
    //         $(".editar_sms").on('click', function () {
    //             idSms = $(this).attr('sms');
    //             $("#modal_editar_body").html("<div style='text-align: center;'>Carregando....</div>");
    //
    //             $.ajax({
    //                 method: "GET",
    //                 url: "/sms/" + idSms + "/edit",
    //                 headers: {
    //                     "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
    //                 },
    //                 data: {id: idSms, project: projectId},
    //                 error: function () {
    //                     alertCustom('error', 'Ocorreu algum erro');
    //                 },
    //                 success: function (data) {
    //                     $("#modal_editar_body").html(data);
    //                     $("#tempo_sms_editar").mask("0#");
    //                     $("#editar").unbind("click");
    //                     $("#editar").on("click", function () {
    //                         $(".loading").css("visibility", "visible");
    //
    //                         var paramObj = {};
    //                         $.each($('#editar_sms').serializeArray(), function (_, kv) {
    //                             if (paramObj.hasOwnProperty(kv.name)) {
    //                                 paramObj[kv.name] = $.makeArray(paramObj[kv.name]);
    //                                 paramObj[kv.name].push(kv.value);
    //                             } else {
    //                                 paramObj[kv.name] = kv.value;
    //                             }
    //                         });
    //                         paramObj['id'] = idSms;
    //
    //                         $.ajax({
    //                             method: "PUT",
    //                             url: "/sms/" + idSms,
    //                             headers: {
    //                                 "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
    //                             },
    //                             data: {smsData: paramObj},
    //                             error: function () {
    //                                 $(".loading").css("visibility", "hidden");
    //                                 alertCustom('error', 'Ocorreu algum erro');
    //                             },
    //                             success: function (data) {
    //                                 $(".loading").css("visibility", "hidden");
    //                                 alertCustom("success", "SMS atualizado");
    //                                 $("#modal_add").hide();
    //                                 $($.fn.dataTable.tables(true)).css("width", "100%");
    //                                 $($.fn.dataTable.tables(true)).DataTable().columns.adjust().draw();
    //                             }
    //                         });
    //                     });
    //                 }
    //             });
    //         });
    //     }
    // });

});
