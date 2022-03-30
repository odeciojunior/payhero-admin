$(function () {

    var projectId = $("#project-id").val();

    $("#tab_partners").on('click', function () {
        atualizarPartners();
    });

    atualizarPartners();

    function atualizarPartners() {
        $("#data-table-partners").html("<tr class='text-center'><td colspan='11'>Carregando...</td></tr>");

        $.ajax({
            method: "GET",
            url: "/partnes",
            data: projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error() {
                $("#data-table-partners").html("Erro ao encontrar dados");
            },
            success: function success(response) {
                $("#data-table-partners").html('');
                $.each(response.data, function (index, value) {
                    dados = '';
                    dados += '<tr>';
                    dados += '<td class="shipping-id text-center" style="vertical-align: middle; display: none;">' + value.shipping_id + '</td>';
                    dados += '<td class="shipping-type text-center" style="vertical-align: middle; display: none;">' + value.type + '</td>';
                    dados += '<td class="shipping-value text-center" style="vertical-align: middle; display: none;">' + value.value + '</td>';
                    dados += '<td class="shipping-zip-code-origin text-center" style="vertical-align: middle; display: none;">' + value.zip_code_origin + '</td>';
                    dados += '<td class="shipping-id text-center" style="vertical-align: middle;">' + value.type + '</td>';
                    dados += '<td class="shipping-name text-center" style="vertical-align: middle;">' + value.name + '</td>';
                    dados += '<td class="shipping-type text-center" style="vertical-align: middle;">' + value.value + '</td>';
                    dados += '<td class="shipping-information text-center" style="vertical-align: middle;">' + value.information + '</td>';
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger detalhes-partnes' title='Visualizar'  partnes='" + value.partnes_id + "' data-target='#modal-detalhes-frete' data-toggle='modal' type='button'><i class='icon wb-eye' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger editar-partnes' title='Editar'  partnes='" + value.partnes_id + "' data-target='#modal-detalhes-frete' data-toggle='modal' type='button'><i class='icon wb-pencil' aria-hidden='true'></i></button></td>";
                    dados += "<td style='vertical-align: middle' class='text-center'><button class='btn btn-sm btn-outline btn-danger excluir-partnes' title='Excluir'  partnes='" + value.partnes_id + "'  data-toggle='modal' data-target='#modal_excluir' type='button'><i class='icon wb-trash' aria-hidden='true'></i></button></td>";
                    dados += '</tr>';

                    $("#data-table-partners").append(dados);
                });

                if (response.data === '') {
                    $("#data-table-partners").html("<tr class='text-center'><td colspan='11' style='height: 70px; vertical-align:middle;'>Nenhum registro encontrado </td></tr>");
                }
            }
        });
    }
});
