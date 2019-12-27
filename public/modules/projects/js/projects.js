$(() => {
    let projectId = $(window.location.pathname.split('/')).get(-1);

    // COMPORTAMENTOS DA TELA
    $('#tab-info').click(() => {
        show();
    });

    $("#tab_configuration").click(function () {
        $("#image-logo-email").imgAreaSelect({remove: true});
        $("#previewimage").imgAreaSelect({remove: true});
        updateConfiguracoes();
    });

    $('#toggler').on('click', function () {
        if ($("#collapseOne").hasClass('show')) {
            $('#showMore').text('exibir mais')
        } else {
            $('#showMore').text('exibir menos')
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
});

