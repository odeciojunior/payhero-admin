$(document).ready(function () {

    loadingOnScreen();
    $.ajax({
        url: '/api/projects/create',
        dataType: "json",
        headers: {
            'Authorization': $('meta[name="access-token"]').attr('content'),
            'Accept': 'application/json',
        },
        error: (response) => {
            $('.page').show()

            loadingOnScreenRemove();
            errorAjaxResponse(response);
        },
        success: (response) => {
            if (!isEmpty(response)) {
                let countApproved = 0;
                $.each(response, (key, company) => {
                    let dataSelect = '';
                    if (company.capture_transaction_enabled) {
                        countApproved = countApproved + 1;
                        if (company.company_type == 'physical person') {
                            if (company.user_address_document_status != 'approved' || company.user_personal_document_status != 'approved') {
                                dataSelect = `<option value=${company.id} ${(company.active_flag == 0 ? 'disabled' : '')} disabled>${company.name}</option>`;
                            } else {
                                dataSelect = `<option value=${company.id} ${(company.active_flag == 0 ? 'disabled' : '')}>${company.name}</option>`;
                            }
                        } else if (company.company_type == 'juridical person') {
                            if (company.company_document_status != 'approved' || company.user_address_document_status != 'approved' || company.user_personal_document_status != 'approved') {
                                dataSelect = `<option value=${company.id} ${(company.active_flag == 0 ? 'disabled' : '')} disabled>${company.name}</option>`;
                            } else {
                                dataSelect = `<option value=${company.id} ${(company.active_flag == 0 ? 'disabled' : '')}>${company.name}</option>`;
                            }
                        }
                    }
                    $('#company').append(dataSelect);
                });

                if (countApproved == 0){
                    $('.page-content').hide();
                    $('#empty-companies-error').show();
                }else{
                    $('.page-content').show();
                    $('#empty-companies-error').hide();
                }
            } else {
                $('#card-project').hide();
                $('.content-error').show();
            }

            $('.page').show()
            loadingOnScreenRemove();
        }
    });

    function verifyFields() {
        if ($('#name').val().length === 0) {
            alertCustom('error', 'É obrigatório preencher o campo Nome!');
            return true;
        } else if ($("#company option:selected").val().length === 0) {
            alertCustom('error', 'É obrigatório selecionar uma empresa!');
            return true;

        } else {
            return false;
        }
    }

    let btnSave = $('#btn-save')
    btnSave.on('click', () => {
        if (verifyFields()) {
            return false;
        } else {
            btnSave.prop("disabled", true);
            loadingOnScreen();
            let formData = new FormData(document.querySelector('#form-create-project'));
            $.ajax({
                method: 'post',
                url: '/api/projects',
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                error: (response) => {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                    btnSave.prop("disabled", false);
                },
                success: (response) => {
                    loadingOnScreenRemove();
                    alertCustom('success', 'Projeto salvo com sucesso!');
                    window.location = "/projects"
                }
            });
        }
    });

    var p = $("#product_photo");
    $("#project-photo").on("change", function () {

        var imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("project-photo").files[0]);

        imageReader.onload = function (oFREvent) {
            p.attr('src', oFREvent.target.result).fadeIn();

            p.on('load', function () {

                var img = document.getElementById('product_photo');
                var x1, x2, y1, y2;

                if (img.naturalWidth > img.naturalHeight) {
                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                    x2 = x1 + (y2 - y1);
                } else {
                    if (img.naturalWidth < img.naturalHeight) {
                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                        y2 = y1 + (x2 - x1);
                    } else {
                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                        x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                    }
                }

                $('input[name="photo_x1"]').val(x1);
                $('input[name="photo_y1"]').val(y1);
                $('input[name="photo_w"]').val(x2 - x1);
                $('input[name="photo_h"]').val(y2 - y1);

                $('#product_photo').imgAreaSelect({
                    x1: x1, y1: y1, x2: x2, y2: y2,
                    aspectRatio: '1:1',
                    handles: true,
                    imageHeight: this.naturalHeight,
                    imageWidth: this.naturalWidth,
                    onSelectEnd: function onSelectEnd(img, selection) {
                        $('input[name="photo_x1"]').val(selection.x1);
                        $('input[name="photo_y1"]').val(selection.y1);
                        $('input[name="photo_w"]').val(selection.width);
                        $('input[name="photo_h"]').val(selection.height);
                    }
                });
            });
        };
    });

    $("#product_photo").on("click", function () {
        $("#project-photo").click();
    });

    $('#product_photo').dropify({
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
    });

});
