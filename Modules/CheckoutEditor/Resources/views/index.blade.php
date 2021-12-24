@push('css')
<link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=03') !!}">
<link rel="stylesheet" href="{!! asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
<link rel="stylesheet" href="//cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/quill-editor.css?v=10') }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/dropfy.css?v=10') }}">
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

    <form id="checkout_editor">
        @method('PUT')
        <div style="display: flex; flex-direction: column; width: 100%">
            <div class="grid-checkout-editor">

                <div id="checkout_type" class="checkout-content select-type">
                    <h1 class="checkout-title">
                        Selecione um tipo
                    </h1>

                    <div id="checkout_type" class="radio-group">
                        <input class="custom-radio" id="checkout_type_steps" type="radio" name="checkout_type_enum" value="0" />
                        <label for="checkout_type_steps">Checkout de 3 passos</label>

                        <input class="custom-radio" id="checkout_type_unique" type="radio" name="checkout_type_enum" value="1" />
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

                    <div class="logo-container">
                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title">
                                    Logo no checkout
                                </h1>
                            </div>

                            <div class="switch-holder mb-3">
                                <label class="switch" style='top:3px'>
                                    <input type="checkbox" id="checkout_logo_enabled" name="checkout_logo_enabled" data-target="logo-content" data-preview=".logo-mobile" class='check switch-checkout'>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="logo-content">
                            <div class="upload-container">
                                <div id='upload_logo'>
                                    <label for="checkout_logo"></label>
                                    <input type="file" id="checkout_logo" name="checkout_logo" data-height="300" data-max-width="300" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                                </div>

                                <div class="instrunctios">
                                    Recomendações
                                    Imagem de 300x300px
                                    Formatos: JPEG ou PNG
                                </div>
                            </div>
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
                                    <input class="custom-icon-radio" id="banner_type_wide" type="radio" name="checkout_banner_type" value="0" />
                                    <label for="banner_type_wide"><img src="{{ asset('/modules/checkouteditor/img/svg/banner-wide.svg') }}"></label>

                                    <input class="custom-icon-radio" id="banner_type_square" type="radio" name="checkout_banner_type" value="1" />
                                    <label for="banner_type_square"><img src="{{ asset('/modules/checkouteditor/img/svg/banner-square.svg') }}"></label>
                                </div>

                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="checkout_banner_enabled" name="checkout_banner_enabled" data-target="banner-top-content" data-preview=".preview-banner" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="banner-top-content">
                            <div id='upload-banner'>
                                <input type="file" name="checkout_banner" id="checkout_banner" class="dropify"  data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
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
                                    <button id="download_template_banner" class="line-button" type="button" data-href="{{ asset('/modules/checkouteditor/files/test_download.xlsx') }}">Baixar gabarito</button>
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
                                        <input type="checkbox" id="countdown_enabled" name="countdown_enabled" data-target="countdown-content" data-preview=".countdown-preview" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="countdown-content">
                                <div class="input-container">
                                    <label for="countdown-time" class="checkout-label">Tempo</label>
                                    <div class="time-div">
                                        <input class="time-input" type="number" id="countdown_time" name="countdown_time" value="15" min="1" max="99" maxlength="3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                        <div class="min-input-label">min</div>
                                    </div>
                                </div>

                                <div class="input-container">
                                    <label for="countdown-time" class="checkout-label">Descrição <span class="observation-span">Opcional</span></label>
                                    <textarea class="checkout-textarea" id="countdown_description" name="countdown_description" rows="4"></textarea>
                                    <div class="textarea-observation">
                                        <span class="dot"></span><span class="observation-span">Visível somente em desktop.</span>
                                    </div>
                                </div>

                                <div class="input-container">
                                    <label for="timeout-message" class="checkout-label">Mensagem ao encerrar o tempo</label>
                                    <textarea class="checkout-textarea" id="countdown_finish_message" name="countdown_finish_message" rows="3"></textarea>
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
                                        <input type="checkbox" id="topbar_enabled" name="topbar_enabled" data-target="textbar-content" data-preview=".textbar-preview" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="textbar-content">
                                <label for="topbar_content" class="checkout-label">Texto na barra</label>
                                <div class="editor-container">
                                    <div id="topbar_content_toolbar_container" class="editor-toolbar-container">
                                        <button class="ql-bold" data-toggle="tooltip" data-placement="bottom" title="Negrito"></button>
                                        <button class="ql-italic" data-toggle="tooltip" data-placement="bottom" title="Itálico"></button>
                                        <button class="ql-underline" data-toggle="tooltip" data-placement="bottom" title="Sublinhar"></button>
                                    </div>
                                    <div id="topbar_content" class="quill-editor">
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
                                        <input type="checkbox" id="notifications_enabled" name="notifications_enabled" data-target="sales-notifications-content" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>

                            </div>

                            <div class="sales-notifications-content">
                                <div class="input-container">
                                    <label class="checkout-label">Intervalo entre notificações</label>

                                    <div class="radio-group">
                                        <input class="custom-radio" id="notifications_interval_15" type="radio" name="notifications_interval" value="15" />
                                        <label for="notifications_interval_15">15 segundos</label>

                                        <input class="custom-radio" id="notifications_interval_30" type="radio" name="notifications_interval" value="30" />
                                        <label for="notifications_interval_30">30 segundos</label>

                                        <input class="custom-radio" id="notifications_interval_45" type="radio" name="notifications_interval" value="45" />
                                        <label for="notifications_interval_45">45 segundos</label>

                                        <input class="custom-radio" id="notifications_interval_60" type="radio" name="notifications_interval" value="60" />
                                        <label for="notifications_interval_60">1 minuto</label>
                                    </div>
                                </div>


                                <div id="notification-table">
                                    <label for="notification-interval" class="checkout-label">Configure as notificações</label>

                                    <div class="notification-table-cointainer">
                                        <table class="table table-hover selectable" id="notification-table" data-plugin="selectable" data-row-selectable="true">
                                            <thead>
                                                <tr>
                                                    <th class="th-notification" style="height: 90px; max-height: 90px">
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-all" type="checkbox">
                                                            <label></label>
                                                        </span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr style="height: 90px; max-height: 90px">
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-item" type="checkbox" id="notification_buying_enabled" name="notification_buying_enabled">
                                                            <label for="notification_buying_enabled"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-item" type="checkbox" id="notification_bought_30_minutes_enabled" name="notification_bought_30_minutes_enabled">
                                                            <label for="notification_bought_30_minutes_enabled"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-item" type="checkbox" id="notification_bought_last_hour_enabled" name="notification_bought_last_hour_enabled">
                                                            <label for="notification_bought_last_hour_enabled"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-item" type="checkbox" id="notification_just_bought_enabled" name="notification_just_bought_enabled">
                                                            <label for="notification_just_bought_enabled"></label>
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
                                                        <input class="table-number-input" type="number" id="notification_buying_minimum" name="notification_buying_minimum" min="1" max="999" style="padding: 3px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>XX</b> pessoas compraram <b>{produto}</b> nos últimos 30 minutos.</td>
                                                    <td>
                                                        <input class="table-number-input" type="number" id="notification_bought_30_minutes_minimum" name="notification_bought_30_minutes_minimum" min="1" max="999" style="padding: 3px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>XX</b> pessoas compraram <b>{produto}</b> na última hora.</td>
                                                    <td>
                                                        <input class="table-number-input" type="number" id="notification_bought_last_hour_minimum" name="notification_bought_last_hour_minimum" min="1" max="999" style="padding: 3px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>{nome}</b> de <b>{cidade}</b> acabou de comprar esse produto</td>
                                                    <td>
                                                        <input class="table-number-input" type="number" id="notification_just_bought_minimum" name="notification_just_bought_minimum" min="1" max="999" style="padding: 3px" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
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
                                        <input type="checkbox" id="social_proof_enabled" name="social_proof_enabled" data-target="social-proof-content" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="social-proof-content">
                                <div class="input-container">
                                    <label for="social_proof_message" class="checkout-label">Mensagem</label>
                                    <textarea class="checkout-textarea" id="social_proof_message" name="social_proof_message" rows="1">Outros {num-visitantes} estão finalizando esta compra nesse momento.</textarea>
                                </div>

                                <div>
                                    <label for="social-proof-vars" class="checkout-label">Adicionar variáveis</label>
                                    <div style="display: flex; margin-bottom: 10px;">
                                        <button id="" class='add-tag' data-input="#social_proof_message" data-tag="{ num-visitantes }">num-visitantes</button>
                                        <button id="" class='add-tag' data-input="#social_proof_message" data-tag="{ nome-produto }">nome-produto</button>
                                    </div>
                                </div>


                                <div class="input-container">
                                    <label for="social_proof_minimum" class="checkout-label">Mínimo de vistantes</label>
                                    <input type="number" class="min-visitors-input" id="social_proof_minimum" name="social_proof_minimum" value="15" min="1" max="999" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
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
                                <label for="invoice_description" class="checkout-label">Descrição na fatura</label>
                                <input type="text" class="checkout-input-text" id="invoice_description" name="invoice_description" />
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
                                <label class="checkout-label">Aceitar pagamentos de</label>
                                <div id="payment_type_accept" class="check-group" style="justify-self: end;">
                                    <input class="custom-bubble-check accept-payment-type" type="checkbox" id="cpf_enabled" name="cpf_enabled">
                                    <label for="cpf_enabled">CNPJ</label>

                                    <input class="custom-bubble-check accept-payment-type" type="checkbox" id="cnpj_enabled" name="cnpj_enabled">
                                    <label for="cnpj_enabled">CPF</label>
                                </div>
                            </div>


                            <div class="input-container" style="flex: 3">
                                <label class="checkout-label">Métodos aceitos</label>
                                <div id="payment_accept" class="check-group" style="justify-self: end;">
                                    <input class="custom-bubble-check accept-payment-method" type="checkbox" id="credit_card_enabled" name="credit_card_enabled" data-target="credit-card-container" data-preview=".accepted-payment-card-creditcard" checked>
                                    <label for="credit_card_enabled">Cartão de crédito</label>

                                    <input class="custom-bubble-check accept-payment-method" type="checkbox" id="bank_slip_enabled" name="bank_slip_enabled" data-target="bank-billet-container" data-preview=".accepted-payment-bank-billet">
                                    <label for="bank_slip_enabled">Boleto</label>

                                    <input class="custom-bubble-check accept-payment-method" type="checkbox" id="pix_enabled" name="pix_enabled" data-target="pix-container" data-preview=".accepted-payment-pix">
                                    <label for="pix_enabled">Pix</label>
                                </div>
                            </div>
                        </div>

                        <div class="row-flex">
                            <div class="input-container" style="flex: 2">
                                <label>Seletor de Quantidade</label>
                                <div class="switch-holder labeled mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="quantity_selector_enabled" name="quantity_selector_enabled" class='check switch-checkout switch-labeled' data-label="count-selector-label">
                                        <span class="slider round"></span>
                                    </label>
                                    <p id="count-selector-label" class="switch-label"></p>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 3">
                                <label>Exigir e-mail no checkout</label>
                                <div class="switch-holder labeled mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="email_required" name="email_required" class='check switch-checkout switch-labeled' data-label="checkout-email-label">
                                        <span class="slider round"></span>
                                    </label>
                                    <p id="checkout-email-label" class="switch-label"></p>
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
                                    <select id='installments_limit' name='installments_limit' class="form-control select-pad">
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
                                    <select id='interest_free_installments' name='interest_free_installments' class="form-control select-pad">
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
                                        <option value="12" selected>12x </option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Parcela pré-selecionada</label>
                                <div class='form-group'>
                                    <select id='preselected_installment' name='preselected_installment' class="form-control select-pad">
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
                                        <option value="12" selected>12x</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="bank-billet-container">
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
                            <div class="input-container">
                                <label for="company_billing" class="checkout-label">Dias para vencimento</label>
                                <div class="tagged-input-div">
                                    <input class="tagged-input" type="number" id="bank_slip_due_days" name="bank_slip_due_days" min="1" max="99" maxlength="3" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
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

                            <div class="input-container credit-card-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Cartão de crédito</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="automatic_discount_credit_card" name="automatic_discount_credit_card" value="5" min="1" max="99" maxlength="2" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    <div class=" input-tag">%</div>
                                </div>
                            </div>

                            <div class="input-container bank-billet-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Boleto</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="automatic_discount_bank_slip" name="automatic_discount_bank_slip" value="5" min="1" max="99" maxlength="2" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    <div class=" input-tag">%</div>
                                </div>
                            </div>

                            <div class="input-container pix-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">PIX</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="automatic_discount_pix" name="automatic_discount_pix" value="5" min="1" max="99" maxlength="2" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
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
                                    <input type="checkbox" id="post_purchase_message_enabled" name="post_purchase_message_enabled" data-target="thanks-page-content" data-preview=".shop-message-preview" class='check switch-checkout'>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="thanks-page-content">
                            <label for="post_purchase_message_content" class="checkout-label">Título da sua mensagem</label>
                            <input type="text" class="checkout-input-text" id="post_purchase_message_title" name="post_purchase_message_title" style="margin-bottom: 15px;">

                            <div class="editor-container">
                                <div id="post_purchase_message_content_toolbar_container" class="editor-toolbar-container">
                                    <button class="ql-bold" data-toggle="tooltip" data-placement="bottom" title="Negrito"></button>
                                    <button class="ql-italic" data-toggle="tooltip" data-placement="bottom" title="Itálico"></button>
                                    <button class="ql-underline" data-toggle="tooltip" data-placement="bottom" title="Sublinhar"></button>
                                </div>

                                <div id="post_purchase_message_content" class="quill-editor">
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
                                    <input type="checkbox" id="whatsapp_enabled" name="whatsapp_enabled" data-target="whatsapp-content" data-preview=".whatsapp-preview" class='check switch-checkout'>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="checkout-subtitle">
                            <p>Ao ativar, seu cliente poderá receber o boleto via WhatsApp.</p>
                        </div>

                        <div class="whatsapp-content">
                            <label for="support_phone" class="checkout-label">Telefone do suporte <span class="observation-span">Opcional</span></label>
                            <div class="row-flex">
                                <input type="text" class="checkout-input-text" id="support_phone" name="support_phone" placeholder="Digite o telefone com DDD do suporte" data-mask="(00) 00000-0000" pattern="\([0-9]{2}\)[\s][0-9]{5}-[0-9]{4,5}"></input>
                                <button id="verify_phone" class="verify-button" type="button">Validar Telefone</button>
                            </div>

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
                                    Prévia
                                </h1>
                            </div>

                            <div id="preview_type_visual" class="radio-group">
                                <input class="custom-icon-radio desktop preview-type" id="preview_visual_computer" type="radio" name="preview-visual-type" data-target="preview-desktop-visual" data-toggle="preview-mobile-visual" checked />
                                <label for="preview_visual_computer"><img src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>

                                <input class="custom-icon-radio mobile preview-type" id="preview_visual_mobile" type="radio" name="preview-visual-type" data-target="preview-mobile-visual" data-toggle="preview-desktop-visual" />
                                <label for="preview_visual_mobile"><img src="{{ asset('/modules/checkouteditor/img/svg/mobile-icon.svg') }}"></label>
                            </div>
                        </div>

                        <div class="preview-container">
                            <div id="preview-desktop-visual" class="preview-content desktop">

                                <div class="preview-header">
                                    <div class="header-colorbar desktop secondary-color countdown-preview"></div>
                                    <div class="header-colorbar desktop primary-color textbar-preview"> </div>

                                    <div class="preview-banner wide-banner desktop">
                                        <img id="preview_banner_img_desktop" class="preview-banner-img" />
                                    </div>
                                </div>

                                <div class="preview-body desktop visual">

                                    <div class="checkout-step-type">
                                        <div class="steps-lines">
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
                                            <div id="finish_button_preview_desktop_visual" class="finish-button desktop"></div>
                                        </div>

                                        <div class="visual-content-right">

                                            <div class="white-placeholder-content"></div>

                                            <div class="list-placeholder-content desktop">
                                                <div class="list-item-placeholder">
                                                    <div class="circle-placeholder"></div>
                                                    <div class="strip-placeholder"></div>
                                                </div>

                                                <div class="list-item-placeholder">
                                                    <div class="circle-placeholder"></div>
                                                    <div class="strip-placeholder"></div>
                                                </div>

                                                <div class="list-item-placeholder desktop">
                                                    <div class="circle-placeholder"></div>
                                                    <div class="strip-placeholder"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div id="preview-mobile-visual" class="preview-content mobile" style="display: none">
                                <div id="preview-mobile-visual-collapse" class="preview-mobile-collapse collapse" aria-expanded="false">
                                    <div class="preview-header">
                                        <div class="header-colorbar mobile secondary-color countdown-preview"></div>
                                        <div class="header-colorbar mobile primary-color textbar-preview"> </div>


                                        <div class="menu-bar-mobile">

                                            <div class="logo-mobile">
                                                <img id="logo_preview" class="preview-logo-img" />
                                            </div>

                                            <div></div>

                                            <div class="menu">
                                                <div class="menu-circle-mobile"></div>
                                                <img class="arrow-icon-mobile" src="{{ asset('/modules/checkouteditor/img/svg/menu-arrow.svg') }}">
                                            </div>
                                        </div>

                                        <div class="preview-banner wide-banner mobile">
                                            <img id="preview_banner_img_mobile" class="preview-banner-img" />
                                        </div>
                                    </div>

                                    <div class="preview-body mobile visual">
                                        <div class="steps-lines mobile">
                                            <div class="step-one primary-color"></div>
                                            <div class="step-two"></div>
                                            <div class="step-three"></div>
                                        </div>

                                        <div class="preview-placeholder three-steps">
                                            <div id="finish_button_preview_mobile_visual" class="finish-button mobile"></div>
                                        </div>

                                        <div class="list-placeholder-content mobile">
                                            <div class="list-item-placeholder">
                                                <div class="circle-placeholder"></div>
                                                <div class="strip-placeholder"></div>
                                            </div>

                                            <div class="list-item-placeholder">
                                                <div class="circle-placeholder"></div>
                                                <div class="strip-placeholder"></div>
                                            </div>

                                            <div class="list-item-placeholder desktop">
                                                <div class="circle-placeholder"></div>
                                                <div class="strip-placeholder"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="expand-mobile-button-container">
                                    <a class="expand-mobile-button collapsed" role="button" data-toggle="collapse" href="#preview-mobile-visual-collapse" aria-expanded="false" aria-controls="preview-mobile-visual-collapse"></a>
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

                                    <input class="theme-radio" id="theme_spaceship" type="radio" name="theme_ready" value="1" />
                                    <label for="theme_spaceship">
                                        <div class="theme-primary-color" style="background: #4B8FEF;" data-color="#4B8FEF"></div>
                                        <div class="theme-secondary-color" style="background: #313C52;" data-color="#313C52"></div>
                                        <div class="theme-label">
                                            Spaceship
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_purple_space" type="radio" name="theme_ready" value="2" />
                                    <label for="theme_purple_space">
                                        <div class="theme-primary-color" style="background: #6C009E;" data-color="#6C009E"></div>
                                        <div class="theme-secondary-color" style="background: #3E005B;" data-color="#3E005B"></div>
                                        <div class="theme-label">
                                            Purple Space
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_cloud_std" type="radio" name="theme_ready" value="3" />
                                    <label for="theme_cloud_std">
                                        <div class="theme-primary-color" style="background: #FF7900;" data-color="#FF7900"></div>
                                        <div class="theme-secondary-color" style="background: #FFFFFF;" data-color="#FFFFFF"></div>
                                        <div class="theme-label">
                                            Cloud Std
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_sunny_day" type="radio" name="theme_ready" value="4" />
                                    <label for="theme_sunny_day">
                                        <div class="theme-primary-color" style="background: #FF7900;" data-color="#FF7900"></div>
                                        <div class="theme-secondary-color" style="background: #FFBF08;" data-color="#FFBF08"></div>
                                        <div class="theme-label">
                                            Sunny Day
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_blue_sky" type="radio" name="theme_ready" value="5" />
                                    <label for="theme_blue_sky">
                                        <div class="theme-primary-color" style="background: #009BF2;" data-color="#009BF2"></div>
                                        <div class="theme-secondary-color" style="background: #008BD9;" data-color="#008BD9"></div>
                                        <div class="theme-label">
                                            Blue Sky
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_all_black" type="radio" name="theme_ready" value="6" />
                                    <label for="theme_all_black">
                                        <div class="theme-primary-color" style="background: #262626;" data-color="#262626"></div>
                                        <div class="theme-secondary-color" style="background: #393939;" data-color="#393939"></div>
                                        <div class="theme-label">
                                            All Black
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_red_mars" type="radio" name="theme_ready" value="7" />
                                    <label for="theme_red_mars">
                                        <div class="theme-primary-color" style="background: #FA0000;" data-color="#FA0000"></div>
                                        <div class="theme-secondary-color" style="background: #9B0000;" data-color="#9B0000"></div>
                                        <div class="theme-label">
                                            Red Mars
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_pink_galaxy" type="radio" name="theme_ready" value="8" />
                                    <label for="theme_pink_galaxy">
                                        <div class="theme-primary-color" style="background: #F68AFF;" data-color="#F68AFF"></div>
                                        <div class="theme-secondary-color" style="background: #9B51A1;" data-color="#9B51A1"></div>
                                        <div class="theme-label">
                                            Pink Galaxy
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_turquoise" type="radio" name="theme_ready" value="9" />
                                    <label for="theme_turquoise">
                                        <div class="theme-primary-color" style="background: #32BCAD;" data-color="#32BCAD"></div>
                                        <div class="theme-secondary-color" style="background: #D3FAF5;" data-color="#D3FAF5"></div>
                                        <div class="theme-label">
                                            Turquoise
                                        </div>
                                    </label>

                                    <input class="theme-radio" id="theme_greener" type="radio" name="theme_ready" value="10" />
                                    <label for="theme_greener">
                                        <div class="theme-primary-color" style="background: #23D07D;" data-color="#23D07D"></div>
                                        <div class="theme-secondary-color" style="background: #02AD5B;" data-color="#02AD5B"></div>
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
                                        <input type="checkbox" id="theme_ready_enabled" name="theme_ready_enabled" data-target="custom-theme-content" data-toggle="theme-ready-content" class='check switch-checkout-accordion'>
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
                                                <input class="color-picker" type="color" id="color_primary" name="color_primary" value="#4B8FEF" styles="height: 20px">
                                            </div>

                                            <div class="input-container" style="margin-right: 20px">
                                                <label for="color_secondary">Cor secundária</label>
                                                <input class="color-picker" type="color" id="color_secondary" name="color_secondary" value="#313C52" styles="height: 20px">
                                            </div>

                                            <div class="input-container" style="margin-right: 20px">
                                                <label for="color_buy_button">Cor do botão de compra</label>
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
                                    Prévia
                                </h1>
                            </div>
                            <div class="radio-group">
                                <input class="custom-icon-radio desktop preview-type" id="preview_payment_desktop" type="radio" name="preview-payment-type" data-target="preview-desktop-payment" data-toggle="preview-mobile-payment" checked readonly/>
                                <label for="preview_payment_desktop"><img src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>

                                <input class="custom-icon-radio mobile preview-type" id="preview_payment_mobile" type="radio" name="preview-payment-type" data-target="preview-mobile-payment" data-toggle="preview-desktop-payment" />
                                <label for="preview_payment_mobile"><img src="{{ asset('/modules/checkouteditor/img/svg/mobile-icon.svg') }}"></label>
                            </div>
                        </div>

                        <div class="preview-container">
                            <div id="preview-desktop-payment" class="preview-content desktop">

                                <div class="preview-header">
                                    <div class="header-colorbar desktop secondary-color countdown-preview"></div>
                                    <div class="header-colorbar desktop primary-color textbar-preview"> </div>
                                </div>

                                <div class="preview-body desktop payment">
                                    <div class="preview-body-payment-content desktop">
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

                                                <div class="accepted-payment accepted-payment-card-creditcard" id="accepted_payment_card_creditcard">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-card.svg') }}" style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);">
                                                    <span>Cartão</span>
                                                </div>

                                                <div class="accepted-payment accepted-payment-pix" id="accepted_payment_pix">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-pix.svg') }}" style="width: 35px;">
                                                    <span>Pix</span>
                                                </div>

                                                <div class="accepted-payment accepted-payment-bank-billet" id="accepted_payment_bank_billet">
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

                                            <div class="finish-button desktop payment-desktop"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="preview-mobile-payment" class="preview-content mobile" style="display: none">
                                <div id="preview-mobile-payment-collapse" class="preview-mobile-collapse">
                                    <div class="preview-header">
                                        <div class="header-colorbar mobile secondary-color countdown-preview"></div>
                                        <div class="header-colorbar mobile primary-color textbar-preview"> </div>
                                    </div>

                                    <div class="preview-body mobile payment">
                                        <div class="preview-card">
                                            <div class="accepted-payment-list">
                                                <div class="accepted-payment accepted-payment-card-creditcard" id="accepted_payment_card_creditcard">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-card.svg') }}" style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);">
                                                </div>

                                                <div class="accepted-payment accepted-payment-pix" id="accepted_payment_pix">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-pix.svg') }}" style="width: 35px;">
                                                </div>

                                                <div class="accepted-payment accepted-payment-bank-billet" id="accepted_payment_bank_billet">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-boleto.svg') }}" style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);">
                                                </div>
                                            </div>

                                            <div class="accepted-payment-content credit-card">
                                                <div class="accepted-payment-credit-cards"></div>
                                                <div class="accepted-payment-form">
                                                    <div style="display: flex;">
                                                        <div class="input-form-placeholder"></div>
                                                        <div class="input-form-placeholder" style="width: 50px; background: #E2E2E2;"></div>
                                                    </div>
                                                    <div class="input-form-placeholder"></div>
                                                    <div class="input-form-placeholder"></div>
                                                    <div class="input-form-placeholder"></div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>

                    <div id="preview_post_purchase" style="display: none;">
                        <div class="title-buttons-group">
                            <div>
                                <h1 class="checkout-title">
                                    Prévia
                                </h1>
                            </div>

                            <div id="preview_type_post_purchase" class="radio-group">
                                <input class="custom-icon-radio desktop preview-type" id="preview_postpurchase_desktop" type="radio" name="preview-post-purchase-type" data-target="preview-desktop-post-purchase" data-toggle="preview-mobile-post-purchase" checked />
                                <label for="preview_postpurchase_desktop"><img src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>

                                <input class="custom-icon-radio mobile preview-type" id="preview_postpurchase_mobile" type="radio" name="preview-post-purchase-type" data-target="preview-mobile-post-purchase" data-toggle="preview-desktop-post-purchase" />
                                <label for="preview_postpurchase_mobile"><img src="{{ asset('/modules/checkouteditor/img/svg/mobile-icon.svg') }}"></label>
                            </div>
                        </div>

                        <div class="preview-container">
                            <div id="preview-desktop-post-purchase" class="preview-content desktop">

                                <div class="preview-body desktop post-purchase">
                                    <img src="{{ asset('/modules/checkouteditor/img/svg/barcode-icon.svg') }}" style="margin: 10px 0">

                                    <div class="input-form-placeholder" style="margin: 10px 0"></div>

                                    <div class="shop-message-preview desktop" style="margin: 10px 0">
                                        <h1 class="shop-message-preview-title">Obrigado por comprar conosco!</h1>

                                        <div class="shop-message-preview-content">Aproveite o <strong>desconto extra</strong> ao comprar no <u>Cartão ou pelo PIX!</u> <strong>É por tempo limitado.</strong></div>
                                    </div>

                                    <div class="card-container" style="margin-bottom: 20px;">
                                        <div class="grey-container" style="margin-bottom: 10px; height: 60px"></div>

                                        <div style="display: flex; width: 100%; justify-content: space-between">
                                            <div style="border-radius: 12px; height: 25px; width: 70px; background-color: #F5F5F5; margin: 0 20px 0 0;border-radius: 4px;"></div>

                                            <div style="border-radius: 12px; height: 25px; width: 80px; background-color: #2E85EC; border-radius: 4px; "></div>

                                            <div class="whatsapp-preview" style="display: flex; padding: 6px; border-radius: 12px; height: 25px; width: 120px; background-color: #36DB8C; border-radius: 4px;">
                                                <img src="{{ asset('/modules/checkouteditor/img/svg/whatsapp-icon.svg') }}">
                                            </div>
                                        </div>


                                    </div>

                                    <div class="card-container" style="display: flex; flex-direction: column; gap: 15px;">
                                        <div style="display: flex; width: 100%; justify-content: space-between; align-items: center; gap: 5px;">
                                            <div style="display: flex; width: 100%; align-items: center; gap: 5px;">
                                                <div class="grey-container" style="border-radius: 4px; height: 50px; width: 50px; min-width: 50px; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);"></div>
                                                <div class="grey-container" style="border-radius: 4px; height: 35px; width: 100%; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);"></div>
                                            </div>

                                            <div style="display: flex; width: 100%; align-items: center; gap: 5px;">
                                                <div class="grey-container" style="border-radius: 4px; height: 50px; width: 50px; min-width: 50px; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);"></div>
                                                <div class="grey-container" style="border-radius: 4px; height: 35px; width: 100%; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);"></div>
                                            </div>
                                        </div>

                                        <div style="display: flex; width: 100%; justify-content: space-between; align-items: center; gap: 5px;">
                                            <div style="display: flex; width: 100%; align-items: center; gap: 5px;">
                                                <div class="grey-container" style="border-radius: 4px; height: 50px; width: 50px; min-width: 50px; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);"></div>
                                                <div class="grey-container" style="border-radius: 4px; height: 35px; width: 100%; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);"></div>
                                            </div>

                                            <div style="display: flex; width: 100%; align-items: center; gap: 5px;">
                                                <div class="grey-container" style="border-radius: 4px; height: 50px; width: 50px; min-width: 50px; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);"></div>
                                                <div class="grey-container" style="border-radius: 4px; height: 35px; width: 100%; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="preview-mobile-post-purchase" class="preview-content mobile" style="display: none">
                                <div class="preview-mobile-collapse">
                                    <div class="preview-body mobile post-purchase">
                                        <img src="{{ asset('/modules/checkouteditor/img/svg/barcode-icon.svg') }}" style="height: 70px; margin: 5px 0">

                                        <div class="input-form-placeholder" style="margin: 10px 0"></div>

                                        <div class="shop-message-preview desktop" style="margin: 10px 0">
                                            <h1 class="shop-message-preview-title">Obrigado por comprar conosco!</h1>
                                            <div class="shop-message-preview-content">Aproveite o <strong>desconto extra</strong> ao comprar no <u>Cartão ou pelo PIX!</u> <strong>É por tempo limitado.</strong></div>
                                        </div>

                                        <div class="preview-card">
                                            <div class="grey-container" style="margin-bottom: 10px; height: 60px"></div>

                                            <div style="display: flex; width: 100%; justify-content: space-between">
                                                <div style="border-radius: 12px; height: 25px; width: 70px; background-color: #F5F5F5; margin: 0 20px 0 0;border-radius: 8px;"></div>

                                                <div style="border-radius: 12px; height: 35px; width: 100px; background-color: #2E85EC; border-radius: 4px; "></div>
                                            </div>

                                            <div class="whatsapp-preview" style="display: flex; padding: 6px; border-radius: 8px; height: 35px; width: 100%; background-color: #36DB8C; border-radius: 4px;">
                                                <img src="{{ asset('/modules/checkouteditor/img/svg/whatsapp-icon.svg') }}">
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>

                <div class="editor-buttons">
                    <div class="save-changes" id="changing_container" >
                        <div style="margin-right: 50px;">
                            Você tem alterações que <strong>não estão salvas</strong>
                        </div>

                        <div class="save-changes-button-group">
                            <button id="cancel_button" type="button" class="change-button cancel-changes-button">Cancelar</button>
                            <button type="submit" form="checkout_editor" class="change-button save-changes-button">Salvar alterações</button>
                            
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
                </div>

            </div>
        </div>
    </form>

    <div class="modal fade" id="modal_banner" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Banner no topo</h5>
                </div>
                <div class="molda-crop modal-body">
                    <div class="img-container">
                        <div class="row">
                            <div class="container-crop">
                                <img id="cropped_image">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer btn-group-crop">

                    <div class='row-flex'>
                        <button type="button" class="btn btn-secondary btn-crop-outline zoom" id="zoom-out">-</button>
                        <input id="zoom-slide" type="range" min="0" max="1" step="0.1" value="0">
                        <button type="button" class="btn btn-secondary btn-crop-outline zoom" id="zoom-in">+</button>
                        <button type="button" class="btn btn-secondary btn-crop-outline" id="crop-reset">Reset</button>
                    </div>

                    <div id='slider'></div>
                    <div>
                        <button type="button" class="btn btn-secondary btn-crop-outline" id="button-cancel-crop" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary btn-crop-filled" id="button-crop">Cortar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_verify_phone" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true" data-dismiss="modal">×</button>
                    <h4 class="modal-title">Create New Topic</h4>
                </div>
                <div class="modal-body centered">
                    <fieldset class='number-code'>
                        <div class="code-input-container">
                            <input name='code' class='code-input' required />
                            <input name='code' class='code-input' required />
                            <input name='code' class='code-input' required />
                            <input name='code' class='code-input' required />
                            <input name='code' class='code-input' required />
                            <input name='code' class='code-input' required />
                        </div>
                    </fieldset>

                </div>
                <div class="modal-footer btn-group-crop">
                    <button id="verify_phone" class="verify-button" type="button">Verificar</button>
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
<script src="{{asset('modules/checkouteditor/js/loadcheckoutdata.js?v='.uniqid())}}"></script>
@endpush