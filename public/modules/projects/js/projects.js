$(() => {
    let projectId = $(window.location.pathname.split('/')).get(-1);

    CKEDITOR.replace('termsaffiliates', {
        language: 'br',
        uiColor: '#AAAAAA',
        height: 250
    });

    // COMPORTAMENTOS DA TELA
    $('#tab-info').click(() => {
        show();
    });

    $("#tab_configuration").click(function () {
        $("#image-logo-email").imgAreaSelect({remove: true});
        $("#previewimage").imgAreaSelect({remove: true});
        updateConfiguracoes();
        getAffiliates();
    });

    $('.toggler').on('click', function () {

        let target = $(this).data('target');

        if ($(target).hasClass('show')) {
            $(this).find('.showMore').html('add');
        } else {
            $(this).find('.showMore').html('remove');
        }
    });

    //parcelas
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

    // frete
    $("#shippement").on('change', function () {
        if ($(this).val() == 0) {
            $("#div-carrier").hide();
            $("#div-shipment-responsible").hide();
        } else {
            $("#div-carrier").show();
            $("#div-shipment-responsible").show();
        }
    });

    //images
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
        getAffiliates();
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

    show();

    //carrega detalhes do projeto
    function show() {

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
            success: (response) => {

                let project = response.data;
                $('.page-title, .title-pad').text(project.name);
                $('#show-photo').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.png');
                $('#created_at').text('Criado em ' + project.created_at);
                if (project.visibility === 'public') {
                    $('#show-visibility').text('Público').addClass('badge-primary');
                } else {
                    $('#show-visibility').text('Privado').addClass('badge-danger');
                }
                if (project.status == '1') {
                    $('#show-status').text('Ativo').addClass('badge-primary');
                } else {
                    $('#show-status').text('Inativo').addClass('badge-danger');
                }
                $('#show-description').text(project.description);

                loadOnAny('#tab_info_geral .card', true);
            },
            error: (response) => {
                errorAjaxResponse(response);
                loadOnAny('#tab_info_geral .card', true);
            }
        });
    }

    $("#copy-link-affiliation").on("click", function () {
        var copyText = document.getElementById("url-affiliates");
        copyText.select();
        document.execCommand("copy");

        alertCustom('success', 'Link copiado!');
    });

    function renderProjectConfig(data) {
        let {project, companies, userProject, shopifyIntegrations} = data;
        $('#update-project #previewimage').attr('src', project.photo ? project.photo : '/modules/global/img/projeto.png');
        $('#update-project #name').val(project.name);
        $('#cost_currency_type').val(project.cost_currency_type);
        $('#update-project #description').text(project.description);
        if (project.visibility === 'public') {
            $('#update-project #visibility').prop('selectedIndex', 0).change();
        } else {
            $('#update-project #visibility').prop('selectedIndex', 1).change();
        }
        $('#update-project #image-logo-email').attr('src', project.logo ? project.logo : '/modules/global/img/projeto.png');
        $('#update-project #url-page').val(project.url_page ? project.url_page : 'https://');
        $('#update-project #contact').val(project.contact);
        $('#update-project #support_phone').val(project.support_phone);
        $('#update-project #invoice-description').val(project.invoice_description);
        $('#update-project #companies').html('');
        for (let company of companies) {
            $('#update-project #companies').append(
                `<option value="${company.id}" ${(company.id === userProject.company_id ? 'selected' : '')} ${(company.company_document_status == 'pending' ? 'disabled' : '')}>
                   ${(company.company_document_status == 'pending' ? company.name + ' (documentos pendentes)' : company.name)}
                </option>
                `)
        }
        $('#update-project .installment_amount').prop('selectedIndex', project.installments_amount - 1).change();
        $('#update-project .parcelas-juros').prop('selectedIndex', project.installments_interest_free - 1).change();
        if (project.boleto === 1) {
            $('#update-project #boleto').prop('selectedIndex', 0).change();
        } else {
            $('#update-project #boleto').prop('selectedIndex', 1).change();

        }
        $('#boleto_due_days').val(project.boleto_due_days);
        $('#update-project #boleto_redirect').val(project.boleto_redirect);
        $('#update-project #card_redirect').val(project.card_redirect);
        $('#update-project #analyzing_redirect').val(project.analyzing_redirect);
        
        CKEDITOR.instances.termsaffiliates.setData(project.terms_affiliates);

        if(project.automatic_affiliation == 1) {
            $('#update-project .automatic-affiliation').prop('selectedIndex', 1).change();
        } else {
            $('#update-project .automatic-affiliation').prop('selectedIndex', 0).change();
        }

        if(project.cookie_duration == 0) {
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

        $('#update-project #percentage-affiliates').val(project.percentage_affiliates);
        $('#update-project #url-affiliates').val(project.url_affiliates);

        $('#shopify-integration-pending, #bt-change-shopify-integration, #bt-shopify-sincronization-product, #bt-shopify-sincronization-template').hide();

        if (project.shopify_id) {
            $('#update-project #shopify-configs').show();
            if (shopifyIntegrations.length !== 0) {
                $('#div-shopify-token').show();
                $('#div-shopify-permissions').show();
                $('#shopify-token').val(shopifyIntegrations[0].token);
                if (shopifyIntegrations[0].status !== 1) {
                    $('#bt-change-shopify-integration')
                        .attr('integration-status', shopifyIntegrations[0].status)
                        .show();
                    $('#bt-change-shopify-integration span').html(shopifyIntegrations[0].status === 2 ? 'Desfazer integração com shopify' : 'Integrar com shopify');
                } else if (shopifyIntegrations[0].status === 1) {
                    $('#shopify-integration-pending').show();
                }
                if (shopifyIntegrations[0].status !== 3) {
                    $('#bt-shopify-sincronization-product, #bt-shopify-sincronization-template')
                        .attr('integration-status', shopifyIntegrations[0].status)
                        .show();
                }

                $('#skiptocart-input').prop('checked', shopifyIntegrations[0].skip_to_cart).val(shopifyIntegrations[0].skip_to_cart);
            }
        }

        $("#checkout_type").val(project.checkout_type);

        // Verificação de email de contato
        if (project.contact_verified) {
            contactVerified();
        } else {
            contactNotVerified();
        }

        // Verificação de telefone de suporte
        if (project.support_phone_verified) {
            supportphoneVerified();
        } else {
            supportphoneNotVerified();
        }

        //select cartão de credito no checkout
        // if (project.credit_card == 1) {
        //     $('#credit_card .credit_card_yes').attr('selected', true);
        // } else {
        //     $('#credit_card .credit_card_no').attr('selected', true);
        // }
    }

    function supportphoneVerified() {
        $("#message_not_verified_support_phone").css("display", "none");
        $("#input_group_support_phone").css("border-color", "forestgreen");
        $("#support_phone").css("border-color", "forestgreen");
        $("#input_group_support_phone").append().html("<i class='fas fa-check' data-toggle='tooltip' data-placement='left' title='Telefone de suporte verificado!' style='color:forestgreen;'></i>");
    }

    function supportphoneNotVerified() {
        $("#message_not_verified_support_phone").css("display", "");
        $("#input_group_support_phone").css("border-color", "red");
        $("#support_phone").css("border-color", "red");
        $("#input_group_support_phone").append().html("<i class='fas fa-times' data-toggle='tooltip' data-placement='left' title='Telefone de suporte não verificado!' style='color:red;'></i>");
    }

    function contactVerified() {
        $("#message_not_verified_contact").css("display", "none");
        $("#input_group_contact").css("border-color", "forestgreen");
        $("#contact").css("border-color", "forestgreen");
        $("#input_group_contact").append().html("<i class='fas fa-check' data-toggle='tooltip' data-placement='left' title='Contato verificado!' style='color:forestgreen;'></i>");
    }

    function contactNotVerified() {
        $("#message_not_verified_contact").css("display", "");
        $("#input_group_contact").css("border-color", "red");
        $("#contact").css("border-color", "red");
        $("#input_group_contact").append().html("<i class='fas fa-times' data-toggle='tooltip' data-placement='left' title='Contato não verificado!' style='color:red;'></i>");
    }

    // Verificar email de contato
    $("#btn_verify_support_phone").on("click", function () {
        event.preventDefault();
        let support_phone = $("#support_phone").val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/projects/' + projectId + '/verifysupportphone',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                support_phone: support_phone,
            }, error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();

            }, success: function (response) {
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
            }
        });
    });

    $("#match_support_phone_verifycode_form").on("submit", function (event) {
        event.preventDefault();
        let verify_code = $("#support_phone_verify_code").val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/projects/' + projectId + '/matchsupportphoneverifycode',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                verifyCode: verify_code
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                $('#modal_verify_support_phone').modal('hide');
                $('#support_phone_verify_code').val('');
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                supportphoneVerified();
            }
        });
    });

    $("#match_contact_verifycode_form").on("submit", function (event) {
        event.preventDefault();
        let verify_code = $("#contact_verify_code").val();
        loadingOnScreen();
        $.ajax({
            method: "POST",
            url: '/api/projects/' + projectId + '/matchcontactverifycode',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                verifyCode: verify_code
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                $('#modal_verify_contact').modal('hide');
                $('#contact_verify_code').val('');
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
                contactVerified();
            }
        });
    });

    // Verificar email de contato
    $("#btn_verify_contact").on("click", function () {
        event.preventDefault();
        loadingOnScreen();
        let contact = $("#contact").val();
        $.ajax({
            method: "POST",
            url: '/api/projects/' + projectId + '/verifycontact',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                contact: contact
            },
            error: function (response) {
                errorAjaxResponse(response);
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
            },
            success: function success(response) {
                loadingOnScreenRemove();
                $(".div1").hide();
                $(".div2").show();
                alertCustom('success', response.message);
            }
        });
    });

    //carrega a tela de edicao do projeto
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

    //abre o modal de delecao
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
        let verify = verificaParcelas(parcelas, parcelasJuros);
        $('#terms_affiliates').val(CKEDITOR.instances.termsaffiliates.getData());
        let formData = new FormData(document.getElementById("update-project"));

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

                    $("#image-logo-email").imgAreaSelect({remove: true});
                    $("#previewimage").imgAreaSelect({remove: true});
                    updateConfiguracoes();
                    getAffiliates();
                    loadingOnScreenRemove();
                }
            });
        } else {
            $("#error-juros").css('display', 'block');
            loadingOnScreenRemove();
        }

    });

    // Sincroniza template do shopify
    $("#bt-shopify-sincronization-template").on('click', function (event) {
        event.preventDefault();

        $("#modal-change-shopify-integration-title").html('Sincronizar template');
        $("#modal-change-shopify-integration-text").html('Seu você alterar o tema da sua loja, para o checkout continuar funcionando apenas sincronize o template novamente');

        $("#bt-modal-change-shopify-integration").unbind('click');
        $("#bt-modal-change-shopify-integration").on('click', function () {
            $("#bt-close-modal-change-shopify-integration").click();
            loadingOnScreen();

            $.ajax({
                method: 'POST',
                url: '/api/apps/shopify/synchronize/templates',
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {
                    project_id: projectId
                },
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);

                    window.location.reload();
                },
                success: function (response) {
                    loadingOnScreenRemove();
                    alertCustom('success', response.message);
                }
            });
        });
    });

    $('#bt-change-shopify-integration').on('click', function (event) {
        event.preventDefault();

        let integrationStatus = $(this).attr('integration-status');

        if (integrationStatus == 2) {
            $("#modal-change-shopify-integration-title").html("Desfazer integração com shopify?");
            $("#modal-change-shopify-integration-text").html('Ao realizar essa operação os pagamentos não serão processados pelo checkout do CloudFox');
        } else if (integrationStatus == 3) {
            $("#modal-change-shopify-integration-title").html("Integrar com shopify?");
            $("#modal-change-shopify-integration-text").html('Ao realizar essa operação os pagamentos serão processados pelo checkout do CloudFox');
        }

        $("#bt-modal-change-shopify-integration").on('click', function () {

            $("#bt-close-modal-change-shopify-integration").click();

            loadingOnScreen();
            if (integrationStatus == 2) {
                $.ajax({
                    method: "POST",
                    url: "/api/apps/shopify/undointegration",
                    dataType: "json",
                    headers: {
                        'Authorization': $('meta[name="access-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    data: {
                        project_id: projectId
                    },

                    error: function (response) {
                        loadingOnScreenRemove();
                        errorAjaxResponse(response);
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
                    url: "/api/apps/shopify/reintegration",
                    dataType: "json",
                    headers: {
                        'Authorization': $('meta[name="access-token"]').attr('content'),
                        'Accept': 'application/json',
                    },
                    data: {
                        project_id: projectId
                    },

                    error: function (response) {
                        errorAjaxResponse(response);
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

    // Sincroniza produtos do shopify
    $("#bt-shopify-sincronization-product").on('click', function (event) {
        event.preventDefault();

        $("#modal-change-shopify-integration-title").html('Sincronizar produtos');
        $("#modal-change-shopify-integration-text").html('Sincronizar produtos da sua loja shopify com o CloudFox');

        $("#bt-modal-change-shopify-integration").unbind('click');
        $("#bt-modal-change-shopify-integration").on('click', function () {
            $("#bt-close-modal-change-shopify-integration").click();
            loadingOnScreen();

            $.ajax({
                method: 'POST',
                url: '/api/apps/shopify/synchronize/products',
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {
                    project_id: projectId
                },
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                    window.location.reload();
                },
                success: function (response) {
                    loadingOnScreenRemove();
                    alertCustom('success', response.message);
                }
            });
        });

    });

    let btnTokenClick = "enable click";
    $('.btn-edit-token').on('click', function (event) {
        event.preventDefault();
        if (btnTokenClick == "enable click") {
            btnTokenClick = "update click";
            $('#shopify-token').prop("disabled", false);
            $('.btn-edit-token').text('Salvar').addClass('bg-grey-700');
        } else {
            if ($('#shopify-token').val() == '') {
                alertCustom('error', 'Token inválido');
                return false;
            }
            loadingOnScreen();
            $.ajax({
                method: 'POST',
                url: '/api/apps/shopify/updatetoken',
                dataType: "json",
                headers: {
                    'Authorization': $('meta[name="access-token"]').attr('content'),
                    'Accept': 'application/json',
                },
                data: {
                    project_id: projectId,
                    token: $('#shopify-token').val(),
                },
                error: function (response) {
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: function (response) {
                    loadingOnScreenRemove();
                    btnTokenClick = "enable click";
                    $('#shopify-token').prop("disabled", true);
                    $('.btn-edit-token').text('Alterar').removeClass('bg-grey-700');
                    alertCustom('success', response.message);
                }
            });
        }
    });

    $('#bt-shopify-verify-permissions').on('click', function (event) {
        event.preventDefault();
        loadingOnScreen();
        $.ajax({
            method: 'POST',
            url: '/api/apps/shopify/verifypermissions',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                project_id: projectId,
            },
            error: function (response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function (response) {
                loadingOnScreenRemove();
                alertCustom('success', response.message);
            }
        });
    });

    $('#skiptocart-input').on('change', function () {

        let input = $(this);
        input.attr('disabled', true).parent()
            .parent()
            .css('opacity', '.5');

        $.ajax({
            method: 'POST',
            url: '/api/apps/shopify/skiptocart',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            },
            data: {
                project_id: projectId,
                skip_to_cart: parseInt(input.val()),
            },
            error: function (response) {
                errorAjaxResponse(response);
                input.attr('disabled', false).parent()
                    .parent()
                    .css('opacity', '1');
            },
            success: function (response) {
                alertCustom('success', response.message);
                input.attr('disabled', false).parent()
                    .parent()
                    .css('opacity', '1');
            }
        });
    });

    var badgeAffiliateRequest = {
        1: "primary",
        2: "warning",
        3: "success",
        4: "danger",
    };

    function getAffiliates() {
        $.ajax({
            method: "GET",
            url: "/api/affiliates/getaffiliates/" + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {
                errorAjaxResponse(response);

            }, success: function (response) {
                $(".body-table-affiliates").html('');
                $.each(response.data, function (index, value) {
                    data = '';
                    data += '<tr>';
                    data += '<td class="" style="vertical-align: middle;">' + value.name + '</td>';
                    data += '<td class="" style="vertical-align: middle;">' + value.date + '</td>';
                    data += '<td class="text-center" style="vertical-align: middle;">' + value.percentage + ' %</td>';
                    data += '<td class="text-center" ><span class="badge badge-' + badgeAffiliateRequest[value.status] + '">' + value.status_translated + '</span></td>';
                    data += "<td class='text-center'>";
                    data += "<a title='Editar' class='mg-responsive pointer edit-affiliate'    affiliate='" + value.id + "'><i class='material-icons gradient'>edit</i></a>"
                    data += "<a title='Excluir' class='mg-responsive pointer delete-affiliate' affiliate='" + value.id + "'><i class='material-icons gradient'>delete_outline</i></a>";
                    data += "</td>";
                    data += '</tr>';
                    $(".body-table-affiliates").append(data);
                });

                $('.delete-affiliate').on('click', function (event) {
                    event.preventDefault();
                    let affiliate = $(this).attr('affiliate');
                    $('#modal-delete-affiliate').modal('show');
                    $("#modal-delete-affiliate .btn-delete").on('click', function () {
                        $("#modal-delete").modal('hide');
                        loadingOnScreen()
                        $.ajax({
                            method: "DELETE",
                            url: "/api/affiliates/" + affiliate,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function (response) {
                                errorAjaxResponse(response);

                                alertCustom('error', 'Ocorreu algum erro');
                                loadingOnScreenRemove()
                            },
                            success: function (data) {
                                loadingOnScreenRemove();
                                getAffiliates();
                            }
                        });
                    });

                });

                $(document).on('click', '.edit-affiliate', function () {
                    let affiliate = $(this).attr('affiliate');
                    $.ajax({
                        method: "GET",
                        url: "/api/affiliates/" + affiliate + "/edit",
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        error: function error(response) {
                            errorAjaxResponse(response);
                        }, success: function success(response) {
                            $('#modal-edit-affiliate .affiliate-id').val(affiliate);
                            $('#modal-edit-affiliate .affiliate-name').val(response.data.name);
                            $('#modal-edit-affiliate .affiliate-email').val(response.data.email);
                            $('#modal-edit-affiliate .affiliate-company').val(response.data.company);
                            $('#modal-edit-affiliate .affiliate-percentage').val(response.data.percentage);
                            if (response.data.status == 1) {
                                $('#modal-edit-affiliate .affiliate-status').prop("selectedIndex", 0).change();
                            } else if (response.data.status == 2) {
                                $('#modal-edit-affiliate .affiliate-status').prop("selectedIndex", 1).change();
                            } else if (response.data.status == 3) {
                                $('#modal-edit-affiliate .affiliate-status').prop("selectedIndex", 2).change();
                            } else if (response.data.status == 4) {
                                $('#modal-edit-affiliate .affiliate-status').prop("selectedIndex", 3).change();
                            }

                            $('#modal-edit-affiliate').modal('show');
                        }
                    });
                });

                $("#modal-edit-affiliate .btn-update").on('click', function () {
                    let formData = new FormData(document.getElementById('form-update-affiliate'));
                    let affiliate = $('#modal-edit-affiliate .affiliate-id').val();
                    loadingOnScreen();
                    $.ajax({
                        method: "POST",
                        url: "/api/affiliates/" + affiliate,
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false,
                        error: function (response) {
                            loadingOnScreenRemove();
                            errorAjaxResponse(response);
                        },
                        success: function success(data) {
                            loadingOnScreenRemove();
                            alertCustom("success", "Afiliado atualizado com sucesso");
                            getAffiliates();
                        }
                    });
                });
            }
        });

        $.ajax({
            method: "GET",
            url: "/api/affiliates/getaffiliaterequests/" + projectId,
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {
                errorAjaxResponse(response);

            }, success: function (response) {
                $(".body-table-affiliate-requests").html('');
                $.each(response.data, function (index, value) {
                    data = '';
                    data += '<tr>';
                    data += '<td class="" style="vertical-align: middle;">' + value.name + '</td>';
                    data += '<td class="" style="vertical-align: middle;">' + value.date + '</td>';
                    data += '<td class="" ><span class="badge badge-' + badgeAffiliateRequest[value.status] + '">' + value.status_translated + '</span></td>';
                    data += "<td style='text-align:center'>";

                    data += "<a title='Aprovar' class='text-white ml-2 badge badge-success pointer evaluate-affiliate' affiliate='" + value.id + "' status='3'>Aprovar</a>";
                    data += "<a title='Recusar' class='text-white ml-2 badge badge-danger pointer evaluate-affiliate' affiliate='" + value.id + "' status='4'>Recusar</a>";
                    data += "<a title='Analizar' class='text-white ml-2 badge badge-warning pointer evaluate-affiliate' affiliate='" + value.id + "' status='2'>Analizar</a>";

                    data += "</td>";
                    data += '</tr>';
                    $(".body-table-affiliate-requests").append(data);
                });

                $(".evaluate-affiliate").on('click', function () {
                    let affiliate = $(this).attr('affiliate');
                    let status = $(this).attr('action');

                    loadingOnScreen();
                    $.ajax({
                        method: "POST",
                        url: "/api/affiliates/evaluateaffiliaterequest",
                        dataType: "json",
                        headers: {
                            'Authorization': $('meta[name="access-token"]').attr('content'),
                            'Accept': 'application/json',
                        },
                        data: { status: status, affiliate: affiliate },
                        error: function (response) {
                            loadingOnScreenRemove();
                            errorAjaxResponse(response);
                        },
                        success: function success(data) {
                            loadingOnScreenRemove();
                            alertCustom("success", "Solicitação de afiliação atualizada com sucesso");
                            getAffiliates();
                        }
                    });
                });
            }
        });

    }
});

