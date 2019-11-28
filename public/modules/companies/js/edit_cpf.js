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

let documentType = '';

$(document).ready(function () {

    initForm();

    function initForm() {
        let encodedId = extractIdFromPathName();

        $.ajax({
            method: "GET",
            url: "/api/companies/" + encodedId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {

                $.each(response.banks, function (index, value) {
                    $("#bank").append("<option value='" + value.code + "'>" + value.name + "</option>")
                });

                $("#bank").val(response.company.bank);
                $("#agency").val(response.company.agency);
                $("#agency_digit").val(response.company.agency_digit);
                $("#account").val(response.company.account);
                $("#account_digit").val(response.company.account_digit);

                $("#status-document-fisic").addClass(companyStatus[response.company.document_status]).html(companyStatusTranslated[response.company.document_status]);

                $(".details-document-person-fisic").on('click', function () {

                    Dropzone.forElement('#dropzoneDocumentsFisicPerson').removeAllFiles(true);

                    getDocuments(encodedId);

                });

            }
        });
    }

    $("#update_bank_data").on("click", function (event) {
        event.preventDefault();
        let form_data = new FormData(document.getElementById('company_update_bank_form'));
        loadingOnScreen();

        var encodedId = extractIdFromPathName();

        $.ajax({
            method: "POST",
            url: "/api/companies/" + encodedId,
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });

    function getDocuments(encodedId) {

        loadOnTable('#table-body-documents-person-fisic', '#table-documents-person-fisic');

        $.ajax({
            method: 'POST',
            url: `/api/companies/${encodedId}/getdocuments`,
            data: {
                document_type: $("#document_type").val()
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
                $("#loaderLine").remove();

            },
            success: function success(response) {
                $("#loaderLine").remove();
                htmlTable(response.data);
                $("#modal-document-person-fisic").modal('show');
                $("#company_id").val(encodedId);
            },
        });
    }

    function htmlTable(dataTable) {
        $("#loaderLine").remove();
        let dados = '';
        if (dataTable.length == 0) {
            $("#table-body-documents-person-fisic").append('<tr><td class="text-center" colspan="4">Nenhum documento enviado</td></tr>')
        } else {
            $("#document-person-fisic-refused-motived").html('');
            $("#table-body-documents-person-fisic").html('');
            $.each(dataTable, function (index, value) {
                dados = `<tr>
                        <td class='text-center'>${value.date}</td>
                        <td class='text-center' style='cursor: pointer;'>
                            <span class='badge ${companyStatus[value.status]}'>
                                    ${companyStatusTranslated[value.status]}</td>
                               </span>
                        </td>`;

                if (value.refused_reason != '' && value.refused_reason != null) {
                    dados += `
                                <td class='text-center' style='color:red;'>${value.refused_reason}</td>
                             `;

                } else {
                    dados += `
                                <td class='text-center' style='color:red;'></td>
                             `;
                }
                dados += `<td class='text-center'>
                            <a href='${value.document_url}' target='_blank' role='button' class='detalhes_document'><i class='material-icons gradient'>remove_red_eye</i></a>
                        </td>
                        
                    </tr>`;
                $("#table-body-documents-person-fisic").append(dados);

            });
        }
    }

});

Dropzone.autoDiscover = false;

const myDropzone = new Dropzone('#dropzoneDocumentsFisicPerson', {
    headers: {
        'Authorization': $('meta[name="access-token"]').attr('content'),
        'Accept': 'application/json',
    },
    paramName: "file",
    maxFilesize: 2,
    url: '/api/companies/uploaddocuments',
    acceptedFiles: ".jpg,.jpeg,.doc,.pdf,.png",
    previewsContainer: ".dropzone-previews",
    success: function success(file, response) {
        alertCustom('success', response.message);

        if (file.previewElement) {
            return file.previewElement.classList.add('dz-success');
        }

    }, error: function (file, response) {

        if (response.search('Max filesize') > 0) {
            response = 'O documento é muito grande. Tamanho maximo: 2mb.';
        } else if (response.search('upload files of this type') > 0) {
            response = 'O documento deve estar em um dos seguintes formatos: jpeg, jpg, png.';
        }

        errorAjaxResponse(response);
        myDropzone.removeFile(file);
    }, complete: function () {
        loadOnTable('#table-body-documents-person-fisic', '#table-documents-person-fisic');
        let codeId = extractIdFromPathName();

        $.ajax({
            method: 'POST',
            url: `/api/companies/${codeId}/getdocuments`,
            data: {
                document_type: document.querySelector('#document_type').value

            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
                $("#loaderLine").remove();

            },
            success: function success(response) {
                console.log(response.data);
                htmlTableDoc(response.data);

            }
        });

        ajaxVerifyDocumentPending();

        function htmlTableDoc(dataTable) {
            document.querySelector('#loaderLine').remove();
            let dados = '';
            if (dataTable.length == 0) {
                document.querySelector("#table-body-documents-person-fisic").innerHTML = '<span>Nenhum documento enviado</span>';
            } else {
                document.querySelector("#table-body-documents-person-fisic").innerHTML = '';
                document.querySelector("#document-person-fisic-refused-motived").innerHTML = '';
                for (let value  of dataTable) {
                    dados += `<tr>
                        <td class='text-center'>${value.date}</td>
                        <td class='text-center' style='cursor: pointer;'>
                            <span class='badge ${companyStatus[value.status]}'>
                                    ${companyStatusTranslated[value.status]}
                               </span>
                        </td>`;

                    if (value.refused_reason != '' && value.refused_reason != null) {
                        dados += `
                                <td class='text-center' style='color:red;'>${value.refused_reason}</td>
                             `;

                    } else {
                        dados += `
                                <td class='text-center' style='color:red;'></td>
                             `;
                    }
                    dados += `<td class='text-center'>
                            <a href='${value.document_url}' target='_blank' role='button' class='detalhes_document'>
                            <i class='material-icons gradient'>remove_red_eye</i></a>
                        </td>
                        
                    </tr>`;

                }

                document.querySelector("#table-body-documents-person-fisic").innerHTML = dados;

            }
        }
    }

});

