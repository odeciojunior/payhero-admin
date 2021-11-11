@push('css')
<link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=03') !!}">
<link rel="stylesheet" href="{!! asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
<link rel="stylesheet" href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/quill-editor.css?v=10') }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/dropfy.css?v=10') }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/color-theme-radio.css?v=10') }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/custom-inputs.css?v=10') }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/cropper.css?v=10') }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/style.css?v=10') }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/preview-styles.css?v=10') }}">
@endpush

<!-- Page -->
<div class="checkout-container" style="max-height: 3585px;  margin-bottom: 20px;">

    <div class="card card-body">
        <h1 class="checkout-title">
            Editor de Checkout
        </h1>
        <div class="checkout-subtitle">
            <span class="tag"><b>NOVO!</b></span> Adicione banner, temas pré-prontos ou personalize o seu próprio.
        </div>

    </div>

    <form id="form_checkout_editor">
        <div style="display: flex; flex-direction: column; width: 100%">
            <div class="grid-checkout-editor">

                <div id="checkout_type" class="checkout-content select-type">
                    <h1 class="checkout-title">
                        Selecione um tipo
                    </h1>

                    <div id="checkout_type" class="radio-group">
                        <input class="custom-radio" id="checkout_type_steps" type="radio" name="checkout-type" value="three_steps" checked />
                        <label for="checkout_type_steps">Checkout de 3 passos</label>

                        <input class="custom-radio" id="checkout_type_unique" type="radio" name="checkout-type" value="unique" />
                        <label for="checkout_type_unique">Checkout único</label>
                    </div>

                </div>

                <div class="checkout-content visual">
                    <span class="title-icon">
                        <img class="icon-title" src="{{ asset('/modules/checkouteditor/img/svg/visual.svg') }}">
                        <h1 class="checkout-title">
                            Visual
                        </h1>
                    </span>

                    <div class="upload-container">
                        <div id='upload_logo'>
                            <label for="logo_upload">Logo no checkout</label>
                            <input type="file" id="logo_upload" name="logo" data-height="300" data-max-width="300" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                        </div>

                        <div class="instrunctios">
                            Recomendações
                            Imagem de 300x300px
                            Formatos: JPEG ou PNG
                        </div>

                    </div>

                    <hr>

                    <div class="banner-top-container">
                        <div class="title-buttons-group">

                            
                                <h1 class="checkout-title">
                                    Banner no topo
                                </h1>
                            

                            <div style=" display: flex; min-width: 140px; align-items: center;">
                                <div id="banner_type" class="radio-group" style="justify-self: end;">
                                    <input class="custom-icon-radio" id="banner_type_wide" type="radio" name="banner-type" value="wide" checked />
                                    <label for="banner_type_wide"><img src="{{ asset('/modules/checkouteditor/img/svg/banner-wide.svg') }}"></label>

                                    <input class="custom-icon-radio" id="banner_type_square" type="radio" name="banner-type" value="square" />
                                    <label for="banner_type_square"><img src="{{ asset('/modules/checkouteditor/img/svg/banner-square.svg') }}"></label>
                                </div>

                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="banner_top_flag" name="banner_top_flag" data-target="banner-top-content" data-preview=".preview-banner" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="banner-top-content">
                            <div id='upload-banner'>
                                <input type="file" id="banner_upload" class="dropify" name="banner_top" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                            </div>

                            <div class="banner-intructions ">
                                <div class="instrunctios">
                                    <b>Indicações</b>
                                    Banner container: 960x210px
                                    Banner tela inteira: 1280x280px
                                </div>

                                <div class="instrunctios">
                                    Resoluções menores não serão aceitos. <b>Formatos: JPEG e PNG.</b>
                                </div>

                                <div class="button-template">
                                    <button id="download_template_banner" class="line-button" type="button" data-href="{{ asset('/modules/checkouteditor/img/files/test-download.jpg') }}">Baixar gabarito</button>
                                </div>

                            </div>
                        </div>

                        <hr>

                        <div class="countdown-container">
                            <div class="title-buttons-group">
                                <div>
                                    <h1 class="checkout-title">
                                        Contador regressivo
                                    </h1>
                                </div>

                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="countdown_flag" name="countdown_flag" data-target="countdown-content" data-preview=".countdown-preview" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="countdown-content">
                                <div class="input-container">
                                    <label for="countdown-time" class="checkout-label">Tempo</label>
                                    <div class="time-div">
                                        <input class="time-input" type="number" id="countdown-time" name="countdown-time" min="1" max="99" maxlength="3">
                                        <div class="min-input-label">min</div>
                                    </div>
                                </div>

                                <div class="input-container">
                                    <label for="countdown-time" class="checkout-label">Descrição <span class="observation-span">Opcional</span></label>
                                    <textarea class="checkout-textarea" id="countdown-description" name="story" rows="3"></textarea>
                                    <div class="textarea-observation">
                                        <span class="dot"></span><span class="observation-span">Visível somente em desktop.</span>
                                    </div>
                                </div>

                                <div class="input-container">
                                    <label for="timeout-message" class="checkout-label">Mensagem ao encerrar o tempo</label>
                                    <textarea class="checkout-textarea" id="timeout-message" name="timeout-message" rows="3"></textarea>
                                </div>

                            </div>
                        </div>

                        <hr>

                        <div class="textbar-container">
                            <div class="title-buttons-group">
                                <div>
                                    <h1 class="checkout-title">
                                        Barra de texto
                                    </h1>
                                </div>

                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="textbar_flag" name="textbar_flag" data-target="textbar-content" data-preview=".textbar-preview" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="textbar-content">
                                <label for="textbar_editor" class="checkout-label">Texto na barra</label>
                                <div class="editor-container">
                                    <div id="textbar_editor_toolbar_container" class="editor-toolbar-container">
                                        <button class="ql-bold" data-toggle="tooltip" data-placement="bottom" title="Negrito"></button>
                                        <button class="ql-italic" data-toggle="tooltip" data-placement="bottom" title="Itálico"></button>
                                        <button class="ql-underline" data-toggle="tooltip" data-placement="bottom" title="Sublinhar"></button>
                                    </div>
                                    <div id="textbar_editor" class="quill-editor">
                                        Aproveite o <strong>desconto extra</strong> ao comprar no <u>Cartão ou pelo PIX!</u> <strong>É por tempo limitado.</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="sales-notifications-container">
                            <div class="title-buttons-group">
                                <div>
                                    <h1 class="checkout-title">
                                        Notificação de vendas
                                    </h1>
                                </div>

                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="sales_notifications_flag" name="sales_notifications_flag" data-target="sales-notifications-content" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                            </div>

                            <div class="sales-notifications-content">
                                <div class="input-container">
                                    <label for="notification-interval" class="checkout-label">Intervalo entre notificações</label>

                                    <div id="notification-interval" class="radio-group">
                                        <input class="custom-radio" id="notification-interval-15" type="radio" name="notification-interval" value="15" checked />
                                        <label for="notification-interval-15">15 segundos</label>

                                        <input class="custom-radio" id="notification-interval-30" type="radio" name="notification-interval" value="30" />
                                        <label for="notification-interval-30">30 segundos</label>

                                        <input class="custom-radio" id="notification-interval-45" type="radio" name="notification-interval" value="45" />
                                        <label for="notification-interval-45">45 segundos</label>

                                        <input class="custom-radio" id="notification-interval-60" type="radio" name="notification-interval" value="60" />
                                        <label for="notification-interval-60">1 minuto</label>
                                    </div>
                                </div>


                                <div id="notification-table">
                                    <label for="notification-interval" class="checkout-label">Configure as notificações</label>
                                    
                                    <div class="notification-table-cointainer">
                                        <table class="table table-hover selectable" id="notification-table" data-plugin="selectable" data-row-selectable="true">
                                            <thead>
                                                <tr>
                                                    <th class="th-notification">
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-all" type="checkbox">
                                                            <label></label>
                                                        </span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-item" type="checkbox" id="notification-select-1" value="619">
                                                            <label for="notification-select-1"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-item" type="checkbox" id="notification-select-2" value="620">
                                                            <label for="notification-select-2"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-item" type="checkbox" id="notification-select-3" value="620">
                                                            <label for="notification-select-3"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-item" type="checkbox" id="notification-select-4" value="620">
                                                            <label for="notification-select-4"></label>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <table class="table table-hover notification-counts" id="notification-table-count" data-row-selectable="true">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Mensagem
                                                    </th>
                                                    <th>
                                                        Qnd Mínima
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><b>XX</b> pessoas estão comprando <b>{produto}</b> nesse momento.</td>
                                                    <td>
                                                        <input class="table-number-input" type="number" id="notification-row-1" name="notification-row-1" min="1" max="999" style="padding: 3px">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>XX</b> pessoas compraram <b>{produto}</b> nos últimos 30 minutos.</td>
                                                    <td>
                                                        <input class="table-number-input" type="number" id="notification-row-2" name="notification-row-2" min="1" max="999" style="padding: 3px">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>XX</b> pessoas compraram <b>{produto}</b> na última hora.</td>
                                                    <td>
                                                        <input class="table-number-input" type="number" id="notification-row-3" name="notification-row-3" min="1" max="999" style="padding: 3px">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>{nome}</b> de <b>{cidade}</b> acabou de comprar esse produto</td>
                                                    <td>
                                                        <input class="table-number-input" type="number" id="notification-row-4" name="notification-row-4" min="1" max="999" style="padding: 3px">
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>



                            </div>
                        </div>

                        <hr>

                        <div class="social-proof-container">
                            <div class="title-buttons-group">
                                <div>
                                    <h1 class="checkout-title">
                                        Prova Social
                                    </h1>
                                </div>

                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="social_proof_flag" name="social_proof_flag" data-target="social-proof-content" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="social-proof-content">
                                <div class="input-container">
                                    <label for="social-proof-message" class="checkout-label">Mensagem</label>
                                    <textarea class="checkout-textarea" id="social_proof_message" name="social-proof-message" rows="1" ></textarea>
                                </div>

                                <div>
                                    <label for="social-proof-vars" class="checkout-label">Adicionar variáveis</label>
                                    <div style="display: flex; margin-bottom: 10px;">
                                        <button id="" class='add-tag' data-input="#social_proof_message" data-tag="{ num-visitantes }">num-visitantes</button>
                                        <button id="" class='add-tag' data-input="#social_proof_message" data-tag="{ nome-produto }">nome-produto</button>    
                                    </div>
                                </div>
                                

                                <div class="input-container">
                                    <label for="social-proof-message" class="checkout-label">Mínimo de vistantes</label>
                                    <input type="number" class="min-visitors-input" id="social-proof-min-visitors" name="social-proof-min-visitors" min="1" max="999">
                                </div>

                            </div>

                        </div>

                    </div>



                </div>

                <div class="checkout-content payment" id="payment_container">
                    <div class="title-buttons-group">
                        <span class="title-icon">
                            <img class="icon-title" src="{{ asset('/modules/checkouteditor/img/svg/payments.svg') }}">
                            <h1 class="checkout-title">
                                Pagamentos
                            </h1>
                        </span>
                    </div>


                    <div class="billing-content">

                        <div class="row-flex">
                            <div class="input-container" style="flex: 2">
                                <label for="invoice_description_message" class="checkout-label">Descrição na fatura</label>
                                <input type="text" class="checkout-input-text" id="invoice_description_message" name="invoice_description_message" />
                            </div>

                            <div class="input-container" style="flex: 3">
                                <label for="company_billing" class="checkout-label">Empresa responsável pelo faturamento</label>
                                <div class='form-group'>
                                    <select id='companies' name='company_id' class="form-control select-pad"> </select>
                                </div>
                            </div>
                        </div>


                        <div class="row-flex">
                            <div class="input-container" style="flex: 2">
                                <label for="company_billing" class="checkout-label">Aceitar pagamentos de</label>
                                <div id="payment_accept" class="radio-group" style="justify-self: end;">
                                    <input class="custom-radio" id="payment_cpf" type="radio" name="payment_accept" value="cpf" checked />
                                    <label for="payment_cpf">CPF</label>

                                    <input class="custom-radio" id="payment_cnpj" type="radio" name="payment_accept" value="cnpj" />
                                    <label for="payment_cnpj">CNPJ</label>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 3">
                                <label for="company_billing" class="checkout-label">Aceitar pagamentos de</label>
                                <div id="payment_accept" class="check-group" style="justify-self: end;">

                                    <input class="custom-bubble-check accept-payment-method" id="accept_credit_card" type="checkbox" name="accept_payment_method" data-target="credit-card-container" data-preview="accepted_payment_card_creditcard" value="creditcard_accept" />
                                    <label for="accept_credit_card">Cartão de crédito</label>

                                    <input class="custom-bubble-check accept-payment-method" id="accept_billet" type="checkbox"  data-target="billet-container" name="accept_payment_method" data-preview="accepted_payment_card_billet" value="billet_accept" />
                                    <label for="accept_billet">Boleto</label>

                                    <input class="custom-bubble-check accept-payment-method" id="accept_pix" type="checkbox" data-preview="accepted_payment_card_pix" value="pix_accept" name="accept_payment_method" />
                                    <label for="accept_pix">Pix</label>

                                </div>
                            </div>
                        </div>

                        <div class="row-flex">
                            <div class="input-container" style="flex: 2">
                                <label for="count_selector_flag">Seletor de Quantidade</label>
                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="count_selector_flag" name="count_selector_flag" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="input-container" style="flex: 3">
                                <label for="count_selector_flag">Exigir e-mail no checkout</label>
                                <div class="switch-holder mb-3">    
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="checkout_email_flag" name="checkout_email_flag" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            
                        </div>

                    </div>

                    <div class="credit-card-container">
                        <hr>
                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title" data-target="countdown-content">
                                    Cartão de crédito
                                </h1>
                            </div>

                            <div>

                            </div>
                        </div>

                        <div style="display: flex; gap: 20px;">
                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Limite de parcelas</label>
                                <div class='form-group'>
                                    <select id='installment_limit' name='installment_limit' class="form-control select-pad">
                                        <option value="1">1x</option>
                                        <option value="2">2x</option>
                                        <option value="3">3x</option>
                                        <option value="4">4x</option>
                                        <option value="5">5x</option>
                                        <option value="6">6x</option>
                                        <option value="7">7x</option>
                                        <option value="8">8x</option>
                                        <option value="9">9x</option>
                                        <option value="10">10x</option>
                                        <option value="11">11x</option>
                                        <option value="12">12x</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Sem juros até</label>
                                <div class='form-group'>
                                    <select id='interest_free' name='interest_free' class="form-control select-pad">
                                        <option value="1">1x</option>
                                        <option value="2">2x</option>
                                        <option value="3">3x</option>
                                        <option value="4">4x</option>
                                        <option value="5">5x</option>
                                        <option value="6">6x</option>
                                        <option value="7">7x</option>
                                        <option value="8">8x</option>
                                        <option value="9">9x</option>
                                        <option value="10">10x</option>
                                        <option value="11">11x</option>
                                        <option value="12">12x</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Parcela pré-selecionada</label>
                                <div class='form-group'>
                                    <select id='selected_portion' name='selected_portion' class="form-control select-pad">
                                        <option value="1">1x</option>
                                        <option value="2">2x</option>
                                        <option value="3">3x</option>
                                        <option value="4">4x</option>
                                        <option value="5">5x</option>
                                        <option value="6">6x</option>
                                        <option value="7">7x</option>
                                        <option value="8">8x</option>
                                        <option value="9">9x</option>
                                        <option value="10">10x</option>
                                        <option value="11">11x</option>
                                        <option value="12">12x</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="billet-container">
                        <hr>

                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title" data-target="countdown-content">
                                    Boleto
                                </h1>
                            </div>

                            <div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 20px;">
                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Dias para vencimento</label>
                                <div class="tagged-input-div">
                                    <input class="tagged-input" type="number" id="expiration_days" name="expiration_days" min="1" max="99" maxlength="3">
                                    <div class=" input-tag">dias</div>
                                </div>
                            </div>

                            <div style="flex: 1">
                            </div>
                        </div>


                    </div>
                    
                    <hr>

                    <div class="discount-container">
                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title" data-target="countdown-content">
                                    Descontos automáticos
                                </h1>
                            </div>

                            <div>
                            </div>
                        </div>

                        <div style="display:flex; justify-content: center;">
                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Cartão de crédito</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="discount_applied_credit_card" name="discount_applied_credit_card" min="1" max="99" maxlength="2">
                                    <div class=" input-tag">%</div>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Boleto</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="discount_applied_bank_billet" name="discount_applied_bank_billet" min="1" max="99" maxlength="2">
                                    <div class=" input-tag">%</div>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">PIX</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="discount_applied_pix" name="discount_applied_pix" min="1" max="99" maxlength="2">
                                    <div class=" input-tag">%</div>
                                </div>
                            </div>
                        </div>

                        
                    </div>

                </div>

                <div class="checkout-content post-purchase-pages" id="post_purchase" style="height: calc(100vh - 90px); max-height: calc(100vh - 70px);">

                    <span class="title-icon">
                        <img class="icon-title" src="{{ asset('/modules/checkouteditor/img/svg/paid-page.svg') }}">
                        <h1 class="checkout-title">
                            Páginas Pós-compra
                        </h1>
                    </span>

                    <div class="checkout-subtitle">
                        <p>Altere o destino do cliente após a compra, personalize mensagens e mais.</p>
                    </div>


                    <div class="thanks-page-container">
                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title">
                                    Mensagem na página de obrigado
                                </h1>
                            </div>

                            <div class="switch-holder mb-3">
                                <label class="switch" style='top:3px'>
                                    <input type="checkbox" id="thanks_page_flag" name="thanks_page_flag" data-target="thanks-page-content" data-preview=".shop-message-preview" class='check switch-checkout'>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="thanks-page-content">
                            <label for="thanks_page_editor" class="checkout-label">Título da sua mensagem</label>
                            <input type="text" class="checkout-input-text" id="thanks_page_title" name="thanks_page_title" style="margin-bottom: 15px;" value="Obrigado por comprar conosco!">

                            <div class="editor-container">
                                <div id="thanks_page_editor_toolbar_container" class="editor-toolbar-container">
                                    <button class="ql-bold" data-toggle="tooltip" data-placement="bottom" title="Negrito"></button>
                                    <button class="ql-italic" data-toggle="tooltip" data-placement="bottom" title="Itálico"></button>
                                    <button class="ql-underline" data-toggle="tooltip" data-placement="bottom" title="Sublinhar"></button>
                                </div>
                                <div id="thanks_page_editor" class="quill-editor">
                                    Aproveite o <strong>desconto extra</strong> ao comprar no <u>Cartão ou pelo PIX!</u> <strong>É por tempo limitado.</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="whatsapp-container">
                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title">
                                    Botão do WhatsApp
                                </h1>
                            </div>

                            <div class="switch-holder mb-3">
                                <label class="switch" style='top:3px'>
                                    <input type="checkbox" id="whatsapp_flag" name="whatsapp_flag" data-target="whatsapp-content" data-preview=".whatsapp-preview" class='check switch-checkout'>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="checkout-subtitle">
                            <p>Ao ativar, seu cliente poderá receber o boleto via WhatsApp.</p>
                        </div>

                        <div class="whatsapp-content">
                            <label for="whatsapp_phone" class="checkout-label">Telefone do suporte <span class="observation-span">Opcional</span></label>
                            <input type="text" class="checkout-input-text" id="whatsapp_phone" name="whatsapp_phone" style="margin-bottom: 15px;" placeholder="Digite o telefone com DDD do suporte" pattern="\([0-9]{2}\)[\s][0-9]{5}-[0-9]{4,5}"></input>
                            <div class="textarea-observation">
                                <span class="dot"></span><span class="observation-span">Visível somente em desktop.</span>
                            </div>
                        </div>
                    </div>


                    <!-- <div class="custom-pages-container"">
                            <div class=" title-buttons-group">
                        <div>
                            <h1 class="checkout-title">
                                Páginas personalizadas
                            </h1>
                        </div>

                        <div class="switch-holder mb-3">
                            <label class="switch" style='top:3px'>
                                <input type="checkbox" id="custom_pages_flag" name="custom_pages_flag" data-target="custom-pages-content" class='check switch-checkout'>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

                    <div class="checkout-subtitle">
                        <p>Ao deixar os campos vazios, serão mantidas as páginas padrões.</p>
                    </div>

                    <div class="custom-pages-content">
                        <div class="input-container">
                            <label for="credit_card_description_custom_pages" class="checkout-label">Cartão de Crédito</label>
                            <input type="text" class="checkout-input-text" id="credit_card_description_custom_pages" name="credit_card_description_custom_pages" placeholder="Digite ou cole a URL aqui" />
                        </div>

                        <div class="input-container">
                            <label for="billet_description_custom_pages" class="checkout-label">Boleto</label>
                            <input type="text" class="checkout-input-text" id="billet_description_custom_pages" name="billet_description_custom_pages" placeholder="Digite ou cole a URL aqui" />
                        </div>

                        <div class="input-container">
                            <label for="pix_description_custom_pages" class="checkout-label">Pix</label>
                            <input type="text" class="checkout-input-text" id="pix_description_custom_pages" name="credit_card_description_custom_pages" placeholder="Digite ou cole a URL aqui" />
                        </div>
                    </div>-->


                </div>

            <div class="preview" id="preview_div">
                <div id="preview_visual">
                    <div class="title-buttons-group">
                        <div>
                            <h1 class="checkout-title">
                                Prévia  | Visual
                            </h1>
                        </div>

                        <div id="banner_type" class="radio-group">
                            <input class="custom-icon-radio" id="preview_visual_computer" type="radio" name="preview-visual-type" checked />
                            <label for="preview_visual_computer"><img src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>

                            <input class="custom-icon-radio" id="preview_visual_tablet" type="radio" name="preview-visual-type" />
                            <label for="preview_visual_tablet"><img src="{{ asset('/modules/checkouteditor/img/svg/tablet-icon.svg') }}"></label>

                            <input class="custom-icon-radio" id="preview_visual_mobile" type="radio" name="preview-visual-type" />
                            <label for="preview_visual_mobile"><img src="{{ asset('/modules/checkouteditor/img/svg/mobile-icon.svg') }}"></label>
                        </div>
                    </div>

                    <div class="preview-container">

                        <div id="countdown_preview" class="header-colorbar secondary-color countdown-preview"> </div>
                        <div id="textbar_preview" class="header-colorbar primary-color textbar-preview"> </div>

                        <div id="banner_preview" class="preview-banner wide">
                            <img id="preview_banner_img" class="preview-banner-img">
                        </div>

                        <div class="preview-body visual">

                            <div class="checkout-step-type">
                                <div id="three_steps_preview" class="steps-lines">
                                    <div class="step-one primary-color"></div>
                                    <div class="step-two"></div>
                                    <div class="step-three"></div>
                                </div>
                                
                                <div></div>
                                
                                <div class="steps-circle">
                                    <div></div>
                                    <div class="circle primary-color"></div>
                                </div>
                            </div>

                            <div class="preview-body-visual-content desktop">

                                <div class="visual-content-left three-steps">
                                    <div id="finish_button_preview"class="finish-button-computer visual-desktop"></div>
                                </div>
                                
                                <div class="visual-content-right">

                                    <div class="white-placeholder-content"></div>

                                    <div class="list-placeholder-content">
                                        <div class="list-item-placeholder">
                                            <div class="circle-placeholder"></div>
                                            <div class="strip-placeholder"></div>
                                        </div>

                                        <div class="list-item-placeholder">
                                            <div class="circle-placeholder"></div>
                                            <div class="strip-placeholder"></div>
                                        </div>

                                        <div class="list-item-placeholder">
                                            <div class="circle-placeholder"></div>
                                            <div class="strip-placeholder"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>


                    <div class="preview-colors">
                        
                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title" style="margin: 0;">
                                    Temas Prontos
                                </h1>
                                <div class="checkout-subtitle">
                                    <p style="margin: 0;">Utilizar um tema pronto.</p>
                                </div>

                            </div>
                        </div>

                        <div class="theme-ready-content">
                            <div id="theme_ready" class="radio-group theme-ready">
                                
                                <input class="theme-radio" id="theme_spaceship" type="radio" name="theme_ready" value="theme_spaceship" checked/>
                                <label for="theme_spaceship">
                                    <div class="theme-primary-color" style="background: #4B8FEF;"></div>
                                    <div class="theme-secondary-color" style="background: #313C52;"></div>
                                    <div class="theme-label">
                                        Spaceship
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_purple_space" type="radio" name="theme_ready" value="theme_purple_space" />
                                <label for="theme_purple_space">
                                    <div class="theme-primary-color" style="background: #6C009E;"></div>
                                    <div class="theme-secondary-color" style="background: #3E005B;"></div>
                                    <div class="theme-label">
                                        Purple Space
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_cloud_std" type="radio" name="theme_ready" value="theme_cloud_std" />
                                <label for="theme_cloud_std">
                                    <div class="theme-primary-color" style="background: #FF7900;"></div>
                                    <div class="theme-secondary-color" style="background: #FFFFFF;"></div>
                                    <div class="theme-label">
                                        Cloud Std
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_sunny_day" type="radio" name="theme_ready" value="theme_sunny_day" />
                                <label for="theme_sunny_day">
                                    <div class="theme-primary-color" style="background: #FF7900;"></div>
                                    <div class="theme-secondary-color" style="background: #FFBF08;"></div>
                                    <div class="theme-label">
                                        Sunny Day
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_blue_sky" type="radio" name="theme_ready" value="theme_blue_sky" />
                                <label for="theme_blue_sky">
                                    <div class="theme-primary-color" style="background: #009BF2;"></div>
                                    <div class="theme-secondary-color" style="background: #008BD9;"></div>
                                    <div class="theme-label">
                                        Blue Sky
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_all_black" type="radio" name="theme_ready" value="theme_all_black" />
                                <label for="theme_all_black">
                                    <div class="theme-primary-color" style="background: #262626;"></div>
                                    <div class="theme-secondary-color" style="background: #393939;"></div>
                                    <div class="theme-label">
                                        All Black
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_red_mars" type="radio" name="theme_ready" value="theme_red_mars" />
                                <label for="theme_red_mars">
                                    <div class="theme-primary-color" style="background: #FA0000;"></div>
                                    <div class="theme-secondary-color" style="background: #9B0000;"></div>
                                    <div class="theme-label">
                                        Red Mars
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_pink_galaxy" type="radio" name="theme_ready" value="theme_pink_galaxy" />
                                <label for="theme_pink_galaxy">
                                    <div class="theme-primary-color" style="background: #F68AFF;"></div>
                                    <div class="theme-secondary-color" style="background: #9B51A1;"></div>
                                    <div class="theme-label">
                                        Pink Galaxy
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_turquoise" type="radio" name="theme_ready" value="theme_turquoise" />
                                <label for="theme_turquoise">
                                    <div class="theme-primary-color" style="background: #32BCAD;"></div>
                                    <div class="theme-secondary-color" style="background: #D3FAF5;"></div>
                                    <div class="theme-label">
                                        Turquoise
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_greener" type="radio" name="theme_ready" value="theme_greener" />
                                <label for="theme_greener">
                                    <div class="theme-primary-color" style="background: #23D07D;"></div>
                                    <div class="theme-secondary-color" style="background: #02AD5B;"></div>
                                    <div class="theme-label">
                                        Greener
                                    </div>
                                </label>
                                </label>

                            </div>
                        </div>

                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title" style="margin: 0;">
                                    Criar tema personalizado
                                </h1>
                                <div class="checkout-subtitle">
                                    <p style="font-size: 12px; margin: 0;">Personalize com as cores da sua marca.</p>
                                </div>
                            </div>
                            <div class="switch-holder mb-3">
                                <label class="switch" style='top:3px'>
                                    <input type="checkbox" id="theme_ready_flag" name="theme_ready_flag" data-target="custom-theme-content" data-toggle="theme-ready-content" class='check switch-checkout-accordion'>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="custom-theme-content">
                            <div>
                                <div class="custom-theme-color">
                                    <div style="display: flex; ">
                                        <div class="input-container" style="margin-right: 20px">
                                            <label for="custom_primary_color">Cor primária</label>
                                            <input class="color-picker" type="color" id="custom_primary_color" name="custom_primary_color" value="#4B8FEF" styles="height: 20px">
                                        </div>

                                        <div class="input-container" style="margin-right: 20px">
                                            <label for="custom_secondary_color">Cor secundária</label>
                                            <input class="color-picker" type="color" id="custom_secondary_color" name="custom_secondary_color" value="#313C52" styles="height: 20px">
                                        </div>

                                        <div class="input-container" style="margin-right: 20px">
                                            <label for="custom_finish_color">Cor do botão de compra</label>
                                            <input class="color-picker" type="color" id="custom_finish_color" name="custom_finish_color" value="#23D07D" styles="height: 20px">
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="checkbox-container">
                                <input class="checkbox" id="default_finish_color" type="checkbox" />
                                <label for="default_finish_color">Manter “Finalizar compra” verde</label>
                            </div>
                        </div>

                        
                        

                        
                    </div>
                </div>

                <div id="preview_payment" style="display: none;">
                    <div class="title-buttons-group">
                        <div>
                            <h1 class="checkout-title">
                                Prévia | Pagamento
                            </h1>
                        </div>
                        <div id="preview_type" class="banner-type-group">
                            <input class="custom-radio-banner" id="preview_type_computer" type="radio" name="preview_type" value="1" checked />
                            <label for="preview_type_computer"><img src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>
                        </div>
                    </div>


                    <div class="preview-container">

                        <div id="countdown_preview" class="header-colorbar secondary-color countdown-preview"> </div>
                        <div id="textbar_preview" class="header-colorbar primary-color textbar-preview"> </div>

                        <div id="banner_preview" class="preview-banner wide banner-preview">
                            <img id="preview_banner_img" class="preview-banner-img">
                        </div>

                        <div class="preview-body payment">
                            <div class="preview-body-content-payment">

                                <div class="preview-payment-cupom">
                                    <div class="placeholder-retangle-payment"></div>

                                    <div class="placeholder-input-payment">
                                        <!-- <input type="text" class="placeholder-text-payment" placeholder="Digite ou cole aqui o código do cupom" disabled></input> -->
                                        <div class="input-form-placeholder cupom"></div>
                                        <div class="add-cupom">ADICIONAR CUPOM</div>
                                    </div>
                                </div>

                                <div class="preview-payment-content">
                                    <div class="payment-title">3. DADOS DE PAGAMENTO</div>

                                    <div class="accepted-payment-list">
                                        
                                        <div class="accepted-payment" id="accepted_payment_card_creditcard">
                                            <img src="{{ asset('/modules/checkouteditor/img/svg/icon-card.svg') }}" style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);">
                                            <span>Cartão</span>
                                        </div>
                                    
                                        <div class="accepted-payment" id="accepted_payment_card_pix">
                                            <img src="{{ asset('/modules/checkouteditor/img/svg/icon-pix.svg') }}" style="width: 35px;">
                                            <span>Pix</span>
                                        </div>
                                    
                                        <div class="accepted-payment" id="accepted_payment_card_billet">
                                            <img src="{{ asset('/modules/checkouteditor/img/svg/icon-boleto.svg') }}" style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);">
                                            <span>Boleto</span>
                                        </div>
                                        
                                    </div>

                                    <div class="accepted-payment-content credit-card">
                                        <div class="accepted-payment-credit-cards">

                                        </div>

                                        <div class="accepted-payment-form">

                                            <div style="display: flex;">
                                                <div class="input-form-placeholder"></div>
                                                <div class="input-form-placeholder" style="width: 50px; background: #E2E2E2;"></div>
                                            </div>
                                            
                                            <div class="input-form-placeholder"></div>
                                            
                                            <div class="input-form-placeholder"></div>
                                            
                                        </div>

                                    </div>

                                    <div class="finish-button-computer payment-desktop"></div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="preview_post_purchase" style="display: none;">
                    <div class="title-buttons-group">
                        <div>
                            <h1 class="checkout-title">
                                Prévia | Pós-compra
                            </h1>
                        </div>
                        <div id="preview_type" class="banner-type-group">
                            <input class="custom-radio-banner" id="preview_type_computer" type="radio" name="preview_type" value="1" checked />
                            <label for="preview_type_computer"><img src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>
                        </div>
                    </div>

                    <div class="preview-container">
                        <div class="preview-body post-purchase">
                            <img src="{{ asset('/modules/checkouteditor/img/svg/barcode-icon.svg') }}" style="margin: 10px 0">

                            <div class="input-form-placeholder" style="margin: 10px 0"></div>

                            <div class="shop-message-preview desktop" style="margin: 10px 0">
                            <h1 class="shop-message-preview-title">Obrigado por comprar conosco!</h1>

                                <div class="shop-message-preview-content">Aproveite o <strong>desconto extra</strong> ao comprar no <u>Cartão ou pelo PIX!</u> <strong>É por tempo limitado.</strong></div>
                            </div>

                            <div class="card-container">
                                <div class="grey-container" style="margin-bottom: 10px;"></div>

                                <div style="display: flex; width: 100%;">
                                    <div style="border-radius: 12px; height: 40px; width: 120px; background-color: #F5F5F5; margin-right: 10px; margin-bottom: 10px;"></div>
                                    <div style="border-radius: 12px; height: 40px; width: 100%; background-color: #2E85EC;"></div>
                                </div>

                                <div class="whatsapp-preview" style="display: flex; padding: 10px; border-radius: 12px; height: 40px; width: 100%; background-color: #36DB8C;">
                                        <img src="{{ asset('/modules/checkouteditor/img/svg/whatsapp-icon.svg') }}">
                                    </div>
                            </div>

                            <div class="card-container">
                                <div style="display: flex; width: 100%;">
                                    <div class="grey-container" style="height: 40px; width: 40px; margin: 0 10px 10px 0;"></div>
                                    <div class="grey-container" style="height: 40px; width: 100%; margin-bottom: 0 10px 10px 0;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

            </div>

            <!-- <div class="editor-buttons">
                <div class="save-changes" id="changing_container" style="display: none;">
                    <div style="margin-right: 50px;">
                        Você tem alterações que <strong>não estão salvas</strong>
                    </div>

                    <div class="save-changes-button-group">
                        <button type="button" class="change-button cancel-changes-button">Cancelar</button>
                        <button type="button" class="change-button save-changes-button">Salvar alterações</button>
                    </div>
                </div>

                <div class="save-changes" id="done" style="display: none;">
                    <div style="margin-right: 50px;">
                        Alterações salvas com sucesso!
                    </div>
                    
                    <div>
                        <img class="save-icon" src="{{ asset('/modules/checkouteditor/img/svg/save-check.svg') }}">
                    </div>
                </div>
            </div>        -->

        </div>
</div>
    
</form>

    <div class="modal fade" id="modal_banner" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" >
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Imagem de fundo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" style="display: flex; justify-content: center;">
                    <div class="img-container">
                        <div class="row">
                            <div class="container-crop" style="width: 500px; max-height: 300px; border: none; object-fit: cover; border-radius: 5px;">  
                                <img id="cropped_image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer btn-group-crop" >
                    <button type="button" class="btn btn-secondary btn-crop-outline" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary btn-crop-filled" id="button-crop">Cortar</button>
                </div>
            </div>
        </div>
    </div>


</div>

@push('scripts')
<script src="{{ asset('modules/global/adminremark/global/js/Plugin/cropper.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
<script src="//cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="//cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Plugin/asselectable.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Plugin/selectable.js') }}"></script>
<script src="{{asset('modules/checkouteditor/js/cropper.min.js?v='.uniqid())}}"></script>
<script src="{{asset('modules/checkouteditor/js/checkout-editor.js?v='.uniqid())}}"></script>
@endpush