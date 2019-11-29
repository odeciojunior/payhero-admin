let companyStatus = {
    pending: 'badge badge-primary',
    analyzing: 'badge badge-pending',
    approved: 'badge badge-success',
    refused: 'badge badge-danger',
};

let companyStatusTranslated = {
    pending: 'Pendente',
    analyzing: 'Em análise',
    approved: 'Aprovado',
    refused: 'Recusado',
};
$(document).ready(function () {

    atualizar(1);

    function atualizar(page) {

        loadOnTable('#companies_table_data', '#companies_table');
        $.ajax({
            method: "GET",
            url: "/api/companies?page=" + page,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                console.log(response.data);
                $('#companies_table_data').html('');
                if (response.data == '') {
                    $('#companies_table_data').html("<tr class='text-center'><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhuma empresa encontrada</td></tr>");
                } else {
                    $.each(response.data, function (index, value) {
                        dados = `
                    
                        <tr>
                            <td>${value.fantasy_name}</td>
                            <td>${value.company_document}</td>
                            <td>
                                <span class='badge ${companyStatus[value.document_status]}'>${companyStatusTranslated[value.document_status]}</span>
                            </td>
                            <td><a title='Editar' href="/companies/${value.id_code}/edit?type=${value.type}" class='edit-company' data-company='${value.id_code}' role='button'><i class='material-icons gradiente'>edit</i></a></td>
                            <td><a title='Excluir' class='pointer delete-company' company='${value.id_code}' data-toggle='modal' data-target='#modal-delete' role='button'><i class='material-icons gradient'>delete</i></a></td>
                        </tr>
                    `;
                        $('#companies_table').addClass('table-striped');
                        $("#companies_table_data").append(dados);
                    });

                    $(".delete-company").unbind('click');
                    $(".delete-company").on("click", function (event) {
                        event.preventDefault();
                        var company = $(this).attr('company');

                        $("#bt-delete").unbind('click');
                        $("#bt-delete").on('click', function () {
                            $("#close-modal-delete").click();
                            loadingOnScreen();

                            $.ajax({
                                method: "DELETE",
                                url: "/api/companies/" + company,
                                dataType: "json",
                                headers: {
                                    'Authorization': $('meta[name="access-token"]').attr('content'),
                                    'Accept': 'application/json',
                                },
                                error: function (response) {
                                    loadingOnScreenRemove();
                                    errorAjaxResponse(response);
                                },
                                success: function success(data) {
                                    loadingOnScreenRemove();
                                    alertCustom("success", data.message);
                                    atualizar(page);
                                }

                            });
                        });
                    });
                }
            }
        });
    }
});
