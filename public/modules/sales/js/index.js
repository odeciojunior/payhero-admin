$(document).ready(function () {
    atualizar();

    $("#filtros").on("click", function () {
        if ($("#div_filtros").is(":visible")) {
            $("#div_filtros").slideUp();
        } else {
            $("#div_filtros").slideDown();
        }
    });

    $("#bt_filtro").on("click", function (event) {
        event.preventDefault();
        atualizar();
    });
    $("#bt_get_csv").on("click", function () {
        $('<input>').attr({
            id: 'export-sales',
            type: 'hidden',
            name: 'type',
            value: 'csv'
        }).appendTo('form');

        $('#filter_form').submit();
        $('export-sales').remove();
    });

    $("#bt_get_xls").on("click", function () {
        $('<input>').attr({
            id: 'export-sales',
            type: 'hidden',
            name: 'type',
            value: 'xls'
        }).appendTo('form');

        $('#filter_form').submit();
        $('export-sales').remove();
    });

    function downloadFile(data, fileName) {
        var type = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "text/plain";

        // Create an invisible A element
        var a = document.createElement("a");
        a.style.display = "none";
        document.body.appendChild(a);

        // Set the HREF to a Blob representation of the data to be downloaded
        a.href = window.URL.createObjectURL(new Blob([data], {type: type}));

        // Use download attribute to set set desired file name
        a.setAttribute("download", fileName);

        // Trigger the download by simulating click
        a.click();

        // Cleanup
        window.URL.revokeObjectURL(a.href);
        document.body.removeChild(a);
    }

    function atualizar() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        loadOnTable('#dados_tabela', '#tabela_vendas');

        if (link == null) {
            link = '/sales/getsales?' + 'projeto=' + $("#projeto").val() + '&transaction=' + $("#transaction").val().replace('#', '') + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        } else {
            link = '/sales/getsales' + link + '&projeto=' + $("#projeto").val() + '&transaction=' + $("#transaction").val().replace('#', '') + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error() {
                //
            },
            success: function success(response) {
                $('#dados_tabela').html('');
                $('#tabela_vendas').addClass('table-striped');

                var statusArray = {
                    1: 'success',
                    2: 'pendente',
                    4: 'danger',
                    6: 'primary'
                };

                $.each(response.data, function (index, value) {
                    console.log(value.status_translate);
                    dados = '';
                    dados += '<tr>';
                    dados += "<td class='display-sm-none display-m-none display-lg-none'>" + value.sale_code + "</td>";
                    dados += "<td>" + value.project + "</td>";
                    dados += "<td>" + value.product + "</td>";
                    dados += "<td class='display-sm-none display-m-none display-lg-none'>" + value.client + "</td>";
                    dados += "<td><img src='/modules/global/img/cartoes/" + value.brand + ".png'  style='width: 60px'></td>";

                    dados += "<td><span class='badge badge-" + statusArray[value.status] + "'>" + value.status_translate + "</span></td>";

                    // if (value.status == '1') {
                    //     dados += "<td><span class='badge badge-success'>Aprovada</span></td>";
                    // } else if (value.status == '2') {
                    //     dados += "<td><span class='badge badge-pendente'>Pendente</span></td>";
                    // } else if (value.status == '4') {
                    //     dados += "<td><span class='badge badge-danger'>Estornada</span></td>";
                    // } else if (value.status == '5') {
                    //     dados += "<td><span class='badge badge-pendente'>Cancelada</span></td>";
                    // }

                    dados += "<td class='display-sm-none display-m-none'>" + value.start_date + "</td>";
                    dados += "<td class='display-sm-none'>" + value.end_date + "</td>";
                    dados += "<td style='white-space: nowrap'><b>" + value.total_paid + "</b></td>";
                    dados += "<td><a role='button' class='detalhes_venda pointer' venda='" + value.id + "' data-target='#modal_detalhes' data-toggle='modal'><i class='material-icons gradient'>remove_red_eye</i></button></a></td>";
                    dados += '</tr>';
                    $("#dados_tabela").append(dados);
                });
                if (response.data == '') {
                    $('#dados_tabela').html("<tr class='text-center'><td colspan='10' style='height: 70px;vertical-align: middle'> Nenhuma venda encontrada</td></tr>");
                }
                pagination(response, 'sales', atualizar);

                $('.detalhes_venda').unbind('click');

                $('.detalhes_venda').on('click', function () {
                    var venda = $(this).attr('venda');

                    $('#modal_venda_titulo').html('Detalhes da venda ' + venda + '<br><hr>');

                    $('#modal_venda_body').html("<h5 style='width:100%; text-align: center'>Carregando..</h5>");

                    var data = {sale_id: venda};

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

                            $('.modal-body').html(response);

                            $(".copy_link").on("click", function () {
                                var temp = $("<input>");
                                $("#nav-tabContent").append(temp);
                                temp.val($(this).attr('link')).select();
                                document.execCommand("copy");
                                temp.remove();
                                alertCustom('success', 'Link copiado!');
                            });
                            $(".copy_link").on("click", function () {
                                var temp = $("<input>");
                                $("#nav-tabContent").append(temp);
                                temp.val($(this).attr('digitable-line')).select();
                                document.execCommand("copy");
                                temp.remove();
                                alertCustom('success', 'Linha Digitável copiado!');
                            });
                        }
                    });
                });

                $('.estornar_venda').unbind('click');

                $('.estornar_venda').on('click', function () {

                    id_venda = $(this).attr('venda');

                    $('#modal_estornar_titulo').html('Estornar venda #' + id_venda + ' ?');
                    $('#modal_estornar_body').html('');
                });
            }
        });
    }

    function csvSalesExport() {
        var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        if (link == null) {
            link = '/sales/getcsvsales?' + 'projeto=' + $("#projeto").val() + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        } else {
            link = '/sales/getcsvsales' + link + '&projeto=' + $("#projeto").val() + '&forma=' + $("#forma").val() + '&status=' + $("#status").val() + '&comprador=' + $("#comprador").val() + '&data_inicial=' + $("#data_inicial").val() + '&data_final=' + $("#data_final").val();
        }

        $.ajax({
            method: "GET",
            url: link,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                console.log(response);
            },
            success: function success(response) {
                downloadFile(response, 'export.xlsx');
            }
        });
    }
});
