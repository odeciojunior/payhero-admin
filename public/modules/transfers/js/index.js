$(function () {

    updateTransfersTable();

    $("#extract_company_select").on("change", function () {

        updateTransfersTable();
    });

    function updateTransfersTable(link = null) {

        /*$("#table-transfers-body").html("<tr class='text-center'><td colspan='11'Carregando...></td></tr>");*/
        loadOnTable('#table-transfers-body', '#transfersTable');

        if (link == null) {
            link = '/transfers';
        } else {
            link = '/transfers' + link;
        }

        $.ajax({
            method: "GET",
            url: link,
            data: {company: $("#extract_company_select").val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                $("#table-transfers-body").html('Erro ao encontrar dados');
            },
            success: function success(response) {

                $("#table-transfers-body").html('');

                if (response.data == '') {

                    $("#table-transfers-body").html("<tr><td colspan='3' class='text-center'>Nenhuma movimentação até o momento</td></tr>");
                    $("#pagination").html("");
                } else {
                    data = '';

                    $.each(response.data, function (index, value) {
                        data += '<tr >';
                        data += '<td style="vertical-align: middle;">' + value.reason + '<a style="cursor:pointer;" class="detalhes_venda pointer" data-target="#modal_detalhes" data-toggle="modal" sale="' + value.sale_id + '">' + '<span style="color:black;">' + value.transaction_id + '</span>' + '</a></td>';
                        data += '<td style="vertical-align: middle;">' + value.date + '</td>';
                        if (value.type_enum === 1) {
                            data += '<td style="vertical-align: middle; color:green;">' + value.value + ' <span style="color:red;">' + value.anticipable_value + '</span> </td>';
                        } else {
                            data += '<td style="vertical-align: middle; color:red;">' + value.value + '</td>';
                        }
                        data += '</tr>';
                    });

                    $("#table-transfers-body").html(data);

                    pagination(response, 'transfers', updateTransfersTable);
                }
                $('.detalhes_venda').on('click', function () {
                    var sale = $(this).attr('sale');

                    $('#modal_venda_titulo').html('Detalhes da venda ' + sale + '<br><hr>');
                    var data = {sale_id: sale};

                    $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");
                    $.ajax({
                        method: "POST",
                        url: '/sales/venda/detalhe',
                        data: data,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        error: function error() {
                            //
                        },
                        success: function success(response) {
                            $('.subTotal').mask('#.###,#0', {reverse: true});

                            $('.modal-body-details').html(response);
                        }
                    });
                });
            }
        });
    }

});
