$(document).ready(function () {
    updateForm();
    $("#country").on("change", function () {
        // updateForm();
        let countryVal = $(this).val();
        if (countryVal == 'usa') {
            $('#fantay_name_label').html('Legal Business Name');
            $('#company_document_label').html('Company Document');
            $('#fantasy_name').attr('placeholder', 'Legal Business Name');
            $('#brazil_company_document').attr('placeholder', 'Document');
        } else {
            $('#fantay_name_label').html('Nome da empresa');
            $('#company_document_label').html('CPF/CNPJ');
            $('#fantasy_name').attr('placeholder', 'Nome da empresa');
            $('#brazil_company_document').attr('placeholder', 'Cpf/Cnpj');
        }
    });

    function updateForm() {
        // var options = {
        //     onKeyPress: function onKeyPress(identificatioNumber, e, field, options) {
        //         var masks = ['000.000.000-00', '00.000.000/0000-00'];
        //         var mask = identificatioNumber.length > 14 ? masks[1] : masks[0];
        //         $('#brazil_company_document').mask(mask, options);
        //     }
        // };
        var options = {
            onKeyPress: function (cpf, ev, el, op) {
                var masks = ['000.000.000-000', '00.000.000/0000-00'];
                $('#brazil_company_document').mask((cpf.length > 14) ? masks[1] : masks[0], op);
            }
        }
        $('#brazil_company_document').length > 11 ? $('#brazil_company_document').mask('00.000.000/0000-00', options) : $('#brazil_company_document').mask('000.000.000-00#', options);
    }

    $("#create_form").on("submit", function (event) {
        event.preventDefault();

        $.ajax({
            method: "POST",
            url: "/api/companies",
            dataType: "json",
            data: $("#create_form").serialize(),
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: function (response) {
                errorAjaxResponse(response);
            },
            success: function success(response) {
                alertCustom('success', response.message);
                window.location.replace('/companies/' + response.idEncoded + '/edit ');
            }
        });
    });
});
