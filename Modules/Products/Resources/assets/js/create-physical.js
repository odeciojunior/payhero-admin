$(document).ready(function () {
    getCompaniesAndProjects().done( function (data){
        $('.site-navbar .sirius-select-container').addClass('disabled');
    });
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

    dropifyOptions = {
        messages: {
            'default': 'Arraste e solte uma imagem ou ',
            'replace': 'Arraste e solte uma imagem ou selecione um arquivo',
            'remove': 'Remover',
            'error': ''
        },
        error: {
            'fileSize': 'O tamanho máximo do arquivo deve ser {{ value }}.',
            'minWidth': 'A imagem deve ter largura maior que 651px.',
            'maxWidth': 'A imagem deve ter largura menor que 651px.',
            'minHeight': 'A imagem deve ter altura maior que 651px.',
            'maxHeight': 'A imagem deve ter altura menor que 651px.',
            'fileExtension': 'A imagem deve ser algum dos formatos permitidos. ({{ value }}).'
        },
        tpl: {
            message: '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">selecione um arquivo</span></p></div>',
            clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
        },
        imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg'],
    };

    $('#product_photo').dropify(dropifyOptions);

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
                    localStorage.removeItem('page')
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
