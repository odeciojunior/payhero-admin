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
            'default': 'Arraste e solte uma imagem ou ',
            'replace': 'Arraste e solte uma imagem ou selecione um arquivo',
            'remove': 'Remover',
            'error': ''
        },
        error: {
            'fileSize': 'O tamanho máximo do arquivo deve ser {{ value }}.',
            'minWidth': 'A imagem deve ter largura maior que 650px.',
            'maxWidth': 'A imagem deve ter largura menor que 650px.',
            'minHeight': 'A imagem deve ter altura maior que 650px.',
            'maxHeight': 'A imagem deve ter altura menor que 650px.',
            'imageFormat': 'A imagem deve ser algum dos formatos permitidos. Apenas ({{ value }}).'
        },
        tpl: {
            message: '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">selecione um arquivo</span></p></div>',
        },
        imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg'],
    });

    $("#my-form-add-product").submit(function (event) {
        event.preventDefault();

        if (verify()) {
            $("button[type='submit']").prop('disabled', true);

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
                    $("button[type='submit']").prop('disabled', false);

                }, success: function (response) {
                    loadOnAny('.page', true);

                    alertCustom('success', response.message);
                    window.location = "/products";
                }
            });
        }
    });

    // Produto Fisico
    alterarCaixinha('#height', 'caixinha-altura');
    alterarCaixinha('#width', 'caixinha-largura');
    alterarCaixinha('#length', 'caixinha-comprimento');
    alterarCaixinha('#weight', 'caixinha-peso');

    function alterarCaixinha(input, newValue) {
        $(input).on('focus', function () { $('#caixinha-img')[0].src = $('#caixinha-img')[0].src.replace('caixinha', newValue) });
        $(input).on('focusout', function () { $('#caixinha-img')[0].src = $('#caixinha-img')[0].src.replace(newValue, 'caixinha') });
    }

});
