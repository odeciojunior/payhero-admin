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

let companyTypeDocument = {
    bank_document_status: 'Comprovante de extrato bancário',
    address_document_status: 'Comprovante de endereço',
    contract_document_status: 'Comprovante de contrato social'
};

$(document).ready(function () {
    //On Ready
    //Clear all content of form
    let redirectBackLink = $("#redirect_back_link");
    let companyUpdateForm = $("#company_update_form");
    let companyBankUpdateForm = $("#company_bank_update_form");
    initLinks();
    initForm();

    $(document).on("blur", '#routing_number', function () {
        $.ajax({
            method: "GET",
            url: "https://www.routingnumbers.info/api/data.json?rn=" + $("#routing_number").val(),
            success: function success(data) {
                if (data.message === 'OK') {
                    $("#bank").val(data.customer_name);
                } else {
                    alertCustom('error', data.message);
                    $('#routing_number').focus();
                }
            }
        });
    });

    $("#zip_code").on("input", function () {
        var zip_code = $('#zip_code').val().replace(/[^0-9]/g, '');
        if (zip_code.length !== 8) return false;
        $.ajax({
            url: "https://viacep.com.br/ws/" + zip_code + "/json/",
            type: "GET",
            cache: false,
            async: false,
            success: function success(response) {
                if (response.localidade) {
                    $("#city").val(unescape(response.localidade));
                }
                if (response.bairro) {
                    $("#neighborhood").val(unescape(response.bairro));
                }
                if (response.uf) {
                    $("#state").val(unescape(response.uf));
                }
                if (response.logradouro) {
                    $("#street").val(unescape(response.logradouro));
                }
            }
        });
    });

    //Functions
    function initLinks() {
        let encodedId = extractIdFromPathName();
        let origin = $(location).attr('origin');
        let path = origin + '/companies';
        let apiPath = origin + '/api/companies';
        redirectBackLink.attr('href', path);
        companyUpdateForm.attr('action', (apiPath + "/" + encodedId));
        companyBankUpdateForm.attr('action', (apiPath + "/" + encodedId));
    }

    function initForm() {
        //Get CompanyId from path
        let encodedId = extractIdFromPathName();
        //Get Company data from laravel api
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
                console.log(response);
                let company = response.company;
                let lists = {bank: response.banks};
                let functions = {bank: selectItemsFunction};
                fillAllFormInputsWithModel('company_update_form', company);
                fillAllFormInputsWithModel('company_bank_update_form', company, lists, functions);
                $("#company_id").attr('value', company.id_code);

                $("#td-bank-status").append(`
                    <span class='badge ${companyStatus[company.bank_document_status]}'>
                        ${companyStatusTranslated[company.bank_document_status]} 
                    </span>
                    `);
                $("#td-address-status").append(`
                    <span class='badge ${companyStatus[company.address_document_status]}'>
                        ${companyStatusTranslated[company.address_document_status]} 
                    </span>
                    `);
                $("#td-contract-status").append(`
                    <span class='badge ${companyStatus[company.contract_document_status]}'>
                        ${companyStatusTranslated[company.contract_document_status]} 
                    </span>
                    `);
                configSubmits();
                verifyDocuments(company);
                // getRefusedDocuments(response.company.refusedDocuments);
                verifyCompanyAddress(company);
                openDocument();
                //mascara cnpj
                var optionsCompanyDocument = {
                    onKeyPress: function (cpf, ev, el, op) {
                        var masks = ['000.000.000-000', '00.000.000/0000-00'];
                        $('#company_document').mask((cpf.length > 14) ? masks[1] : masks[0], op);
                    }
                }
                $('#company_document').length > 11 ? $('#company_document').mask('00.000.000/0000-00', optionsCompanyDocument) : $('#company_document').mask('000.000.000-00#', optionsCompanyDocument);

                $("#support_telephone").mask("(00) 0000-00009");

                $(".details-document-person-juridic").on('click', function () {
                    $("#document-type").val('');
                    $("#modal-title-document-person-juridic").html('');

                    $("#document-type").val($(this).data('document'));
                    $("#modal-title-document-person-juridic").html(`${companyTypeDocument[$(this).data('document')]}`);

                    $("#modal-document-person-juridic").modal('show');

                    Dropzone.forElement('#dropzoneDocumentsJuridicPerson').removeAllFiles(true);

                    getDocuments(encodedId);

                });
            }
        });
    }

    //Config Submit
    function configSubmits() {
        companyUpdateForm.on("submit", function (event) {
            event.preventDefault();
            let form_data = new FormData(document.getElementById('company_update_form'));
            loadingOnScreen();
            $.ajax({
                method: "POST",
                url: companyUpdateForm.attr('action'),
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
                    alertCustom('success', response.message);
                    loadingOnScreenRemove();
                }
            });
        });
        companyBankUpdateForm.on("submit", function (event) {
            event.preventDefault();
            let form_data = new FormData(document.getElementById('company_bank_update_form'));
            loadingOnScreen();
            $.ajax({
                method: "POST",
                url: companyBankUpdateForm.attr('action'),
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
    }

    function getStatusBadge(bankDocumentStatus) {
        let badge = null;
        switch (bankDocumentStatus) {
            case 1://'pending':
                badge = 'primary';
                break;
            case 2://'analyzing':
                badge = 'pendente';
                break;
            case 3://'approved':
                badge = 'success';
                break;
            case 4://'refused':
                badge = 'danger';
                break;
            default:
                badge = '';
                break;
        }

        return badge;
    }

    function selectItemsFunction(item) {
        return {value: item.code, text: (item.code + ' - ' + item.name)};
    }

    function openDocument() {
        $(".document-url").on("click", function (e) {
            e.preventDefault();
            let documentUrl = $(this).attr('href');
            loadingOnScreen();
            $.ajax({
                method: "POST",
                url: '/api/companies/opendocument',
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {document_url: documentUrl},
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: function success(response) {
                    loadingOnScreenRemove();
                    window.open(response.data, '_blank');
                }
            });
        });
    }

    //vefica se os documentos da empresa estão aprovados e desabilita todos os inputs
    function verifyDocuments(company) {
        if (company.address_document_status == 3 && company.bank_document_status == 3 && company.contract_document_status == 3) {
            $(".form-basic-informations :input[type=text]").attr("disabled", true);
            $(".dz-hidden-input").prop("disabled", true);
            $('#dropzoneDocuments').css({
                'cursor': 'not-allowed',
            });
            $('.text-dropzone').css({
                'cursor': 'not-allowed',
            });
        }
    }

    function verifyCompanyAddress(company) {
        if (company.zip_code == '' || company.street == '' || company.number == '' || company.neighborhood == '' || company.state == '' || company.city == '' || company.country == '') {
            $('#row_dropzone_documents').hide();
            $('#div_address_pending').show();
        } else {
            $('#row_dropzone_documents').show();
            $('#div_address_pending').hide();
        }
    }

    function getRefusedDocuments(refusedDocuments) {
        $.each(refusedDocuments, function (index, value) {
            $('#div_documents_refused').append('<div class="alert alert-danger text-center my-20">' +
                '<p>O ' + value.type_translated + ' que foi enviado na data: ' + value.date + ' foi reprovado pelo motivo abaixo: <br><a href="' + value.document_url + '" class="document-url">Visualizar documento</a> <br> <b>' + value.refused_reason + '</b> <br></p>' +
                '</div>');
        });
    }

    function htmlTitleModal() {
        let nameDocument = $(".details-document-person-juridic").data('document');
    }

    function getDocuments(encodedId) {
        loadOnTable('#table-body-document-person-juridic', '#table-document-person-juridic');
        $.ajax({
            method: 'POST',
            url: `/api/companies/${encodedId}/getdocuments`,
            data: {
                document_type: $("#document-type").val()
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
                $("#company-id").val(encodedId);
                $("#modal-document-person-fisic").modal('show');
            },
        });
    }

    function htmlTable(dataTable) {
        $("#loaderLine").remove();

        let dados = '';
        if (dataTable.length == 0) {
            $("#table-body-document-person-juridic").append('<span>Nenhum documento enviado</span>');
        } else {
            $("#table-body-document-person-juridic").html('');
            $.each(dataTable, function (index, value) {
                dados = `
                    <tr>
                        <td class='text-center'>${value.date}</td>
                        <td class='text-center' style='cursor: pointer;'>
                            <span class='badge ${companyStatus[value.status]}'>
                                ${companyStatusTranslated[value.status]}
                            </span>
                        </td>`;

                if (value.refused_reason != '' && value.refused_reason != null) {
                    dados += `<td class='text-center' style='color: red;'>${value.refused_reason}</td>`;
                } else {
                    dados += `<td class='text-center' style='color:red;'></td>`;
                }

                dados += `
                    <td class='text-center'>
                        <a href='${value.document_url}' target='_blank' role='button' class='detalhes_document'><i class='material-icons gradient'>remove_red_eye</i></a>
                    </td>
                    </tr>
                `;
                $("#table-body-document-person-juridic").append(dados);

            });
        }

    }

});

Dropzone.autoDiscover = false;

const myDropzone = new Dropzone('#dropzoneDocumentsJuridicPerson', {
    headers: {
        'Authorization': $('meta[name="access-token"]').attr('content'),
        'Accept': 'application/json',
    },
    paramName: "file",
    maxFilesize: 2,
    url: '/api/companies/uploaddocuments',
    acceptedFiles: ".jpg,.jpeg,.doc,.pdf,.png",
    previewsContainer: ".dropzone-previews",
    success: function (file, response) {
        alertCustom('success', response.message);

        if (file.previewElement) {
            return file.previewElement.classList.add('dz-success');
        }

    }, error: function (file, response) {
        console.log(response);
        if (response.search('Max filesize') > 0) {
            response = 'O documento é muito grande. Tamanho maximo: 2mb.';
        } else if (response.search('upload files of this type') > 0) {
            response = 'O documento deve estar em um dos seguintes formatos: jpeg, jpg, png.';
        } else {
            errorAjaxResponse(response);
        }

        errorAjaxResponse(response);
        myDropzone.removeFile(file);

    }, complete: function () {
        loadOnTable('#table-body-document-person-juridic', '#table-document-person-juridic');
        let codeId = extractIdFromPathName();

        $.ajax({
            method: 'POST',
            url: `/api/companies/${codeId}/getdocuments`,
            data: {
                document_type: document.querySelector('#document-type').value
            },
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
                $("#loaderLine").remove();
            }, success: function (response) {
                htmlTableDocumentJuridic(response.data);
            }
        });

        function htmlTableDocumentJuridic(dataTable) {
            document.querySelector("#loaderLine").remove();
            let dados = '';
            if (dataTable.length == 0) {
                document.querySelector('#table-body-document-person-juridic').innerHTML = '<span>Nenhum documento enviado</span>'
            } else {
                document.querySelector('#table-body-document-person-juridic').innerHTML = '';
                for (let value of dataTable) {
                    dados += `<tr>
                            <td class='text-center'>${value.date}</td>
                            <td class='text-center' style='cursor: pointer;'>
                                <span class='badge ${companyStatus[value.status]}'>
                                    ${companyStatusTranslated[value.status]}
                                </span>
                            </td>`;
                    if (value.refused_reason != '' && value.refused_reason != null) {
                        dados += `<td class='text-center' style='color:red;'>${value.refused_reason}</td>`;
                    } else {
                        dados += `<td class='text-center' style='color:red;'></td>`;
                    }

                    dados += `<td class='text-center'>
                            <a href='${value.document_url}' target='_blank' role='button' class='detalhes_document'>
                            <i class='material-icons gradient'>remove_red_eye</i></a>
                        </td>
                        
                    </tr>`;
                }

                document.querySelector('#table-body-document-person-juridic').innerHTML = dados;
            }
        }

    }
});


