$(document).ready(function () {
  //  -------------------- DROPIFY --------------------------
  $('#logo_upload').dropify({
      messages: {
          'default': '',
          'replace': '',
          'remove': 'Remover',
          'error': ''
      },
      error: {
          'fileSize': 'O tamanho máximo do arquivo deve ser {{ value }}.',
          'minWidth': '',
          'maxWidth': 'A imagem deve ter largura menor que 300px.',
          'minHeight': '',
          'maxHeight': 'A imagem deve ter altura menor que 300px.',
          'fileExtension': 'A imagem deve ser algum dos formatos permitidos. ({{ value }}).'
      },
      tpl: {
          message: '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}<span style="color: #2E85EC;">Clique ou arraste e solte aqui</span></p></div>',
          clearButton: '<button type="button" class="dropify-clear o-bin-1"></button>',
      },
      imgFileExtensions: ['png', 'jpg', 'jpeg', 'svg'],
  });


  var drEventBanner = $('#banner_upload').dropify({
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
    imgFileExtensions: ['png', 'jpg', 'jpeg'],
  });

  var bs_modal = $('#modal_banner');
  var image = document.getElementById('cropped_image');
  var cropper,reader,file;

  drEventBanner.on('dropify.fileReady', function(event, element) {
    var files = event.target.files;
    var done = function(url) {
        image.src = url;
        bs_modal.modal('show');
    };

    if (files && files.length > 0) {
      file = files[0];

      if (URL) {
          done(URL.createObjectURL(file));
      } else if (FileReader) {
          reader = new FileReader();
          reader.onload = function(e) {
              done(reader.result);
          };
          reader.readAsDataURL(file);
      }
    }
  });

  drEventBanner.on('dropify.beforeClear', function(event, element) {
    var imgPreview = document.getElementById('preview_banner_img');
    imgPreview.src = '';
  });
  
  //  ----------------- Crop Modal ----------------------

  bs_modal.on('shown.bs.modal', function() {
    cropper = new Cropper(image, {
        highlight: false,
        movable: false,
        viewMode: 3,
        aspectRatio: 960 / 210,
    });
  }).on('hidden.bs.modal', function() {
      cropper.destroy();
      cropper = null;
  });

  $("#button-crop").on('click', function() {
    var canvas = cropper.getCroppedCanvas();
    var src = canvas.toDataURL();

    var imgPreview = document.getElementById('preview_banner_img');
    imgPreview.src = src;

    replacePreview('banner_top', src, 'Image.jpg');
    console.log(drEventBanner);
    drEventBanner.attr('data-file', src);
    bs_modal.modal('hide');
  });
  
  $("#button-cancel-crop").on('click', function() {
    $('#banner_upload').parent().find(".dropify-clear").trigger('click');
  });
  


  // ----------------------- Funções de Botão ----------------------------

  $('#default_finish_color').on('change', function (){
    if($(this).is(':checked')){
      $(":root").css('--finish-button-color', '#23D07D');
      $('#custom_finish_color').prop('disabled', true);
      $('#custom_finish_color').css('opacity', '0.3');
    }else{
      $(":root").css('--finish-button-color', $('#custom_finish_color').val());
      $("#custom_finish_color").prop('disabled', false);
      $('#custom_finish_color').css('opacity', '1');
    }
  });

  $('#checkout-type').on('click', '.btn', function() {
    $(this).addClass('btn-active').siblings().removeClass('btn-active');
  });

  $("#download_template_banner").on("click", function (e) {
    e.preventDefault();
    window.open($(this).attr('data-href'), "_blank");
  });

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
    const form = document.querySelector('#form_checkout_editor');
    const checkboxes = form.querySelectorAll('input[name=accept_payment_method]');
    const checkboxLength = checkboxes.length;
    var oneChecked = false;

    for (let i = 0; i < checkboxLength; i++) {
        if (checkboxes[i].checked) oneChecked = true;
    }

    if(!oneChecked){
      $(this).prop('checked', true);
    }else {
      if($(this).is(':checked')){
        $('#' + $(this).attr('data-preview')).show('slow', 'swing')
        $('.' + $(this).attr('data-target')).slideDown('slow', 'swing')
      }else {
        $('#' + $(this).attr('data-preview')).hide('slow', 'swing')
        $('.' + $(this).attr('data-target')).slideUp('slow', 'swing')
      }
    }
  });

  $('.accept-payment-type').on('change', function() {
    const form = document.querySelector('#form_checkout_editor');
    const checkboxes = form.querySelectorAll('input[name=accept_payment_type]');
    const checkboxLength = checkboxes.length;
    var oneChecked = false;

    for (let i = 0; i < checkboxLength; i++) {
        if (checkboxes[i].checked) oneChecked = true;
    }

    if(!oneChecked){
      $(this).prop('checked', true);
    }
  });
  

  $('input[name=banner-type').on('click', function(){
    var bannerType = $(this).val();

    if(bannerType === "square"){
      $('.preview-banner').addClass('square-computer');
      $('.preview-banner').removeClass('wide');
    }else {
      $('.preview-banner').removeClass('square-computer');
      $('.preview-banner').addClass('wide');banner_preview
    }
  });

  $('input[name=checkout-type').on('click', function(){
    var checkoutType = $(this).val();

    if(checkoutType === "three_steps"){
      $('.visual-content-left').addClass('three-steps');
      $('.visual-content-left').removeClass('unique');
      $('#three_steps_preview').slideDown('slow', 'swing');
      $('#finish_button_preview').slideDown('slow', 'swing');
    }else {
      $('.visual-content-left').removeClass('three-steps');
      $('.visual-content-left').addClass('unique');
      $('#three_steps_preview').slideUp('slow', 'swing');
      $('#finish_button_preview').slideUp('slow', 'swing');
    }
  });

  $('.add-tag').on('click', function(e){
    e.preventDefault();
    var input = $(this).attr('data-input');
    $(input).val($(input).val() + " " +$(this).attr('data-tag') + " ");
    $(input).focus();
  });

  // ----------------- Função de Collapse --------------------
  $('.switch-checkout').off().on('click', function () {
    let checked = $(this).prop('checked');
    if (checked) {
      $("."+$(this).attr('data-target')).slideDown('slow', 'swing');
      $($(this).attr('data-preview')).slideDown('slow', 'swing');
    } else {
      $("."+$(this).attr('data-target')).slideUp('slow', 'swing');
      $($(this).attr('data-preview')).slideUp('slow', 'swing');
      
    }
  });

  $('.switch-checkout-accordion').off().on('click', function () {
    let checked = $(this).prop('checked');
    if (checked) {
      $("."+$(this).attr('data-target')).slideDown('slow', 'swing');
      $("."+$(this).attr('data-toggle')).slideUp('slow', 'swing');

      var primaryColor = $('#custom_primary_color').val()
      var secondaryColor = $('#custom_secondary_color').val()
      var finishButtonColor = '#23D07D';
      
      if(!$('#default_finish_color').is(':checked')){
        var finishButtonColor   =  $('#custom_finish_color').val();
      }
      
      $(":root").css('--primary-color', primaryColor);
      $(":root").css('--secondary-color', secondaryColor);
      
      $(":root").css('--finish-button-color', finishButtonColor);

    } else {
      $("."+$(this).attr('data-target')).slideUp('slow', 'swing');
      $("."+$(this).attr('data-toggle')).slideDown('slow', 'swing');

      var primaryColor   = $('label[for="' + $('input[name=theme_ready]:checked').attr('id') + '"]').children(".theme-primary-color").css('background-color')
      var secondaryColor = $('label[for="' + $('input[name=theme_ready]:checked').attr('id') + '"]').children(".theme-secondary-color").css('background-color')

     

      $(":root").css('--primary-color', primaryColor);
      $(":root").css('--secondary-color', secondaryColor);
      $(":root").css('--finish-button-color', primaryColor);
    }
  });

  // ----------------- Função Colors --------------------
  $('.theme-radio').on('click', function() {
  
    var primaryColor = $('label[for="' + $(this).attr('id') + '"]').children(".theme-primary-color").css('background-color')
    var secondaryColor = $('label[for="' + $(this).attr('id') + '"]').children(".theme-secondary-color").css('background-color')

    $(":root").css('--primary-color', primaryColor);
    $(":root").css('--secondary-color', secondaryColor);
    $(":root").css('--finish-button-color', primaryColor);
  });

  (function hideToogleAccordions() {
    if ( $(".switch-checkout-accordion").is('checked')){
      $("."+$(".switch-checkout-accordion").attr('data-toggle')).slideUp('slow', 'swing');
    } else {
      $("."+$(".switch-checkout-accordion").attr('data-target')).slideUp('slow', 'swing');
    }
  })();

  $("#whatsapp_phone").mask("(00) 00000-0000");

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

  quillThanksPage.on('text-change', function() {
    $(".shop-message-preview-content").empty();
    $(".shop-message-preview-content").append( $(quillThanksPage.root.innerHTML) );
  });

  $('#thanks_page_title').on('input', function(){
    $(".shop-message-preview-title").empty();
    $(".shop-message-preview-title").append( $(this).val() );
  });
  
  // Enable all tooltips
  $('[data-toggle="tooltip"]').tooltip();


  $('#form_checkout_editor').on('change', function(){
    $('#changing_container').fadeIn('slow', 'swing');
  });

  $('#changing_container').on('click', function(e){
    e.preventDefault();

    $(this).fadeOut('slow', 'swing');

    // Save form...

    $('#done').fadeIn('slow', 'swing');

    setTimeout(function(){
      $('#done').fadeOut('slow', 'swing');
    }, 5000)

  });

  $('input[name=number]').on('input', () => {
    $(this).attr('value', $(this).val().replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'));
  });

  
});


// -------------------------- Funções de Scroll -----------------------

$(window).on('scroll', function() {
  if($('#tab-checkout').hasClass('active')){

    var scrollWindow = $(this).scrollTop();
    var topVisual = $('#checkout_type').position().top;  
    var topPayment = $('#payment_container').position().top - 200;  
    var topPostPurchase = $('#post_purchase').position().top - 200;  
    
    if(scrollWindow > topVisual) {
      var marginTop = (scrollWindow - topVisual);  
      $('#preview_div').css("margin-top", marginTop + 10);
    } else {
      $('#preview_div').css("margin-top", 0);
    };
  
    if(scrollWindow < topPayment){
      $('#preview_payment').fadeOut('slow', 'swing');
      $('#preview_post_purchase').fadeOut('slow', 'swing');
      $('#preview_visual').fadeIn('slow', 'swing');
    } else if (scrollWindow > topPayment && scrollWindow < topPostPurchase){
      $('#preview_visual').fadeOut('slow', 'swing');
      $('#preview_post_purchase').fadeOut('slow', 'swing');
      $('#preview_payment').fadeIn('slow', 'swing');
    }else if ( scrollWindow > topPostPurchase){
      $('#preview_visual').fadeOut('slow', 'swing');
      $('#preview_payment').fadeOut('slow', 'swing');
      $('#preview_post_purchase').fadeIn('slow', 'swing');
    }
  }
});

function replacePreview(name, src, fname = '') {
  let input = $('input[name="' + name + '"]');
  let wrapper = input.closest('.dropify-wrapper');
  let preview = wrapper.find('.dropify-preview');
  let filename = wrapper.find('.dropify-filename-inner');
  let render = wrapper.find('.dropify-render').html('');

  input.val('').attr('title', fname);
  wrapper.removeClass('has-error').addClass('has-preview');
  filename.html(fname);

  render.append($('<img style="width: 100%; border-radius: 8px; object-fit: cover;" />').attr('src', src).css('height', input.attr('height')));
  preview.fadeIn();
}
