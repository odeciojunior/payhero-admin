$(() => {
    // MODAL DETALHES DA TRANSAÇÃO
    $(document).on('click', '.details_transaction', function () {

        let withdrawal = $(this).attr('withdrawal');
        loadOnAny('#modal-transactionsDetails');
        $('#withdrawal-code').html('');
        $('#transactions-table-data').html('');
        $('#modal_detalhes_transacao').modal('show');

        $.ajax({
            method: "GET",
            url: '/api/withdrawals/get-transactions-by-brand/' + withdrawal,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                //$('#modal_detalhes_transacao').modal('hide');
                loadOnAny('#modal-transactionsDetails', true);
                errorAjaxResponse(response);
            },
            success: (response) => {

                $("#withdrawal-code").html(`<span class="mr-30">ID #${response.id}</span><span>Feita em ${response.date_request}</span>`);
                let dataHtml = '';

                $.each(response.transactions, function (index, value) {
                    let is_liquidated = '';

                    if ( value.liquidated == true) {
                        is_liquidated = '<span class="material-icons is-released-on"> check </span>';
                    }
                    else {
                        is_liquidated = '<span class="material-icons is-released-off"> close </span>';
                    }


                    dataHtml += `<tr>
                                <td>
                                    <img src='/modules/global/assets/img/cartoes/${value.brand}.png'   width='35px;' style='border-radius:6px;'><br>
                                </td>
                                <td>
                                    <span class="small">${is_liquidated}</span>
                                </td>
                                <td>
                                    <span class='small'>${value.date}</span>
                                </td>
                                <td>
                                    <span class='small font-weight-bold'>${value.value}</span>
                                </td>
                            </tr>`;

                });

                $("#transactions-table-data").append(dataHtml);

                if (response.transactions == '') {
                    $("#transactions-table-data").html("<tr><td colspan='10' class='text-center'>Nenhum saque realizado até o momento</td></tr>");
                }

                loadOnAny('#modal-transactionsDetails', true);
            }
        });
    });

});
