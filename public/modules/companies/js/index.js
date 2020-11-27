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

let captureTransaction = {
    1: '<i class="icon wb-check" aria-hidden="true" style="color:green; font-size:20px"></i>',
    0: '<i class="icon wb-close"  aria-hidden="true" style="color:red; font-size:20px"></i>',
}

let captureTransactionBadge = {
    0: 'badge badge-danger',
    1: 'badge badge-success',
}

let companyType = {
    1: 'physical person',
    2: 'juridical person',
};

$(document).ready(function () {
    function statusCompanyJuridicalPerson(company) {
        if (companyStatusTranslated[company.contract_document_status] === 'Aprovado'
            && companyStatusTranslated[company.address_document_status] === 'Aprovado'
            && companyStatusTranslated[company.bank_document_status] === 'Aprovado') {
            return 'approved';
        } else if (companyStatusTranslated[company.contract_document_status] === 'Pendente'
            || companyStatusTranslated[company.address_document_status] === 'Pendente'
            || companyStatusTranslated[company.bank_document_status] === 'Pendente') {
            return 'pending';
        } else if (companyStatusTranslated[company.contract_document_status] === 'Recusado'
            || companyStatusTranslated[company.address_document_status] === 'Recusado'
            || companyStatusTranslated[company.bank_document_status] === 'Recusado') {
            return 'refused';
        } else if (companyStatusTranslated[company.contract_document_status] === 'Em análise'
            || companyStatusTranslated[company.address_document_status] === 'Em análise'
            || companyStatusTranslated[company.bank_document_status] === 'Em análise') {
            return 'analyzing';

        }
    }

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
                $('#companies_table_data').html('');

                if (response.data.length < 1) {
                    $('#companies_table_data').html("<tr class='text-center'><td colspan='11' style='height: 70px;vertical-align: middle'> Nenhuma empresa encontrada</td></tr>");
                    return false;
                }

                $.each(response.data, function (index, value) {
                    dados = `
                        <tr data-id='${value.id_code}' class="text-center">
                            <td>${value.fantasy_name}</td>
                            <td>
                    `;

                    if (companyType[value.type] === 'physical person') {
                        if (companyStatusTranslated[value.bank_document_status] == 'Recusado') {
                            dados += `<span class='badge ${companyStatus[value.bank_document_status]}'>${companyStatusTranslated[value.bank_document_status]}</span>`;
                        } else if (companyStatusTranslated[value.bank_document_status] == 'Aprovado' && value.capture_transaction_enabled) {
                            dados += `<span class='badge ${companyStatus[value.bank_document_status]}'>${companyStatusTranslated[value.bank_document_status]}</span>`;
                        } else {
                            dados += `<span class='badge ${companyStatus['analyzing']}'>${companyStatusTranslated['analyzing']}</span>`;
                        }
                        // dados += `<span class='badge ${companyStatus[value.bank_document_status]}'>${companyStatusTranslated[value.bank_document_status]}</span>`;
                    } else {
                        if (companyStatusTranslated[statusCompanyJuridicalPerson(value)] == 'Recusado') {
                            dados += `<span class='badge ${companyStatus[statusCompanyJuridicalPerson(value)]}'>${companyStatusTranslated[statusCompanyJuridicalPerson(value)]}</span>`;
                        } else if (companyStatusTranslated[statusCompanyJuridicalPerson(value)] == 'Aprovado' && value.capture_transaction_enabled) {
                            dados += `<span class='badge ${companyStatus[statusCompanyJuridicalPerson(value)]}'>${companyStatusTranslated[statusCompanyJuridicalPerson(value)]}</span>`;
                        } else {
                            dados += `<span class='badge ${companyStatus['analyzing']}'>${companyStatusTranslated['analyzing']}</span>`;
                        }
                    }

                    dados += `
                            </td>
                                <td><a title='Editar' href="/companies/${value.id_code}/edit?type=${value.type}" class='edit-company' data-company='${value.id_code}' role='button'><i class='material-icons gradiente'>edit</i></a></td>
                                <td><a title='Excluir' class='pointer delete-company' company='${value.id_code}' data-toggle='modal' data-target='#modal-delete' role='button'><i class='material-icons gradient'>delete</i></a></td>
                            </tr>
                        `;
                    $('#companies_table').addClass('table-striped');
                    $("#companies_table_data").append(dados);
                });

                Sortable.create(document.getElementById('companies_table_data'), {
                    onEnd: function (evt) {
                        var orderCompanies = [];
                        var listCompanies = $('#companies_table_data');
                        $(listCompanies).find("tr").each(function (index, tr) {
                            orderCompanies.push($(tr).data('id'));
                        });

                        $.ajax({
                            method: "POST",
                            url: "/api/companies/updateorder",
                            dataType: "json",
                            data: {order: orderCompanies},
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
                            }

                        });
                    }
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
        });
    }
});
