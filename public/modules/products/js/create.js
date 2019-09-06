$(document).ready(function () {
    /**
     * Helper Functions
     */
    function isEmpty(obj) {
        return Object.keys(obj).length === 0;
    }

    function verify() {
        let ver = true;
        if ($('#name').val().length === 0) {
            alertCustom("error", "O campo Nome é obrigatório");
            ver = false;
            $("#nav-basic-tab").click();
            $('#name').focus();
        }
        if ($("#description").val().length === 0) {
            alertCustom("error", "O campo Descrição é obrigatório");
            ver = false;
            $("#nav-basic-tab").click();
            $("#description").focus();
        }
        return ver;
    }

    getCategories();

    function getCategories() {
        $.ajax({
            method: 'GET',
            url: '/api/products/create',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            error: function error(response) {
                if (response.status === 422) {
                    for (error in response.errors) {
                        alertCustom('error', String(response.errors[error]));
                    }
                } else {
                    alertCustom('error', response.responseJSON.message);
                }
            },
            success: function (response) {
                console.log(response);
                if (!isEmpty(response.data.categories)) {
                    /**
                     * Select com as categorias
                     */
                    $.each(response.data.categories, function (i, category) {
                        $("#select-categories").append($('<option>', {
                            value: category.id,
                            text: category.name
                        }));

                    });
                } else {
                    alertCustom('success', 'Ocorreu um erro, tente novamente mais tarde');
                    window.location = "/products";
                }
            }
        });
    }

    $("#my-form-add-product").submit(function (event) {
        event.preventDefault();

        if (verify()) {
            loadingOnScreen();
            let myForm = document.getElementById('my-form-add-product');
            let formData = new FormData(myForm);
            $.ajax({
                method: 'POST',
                url: "/api/products",
                processData: false,
                cache: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                dataType: "json",
                error: function (response) {
                    loadingOnScreenRemove();

                    if (response.status === 422) {
                        for (error in response.responseJSON.errors) {
                            alertCustom('error', String(response.responseJSON.errors[error]));
                        }
                    } else {
                        alertCustom('error', response.responseJSON.message);
                    }
                }, success: function (response) {
                    loadingOnScreenRemove();

                    alertCustom('success', response.message);
                    window.location = "/products";
                }
            });
        }

    });

    var p = $("#previewimage");
    $("#photo").on("change", function () {

        var imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("photo").files[0]);

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

    $("#previewimage").on("click", function () {
        $("#photo").click();
    });

    $('.money').mask('#.###,#0', {reverse: true});

    $("#next_step").on("click", function () {
        $("#nav-logistic-tab").click();
        $("#previewimage").imgAreaSelect({remove: true});
    });

});
