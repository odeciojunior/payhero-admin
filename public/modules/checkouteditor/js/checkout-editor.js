$(document).ready(function () {

  $('#logo_upload').dropify({
      messages: {
          'default': '',
          'replace': '',
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
          message: '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Clique ou arraste e solte aqui</span></p></div>',
          clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
      },
      imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg'],
  });

  $('#banner_upload').dropify({
      messages: {
          'default': '',
          'replace': '',
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
          message: '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Faça upload do seu banner</span></p></div>',
          clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
      },
      imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg'],
  });

  $('#checkout-type').on('click', '.btn', function() {
    $(this).addClass('btn-active').siblings().removeClass('btn-active');
  });

  $("#download-template-banner").on("click", function () {
    console.log($('#1').is(":checked"))
  });
  

  // ----------------- Editor de Texto --------------------
  
  var quillTextbar = new Quill('#textbar_editor', {
    modules: {
      toolbar: '#textbar_editor_toolbar_container'
    },
    placeholder: '',
    theme: 'snow'
  });

  var quillThanksPage = new Quill('#thanks_page_editor', {
    modules: {
      toolbar: '#thanks_page_editor_toolbar_container'
    },
    placeholder: '',
    theme: 'snow'
  });
  
  // Enable all tooltips
  $('[data-toggle="tooltip"]').tooltip();


  // ----------------- Função de Collapse --------------------
  $('.switch-checkout').off().on('click', function () {
    let checked = $(this).prop('checked');
    if (checked) {
      $("."+$(this).attr('data-target')).show('fast')
    } else {
      $("."+$(this).attr('data-target')).hide('fast')
    }
  });

  // ----------------- Função de Collapse --------------------
  $('.switch-checkout-accordion').off().on('click', function () {
    let checked = $(this).prop('checked');
    if (checked) {
      $("."+$(this).attr('data-target')).show('fast')
      $("."+$(this).attr('data-toggle')).hide('fast')

      var primaryColor   = $('label[for="' + $('input[name=theme_ready]:checked').attr('id') + '"]').children(".theme-primary-color").css('background-color')
      var secondaryColor = $('label[for="' + $('input[name=theme_ready]:checked').attr('id') + '"]').children(".theme-secondary-color").css('background-color')

      $(":root").css('--primary-color', primaryColor);
      $(":root").css('--secondary-color', secondaryColor);
    } else {
      $("."+$(this).attr('data-target')).hide('fast')
      $("."+$(this).attr('data-toggle')).show('fast')

      var primaryColor = $('#custom_primary_color').val()
      var secondaryColor = $('#custom_secondary_color').val()

      $(":root").css('--primary-color', primaryColor);
      $(":root").css('--secondary-color', secondaryColor);
    }
  });

  // ----------------- Função Colors --------------------
  $('.theme-radio').on('click', function() {
  
    var primaryColor = $('label[for="' + $(this).attr('id') + '"]').children(".theme-primary-color").css('background-color')
    var secondaryColor = $('label[for="' + $(this).attr('id') + '"]').children(".theme-secondary-color").css('background-color')

    $(":root").css('--primary-color', primaryColor);
    $(":root").css('--secondary-color', secondaryColor);
  });

  (function hideToogleAccordions() {
    if ( $(".switch-checkout-accordion").is('checked')){
      $("."+$(".switch-checkout-accordion").attr('data-toggle')).hide('fast')
    } else {
      $("."+$(".switch-checkout-accordion").attr('data-target')).hide('fast')
    }
  })();

  // Custom buttons
  $('#custom_primary_color').on('input', function(){
    $(":root").css('--primary-color', $(this).val());
  });

  $('#custom_secondary_color').on('input', function(){
    $(":root").css('--secondary-color', $(this).val());
  });

  $('#custom_finish_color').on('input', function(){
    $(":root").css('--finish-button-color', $(this).val());
  });

  $('#download_template_banner').on('click',function(e) {
    e.preventDefault();  
  });


  $('.accept-payment-method').on('change', function() {
    if($(this).is(':checked')){
      $('#' + $(this).attr('data-target-preview')).removeClass('hide')
      $('.' + $(this).attr('data-target')).fadeIn('slow')
    }else {
      $('#' + $(this).attr('data-target-preview')).addClass('hide')
      $('.' + $(this).attr('data-target')).fadeOut('slow')
    }
  });


});


// -------------------------- Funções de Scroll -----------------------

$(window).on('scroll', function() {
  if($('#tab-checkout').hasClass('active')){

    var scrollWindow = $(this).scrollTop();
    var topVisual = $('#checkout_type').position().top;  
    var topPayment = $('#payment_container').position().top - 200;  

    if(scrollWindow > topVisual) {
      var marginTop = (scrollWindow - topVisual);
      $('#preview_div').css("margin-top", marginTop + 10);
    } else {
      $('#preview_div').css("margin-top", 0);
    };
  
    if(scrollWindow < topPayment){
      $('#preview_payment').fadeOut('slow');
      $('#preview_visual').fadeIn('slow');
    } else if (scrollWindow > topPayment){
      $('#preview_visual').fadeOut('slow');
      $('#preview_payment').fadeIn('slow');
    }
  }
  

});


function showBannerPreview(event) {
  if(event.target.files.length > 0){
    const src = URL.createObjectURL(event.target.files[0])
    const imgPreview = document.getElementById("preview_banner_img")
    
    imgPreview.src = src
    img.style.display = 'block'
  }
}
