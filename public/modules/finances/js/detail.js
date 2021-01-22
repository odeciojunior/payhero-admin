$(() => {
    // MODAL DETALHES DA TRANSAÇÃO

    $('.details_transaction').modal({backdrop: 'static', keyboard: false})

    $(document).on('click', '.details_transaction', function () {

            // $('.details_transaction').disabled = true;
            // setTimeout(function () {
            //     $('.details_transaction').disabled = false;
            // }.bind('.details_transaction'), 10000);

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
                $('#modal_detalhes_transacao').modal('hide');
                errorAjaxResponse(response);
            },
            success: (response) => {

                $("#withdrawal-code").html(`<span class="mr-30">ID #${response.id}</span><span>Solicitado em ${response.date_request}</span>`);
                let dataHtml = '';

                $.each(response.transactions, function (index, value) {
                    let is_liquidated = '';

                    if ( value.liquidated == true) {
                        is_liquidated = 'is-released-on';
                    }
                    else {
                        is_liquidated = 'is-released-off';
                    }

                    dataHtml += `<tr>
                                <td>
                                    <img src='/modules/global/img/${value.brand}.svg'   width='50px;' style='border-radius:6px;'><br>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center align-self-center ">
                                        <span class="transaction-status d-flex justify-content-center align-items-center align-self-center rounded-circle rounded-circle" >
                                            <span class="rounded-circle ${is_liquidated} " ></span>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class='small'>${value.date}</span>
                                </td>
                                <td>
                                    <span class='small font-weight-bold'>${value.value}</span>
                                </td>
                            </tr>`;

                });

                dataHtml += `<tr>
                                <td> </td>
                                <td> </td>
                                <td>
                                    <span class='small font-weight-bold'>Total </span>
                                </td>
                                <td>
                                    <span class='small font-weight-bold'>${response.total_withdrawal}</span>
                                </td>
                            </tr>`;

                $("#transactions-table-data").append(dataHtml);

                if (response.transactions == '') {
                    $("#transactions-table-data").html("<tr><td colspan='10' class='text-center'>Nenhum saque realizado até o momento</td></tr>");
                }

                loadOnAny('#modal-transactionsDetails', true);
            }
        });


        let exportFinanceFormat = 'xls'
        $("#bt_get_csv_transfer").on("click", function () {
            $('#export-finance-getnet-transfer').removeClass('d-none');
            exportFinanceFormat = 'xls';
        });
        $("#bt_get_csv_transfer").on("click", function () {
            $('#export-finance-getnet-transfer').removeClass('d-none');
            exportFinanceFormat = 'csv';
        });

        $(".btn-confirm-export-finance-getnet-transfer").on("click", function () {
            var regexEmail = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
            var email = $('#email_finance_export_transfer').val();

            if( email == '' || !regexEmail.test(email) ) {
                alertCustom('error', 'Preencha o email corretamente');
                return false;
            } else {
                financesGetnetExport(exportFinanceFormat);
                $('#export-finance-getnet-transfer').addClass('d-none');
            }
        });

        // // Download do relatorio
        function financesGetnetExport(fileFormat) {
            var email = $('#email_finance_export_transfer').val();
            $.ajax({
                method: "POST",
                url: '/api/withdrawals/get-transactions/' + withdrawal,
                data: {
                    email,
                    "format" : fileFormat,
                } ,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: response => {
                    errorAjaxResponse(response);
                },
                success: response => {
                    $('#export-finance-email-transfer').text(email);
                    $('#alert-finance-export-transfer').show()
                        .shake();

                }
            });

        }

    });
















});
