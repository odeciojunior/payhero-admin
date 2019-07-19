$(function () {

    var projectId = $("#project-id").val();
    $('#tab_sms').on('click', function () {
        atualizarSms();
    });
    atualizarSms();
    $("#add-sms").on('click', function () {
        $("#modal-title").html('Adicionar SMS <br><hr class="my-0">');
        $("#modal_add_size").addClass('modal_simples');
        $("#modal_add_size").addClass('modal-lg');

        $("#modal-add-body").html("<div style='text-align:center;'>Carregando...</div>");

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
                $("#btn-modal").addClass('btn-save');
                $("#btn-modal").text('Salvar');
                $("#btn-modal").show();
                $('#modal-add-body').html(data);
                $(".btn-save").unbind('click');
                $(".btn-save").on('click', function () {

                    var formData = new FormData(document.getElementById('form-register-sms'));
                    formData.append("project", projectId);

                    $.ajax({
                        method: "POST",
                        url: "/sms",
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
                            if (data.status == '422') {
                                for (error in data.responseJSON.errors) {
                                    alertCustom('error', String(data.responseJSON.errors[error]));
                                }
                            }
                        }, success: function () {
                            $(".loading").css("visibility", "hidden");
                            alertCustom("success", "SMS Adicionado!");
                            atualizarSms();
                        }
                    });
                });

            }
        });

    });

    function atualizarSms() {
        $("#data-table-sms").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");
        $.ajax({
            method: "GET",
            url: '/sms',
            data: {project: projectId},
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            error: function () {
                $("#data-table-sms").html('Erro ao encontrar dados');
            },
            success: function (response) {
                $("#data-table-sms").html('');
                data = '';
                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">SMS</td>';
                data += '<td class= style="vertical-align: middle;">Boleto gerado</td>';
                data += '<td class= style="vertical-align: middle;">Imediato</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, não esqueça de pagar seu boleto para enviarmos seu pedido! {url_boleto}</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';

                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">SMS</td>';
                data += '<td class= style="vertical-align: middle;">Boleto vencendo</td>';
                data += '<td class= style="vertical-align: middle;">10:00 horas</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! {url_boleto}</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';

                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">SMS</td>';
                data += '<td class= style="vertical-align: middle;">Carrinho abandonado</td>';
                data += '<td class= style="vertical-align: middle;">4 horas depois</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';

                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">SMS</td>';
                data += '<td class= style="vertical-align: middle;">Carrinho abandonado</td>';
                data += '<td class= style="vertical-align: middle;">10:00 horas próximo dia</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';

                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">Email</td>';
                data += '<td class= style="vertical-align: middle;">Boleto gerado</td>';
                data += '<td class= style="vertical-align: middle;">Imediato</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, aqui está seu boleto. Como você optou por Boleto Bancário, estamos enviando por aqui para você não se esquecer. O boleto deve ser pago até a data de vencimento para enviarmos seu(s) pedido(s)! {url_boleto}</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';

                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">Email</td>';
                data += '<td class= style="vertical-align: middle;">Boleto gerado</td>';
                data += '<td class= style="vertical-align: middle;">10:00 horas próximo dia</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';

                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">Email</td>';
                data += '<td class= style="vertical-align: middle;">Boleto vencendo</td>';
                data += '<td class= style="vertical-align: middle;">10:00 horas</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, seu boleto vencerá hoje, ainda dá tempo de pagar! Não se esqueça, só enviaremos o seu pedido (que já está separado) se você efetuar o pagamento! {url_boleto}</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';

                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">Email</td>';
                data += '<td class= style="vertical-align: middle;">Carrinho abandonado</td>';
                data += '<td class= style="vertical-align: middle;">4 horas depois</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, nossos produtos estão com preços especiais e o estoque é bem limitado. Recomendamos que você finalize a compra ainda hoje para garantir a promoção e economizar dinheiro! {link_carrinho_abandonado}</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';

                data += '<tr>';
                data += '<td class= style="vertical-align: middle;">Email</td>';
                data += '<td class= style="vertical-align: middle;">Carrinho abandonado</td>';
                data += '<td class= style="vertical-align: middle;">10:00 horas próximo dia</td>';
                data += '<td class="shipping-zip-code-origin " style="vertical-align:">Olá {primeiro_nome}, vimos que você não aproveitou a promoção de ontem. O seu pedido ainda está separado aguardando a finalização da compra, mas não podemos segurar por muito tempo! {link_carrinho_abandonado</td>';
                data += '<td class="shipping-status " style="vertical-align: middle;">';
                data += '<span class="badge badge-success mb-1">Ativo</span>';
                data += '<span class="badge badge-primary">Grátis</span>';
                data += '</td>';
                data += '<td class="shipping-pre-selected " style="vertical-align: middle;">';
                data += '</td>';
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled details-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'>remove_red_eye</i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled edit-sms' data-target='#modal-content' data-toggle='modal'> <i class='material-icons gradient'> edit </i> </a></td>";
                data += "<td style='vertical-align: middle' class=''><a role='button' class='pointer disabled delete-sms' data-toggle='modal' data-target='#modal-delete'>   <i class='material-icons gradient'> delete_outline </i> </a></td>";
                data += '</tr>';


                $("#data-table-sms").append(data);

                // $.each(response.data, function (index, value) {
                //     data = '';
                //     data += '<tr>';
                //     data += '<td class="shipping-id text-center" style="vertical-align: middle;">' + value.event + '</td>';
                //     data += '<td class="shipping-type text-center" style="vertical-align: middle;">' + value.time + '</td>';
                //     data += '<td class="shipping-value text-center" style="vertical-align: middle;">' + value.period + '</td>';
                //     data += '<td class="shipping-zip-code-origin text-center" style="vertical-align:">' + value.message + '</td>';
                //     data += '<td class="shipping-status text-center" style="vertical-align: middle;">';
                //     if (value.status === 1) {
                //         data += '<span class="badge badge-success">Ativo</span>';
                //     } else {
                //         data += '<span class="badge badge-danger">Desativado</span>';
                //     }
                //
                //     data += '</td>';
                //
                //     data += '<td class="shipping-pre-selected text-center" style="vertical-align: middle;">';
                //
                //     data += '</td>';
                //
                //     data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger details-sms'  sms='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                //     data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger edit-sms'  sms='" + value.id + "' data-target='#modal-content' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                //     data += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger delete-sms'  sms='" + value.id + "'  data-toggle='modal' data-target='#modal-delete' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";
                //
                //     data += '</tr>';
                //     $("#data-table-sms").append(data);
                // });
                // if (response.data == '') {
                //     $("#data-table-sms").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align: middle;'>Nenhum registro encontrado</td></tr>")
                // }
                $(".details-sms").unbind('click');
                $(".details-sms").on('click', function () {
                    var sms = $(this).attr('sms');
                    $("#modal-title").html('Detalhes do SMS <br><hr>');
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando...</h5>");
                    var data = {smsId: sms};
                    $("#btn-modal").hide();
                    $.ajax({
                        method: "GET",
                        url: "/sms/" + sms,
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        }, success: function (response) {
                            $("#modal-add-body").html(response);

                        }
                    });
                });
                $(".edit-sms").unbind('click');
                $(".edit-sms").on('click', function () {
                    $("#modal-add-body").html("");
                    var sms = $(this).attr('sms');
                    $("#modal-title").html("Editar SMS<br><hr>");
                    $("#modal_add_size").addClass('modal-lg');
                    $("#modal-add-body").html("<h5 style='width:100%; text-align: center;'>Carregando.....</h5>");
                    var data = {smsId: sms};
                    $.ajax({
                        method: "GET",
                        url: "/sms/" + sms + "/edit",
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function () {
                            //
                        }, success: function (response) {
                            $("#btn-modal").addClass('btn-update');
                            $("#btn-modal").text('Atualizar');
                            $("#btn-modal").show();
                            $("#modal-add-body").html(response);

                            $(".btn-update").unbind('click');
                            $(".btn-update").on('click', function () {

                                $.ajax({
                                    method: "PUT",
                                    url: "/sms/" + sms,
                                    headers: {
                                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        event: $("#event").val(),
                                        time: $("#time").val(),
                                        period: $("#period").val(),
                                        message: $("#message").val(),
                                        status: $("#status").val(),
                                    },
                                    error: function (response) {
                                        if (response.status == '422') {
                                            for (error in response.responseJSON.errors) {
                                                alertCustom('error', String(response.responseJSON.errors[error]));
                                            }
                                        }
                                    },
                                    success: function (data) {
                                        alertCustom("success", "Notificação atualizada com sucesso");
                                        atualizarSms();
                                    }
                                });

                            });
                        }
                    });

                });
                $('.delete-sms').on('click', function (event) {
                    event.preventDefault();
                    var sms = $(this).attr('sms');
                    $("#modal_excluir_titulo").html("Remover Cupom?");
                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on('click', function () {
                        $("#fechar_modal_excluir").click();

                        $.ajax({
                            method: "DELETE",
                            url: "/sms/" + sms,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function () {
                                if (response.status == '422') {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                }
                            },
                            success: function (data) {
                                alertCustom("success", "Notificação Removida com sucesso");
                                atualizarSms();
                            }

                        })
                    });

                });

            }
        });
    }

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
