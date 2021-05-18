$(document).ready(function () {
    /**
     * Helper Functions
     */

    loadingOnScreen();
    $('.page').show();
    loadingOnScreenRemove();

    function verify() {
        let ver = true;
        if ($.trim($('#name').val()) === '') {
            alertCustom("error", "O campo Nome é obrigatório");
            ver = false;
            $('#name').focus();
        }
        if ($.trim($("#description").val()) === '') {
            alertCustom("error", "O campo Descrição é obrigatório");
            ver = false;
            $("#description").focus();
        }
        if ($.trim($('#digital_product_url').val()) === '') {
            alertCustom('error', 'Selecione o produto digital');
            ver = false;
            $('#digital_product_url').focus();
        }
        if ($.trim($('#url_expiration_time').val()) === '') {
            alertCustom('error', 'Preencha o campo Tempo de expiração da url');
            ver = false;
            $("#url_expiration_time").focus();
        }
        return ver;
    }

    $('#product_photo').dropify({
        messages: {
            'default': 'Arraste e solte ou clique para adicionar um arquivo',
            'replace': 'Arraste e solte ou clique para substituir',
        },
    });

    $("#my-form-add-product").submit(function (event) {
        if ($('#digital_product_url').val() == '') {
            alertCustom('error', 'Selecione o produto digital');
            return false;
        }
        if ($('#url_expiration_time').val() == '') {
            alertCustom('error', 'Preencha o campo Tempo de expiração da url');
            return false;
        }
        event.preventDefault();

        if (verify()) {
            loadOnAny('.page', false);
            let myForm = document.getElementById('my-form-add-product');
            let formData = new FormData(myForm);

            formData.append('type_enum', 'digital');
            $.ajax({
                method: 'POST',
                url: "/api/products",
                processData: false,
                cache: false,
                contentType: false,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: formData,
                error: function (response) {
                    loadOnAny('.page', true);

                    errorAjaxResponse(response);

                }, success: function (response) {
                    loadOnAny('.page', true);

                    alertCustom('success', response.message);
                    window.location = "/products";
                }
            });
        }

    });

    $('#url_expiration_time').mask('0#');

    /* Upload Digital Product Input */
    document.getElementById('digital_product').addEventListener("change", function () {
        productName = this.value.split('\\')[2] || '';
        document.getElementById('file_return').innerHTML = productName;
    });
});
