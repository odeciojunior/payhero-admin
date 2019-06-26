$(function () {

    $("#tab_configuration").click(function () {
        $("#previewimage").imgAreaSelect({remove: true});
        updateConfiguracoes();
    });

    var projectId = $("#project-id").val();

    ///// UDPATE CONFIGURAÇÃO Tela Project
    function updateConfiguracoes() {
        $.ajax({
            method: "GET",
            url: "/projects/" + projectId + '/edit',
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            }, error: function () {
                alertCustom('error', 'Ocorreu algum error');
            }, success: function (data) {
                console.log(data.valueOf());
                var verifyJuros = true;

                /* verifica quantidade parcelas com a quantidade parcelas sem juros */
                let parcelas = '';
                let parcelasJuros = '';
                $(".installment_amount").on('change', function () {
                    parcelas = parseInt($(".installment_amount option:selected").val());
                    parcelasJuros = parseInt($(".parcelas-juros option:selected").val());
                    verificaParcelas(parcelas, parcelasJuros);
                });

                $(".parcelas-juros").on('change', function () {
                    parcelas = parseInt($(".installment_amount option:selected").val());
                    parcelasJuros = parseInt($(".parcelas-juros option:selected").val());
                    verificaParcelas(parcelas, parcelasJuros);
                });

                // valida se tem frete e esconde campos
                $("#shippement").on('change', function () {
                    if ($(this).val() == 0) {
                        $("#div-carrier").hide();
                        $("#div-shipment-responsible").hide();
                    } else {
                        $("#div-carrier").show();
                        $("#div-shipment-responsible").show();
                    }
                });

                function verificaParcelas(parcelas, parcelasJuros) {
                    console.log('Parcelas: ' + parcelas + '/////' + 'juros: ' + parcelasJuros);
                    if (parcelas < parcelasJuros) {
                        $("#error-juros").css('display', 'block');
                        verifyJuros = false;
                    } else {
                        verifyJuros = true;
                        $("#error-juros").css('display', 'none');
                    }
                }

                var p = $("#previewimage");
                $('#photoProject').unbind('change');
                $("#photoProject").on('change', function () {
                    var imageReader = new FileReader();
                    imageReader.readAsDataURL(document.getElementById("photoProject").files[0]);

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
                                onSelectEnd: function (img, selection) {
                                    $('input[name="photo_x1"]').val(selection.x1);
                                    $('input[name="photo_y1"]').val(selection.y1);
                                    $('input[name="photo_w"]').val(selection.width);
                                    $('input[name="photo_h"]').val(selection.height);
                                }
                            });
                        })
                    };
                });

                $("#previewimage").unbind('click');
                $("#previewimage").on("click", function () {
                    $("#photoProject").click();
                });

                $("#image-logo-email").unbind('click');
                $("#image-logo-email").on('click', function () {
                    $("#photo-logo-email").click();
                });

                var ratio = '1:1';

                $('#ratioImage').unbind('change');
                $("#ratioImage").on('change', function () {
                    console.log($('#ratioImage option:selected').val());
                    ratio = $('#ratioImage option:selected').val();
                    $("#photoProject").imgAreaSelect({remove: true});
                    console.log(ratio);
                    updateConfiguracoes();
                    imgNatural(ratio);
                });

                var photoLogo = $("#image-logo-email");
                $("#photo-logo-email").on('change', function () {
                    $(".container-image").css('display', 'block');
                    console.log(ratio);
                    let imageReader = new FileReader();
                    imageReader.readAsDataURL(document.getElementById("photo-logo-email").files[0]);
                    imageReader.onload = function (ofREvent) {
                        photoLogo.attr('src', ofREvent.target.result).fadeIn();
                        photoLogo.on('load', function () {
                            imgNatural();
                        });
                    }
                });

                function imgNatural() {
                    let img = document.getElementById("image-logo-email");
                    let x1, x2, y1, y2;

                    if (img.naturalWidth > img.naturalHeight) {
                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                        x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
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

                    $('input[name="logo_x1"]').val(x1);
                    $('input[name="logo_y1"]').val(y1);
                    $('input[name="logo_w"]').val(x2 - x1);
                    $('input[name="logo_h"]').val(y2 - y1);
                    $("#image-logo-email").imgAreaSelect({
                        x1: x1, y1: y1, x2: x2, y2: y2,
                        aspectRatio: ratio,
                        handles: true,
                        imageHeight: this.naturalHeight,
                        imageWidth: this.naturalWidth,
                        onSelectEnd: function (img, selection) {
                            $('input[name="logo_x1"]').val(selection.x1);
                            $('input[name="logo_y1"]').val(selection.y1);
                            $('input[name="logo_w"]').val(selection.width);
                            $('input[name="logo_h"]').val(selection.height);
                        }
                    });
                }

                // deletar projeto
                $('#bt_deletar_projeto').on('click', function () {

                    var name = $(this).closest("tr").find("td:first-child").text();
                    $('#modal_excluir_titulo').html('Excluir o projeto ?');

                    $('#bt_excluir').unbind('click');

                    $('#bt_excluir').on('click', function () {

                        $('.loading').css("visibility", "visible");

                        $.ajax({
                            method: "POST",
                            url: "/projetos/deletarprojeto",
                            data: {projeto: $("#project_id").val()},
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function () {
                                $('.loading').css("visibility", "hidden");
                                $('#fechar_modal_excluir').click();
                                alertPersonalizado('error', 'Ocorreu algum erro');
                            },
                            success: function (data) {
                                $('.loading').css("visibility", "hidden");
                                if (data != 'sucesso') {
                                    alertPersonalizado('error', data);
                                } else {
                                    window.location = "/projetos";
                                }
                            }
                        });

                    });
                });

            }
        });
    }

    updateConfiguracoes();
});
