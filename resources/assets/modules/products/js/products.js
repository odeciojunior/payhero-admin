$(document).ready(function () {
    var p = $("#previewimage");
    $("#photo").on("change", function () {
        var imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("photo").files[0]);

        imageReader.onload = function (oFREvent) {
            p.attr("src", oFREvent.target.result).fadeIn();

            p.on("load", function () {
                var img = document.getElementById("previewimage");
                var x1, x2, y1, y2;

                if (img.naturalWidth > img.naturalHeight) {
                    y1 = Math.floor((img.naturalHeight / 100) * 10);
                    y2 = img.naturalHeight - Math.floor((img.naturalHeight / 100) * 10);
                    x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                    x2 = x1 + (y2 - y1);
                } else {
                    if (img.naturalWidth < img.naturalHeight) {
                        x1 = Math.floor((img.naturalWidth / 100) * 10);
                        x2 = img.naturalWidth - Math.floor((img.naturalWidth / 100) * 10);
                        y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                        y2 = y1 + (x2 - x1);
                    } else {
                        x1 = Math.floor((img.naturalWidth / 100) * 10);
                        x2 = img.naturalWidth - Math.floor((img.naturalWidth / 100) * 10);
                        y1 = Math.floor((img.naturalHeight / 100) * 10);
                        y2 = img.naturalHeight - Math.floor((img.naturalHeight / 100) * 10);
                    }
                }

                $('input[name="photo_x1"]').val(x1);
                $('input[name="photo_y1"]').val(y1);
                $('input[name="photo_w"]').val(x2 - x1);
                $('input[name="photo_h"]').val(y2 - y1);

                $("#previewimage").imgAreaSelect({
                    x1: x1,
                    y1: y1,
                    x2: x2,
                    y2: y2,
                    aspectRatio: "1:1",
                    handles: true,
                    imageHeight: this.naturalHeight,
                    imageWidth: this.naturalWidth,
                    onSelectEnd: function (img, selection) {
                        $('input[name="photo_x1"]').val(selection.x1);
                        $('input[name="photo_y1"]').val(selection.y1);
                        $('input[name="photo_w"]').val(selection.width);
                        $('input[name="photo_h"]').val(selection.height);
                    },
                });
            });
        };
    });

    $("#previewimage").on("click", function () {
        $("#photo").click();
    });

    $(".money").mask("#.###,#0", { reverse: true });

    $("#shipping").on("change", function () {
        if ($(this).val() == "proprio") {
            $("#div_carrier_id").hide();
        } else {
            $("#div_carrier_id").show();
        }
    });

    $("input[type=radio][name=format]").change(function () {
        if (this.value == "1") {
            $("#nav-logistic-tab").show();
            $("#div_next_step").show();
            $("#div_save_digital_product").hide();
            $("#div_digital_product_upload").css("visibility", "hidden");
        } else if (this.value == "0") {
            $("#nav-logistic-tab").hide();
            $("#div_next_step").hide();
            $("#div_save_digital_product").show();
            $("#div_digital_product_upload").css("visibility", "visible");
        }
    });

    $("#next_step").on("click", function () {
        $("#nav-logistic-tab").click();
        $("#previewimage").imgAreaSelect({ remove: true });
    });
    $(".btnSave").on("click", function () {
        var name = $("#name").val();
        var description = $("#description").val();
        console.log(description);
        if (name == "") {
            alertCustom("error", "O campo Nome é obrigatório");
        }
        if (description == "") {
            alertCustom("error", "O campo Descrição é obrigatório");
        }
    });

    $(".delete-product").on("click", function (event) {
        event.preventDefault();
        var product = $(".delete-product").attr("product");
        $("#bt_excluir").unbind("click");
        $("#bt_excluir").on("click", function () {
            $("#close-modal-delete").click();
            $.ajax({
                method: "DELETE",
                url: "/products/" + product,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                error: function (response) {
                    if (response.status == "422") {
                        for (error in response.responseJSON.errors) {
                            alertCustom("error", String(response.responseJSON.errors[error]));
                        }
                    }
                    if (response.status == "400") {
                        alertCustom("error", response.responseJSON.message);
                    }
                },
                success: function (response) {
                    alertCustom("success", response.message);
                    window.location = "/products";
                },
            });
        });
    });
});
