var companyStatus = {
    pending: 'badge badge-primary',
    analyzing: 'badge badge-pending',
    approved: 'badge badge-success',
    refused: 'badge badge-danger',
};

var companyStatusTranslated = {
    pending: 'Pendente',
    analyzing: 'Em análise',
    approved: 'Aprovado',
    refused: 'Recusado',
};

var initForm = null;
let companyIdCode = null;
let gatewayTax = {
    'plan-2': 6.9,
    'plan-15': 6.5,
    'plan-30': 5.9
}


let userIdCode = '';
$(document).ready(function () {

    initForm = function () {

        var encodedId = extractIdFromPathName();

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
                if (response.company.country === 'usa') {
                    $('#rounting_number').val(response.company.bank).trigger('input');
                    $('#account_routing_number').val(response.company.account);
                    $('#company_update_bank_form').hide();
                    $('#company_bank_routing_number_form').show();
                } else {
                    $.each(response.banks, function (index, value) {
                        $("#bank").append(`<option value="${value.code}">${value.code} - ${value.name}</option>`)
                    });
                    $("#bank").val(response.company.bank);
                    $("#agency").val(response.company.agency);
                    $("#agency_digit").val(response.company.agency_digit);
                    $("#account").val(response.company.account);
                    $("#account_digit").val(response.company.account_digit);
                    $('#account_type').val(response.company.account_type);
                }
                userIdCode = response.company.user_code;
                let company = response.company;
                if (response.company.capture_transaction_enabled) {
                    companyIdCode = company.id_code;

                    $("#tax-payment").val(company.gateway_tax + '%')

                    $(".select-gateway-tax").html('');

                    $(".select-gateway-tax").append(`
                        <select id="gateway-release-payment" class="form-control">
                            <option value="plan-2" ${company.gateway_tax == 6.9 ? 'selected' : ''}>Após postagem de rastreio válida (taxa de 6.9%)</option>
                            <option value="plan-15" ${company.gateway_tax == 6.5 ? 'selected' : ''}>15 dias (taxa de 6.5%)</option>
                            <option value="plan-30" ${company.gateway_tax == 5.9 ? 'selected' : ''}>30 dias (taxa de 5.9%)</option>
                        </select>
                    `);
                    $('#tab_tax_gateways .gateway-tax').removeAttr('hidden');
                    $('#tab_tax_gateways .cielo-tax').hide();
                } else {

                    $('#credit-card-tax-cielo').val(company.credit_card_tax + '%');
                    $('#boleto-tax-cielo').val(company.boleto_tax + '%');
                    $("#credit-card-release-cielo").val('plan-' + company.credit_card_release_money);
                    $("#boleto-release-cielo").val('plan-' + company.boleto_release_money);

                    $('#tab_tax_gateways .cielo-tax').removeAttr('hidden');
                    $('#tab_tax_gateways .gateway-tax').hide();
                }

                if (response.company.country === 'brazil') {
                    $('#agency').attr('maxlength', '4');
                } else {
                    $('#agency').attr('maxlength', '20');
                }

                $("#td-status-document-person-fisic").html('');
                $("#td-status-document-person-fisic").append(`<span class='badge ${companyStatus[response.company.document_status]}'>${companyStatusTranslated[response.company.document_status]}</span>`);

                if (response.company.document_status === 'pending' || response.company.document_status === 'refused') {
                    $("#text-alert-documents-cpf").show();
                    $(".details-document-person-fisic").show();
                } else if (response.company.document_status === 'analyzing' || response.company.document_status === 'approved') {
                    $("#text-alert-documents-cpf").hide();
                    $(".details-document-person-fisic").hide();
                }

                $(".details-document-person-fisic").on('click', function () {
                    Dropzone.forElement('#dropzoneDocumentsFisicPerson').removeAllFiles(true);
                    getDocuments(encodedId);
                });

                if (response.company.has_project) {
                    $('#active_flag').attr('disabled', true);
                    $("#active_flag").css("cursor", "not-allowed");
                    $(".switch").css("cursor", "not-allowed");
                    $(".slider").css("cursor", "not-allowed");
                    $(".switch").css("opacity", "0.5");
                }

                $('#active_flag').val(response.company.active_flag);

                if (response.company.active_flag) {
                    $("#active_flag").attr('checked', 'checked');
                } else {
                    $("#active_flag").attr('checked', false);
                }


                // update tax payment after change select input
                $("#gateway-release-payment").on("change", function () {
                    $("#tax-payment").val(gatewayTax[$(this).val()] + '%');
                })

                $("#tab_tax_gateways #installment-tax").html(company.installment_tax).attr('disabled', 'disabled');
                setValuesHtml(company)
            }
        });
    };

    initForm();

    $("#update_payment_tax_cnpj").unbind('click');
    $("#update_payment_tax_cnpj").on('click', function () {
        loadingOnScreen();

        $.ajax({
            method: "POST",
            url: `/api/companies/${companyIdCode}/updatetax`,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                gateway_release_payment: $("#gateway-release-payment").val(),
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
                $("#tax-payment").val(response.data.new_gateway_tax + '%');
            }
        });
    });

    function setValuesHtml(data) {

        if (data.antecipation_enabled_flag) {
            $('.info-antecipation-tax').show();
            $('#tab_tax_gateways  #label-antecipation-tax').text(data.antecipation_tax + '%.');
        } else {
            $('.title-antecipation-tax').hide();
            $('.form-antecipation-tax').hide();
        }

        $("#tab_tax_gateways  #transaction-tax").html(data.transaction_rate).attr('disabled', 'disabled');
    }

    $("#update_bank_data").on("click", function (event) {
        event.preventDefault();
        var form_data = new FormData(document.getElementById('company_update_bank_form'));
        loadingOnScreen();
        var encodedId = extractIdFromPathName();

        if (!$('#active_flag').is(':checked')) {
            form_data.append('active_flag', '0');
        }

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
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                loadingOnScreenRemove();
                initForm();
                alertCustom('success', response.message);
            }
        });
    });

    $('.check').on('change', function () {
        if ($(this).is(':checked')) {
            $(this).val(1);
        } else {
            $(this).val(0);
        }
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

    $('#agency').on('input', function () {
        $(this).val($(this).val().replace(/[^a-z0-9]/gi, ''));
    });

    $('#account').on('input', function () {
        $(this).val($(this).val().replace(/[^a-z0-9]/gi, ''));
    });

    function htmlTable(dataTable) {
        $("#loaderLine").remove();
        var dados = '';
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
        var codeId = extractIdFromPathName();

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
                htmlTableDoc(response.data);
                initForm();
            }
        });

        ajaxVerifyDocumentPending();

        function htmlTableDoc(dataTable) {
            document.querySelector('#loaderLine').remove();
            var dados = '';
            if (dataTable.length == 0) {
                document.querySelector("#table-body-documents-person-fisic").innerHTML = '<span>Nenhum documento enviado</span>';
            } else {
                document.querySelector("#table-body-documents-person-fisic").innerHTML = '';
                document.querySelector("#document-person-fisic-refused-motived").innerHTML = '';
                for (var value of dataTable) {
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

