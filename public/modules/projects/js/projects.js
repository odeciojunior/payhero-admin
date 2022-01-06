$(() => {
    let projectId = $(window.location.pathname.split('/')).get(-1);

    $('.percentage-affiliates').mask('###', {'translation': {0: {pattern: /[0-9*]/}}});

    // COMPORTAMENTOS DA TELA
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
    // FIM - COMPORTAMENTOS DA TELA



    // CARD 1 FOTO, NOME, CRIADO EM, DESCRICAO E RESUMO
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

    // CARD 2 CARREGA TELA DE EDITAR PROJETO
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

    // CARD 2 COMPORTAMENTO ABAS (Principal, Marketing e Recuperacao)
    $('#slick-tabs').slick({
        infinite: false,
        speed: 300,
        slidesToShow: 7,
        variableWidth: true,
        nextArrow: false,
        prevArrow: false,

        responsive: [
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 3,
                }
            },
        ]
    });

    let firstCategory = [
        "tab-domains",
        "tab_plans",
        "tab-fretes",
    ]

    let secondCategory = [
        "tab_pixels",
        "tab_upsell",
        "tab_order_bump",
        "tab_coupons",
        "tab_reviews",
    ]

    let thirdCategory = [
        "tab_sms",
    ]

    $('.nav-tabs-horizontal .nav-link').click((e) => {
        let currentActive = $('.nav-link.active')
        let currentElement = e.target.id

        if (currentActive.attr('id') !== currentElement) {
            currentActive.removeClass('active')
        }

        if ($.inArray(currentElement, firstCategory) !== -1) {
            $('#first-category').css('color', '#2E85EC')
            $('#second-category').css('color', '#9C9C9C')
            $('#third-category').css('color', '#9C9C9C')
        }

        if ($.inArray(currentElement, secondCategory) !== -1) {
            $('#first-category').css('color', '#9C9C9C')
            $('#second-category').css('color', '#2E85EC')
            $('#third-category').css('color', '#9C9C9C')
        }

        if ($.inArray(currentElement, thirdCategory) !== -1) {
            $('#first-category').css('color', '#9C9C9C')
            $('#second-category').css('color', '#9C9C9C')
            $('#third-category').css('color', '#2E85EC')
        }
    })

    $('.slick-track').on('click', function () {
        $('.nav-tabs-horizontal .tab-pane').removeClass('active show');
    });
    

    // CARD 3 CONFIGURACAO DO PLUGIN DE ADD FOTO
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

    //CONFIGURACOES CARD 3 & 4
    function renderProjectConfig(data) {
        // let {project, companies, userProject, shopifyIntegrations, projectUpsell} = data;
        let {project, companies, userProject, shopifyIntegrations, projectUpsell} = data;

        $('#update-project #previewimage').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.svg');
        $('#update-project #name').val(project.name);
        $('#update-project #description').text(project.description);

        
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
        $('#update-project #percentage-affiliates').val(project.percentage_affiliates);


        // TIPO DE COMISSAO 
        $('#update-project .commission-type-enum input').filter(`[value=${project.commission_type_enum}]`).prop("checked", true);


        // INSERI TEXTO DO BANCO
        //quill.setText(project.terms_affiliates ?? ' ');
        quill.clipboard.dangerouslyPasteHTML(0, project.terms_affiliates ?? ' ');

        // AFILIACAO AUTOMATICA
        if (project.automatic_affiliation == 1) {
            $('#update-project .automatic-affiliation input').prop("checked", true);
        } 

        
        // URL CONVIDE AFILIADOS
        $('#update-project #url-affiliates').val(project.url_affiliates);

    }


    // CARD 4 BOTAO DE COPIAR LINK
    $("#copy-link-affiliation").on("click", function () {
        var copyText = document.getElementById("url-affiliates");
        copyText.select();
        document.execCommand("copy");

        alertCustom('success', 'Link copiado!');
    });


    // CARD DELETAR PROJETO
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
    show();


    // ATUALIZA AS CONFIGURACOES DO PROJETO
    $("#bt-update-project").on('click', function (event) {
        if ($('#photo_w').val() == '0' || $('#photo_h').val() == '0') {
            alertCustom('error', 'Selecione as dimensões da imagem de capa');
            return false;
        }

        event.preventDefault();
        loadingOnScreen();

        // ENVIA O TEXTO
        let formatedText = quill.root.innerHTML;
        
        $('#terms_affiliates').val(formatedText);
        // $('#terms_affiliates').val(quill.getText());
        
        let verify = verificaParcelas(parcelas, parcelasJuros);
        
        let statusUrlAffiliates = 0;
        if ($('#status-url-affiliates').prop('checked')) {
            statusUrlAffiliates = 1;
        }
        
        let automaticAffiliation = 0;
        if($('#update-project .automatic-affiliation input').prop("checked")){
            automaticAffiliation = 1;
        };

        let formData = new FormData(document.getElementById("update-project"));
        formData.append('status_url_affiliates', statusUrlAffiliates);
        formData.append("automatic_affiliation", automaticAffiliation);


        if (!verify) {
            $.ajax({
                method: "POST",
                url: "/api/projects/" + projectId + "/settings",
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
