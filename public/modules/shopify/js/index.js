$(document).ready(function () {

    var p = $("#previewimage");
    $("#foto").on("change", function () {

        var imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("foto").files[0]);

        imageReader.onload = function (oFREvent) {
            p.attr('src', oFREvent.target.result).fadeIn();

            p.on('load', function () {

                var img = document.getElementById('previewimage');
                var x1, x2, y1, y2;

                if (img.naturalWidth > img.naturalHeight) {
                    y1 = Math.floor(img.naturalHeight / 100 * 10);
                    y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                    x2 = x1 + (y2 - y1);
                } else {
                    if (img.naturalWidth < img.naturalHeight) {
                        x1 = Math.floor(img.naturalWidth / 100 * 10);
                        ;
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

                $('#previewimage').imgAreaSelect({
                    x1: x1, y1: y1, x2: x2, y2: y2,
                    aspectRatio: '1:1',
                    handles: true,
                    imageHeight: this.naturalHeight,
                    imageWidth: this.naturalWidth,
                    onSelectEnd: function (img, selection) {
                        $('input[name="photo_x1"]').val(selection.x1);
                        $('input[name="photo_y1"]').val(selection.y1);
                        $('input[name="photo_w"]').val(selection.width);
                        $('input[name="photo_h"]').val(selection.height);
                    },
                    parent: $('#conteudo_modal_add')
                });
            })
        };

    });

    $("#selecionar_foto").on("click", function () {
        $("#foto").click();
    });

    $("#bt_adicionar_integracao").on("click", function () {

        if ($('#token').val() == '' || $('#url_store').val() == '' || $('#foto_projeto').val() == '' || $('#company').val() == '') {
            alertPersonalizado('error', 'Dados informados inválidos');
            return false;
        }
        $('.loading').css("visibility", "visible");

        var form_data = new FormData(document.getElementById('form_add_integracao'));

        $.ajax({
            method: "POST",
            url: "/apps/shopify/adicionarintegracao",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            cache: false,
            data: form_data,
            error: function (response) {
                $('.loading').css("visibility", "hidden");
                alertPersonalizado('error', response.message);//'Ocorreu algum erro'
                $('#previewimage_brinde_cadastrar').imgAreaSelect({remove: true});
            },
            success: function (response) {
                $('.loading').css("visibility", "hidden");
                alertPersonalizado('success', response.message);
                window.location.reload(true);

                $('#previewimage_brinde_cadastrar').imgAreaSelect({remove: true});
            },
        });

    });

    function alertPersonalizado(tipo, mensagem) {

        swal({
            position: 'bottom',
            type: tipo,
            toast: 'true',
            title: mensagem,
            showConfirmButton: false,
            timer: 6000
        });
    }

});
