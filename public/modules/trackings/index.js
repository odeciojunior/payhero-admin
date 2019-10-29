$(() => {

    $(document).on('click', '.copy', function () {
        var temp = $("<input>");
        $("body").append(temp);
        temp.val($(this).html()).select();
        document.execCommand("copy");
        temp.remove();
        alertCustom('success', 'Código copiado!');
    });

    $('#bt_filtro').on('click', function(){
       index();
    });

    index();

    function index(link = null) {

        if (link == null) {
            link = '/api/tracking?' + 'tracking_code=' + $('#tracking_code').val() + '&status=' + $('#status').val();
        } else {
            link = '/api/tracking' + link + '&tracking_code=' + $('#tracking_code').val() + '&status=' + $('#status').val();
        }

        loadOnTable('#dados_tabela', '#tabela_trackings');
        $.ajax({
            method: 'GET',
            url: link,
            dataType: 'json',
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: response => {
                errorAjaxResponse(response);
            },
            success: response => {
                $('#dados_tabela').html('');
                $('#tabela_trackings').addClass('table-striped');
                $.each(response.data, function (index, tracking) {
                    let dados = `<tr>
                                     <td>
                                         <img style="width: 35px; margin-right: 10px;" src="${tracking.product.photo}"/>
                                         ${tracking.product.name}
                                     </td>
                                     <td class="detalhes_venda pointer" venda="${tracking.sale}">#${tracking.sale}</td>
                                     <td class="copy pointer" title="Copiar código">${tracking.tracking_code}</td>
                                     <td>
                                        <span class="badge badge-${tracking.tracking_status_enum === 3 ? 'success': tracking.tracking_status_enum === 5 ? 'danger' : 'primary'}">${tracking.tracking_status}</span>
                                     </td>
                                     <td></td>
                                 </tr>`;
                    $('#dados_tabela').append(dados);
                });

                pagination(response, 'trackings', index);
            }
        });
    }
});
