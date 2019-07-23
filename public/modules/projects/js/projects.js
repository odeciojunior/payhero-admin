$(function () {

    $("#tab_configuration").click(function () {
        $("#image-logo-email").imgAreaSelect({remove: true});
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
                    if (parcelas < parcelasJuros) {
                        $("#error-juros").css('display', 'block');
                        return true;
                    } else {
                        $("#error-juros").css('display', 'none');
                        return false;
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

                            x1 = Math.floor(img.naturalWidth / 100 * 10);
                            x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);

                            $('#previewimage').imgAreaSelect({
                                x1: x1, y1: y1, x2: x2, y2: y2,
                                aspectRatio: '1:1',
                                handles: true,
                                imageHeight: this.naturalHeight,
                                imageWidth: this.naturalWidth,
                                onSelectEnd: function (img, selection) {
                                    $('#photo_x1').val(selection.x1);
                                    $('#photo_y1').val(selection.y1);
                                    $('#photo_w').val(selection.width);
                                    $('#photo_h').val(selection.height);
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
                    ratio = $('#ratioImage option:selected').val();
                    $("#image-logo-email").imgAreaSelect({remove: true});
                    updateConfiguracoes();
                    imgNatural(ratio);
                });

                var photoLogo = $("#image-logo-email");
                $("#photo-logo-email").on('change', function () {
                    $(".container-image").css('display', 'block');
                    let imageReader = new FileReader();
                    imageReader.readAsDataURL(document.getElementById("photo-logo-email").files[0]);
                    imageReader.onload = function (ofREvent) {
                        photoLogo.attr('src', ofREvent.target.result).fadeIn();
                        photoLogo.on('load', function () {
                            let img = document.getElementById("image-logo-email");
                            $('input[name="logo_h"]').val(img.clientWidth);
                            $('input[name="logo_w"]').val(img.clientHeight);
                        });
                    }

                });

                $("#bt-update-project").unbind('click');
                $("#bt-update-project").on('click', function (event) {
                    event.preventDefault();

                    parcelas = parseInt($(".installment_amount option:selected").val());
                    parcelasJuros = parseInt($(".parcelas-juros option:selected").val());
                    var verify = verificaParcelas(parcelas, parcelasJuros);

                    var formData = new FormData(document.getElementById("update-project"));

                    if (!verify) {
                        $.ajax({
                            method: "POST",
                            url: "/projects/" + projectId,
                            processData: false,
                            contentType: false,
                            cache: false,
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                            },
                            data: formData,
                            error: function (response) {
                                if (response.status === '422') {
                                    for (error in response.responseJSON.errors) {
                                        alertCustom('error', String(response.responseJSON.errors[error]));
                                    }
                                }
                            }, success: function (response) {
                                alertCustom('success', response.message);

                                $("#image-logo-email").imgAreaSelect({remove: true});
                                $("#previewimage").imgAreaSelect({remove: true});
                                updateConfiguracoes();
                            }
                        });
                    } else {
                        $("#error-juros").css('display', 'block');
                    }

                });

                $('#bt-delete-project').on('click', function (event) {
                    event.preventDefault();
                    var name = $("#name").val();
                    $("#modal_excluir_titulo").html("Remover projeto " + name + " ?");

                    $("#bt_excluir").unbind('click');
                    $("#bt_excluir").on('click', function () {
                        $("#modal-delete").modal('hide');
                        loadingOnScreen()
                        $.ajax({
                            method: "DELETE",
                            url: "/projects/" + projectId,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            error: function () {
                                alertCustom('error', 'Ocorreu algum erro');
                                loadingOnScreenRemove()
                            },
                            success: function (data) {
                                loadingOnScreenRemove();

                                if (data == 'success') {
                                    window.location = "/projects";
                                } else {
                                    alertCustom('error', "Erro ao deletar projeto");
                                }
                            }
                        });
                    });

                });

                $('#bt-change-shopify-integration').on('click', function (event) {
                    event.preventDefault();

                    var integrationStatus = $(this).attr('integration-status');

                    if (integrationStatus == 2) {
                        $("#modal-change-shopify-integration-title").html("Desfazer integração com shopify?");
                        $("#modal-change-shopify-integration-text").html('Ao realizar essa operação os pagamentos não serão processados pelo checkout do CloudFox');
                    } else if (integrationStatus == 3) {
                        $("#modal-change-shopify-integration-title").html("Integrar com shopify?");
                        $("#modal-change-shopify-integration-text").html('Ao realizar essa operação os pagamentos serão processados pelo checkout do CloudFox');
                    }

                    $("#bt-modal-change-shopify-integration").unbind("click");
                    $("#bt-modal-change-shopify-integration").on('click', function () {

                        $("#bt-close-modal-change-shopify-integration").click();

                        if (integrationStatus == 2) {
                            $.ajax({
                                method: "POST",
                                url: "/apps/shopify/undointegration",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    project_id: projectId
                                },

                                error: function (response) {
                                    loadingOnScreenRemove();
                                    alertCustom('error', response.message);
                                    window.location.reload();
                                },
                                success: function (response) {
                                    loadingOnScreenRemove();
                                    alertCustom('success', response.message);
                                    window.location.reload();
                                }
                            });
                        } else {
                            $.ajax({
                                method: "POST",
                                url: "/apps/shopify/reintegration",
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                data: {
                                    project_id: projectId
                                },

                                error: function (response) {
                                    alertCustom('error', response.message);
                                    loadingOnScreenRemove()
                                    window.location.reload();
                                },
                                success: function (response) {
                                    loadingOnScreenRemove();
                                    alertCustom('success', response.message);
                                    window.location.reload();
                                }
                            });
                        }
                    });

                });

            }
        });
    }

    if($('#shopifyIdLabel').data('shopifyid') != ''){
        $('#segundaInfo').html('Digite o nome do seu domínio cadastrado no shopify (Ex: minhaloja.com) e <strong>clique em salvar</strong>')
    }else{
        $('#segundaInfo').html('Digite o nome do seu <strong>domínio e o IP do servidor</strong> onde seu site está hospedado.');
    }

});
