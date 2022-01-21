$(() => {
    let projectId = $(window.location.pathname.split('/')).get(-1);
    $('.percentage-affiliates').mask('###', {'translation': {0: {pattern: /[0-9*]/}}});
    let onChangeSet = false;
    
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

    // CARD 1 FOTO, NOME, CRIADO EM, DESCRICAO E RESUMO
    const getImageProject = projectPhoto => projectPhoto ? dropifyOptions.defaultFile = projectPhoto : "/modules/global/img/projeto.svg";

    function show() {
        $(".page").addClass("low-opacity");

        //loadingOnScreen();
        // loadOnAny('#tab_info_geral .card', false, {
        //     styles: {
        //         container: {
        //             minHeight: '250px'
        //         }
        //     }
        // });

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
                $(".page").removeClass("low-opacity");
                //loadingOnScreenRemove();
            },
            success: (response) => {

                let project = response.data;
                $('.title-pad').text(project.name);
                $('#show-photo').attr('src', getImageProject(project.photo));
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
                $(".page").removeClass("low-opacity");

                // loadOnAny('#tab_info_geral .card', true);
                loadingOnScreenRemove();
            }
        });
    }

    // CARD 2 CARREGA TELA DE EDITAR PROJETO
    function updateConfiguracoes() {
        // loadOnAny('#tab_configuration_project .card');
        $("#update-project").addClass("low-opacity");
        
        $.ajax({
            method: "GET",
            url: "/api/projects/" + projectId + '/edit',
            dataType: "json",
            headers: {
                'Authorization': $('meta[name="access-token"]').attr('content'),
                'Accept': 'application/json',
            }, error: function (response) {
                $("#update-project").removeClass("low-opacity");
                // loadOnAny('#tab_configuration_project .card', true);
                errorAjaxResponse(response);

            }, success: function (data) {
                localStorage.setItem('projectConfig',JSON.stringify(data));
                renderProjectConfig(data);

                //manter o card azul escondido
                $("#update-project").removeClass("low-opacity");
                // loadOnAny('#tab_configuration_project .card', true);
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
    // FIM COMPORTAMENTOS DA TELA
    

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

    // CARD 3 SE NAO ACHAR IMAGEM SETTA UMA PADRAO
    $("img").on("error", function () {
        $(this).attr("src", "https://cloudfox-files.s3.amazonaws.com/produto.svg");
    });
    
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
    $(".ql-editor").on("focus", function(){
        $(".h-200, .ql-container, .ql-snow").css("border-color", "#88BFFF");
    });
    $(".ql-editor").on("focusout", function(){
        $(".h-200, .ql-container, .ql-snow").css("border-color", "#cccccc");
    });

    //CONFIGURACOES CARD 3 & 4
    function renderProjectConfig(data) {

        let {project} = data;

        // AFILIACOES ON / OFF
        let getStatusAffiliation = $("#status-url-affiliates").prop("checked");
        if (project.status_url_affiliates == 1 && getStatusAffiliation == false) {
            $('#update-project .status-url-affiliates').trigger('click');
            $(".affiliation").children("img").attr("src", "/modules/global/img/projects/afiliatesIcon.svg").css("background-color", "#F2F8FF");
        }

        if(project.status_url_affiliates == 0){
            $(".affiliation").children("img").attr("src", "/modules/global/img/projects/affiliationDisable.svg").css("background-color", "#F2F8FF");

            if(project.status_url_affiliates == 0 && getStatusAffiliation == true){
                $('#update-project .status-url-affiliates').trigger('click');
            }
        }
        
        //IMAGEM DO PROJETO
        $('#update-project #product_photo').attr('src', getImageProject(project.photo));
        $('#product_photo').dropify(dropifyOptions);


        //NOME E DESCRICAO
        $('#update-project #name').val(project.name);
        $('#update-project #description').val(project.description);

        //URL PAGINA
        $('#update-project #url-page').val(project.url_page ? project.url_page : 'https://');


        // DURACAO DE COOKIE
        if (project.cookie_duration == 0) {
            $('.sirius-select').prop("selectedIndex", 0).change();
            $(".sirius-select-text").text("Eterno");
            // $('#update-project .cookie-duration').prop('selectedIndex', 0).change();

        } else if (project.cookie_duration == 7) {
            $('.sirius-select').prop("selectedIndex", 1).change();
            $(".sirius-select-text").text("7 dias");

        } else if (project.cookie_duration == 15) {
            $('.sirius-select').prop("selectedIndex", 2).change();
            $(".sirius-select-text").text("15 dias");

        } else if (project.cookie_duration == 30) {
            $('.sirius-select').prop("selectedIndex", 3).change();
            $(".sirius-select-text").text("1 mês");

        } else if (project.cookie_duration == 60) {
            $('.sirius-select').prop("selectedIndex", 4).change();
            $(".sirius-select-text").text("2 meses");

        } else if (project.cookie_duration == 180) {
            $('.sirius-select').prop("selectedIndex", 5).change();
            $(".sirius-select-text").text("6 meses");

        } else if (project.cookie_duration == 365) {
            $('.sirius-select').prop("selectedIndex", 6).change();
            $(".sirius-select-text").text("1 ano");
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


        // DELETA TEXTO E RESGATA O TEXTO SALVO
        quill.setContents([{ insert: '\n' }]);
        quill.clipboard.dangerouslyPasteHTML(0, project.terms_affiliates ?? ' ');


        // AFILIACAO AUTOMATICA
        if (project.automatic_affiliation == 1) {
            $('#update-project .automatic-affiliation input').prop("checked", true);
        } 

        // URL CONVIDE AFILIADOS
        $('#update-project #url-affiliates').val(project.url_affiliates);

        // COMPORTAMENTO CARD SALVAR / CANCELAR
        if(!onChangeSet){
            $("#update-project :input").on('change', function() {
                $( "#confirm-changes" ).fadeIn( "slow" );
            });
            onChangeSet = true;
        }
        $( "#confirm-changes" ).hide();
    }

    // INPUT AFFILIATION
    $('#update-project .status-url-affiliates').on("click", function(){
        let affiliationStatus = $("#status-url-affiliates").prop("checked")
        if(affiliationStatus == false){
            $(".affiliation").children("img").attr("src", "/modules/global/img/projects/affiliationDisable.svg");
            $(".bg-afiliate-icon").css("background-color", "#F4F4F4");
            
        }else if(affiliationStatus == true){
            $(".affiliation").children("img").attr("src", "/modules/global/img/projects/afiliatesIcon.svg");
            $(".bg-afiliate-icon").css("background-color", "#F2F8FF");
        }
    });

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
    
    // SALVAR AS CONFIGURACOES DO PROJETO
    $("#bt-update-project").on('click', function (event) {
        
        $( "#confirm-changes" ).hide();
        event.preventDefault();
        
        // $('html, body').animate({
        //     scrollTop: 0
        // });
        $(".page").addClass("low-opacity");
        // loadingOnScreen();

        // Pega tags e texto joga no input pra salvar no banco
        let formatedText = quill.root.innerHTML;        
        $('#terms_affiliates').val(formatedText);

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
                    $(".page").removeClass("low-opacity");

                    // loadingOnScreenRemove();
                    errorAjaxResponse(response);

                }, success: function (response) {
                    //chamando atualizacao do projeto
                    updateConfiguracoes();
                    setTimeout(function () {
                        $("#saved-alterations").fadeIn('slow').delay(4000).fadeOut('slow');
                        $( "#confirm-changes" ).hide();
                    },1500);
                    
                    show();
                    $(".page").removeClass("low-opacity");
                    // loadingOnScreenRemove();
                }
            });
        } else {
            $("#error-juros").css('display', 'block');
            loadingOnScreenRemove();
        }

    });

    //CANCELAR
    $("#cancel-edit").on("click", function(){
        renderProjectConfig(JSON.parse(localStorage.getItem("projectConfig")))
        $( "#confirm-changes" ).fadeOut( "slow" );
    })
    show();
});