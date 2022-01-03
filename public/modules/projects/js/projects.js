$(() => {
    let projectId = $(window.location.pathname.split('/')).get(-1);

    // CONGIGURACOES DE COMPORTAMENTO DA TELA
    $('#tab-info').click(() => {
        show();
    });

    $(".tab_configuration").click(function () {
        $("#image-logo-email").imgAreaSelect({remove: true});
        $("#previewimage").imgAreaSelect({remove: true});
        updateConfiguracoes();
        $(this).off();
    });

    $('.toggler').on('click', function () {

        let target = $(this).data('target');

        if ($(target).hasClass('show')) {
            $(this).find('.showMore').html('add');
        } else {
            $(this).find('.showMore').html('remove');
        }
    });

    $('.status-url-affiliates').on('change', function () {
        if ($(this).prop('selectedIndex') == 0) {
            $('.div-url-affiliate').hide();
        } else {
            $('.div-url-affiliate').show();
        }
    });

    // PARCELAS
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
    function verificaParcelas(parcelas, parcelasJuros) {
        if (parcelas < parcelasJuros) {
            $("#error-juros").css('display', 'block');
            return true;
        } else {
            $("#error-juros").css('display', 'none');
            return false;
        }
    }

    // FRETE
    $("#shippement").on('change', function () {
        if ($(this).val() == 0) {
            $("#div-carrier").hide();
            $("#div-shipment-responsible").hide();
        } else {
            $("#div-carrier").show();
            $("#div-shipment-responsible").show();
        }
    });

    // IMAGES
    let p = $("#previewimage");
    $('#photoProject').unbind('change');
    $("#photoProject").on('change', function () {
        let imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("photoProject").files[0]);

        imageReader.onload = function (oFREvent) {
            p.attr('src', oFREvent.target.result).fadeIn();

            p.on('load', function () {

                let img = document.getElementById('previewimage');
                let x1, x2, y1, y2;

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

    $("#previewimage").on("click", function () {
        $("#photoProject").click();
    });

    $("#image-logo-email").on('click', function () {
        $("#photo-logo-email").click();
    });

    let ratio = '1:1';
    $('#ratioImage').unbind('change');
    $("#ratioImage").on('change', function () {
        ratio = $('#ratioImage option:selected').val();
        $("#image-logo-email").imgAreaSelect({remove: true});
        updateConfiguracoes();
        imgNatural(ratio);
    });

    let photoLogo = $("#image-logo-email");
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
    // FIM DAS CONGIGURACOES DE COMPORTAMENTO DA TELA



    // CARD 1 - DE DETALHES DO PROJETO COM FOTO, NOME, CRIADO EM, DESCRICAO, ATIVO, ETC...
    function show() {
        loadingOnScreen();

        loadOnAny('#tab_info_geral .card', false, {
            styles: {
                container: {
                    minHeight: '250px'
                }
            }
        });

        $.ajax({
            url: '/api/projects/' + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            error: (response) => {
                window.location.replace(`${location.origin}/projects`);
                $('.page-content').show()
                loadingOnScreenRemove();
            },
            success: (response) => {

                let project = response.data;
                $('.title-pad').text(project.name);
                $('#show-photo').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.svg');
                $('#created_at').text('Criado em ' + project.created_at);
                if (project.status == '1') {
                    $('#show-status').text('Ativo').addClass('badge-success');
                } else {
                    $('#show-status').text('Inativo').addClass('badge-danger');
                }
                $('#show-description').text(project.description);

                // $('#value-cancel').text('1.2K')
                let approvedSalesValue = parseFloat(project.approved_sales_value).toLocaleString('pt-BR')

                $('#value-chargeback').text(project.chargeback_count)
                $('#value-open-tickets').text(project.open_tickets)
                $('#value-without-tracking').text(project.without_tracking)
                $('#total-approved').text(project.approved_sales)
                $('#total-approved-value').text(approvedSalesValue)

                $('.page-content').show()
                loadOnAny('#tab_info_geral .card', true);
                loadingOnScreenRemove();
            }
        });
    }

    // CARD 2 - CARREGA A TELA DE EDICAO DE PROJETO
    function updateConfiguracoes() {
        loadOnAny('#tab_configuration_project .card');
        $.ajax({
            method: "GET",
            url: "/api/projects/" + projectId + '/edit',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {
                loadOnAny('#tab_configuration_project .card', true);
                errorAjaxResponse(response);

            }, success: function (data) {
                renderProjectConfig(data);
                loadOnAny('#tab_configuration_project .card', true);
            }
        });
    }
    
    // CARD 3 - EDITAR PROJETO CONFIGURACOES DO PLUGIN DROPFY
    dropifyOptions = {
        messages: {
            'default': '',
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
            message: '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span class="text-primary font-size-16">Clique ou arraste seu <br> arquivo aqui</span></p></div>',
            clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
        },
        imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg'],
    };
    $('#product_photo').dropify(dropifyOptions);


    // CARD 3 & 4 - IDENTIFICACAO - AFILIACOES
    function renderProjectConfig(data) {

        let {project} = data;

        // FOTO, NOME E DESCRICAO
        $('#update-project #product_photo').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.svg');
        $('#update-project #name').val(project.name);
        $('#update-project #description').text(project.description);

        // COLLAPESED BEHAVIOR ON-OFF
        if (project.status_url_affiliates == 1) {
            $('#update-project .status-url-affiliates').prop('checked', true)
            $('.div-url-affiliate').show('fast', 'linear')
        } else {
            $('.div-url-affiliate').prop('checked', false)
        }

        // URL DA PAGINA PRINCIPAL
        $('#update-project #url-page').val(project.url_page ? project.url_page : 'https://');

        // DURACAO DE COOKIE
        if (project.cookie_duration == 0) {
            $('#update-project .cookie-duration').prop('selectedIndex', 0).change();
        } else if (project.cookie_duration == 7) {
            $('#update-project .cookie-duration').prop('selectedIndex', 1).change();
        } else if (project.cookie_duration == 15) {
            $('#update-project .cookie-duration').prop('selectedIndex', 2).change();
        } else if (project.cookie_duration == 30) {
            $('#update-project .cookie-duration').prop('selectedIndex', 3).change();
        } else if (project.cookie_duration == 60) {
            $('#update-project .cookie-duration').prop('selectedIndex', 4).change();
        } else if (project.cookie_duration == 180) {
            $('#update-project .cookie-duration').prop('selectedIndex', 5).change();
        } else if (project.cookie_duration == 365) {
            $('#update-project .cookie-duration').prop('selectedIndex', 6).change();
        }

        // PORCENTAGEM
        $('#percentage-affiliates').mask('000', {
            reverse: true,
            onKeyPress: function (val, e, field, options) {
                if (val > 100) {
                    $('#percentage-affiliates').val('')
                }
            }
        });

        // AFILIACAO AUTOMATICA
        if (project.automatic_affiliation == 1) {
            $('#update-project .automatic-affiliation').prop('selectedIndex', 1).change();
        } else {
            $('#update-project .automatic-affiliation').prop('selectedIndex', 0).change();
        }

        

        if (project.commission_type_enum == 1) {
            $('#update-project .commision-type-enum').prop('selectedIndex', 0).change();
        } else {
            $('#update-project .commission-type-enum').prop('selectedIndex', 1).change();
        }

        $('#update-project #percentage-affiliates').val(project.percentage_affiliates);
        $('#update-project #url-affiliates').val(project.url_affiliates);
    }


    


    // CARD 4 - TEXTAREA TERMOS DE AFILIACAO
    var quill = new Quill("#termsaffiliates", {
        theme: "snow",
        modules: {
            toolbar: ['bold', 'italic', 'underline']
        }
    });
    $(document).ready(function() {
        $(".ql-toolbar.ql-snow").addClass("d-flex justify-content-center")
    });



    //CARD 4 - ABRE O MODAL DE CONFIRMACAO DE DELECAO DE PROJETO
    $('#bt-delete-project').on('click', function (event) {
        event.preventDefault();
        let name = $("#name").val();
        $("#modal_excluir_titulo").html("Remover projeto " + name + " ?");

        $("#modal-delete-project .btn-delete").on('click', function () {
            $("#modal-delete").modal('hide');
            loadingOnScreen()
            $.ajax({
                method: "DELETE",
                url: "/api/projects/" + projectId,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                error: function (response) {
                    errorAjaxResponse(response);

                    // alertCustom('error', 'Ocorreu algum erro');
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



    // CARD 4 - CHECKBOS AFILIACAO AUTOMATICA



    // CARD 4 - BOTAO COPIAR LINK AFILIADO
    $("#copy-link-affiliation").on("click", function () {
        var copyText = document.getElementById("url-affiliates");
        copyText.select();
        document.execCommand("copy");

        alertCustom('success', 'Link copiado!');
    });
    
    show();





    
    $('.percentage-affiliates').mask('###', {'translation': {0: {pattern: /[0-9*]/}}});


    //atualiza as configuracoes do projeto
    $("#bt-update-project").on('click', function (event) {
        if ($('#photo_w').val() == '0' || $('#photo_h').val() == '0') {
            alertCustom('error', 'Selecione as dimensões da imagem de capa');
            return false;
        }

        event.preventDefault();
        loadingOnScreen();

        parcelas = parseInt($(".installment_amount option:selected").val());
        parcelasJuros = parseInt($(".parcelas-juros option:selected").val());

        //$('#terms_affiliates').val(termsaffiliates.getData());

        let verify = verificaParcelas(parcelas, parcelasJuros);
        let statusUrlAffiliates = 0;

        if ($('#status-url-affiliates').prop('checked')) {
            statusUrlAffiliates = 1;
        }

        let formData = new FormData(document.getElementById("update-project"));

        formData.append('status_url_affiliates', statusUrlAffiliates);

        let discountCard = $('#credit_card_discount').val().replace('%', '');
        let discountBillet = $('#billet_discount').val().replace('%', '');
        let discountPix = $('#pix_discount').val().replace('%', '');

        discountBillet = (discountBillet == '') ? 0 : discountBillet;
        discountCard = (discountCard == '') ? 0 : discountCard;

        formData.append('credit_card_discount', discountCard);
        formData.append('billet_discount', discountBillet);
        formData.append('pix_discount', discountPix);
        formData.set('countdown_timer_flag', $('[name=countdown_timer_flag]').is(':checked') ? '1' : '0');
        formData.set('product_amount_selector', $('#product_amount_selector').is(':checked') ? '1' : '0');
        formData.set('custom_message_switch', $('[name=custom_message_switch]').is(':checked') ? '1' : '0');

        formData.set('finalizing_purchase_config_toogle', $('[name=finalizing_purchase_config_toogle]').is(':checked') ? '1' : '0');
        formData.set('checkout_notification_config_toogle', $('[name=checkout_notification_config_toogle]').is(':checked') ? '1' : '0');

        if (!verify) {
            $.ajax({
                method: "POST",
                url: "/api/projects/" + projectId,
                processData: false,
                contentType: false,
                cache: false,
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: formData,
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);

                }, success: function (response) {
                    alertCustom('success', response.message);

                    $('html, body').animate({
                        scrollTop: $('#bt-update-project').offset().top
                    }, 'slow');

                    $("#image-logo-email").imgAreaSelect({remove: true});
                    $("#previewimage").imgAreaSelect({remove: true});
                    show();
                    loadingOnScreenRemove();


                }
            });
        } else {
            $("#error-juros").css('display', 'block');
            loadingOnScreenRemove();
        }

    });

});
