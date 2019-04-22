$(function () {

    var Accordion = function (el, multiple) {
        this.el = el || {};
        this.multiple = multiple || false;
        // Variables privadas
        var links = this.el.find('.link');
        // Evento
        links.on('click', {
            el: this.el,
            multiple: this.multiple
        }, this.dropdown)
    }
    Accordion.prototype.dropdown = function (e) {
        var $el = e.data.el;
        $this = $(this),
            $next = $this.next();
        $next.slideToggle();
        $this.parent().toggleClass('open');
        if (!e.data.multiple) {
            $el.find('.submenu').not($next).slideUp().parent().removeClass('open');
        };
    }
    var accordion = new Accordion($('#accordion'), false);
});

feather.replace();

$(document).ready(function () {

    if ($(window).width() > 978) {
        $('#select_parcelas_desktop').attr('name', 'parcelas');
    }
    else {
        $('#select_parcelas_mobile').attr('name', 'parcelas');
    }

    $(window).resize(function () {
        if ($(window).width() > 978) {
            $('#select_parcelas_desktop').attr('name', 'parcelas');
            $('#select_parcelas_mobile').removeAttr('parcelas');
        }
        else {
            $('#select_parcelas_mobile').attr('name', 'parcelas');
            $('#select_parcelas_desktop').removeAttr('parcelas');
        }
    });

    $('#accordion_cartao').on('click', function () {
        if ($('#accordion_cartao').parent().hasClass('open')) {
            if ($('#accordion_boleto').parent().hasClass('open')) {
                $('#accordion_boleto').click();
            }
        }
        else {
            if (!$('#accordion_boleto').parent().hasClass('open')) {
                $('#accordion_boleto').click();
            }
        }
    });

    $('#accordion_boleto').on('click', function () {
        if ($('#accordion_boleto').parent().hasClass('open')) {
            if ($('#accordion_cartao').parent().hasClass('open')) {
                $('#accordion_cartao').click();
            }
        }
        else {
            if (!$('#accordion_boleto').parent().hasClass('open') && !$('#accordion_cartao').parent().hasClass('open')) {
                $('#accordion_cartao').click();
            }
        }
    });

    $('#card-number').on('input', function () {

        $('#card-number').validateCreditCard(function (result) {
            if (result.card_type == null) {
                $('#cartao_logo').html("");
                return false;
            }
            if (result.card_type.name == 'amex') {
                $('#cartao_logo').html("<img src='" + path_amex_card + "' alt=' ' style='height: 40px; width: 70px'>");
            }
            else if (result.card_type.name == 'dinners') {
                $('#cartao_logo').html("<img src='" + path_dinners_card + "' alt=' ' style='height: 40px; width: 70px'>");
            }
            else if (result.card_type.name == 'elo') {
                $('#cartao_logo').html("<img src='" + path_elo_card + "' alt=' ' style='height: 40px; width: 70px'>");
            }
            else if (result.card_type.name == 'hipercard') {
                $('#cartao_logo').html("<img src='" + path_hipercard_card + "' alt=' ' style='height: 40px; width: 70px'>");
            }
            else if (result.card_type.name == 'mastercard' || result.card_type.name == 'maestro') {
                $('#cartao_logo').html("<img src='" + path_master_card + "' alt=' ' style='height: 40px; width: 70px'>");
            }
            else if (result.card_type.name == 'visa') {
                $('#cartao_logo').html("<img src='" + path_visa_card + "' alt=' ' style='height: 40px; width: 70px'>");
            }
        });
    });

    $('#card-name').on('keyup change', function () {
        $t = $(this);
        $('.credit-card-box .card-holder div').html($t.val());
    });
    $('#card-number').on('keyup change', function () {
        $t = $(this);
        $('.credit-card-box .number div').html($t.val());
    });
    $('#card-expiration').on('keyup change', function () {
        $t = $(this);
        $('.credit-card-box .card-expiration-date div').html($t.val());
    });
    $('#card-cvv').on('focus', function () {
        $('.credit-card-box').addClass('hover');
    }).on('blur', function () {
        $('.credit-card-box').removeClass('hover');
    }).on('keyup change', function () {
        $('.ccv div').html($(this).val());
    });


    // AO CLICAR NO BOTÃO DA TOPBAR 
    $('#abrirTopbar').on('click', function () {

        $('#corpo').addClass('blur');
        
        $('.detalhesPedido').css('opacity', '1');

        $('#detalhes').show(500);

        $('#abrirTopbar').css('display', 'none');
        $('#fecharTopbar').css('display', 'block');

        //SE O TAMANHO DA TELA FOR MENOR QUE O ESPERADO PARA DESKTOP, O CORPO DO FORMULÁRIO SUMIRÁ E A TOPBAR TERÁ HEIGHT 100%
        if (screen.width < 767) {
            $('#corpoForm').addClass('hiddenforNav');
            $('.topbar').addClass('.fullscreen');
        }
    });

    // AO CLICAR NO BOTÃO DA TOPBAR 
    $('#fecharTopbar').on('click', function () {

        $('#corpo').removeClass('blur');
        
        $('.detalhesPedido').css('opacity', '0');

        $('#detalhes').hide(500);

        $('#abrirTopbar').css('display', 'block');
        $('#fecharTopbar').css('display', 'none');

        //SE O TAMANHO DA TELA FOR MENOR QUE O ESPERADO PARA DESKTOP, O CORPO DO FORMULÁRIO SUMIRÁ E A TOPBAR TERÁ HEIGHT 100%
        if (screen.width < 767) {
            $('#corpoForm').addClass('hiddenforNav');
            $('.topbar').addClass('.fullscreen');
        }

        // SE O TAMANHO DA TELA FOR MENOR QUE O ESPERADO PARA DESKTOP, O CORPO DO FORMULÁRIO SUMIRÁ E A TOPBAR TERÁ HEIGHT 100%
        if (screen.width < 767) {
            $('#corpoForm').removeClass('hiddenforNav');
            $('.topbar').removeClass('.fullscreen');
        }

    });

});
