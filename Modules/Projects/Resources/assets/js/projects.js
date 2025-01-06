$(() => {
    getCompaniesAndProjects().done(function(data) {
        $('.site-navbar .sirius-select-container').addClass('disabled');
    });

    $(document).on('select2:select', function(e) {
        const selection = $(e.target).parent().find('.select2-selection--multiple')[0];
        if (selection) selection.scrollTop = selection.scrollHeight;
    });

    loadingOnScreen();

    let projectId = $(window.location.pathname.split('/')).get(-1);
    $('.percentage-affiliates').mask('###', { translation: { 0: { pattern: /[0-9*]/ } } });
    let onChangeSet = false;

    // COMPORTAMENTOS DA TELA
    $('#tab-info').click(() => {
        show();
    });

    $('.tab_configuration').click(function() {
        $('#image-logo-email').imgAreaSelect({ remove: true });
        $('#previewimage').imgAreaSelect({ remove: true });
        updateConfiguracoes();
        $(this).off();
    });

    $('.toggler').on('click', function() {
        let target = $(this).data('target');

        if ($(target).hasClass('show')) {
            $(this).find('.showMore').html('add');
        } else {
            $(this).find('.showMore').html('remove');
        }
    });

    // FRETE
    $('#shippement').on('change', function() {
        if ($(this).val() == 0) {
            $('#div-carrier').hide();
            $('#div-shipment-responsible').hide();
        } else {
            $('#div-carrier').show();
            $('#div-shipment-responsible').show();
        }
    });

    // PARCELAS
    let parcelas = '';
    let parcelasJuros = '';
    const formUpdateProject = $('#update-project');
    $('.installment_amount').on('change', function() {
        parcelas = parseInt($('.installment_amount option:selected').val());
        parcelasJuros = parseInt($('.parcelas-juros option:selected').val());
        verificaParcelas(parcelas, parcelasJuros);
    });

    $('.parcelas-juros').on('change', function() {
        parcelas = parseInt($('.installment_amount option:selected').val());
        parcelasJuros = parseInt($('.parcelas-juros option:selected').val());
        verificaParcelas(parcelas, parcelasJuros);
    });

    function verificaParcelas(parcelas, parcelasJuros) {
        if (parcelas < parcelasJuros) {
            $('#error-juros').css('display', 'block');
            return true;
        } else {
            $('#error-juros').css('display', 'none');
            return false;
        }
    }

    // CARD 1 FOTO, NOME, CRIADO EM, DESCRICAO E RESUMO
    const getImageProject = (projectPhoto) =>
        projectPhoto ? (dropifyOptions.defaultFile = projectPhoto) : '/build/global/img/produto.svg';

    function disableTabsAndElements(project) {
        if (!project.created_by_checkout_integration) return;

        const allowedTabs = ['tab_sms', 'tab_configuration'];
        document.querySelectorAll('.nav-item').forEach(item => {
            const link = item.querySelector('.nav-link');
            if (!allowedTabs.some(tabId => item.classList.contains(tabId))) {
                item.classList.add('disabled');
                if (link) {
                    link.addEventListener('click', preventDefaultAction);
                }
            }
        });

        document.querySelector('#tab_sms')?.click();
        disableCheckbox('#status-url-affiliates');
    }

    function preventDefaultAction(event) {
        event.preventDefault();
    }

    function disableCheckbox(selector) {
        const checkbox = document.querySelector(selector);
        if (checkbox) {
            checkbox.disabled = true;
            checkbox.addEventListener('click', preventDefaultAction);
        }
    }


    function show() {
        $('.page').addClass('low-opacity');
        $.ajax({
            url: '/api/projects/' + projectId,
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            error: (response) => {
                window.location.replace(`${location.origin}/projects`);
                $('.page-content').show();
                $('.page').removeClass('low-opacity');
            },
            success: (response) => {
                let project = response.data;
                let project_type = 'my_products';
                if (project.shopify_id != null) project_type = 'shopify';
                if (project.woocommerce_id != null) project_type = 'woocommerce';

                $('#project_type').val(project_type);
                $('.title-pad').text(project.name);
                $('#show-photo').attr('src', getImageProject(project.photo));
                $('#created_at').text('Criado em ' + project.created_at);
                if (project.status == '1') {
                    $('#show-status').text('Ativo').addClass('badge-success');
                } else {
                    $('#show-status').text('Inativo').addClass('badge-danger');
                }
                $('#show-description').text(project.description);

                let approvedSalesValue = parseFloat(project.approved_sales_value).toLocaleString('pt-BR');
                let chargeback = parseFloat(project.chargeback_count).toLocaleString('pt-BR');
                let trackings = parseFloat(project.without_tracking).toLocaleString('pt-BR');
                let TotalOfSales = parseFloat(project.approved_sales).toLocaleString('pt-BR');

                $('#value-chargeback').text(chargeback);
                $('#value-open-tickets').text(project.open_tickets);
                $('#value-without-tracking').text(trackings);
                $('#total-approved').text(TotalOfSales);
                $('#total-approved-value').text(approvedSalesValue);

                $('.page-content').show();
                $('.page').removeClass('low-opacity');

                disableTabsAndElements(project);

                loadingOnScreenRemove();
            },
        });
    }

    // CARD 2 CARREGA TELA DE EDITAR PROJETO
    function updateConfiguracoes() {
        loadOnAny('#update-project');

        $.ajax({
            method: 'GET',
            url: '/api/projects/' + projectId + '/edit',
            dataType: 'json',
            headers: {
                Authorization: $('meta[name="access-token"]').attr('content'),
                Accept: 'application/json',
            },
            error: function(response) {
                loadingOnScreenRemove();

                loadOnAny('#update-project', true);
                $('#trash').removeClass('d-none');
                errorAjaxResponse(response);
            },
            success: function(data) {
                localStorage.setItem('projectConfig', JSON.stringify(data));
                renderProjectConfig(data);

                loadingOnScreenRemove();
                loadOnAny('#update-project', true);
                $('#trash').removeClass('d-none');
            },
        });
    }

    let firstCategory = ['tab-domains', 'tab_plans', 'tab-fretes', 'tab-checkout'];

    let secondCategory = ['tab_pixels', 'tab_upsell', 'tab_order_bump', 'tab_coupons', 'tab_reviews'];

    let thirdCategory = ['tab_sms'];

    $('.nav-tabs-horizontal .nav-link').click((e) => {
        let currentActive = $('.nav-link.active');
        let currentElement = e.target.id;

        if (currentActive.attr('id') !== currentElement) {
            currentActive.removeClass('active');
        }

        if ($.inArray(currentElement, firstCategory) !== -1) {
            $('#first-category').css('color', '#2E85EC');
            $('#second-category').css('color', '#9C9C9C');
            $('#third-category').css('color', '#9C9C9C');
        }

        if ($.inArray(currentElement, secondCategory) !== -1) {
            $('#first-category').css('color', '#9C9C9C');
            $('#second-category').css('color', '#2E85EC');
            $('#third-category').css('color', '#9C9C9C');
        }

        if ($.inArray(currentElement, thirdCategory) !== -1) {
            $('#first-category').css('color', '#9C9C9C');
            $('#second-category').css('color', '#9C9C9C');
            $('#third-category').css('color', '#2E85EC');
        }
    });

    $('.slick-track').on('click', function() {
        $('.nav-tabs-horizontal .tab-pane').removeClass('active show');
    });
    // FIM COMPORTAMENTOS DA TELA

    // CARD 3 CONFIGURACAO DO PLUGIN DE ADD FOTO
    dropifyOptions = {
        messages: {
            default: '',
            replace: 'Arraste e solte uma imagem ou selecione um arquivo',
            remove: 'Remover',
            error: '',
        },
        error: {
            fileSize: 'O tamanho máximo do arquivo deve ser {{ value }}.',
            minWidth: 'A imagem deve ter largura maior que 651px.',
            maxWidth: 'A imagem deve ter largura menor que 651px.',
            minHeight: 'A imagem deve ter altura maior que 651px.',
            maxHeight: 'A imagem deve ter altura menor que 651px.',
            fileExtension: 'A imagem deve ser algum dos formatos permitidos. ({{ value }}).',
        },
        tpl: {
            message:
                '<div class="dropify-message"><span class="file-icon" /> <p class="msg">{{ default }}<span class="text-primary font-size-16">Clique ou arraste seu <br> arquivo aqui</span></p></div>',
            clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
        },
        imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg'],
    };

    // CARD 3 SE NAO ACHAR IMAGEM SETTA UMA PADRAO
    $('img').on('error', function() {
        $(this).attr('src', 'https://azcend-digital-products.s3.amazonaws.com/admin/produto.svg');
    });

    // CARD 4 - TEXTAREA TERMOS DE AFILIACAO
    let quill = new Quill('#termsaffiliates', {
        theme: 'snow',
        modules: {
            toolbar: ['bold', 'italic', 'underline'],
        },
    });

    $(document).ready(function() {
        $('.ql-toolbar.ql-snow').addClass('d-flex justify-content-center');
    });

    $('.ql-editor').on('focus', function() {
        $('.h-200, .ql-container, .ql-snow').css('border-color', '#88BFFF');
    });

    $('.ql-editor').on('focusout', function() {
        $('.h-200, .ql-container, .ql-snow').css('border-color', '#cccccc');
    });

    const statusUrlAffiliatesEl = formUpdateProject.find('#status-url-affiliates');

    statusUrlAffiliatesEl.on('click', function() {
        if ($(this).prop('checked') == true) {
            $('#affiliation-access').show();
        } else {
            $('#affiliation-access').hide();
        }
    });

    //CONFIGURACOES CARD 3 & 4
    function renderProjectConfig(data) {
        let { project } = data;

        // AFILIACOES ON / OFF
        let getStatusAffiliation = statusUrlAffiliatesEl.prop('checked');
        if (project.status_url_affiliates == 1 && getStatusAffiliation == false) {
            statusUrlAffiliatesEl.trigger('click');
            $('.affiliation')
                .children('img')
                .attr('src', '/build/global/img/projects/afiliatesIcon.svg')
                .css('background-color', '#F2F8FF');
            $('#affiliation-access').show();
        }

        if (project.status_url_affiliates == 0) {
            $('.affiliation')
                .children('img')
                .attr('src', '/build/global/img/projects/affiliationDisable.svg')
                .css('background-color', '#F2F8FF');
            $('#affiliation-access').hide();

            if (project.status_url_affiliates == 0 && getStatusAffiliation == true) {
                statusUrlAffiliatesEl.trigger('click');
            }
        }

        //IMAGEM DO PROJETO
        replacePreview('project_photo', project.photo, '');
        if (!project.photo) {
            $('.dropify-render > img').remove();
            $('.dropify-wrapper').removeClass('has-preview');
            $('.dropify-preview').css('display', 'none');
        }
        $('#project_photo').dropify(dropifyOptions);

        //NOME E DESCRICAO
        formUpdateProject.find('#name').val(project.name);
        formUpdateProject.find('#description').val(project.description);

        //URL PAGINA
        formUpdateProject.find('#url-page').val(project.url_page ? project.url_page : 'https://');

        // DURACAO DE COOKIE
        if (project.cookie_duration == 0) {
            $('.cookie-duration').prop('selectedIndex', 0).change(); //.sirius-select
            $('.cookie-duration .sirius-select-text').text('Eterno');
        } else if (project.cookie_duration == 7) {
            $('.cookie-duration').prop('selectedIndex', 1).change(); //.sirius-select
            $('.cookie-duration .sirius-select-text').text('7 dias');
        } else if (project.cookie_duration == 15) {
            $('.cookie-duration').prop('selectedIndex', 2).change(); //.sirius-select
            $('.cookie-duration .sirius-select-text').text('15 dias');
        } else if (project.cookie_duration == 30) {
            $('.cookie-duration').prop('selectedIndex', 3).change(); //.sirius-select
            $('.cookie-duration .sirius-select-text').text('1 mês');
        } else if (project.cookie_duration == 60) {
            $('.cookie-duration').prop('selectedIndex', 4).change(); //.sirius-select
            $('.cookie-duration .sirius-select-text').text('2 meses');
        } else if (project.cookie_duration == 180) {
            $('.cookie-duration').prop('selectedIndex', 5).change(); //.sirius-select
            $('.cookie-duration .sirius-select-text').text('6 meses');
        } else if (project.cookie_duration == 365) {
            $('.cookie-duration').prop('selectedIndex', 6).change(); //.sirius-select
            $('.cookie-duration .sirius-select-text').text('1 ano');
        }

        // PORCENTAGEM
        $('#percentage-affiliates').val(0);

        $('#percentage-affiliates').click(function() {
            if ($(this).val() == '0') $(this).val('');
        });
        $('#percentage-affiliates').blur(function() {
            if ($(this).val() == '') $(this).val('0');
        });

        $('#percentage-affiliates').mask('000', {
            reverse: true,
            onKeyPress: function(val, e, field, options) {
                if (val > 100) {
                    $('#percentage-affiliates').val('');
                }
            },
        });

        if (project.percentage_affiliates) {
            formUpdateProject.find('#percentage-affiliates').val(project.percentage_affiliates);
        }

        // TIPO DE COMISSAO
        formUpdateProject
            .find('.commission-type-enum input')
            .filter(`[value=${project.commission_type_enum}]`)
            .prop('checked', true);

        // DELETA TEXTO E RESGATA O TEXTO SALVO
        quill.setContents([{ insert: '\n' }]);
        quill.clipboard.dangerouslyPasteHTML(0, project.terms_affiliates ? project.terms_affiliates : ' ');

        // AFILIACAO AUTOMATICA
        if (project.automatic_affiliation == 1) {
            formUpdateProject.find('.automatic-affiliation input').prop('checked', true);
        }

        // URL CONVIDE AFILIADOS
        formUpdateProject.find('#url-affiliates').val(project.url_affiliates);

        // COMPORTAMENTO CARD SALVAR / CANCELAR
        if (!onChangeSet) {
            formUpdateProject.on('input change', function() {
                $('#confirm-changes').fadeIn('slow');
            });

            $('.dropify-clear, .o-bin-1').on('click', function() {
                $('.dropify-errors-container > ul > li').remove();
                localStorage.setItem('photo_remove', true);
                $('#confirm-changes').fadeIn('slow');
            });
            onChangeSet = true;
        }
        $('#confirm-changes').hide();
    }

    function replacePreview(name, src, fname = '') {
        let input = $('input[id="' + name + '"]');
        let wrapper = input.closest('.dropify-wrapper');
        let preview = wrapper.find('.dropify-preview');
        let filename = wrapper.find('.dropify-filename-inner');
        let render = wrapper.find('.dropify-render').html('');

        input.val('').attr('title', fname);
        wrapper.removeClass('has-error').addClass('has-preview');
        filename.html(fname);

        render.append(
            $('<img style="width: 100%; border-radius: 8px; object-fit: cover;" />')
                .attr('src', src)
                .css('height', input.attr('height')),
        );
        preview.fadeIn();
    }

    function messageErrors(defaultMessage, menssageError = '') {
        $('#confirm-changes').fadeOut(3000);

        if (menssageError != '') {
            $('#data-error span').html(menssageError);
            $('#data-error').fadeIn(1000).delay(2000).fadeOut(2000);
            setTimeout(function() {
                $('#data-error span').html(defaultMessage);
            }, 5000);
        } else {
            $('#data-error span').html(defaultMessage);
            $('#data-error').fadeIn(2000).delay(2000).fadeOut(2000);
        }
        $('#confirm-changes').fadeIn(2000);
    }

    let imgReady;
    let getDefaultErrorMessage = $('#data-error span').html();
    const projectNameInput = formUpdateProject.find('#name');

    function validateForm(photoIsValid) {
        if (projectNameInput.val().length <= 2) {
            projectNameInput.addClass('error-alert');
            let messageError = '<strong>Ops!</strong> Você precisa preencher os campos indicados.';
            messageErrors(getDefaultErrorMessage, messageError);
            return false;
        }

        if (photoIsValid === false) {
            messageErrors(getDefaultErrorMessage);
            return false;
        }
        projectNameInput.removeClass('error-alert');
        return true;
    }

    $('#project_photo').on('dropify.errors', function() {
        messageErrors(getDefaultErrorMessage);
        imgReady = false;
    });

    $('#project_photo').on('dropify.fileReady', function() {
        imgReady = true;
        if (validateForm(imgReady)) {
            $('#bt-update-project').prop('disabled', false);
        }
    });

    // INPUT AFFILIATION
    statusUrlAffiliatesEl.on('click', function() {
        let affiliationStatus = statusUrlAffiliatesEl.prop('checked');
        if (affiliationStatus == false) {
            $('.affiliation').children('img').attr('src', '/build/global/img/projects/affiliationDisable.svg');
        } else if (affiliationStatus == true) {
            $('.affiliation').children('img').attr('src', '/build/global/img/projects/afiliatesIcon.svg');
        }
    });

    // CARD 4 BOTAO DE COPIAR LINK
    $('#copy-link-affiliation').on('click', function() {
        let copyText = document.getElementById('url-affiliates');
        copyText.select();
        document.execCommand('copy');

        alertCustom('success', 'Link copiado!');
    });

    // CARD DELETAR PROJETO
    $('#bt-delete-project').on('click', function(event) {
        event.preventDefault();
        let name = $('#name').val();
        $('#modal_excluir_titulo').html('Remover loja ' + name + ' ?');

        $('#modal-delete-project .btn-delete').on('click', function() {
            $('#modal-delete').modal('hide');
            loadingOnScreen();
            $.ajax({
                method: 'DELETE',
                url: '/api/projects/' + projectId,
                dataType: 'json',
                headers: {
                    Authorization: $('meta[name="access-token"]').attr('content'),
                    Accept: 'application/json',
                },
                error: function(response) {
                    $('.modal-backdrop').remove();
                    loadingOnScreenRemove();
                    errorAjaxResponse(response);
                },
                success: function(data) {
                    loadingOnScreenRemove();

                    if (data == 'success') {
                        window.location = '/projects';
                    } else {
                        alertCustom('error', 'Erro ao deletar loja');
                    }
                },
            });
        });
    });

    // SALVAR AS CONFIGURACOES DO PROJETO
    formUpdateProject.on('submit', function(event) {
        if (!validateForm(imgReady)) {
            return false;
        }

        let getTextSaveChanges = $('.final-card span').html();
        $('.final-card span').html('Um momento... <strong>Estamos salvando suas alterações.</strong>');

        $('#options-buttons').children().hide();
        $('.loader').show();

        event.preventDefault();
        $('.page').addClass('low-opacity');

        // Pega tags e texto joga no input pra salvar no banco
        let formatedText = quill.root.innerHTML;
        $('#terms_affiliates').val(formatedText);

        let verify = verificaParcelas(parcelas, parcelasJuros);

        let statusUrlAffiliates = 0;
        if (statusUrlAffiliatesEl.prop('checked')) {
            statusUrlAffiliates = 1;
        }

        let automaticAffiliation = 0;
        if (formUpdateProject.find('.automatic-affiliation input').prop('checked')) {
            automaticAffiliation = 1;
        }

        let formData = new FormData(document.getElementById('update-project'));
        formData.append('status_url_affiliates', statusUrlAffiliates);
        formData.append('automatic_affiliation', automaticAffiliation);

        if (!$('#project_photo').prop('files').length) {
            formData.delete('project_photo');
        }

        if (localStorage.getItem('photo_remove') == 'true') {
            formData.append('remove_project_photo', true);
        }

        if (!verify) {
            $.ajax({
                method: 'POST',
                url: '/api/projects/' + projectId + '/settings',
                processData: false,
                contentType: false,
                cache: false,
                dataType: 'json',
                headers: {
                    Authorization: $('meta[name="access-token"]').attr('content'),
                    Accept: 'application/json',
                },
                data: formData,
                error: function(response) {
                    $('.page').removeClass('low-opacity');
                    errorAjaxResponse(response);
                    $('.loader').hide();
                    $('.final-card').hide();
                },
                success: function(response) {
                    localStorage.setItem('photo_remove', false);
                    updateConfiguracoes();
                    $('html, body').animate({
                        scrollTop: 410,
                    });
                    setTimeout(function() {
                        $('#confirm-changes').hide();

                        $('.final-card span').html(getTextSaveChanges);
                        $('#options-buttons').children().show();
                        $('.loader').hide();

                        $('#saved-alterations').fadeIn('slow').delay(2500).fadeOut('slow');
                    }, 1500);

                    show();
                    $('.page').removeClass('low-opacity');
                },
            });
        } else {
            $('#error-juros').css('display', 'block');
            loadingOnScreenRemove();
        }
    });

    //CANCELAR
    $('#cancel-edit').on('click', function() {
        renderProjectConfig(JSON.parse(localStorage.getItem('projectConfig')));
        $('#confirm-changes').fadeOut('slow');
        $('html, body').animate({
            scrollTop: 410,
        });
        localStorage.setItem('photo_remove', false);
        $('#bt-update-project').prop('disabled', false);
    });

    show();
});
