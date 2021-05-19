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

            console.log("error", "O campo Nome é obrigatório");
            alertCustom("error", "O campo Nome é obrigatório");
            ver = false;
            $('#name').focus();
        }
        if ($.trim($("#description").val()) === '') {

            console.log("error", "O campo Descrição é obrigatório");
            alertCustom("error", "O campo Descrição é obrigatório");
            ver = false;
            $("#description").focus();
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
        event.preventDefault();

        if (verify()) {
            loadOnAny('.page', false);
            let myForm = document.getElementById('my-form-add-product');
            let formData = new FormData(myForm);

            formData.append('type_enum', 'physical');
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

    // Produto Fisico
    $('#height').on('focusin', function () { $('#caixinha-img')[0].src = '/modules/global/img/svg/caixinha-altura.svg' });
    $('#width').on('focusin', function () { $('#caixinha-img')[0].src = '/modules/global/img/svg/caixinha-largura.svg' });
    $('#length').on('focusin', function () { $('#caixinha-img')[0].src = '/modules/global/img/svg/caixinha-comprimento.svg' });
    $('#weight').on('focus', function () { $('#caixinha-img')[0].src = '/modules/global/img/svg/caixinha-peso.svg' });
    $('#height, #width, #length, #weight').on('focusout', function () { $('#caixinha-img')[0].src = '/modules/global/img/svg/caixinha.svg' });

});
