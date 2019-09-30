$(document).ready(function () {

    $.ajax({
        url: '/api/projects/create',
        error: () => {
            alertCustom('error', 'Erro ao carregar empresas');
        },
        success: (response) => {
            if (response.length) {
                $.each(response, (key, company) => {
                    $('#company').append(`<option value="${company.id}">${company.name}</option>`);
                });
            } else {
                $('#card-project').hide();
                $('.content-error').show();
            }
        }
    });

    $('#btn-save').on('click', () => {
        loadingOnScreen();
        let formData = new FormData(document.querySelector('#form-create-project'));
        $.ajax({
            method: 'post',
            url: '/api/projects',
            headers: {
                'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            error: () => {
                loadingOnScreenRemove();
                alertCustom('error', 'Erro ao salvar projeto');
            },
            success: (response) => {
                loadingOnScreenRemove();
                alertCustom('success', 'Projeto salvo com sucesso!');
                window.location = "/projects"
            }
        });

    });

    var p = $("#preview-image-project");
    $("#project-photo").on("change", function () {

        var imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("project-photo").files[0]);

        imageReader.onload = function (oFREvent) {
            p.attr('src', oFREvent.target.result).fadeIn();

            p.on('load', function () {

                var img = document.getElementById('preview-image-project');
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

                $('#preview-image-project').imgAreaSelect({
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

    $("#preview-image-project").on("click", function () {
        $("#project-photo").click();
    });
});
