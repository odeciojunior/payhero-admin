@push('css')
<link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=03') !!}">
<link rel="stylesheet" href="{!! asset('modules/global/adminremark/global/vendor/dropify/dropify.min.css') !!}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/quill.snow.css?v=' . uniqid()) }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/dropfy.css?v=' . uniqid()) }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/custom-inputs.css?v='.uniqid()) }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/cropper.css?v='.uniqid()) }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/style.css?v='.uniqid()) }}">
<link rel="stylesheet" href="{{ asset('/modules/checkouteditor/css/preview-styles.css?v='.uniqid()) }}">
@endpush

<!-- Page -->
<div class="checkout-container" style="max-height: 3585px;  margin-bottom: 20px;">

    <!-- <div class="card card-body" style="height: 122px;">
        <h1 class="checkout-title">
            Editor de Checkout
        </h1>
        <div class="checkout-subtitle">
            <span class="tag"><b>NOVO!</b></span> Adicione banner, temas pré-prontos ou personalize o seu próprio.
        </div>

    </div> -->

    <div class='row card no-gutters p-30 rounded-top'>

        <div class="col-12 font-size-24 pl-0 mb-10">
        Editor de Checkout
        </div>

        <div class="col-md-12">
            <div class="badge badge-primary font-size-14 mr-10">
                NOVO
            </div>

            <span class="font-size-16">Adicione banner, temas pré-prontos ou personalize o seu próprio.</span>
        </div>

    </div>

    <form id="checkout_editor">
        @method('PUT')
        <input type="hidden" id="checkout_editor_id">
        <div style="display: flex; flex-direction: column; width: 100%">
            <div class="grid-checkout-editor">

                <div id="checkout_type" class="checkout-content select-type">
                    <h1 class="checkout-title">
                        Selecione um tipo
                    </h1>

                    <div id="checkout_type" class="radio-group">
                        <input class="custom-radio" id="checkout_type_steps" type="radio" name="checkout_type_enum" value="1" style="width: 150px"/>
                        <label for="checkout_type_steps">Checkout de 3 passos</label>

                        <input class="custom-radio" id="checkout_type_unique" type="radio" name="checkout_type_enum" value="2" style="width: 150px"/>
                        <label for="checkout_type_unique">Checkout de 1 passo</label>
                    </div>
                </div>

                <div class="checkout-content visual">
                    <span class="title-icon">
                        <img class="icon-title" src="{{ asset('/modules/checkouteditor/img/svg/visual.svg') }}">
                        <h1 class="checkout-title">
                            Visual
                        </h1>
                    </span>

                    <hr style="margin-bottom: 0px">

                    <div class="favicon-logo-container">
                        <div class="logo-container">
                            <div class="title-buttons-group">
                                <div>
                                    <h1 class="checkout-title">
                                        Logo no checkout
                                    </h1>
                                </div>

                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px; margin-right: 0;'>
                                        <input type="checkbox" id="checkout_logo_enabled" name="checkout_logo_enabled" data-enable=".logo-content" data-preview=".logo-preview-container" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="logo-content low-opacity">

                                <div id='upload_logo'>
                                    <input type="file" id="checkout_logo" name="checkout_logo"
                                        data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                                    <input type="hidden" id="has_checkout_logo" value="false">
                                </div>

                                <div class="instrunctios">
                                    <p><b>Recomendações:</b> Imagem de
                                        300x300px, <b>.jpeg</b> ou <b>.png</b></p>
                                </div>


                                <div id="checkout_logo_error" class="checkout-error" style="display: none">
                                    <p>Por favor,carregue uma imagem de formato válido (jpg, jpeg ou png).</p>
                                </div>
                            </div>
                        </div>

                        <div class="vertical-line"></div>

                        <div class="favicon-container">
                            <div class="title-buttons-group">
                                <div class="row-flex">
                                    <h1 class="checkout-title">
                                        Favicon
                                    </h1>
                                    <div class="quantity-selector-tooltip">
                                        <img id="favicon-tooltip" data-target="favicon-tooltip-container"
                                            src="{{ asset('/modules/checkouteditor/img/svg/info-icon.svg') }}">
                                        <div id="favicon-tooltip-container" class="tooltip-container"
                                            style="display: none">
                                            <div class="tooltip-content">
                                                <p>
                                                    Favicon é a imagem que acompanha o título da sua página na aba do navegador.
                                                </p>
                                                <div class="tab-example">
                                                    <div class="row-flex">
                                                        <img class="sirius-icon"
                                                            src="{{ asset('/modules/checkouteditor/img/svg/icon-sirius.svg') }}">
                                                        <p>Sirius</p>
                                                    </div>
                                                    <p>x</p>
                                                </div>
                                            </div>
                                            <div class="tooltip-arrow"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px; margin-right: 0;'>
                                        <input type="checkbox" id="checkout_favicon_enabled" name="checkout_favicon_enabled" data-enable=".favicon-content" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="favicon-content low-opacity">
                                <div>
                                    <div class="radio-row">
                                        <input type="radio" id="favicon_logo" class="bigger-radio" name="checkout_favicon_type" value="1" checked style="outline: none">
                                        <label for="favicon_logo">Utilizar a logo como favicon</label><br>
                                    </div>

                                    <div class="radio-row">
                                        <input type="radio" id="favicon_uploaded" class="bigger-radio" name="checkout_favicon_type" value="2" style="outline: none">
                                        <label for="favicon_uploaded">Subir um arquivo diferente</label><br>
                                    </div>
                                </div>

                                <div id='upload_favicon' class="low-opacity">
                                    <input type="file" id="checkout_favicon" name="checkout_favicon" data-errors-position="outside" data-show-errors="false" data-max-heigth="32" data-max-width="32" data-show-loader="false" data-max-file-size="10M"  data-allowed-file-extensions="jpg jpeg png ico">
                                    <label for="checkout_favicon">Clique para fazer upload</label>
                                    <input type="hidden" id="has_checkout_favicon" value="false">
                                </div>

                                <div class="instrunctios">
                                    <p>Sua imagem deve ter 32x32px, nos 
                                       formatos .png, .jpg ou ICO</p>
                                </div>

                                <div id="checkout_favicon_error" class="checkout-error" style="display: none">
                                    <p>Por favor, carregue uma imagem de formato e tamanho válido.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr style="margin-top: 0px">

                    <div class="colors-container">
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
                            <div class="radio-group theme-ready theme-ready-first-line">
                                <input class="theme-radio" id="theme_spaceship" type="radio" name="theme_enum" value="1"
                                    checked />
                                <label for="theme_spaceship">
                                    <div class="theme-primary-color" style="background: #4B8FEF;" data-color="#4B8FEF">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #313C52;"
                                        data-color="#313C52"></div>
                                    <div class="theme-label">
                                        Sirius Std
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_cloud_std" type="radio" name="theme_enum"
                                    value="3" />
                                <label for="theme_cloud_std">
                                    <div class="theme-primary-color" style="background: #FF7900;" data-color="#FF7900">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #FFFFFF;"
                                        data-color="#FFFFFF"></div>
                                    <div class="theme-label">
                                        Cloud Std
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_sunny_day" type="radio" name="theme_enum"
                                    value="4" />
                                <label for="theme_sunny_day">
                                    <div class="theme-primary-color" style="background: #FF7900;" data-color="#FF7900">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #FFBF08;"
                                        data-color="#FFBF08"></div>
                                    <div class="theme-label">
                                        Sunny Day
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_blue_sky" type="radio" name="theme_enum"
                                    value="5" />
                                <label for="theme_blue_sky">
                                    <div class="theme-primary-color" style="background: #009BF2;" data-color="#009BF2">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #008BD9;"
                                        data-color="#008BD9"></div>
                                    <div class="theme-label">
                                        Blue Sky
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_all_black" type="radio" name="theme_enum"
                                    value="6" />
                                <label for="theme_all_black">
                                    <div class="theme-primary-color" style="background: #262626;" data-color="#262626">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #393939;"
                                        data-color="#393939"></div>
                                    <div class="theme-label">
                                        All Black
                                    </div>
                                </label>
                            </div>

                            <div class="radio-group theme-ready theme-ready-second-line">

                                <input class="theme-radio" id="theme_purple_space" type="radio" name="theme_enum"
                                    value="2" />
                                <label for="theme_purple_space">
                                    <div class="theme-primary-color" style="background: #6C009E;" data-color="#6C009E">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #3E005B;"
                                        data-color="#3E005B"></div>
                                    <div class="theme-label">
                                        Purple Space
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_red_mars" type="radio" name="theme_enum"
                                    value="7" />
                                <label for="theme_red_mars">
                                    <div class="theme-primary-color" style="background: #FA0000;" data-color="#FA0000">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #9B0000;"
                                        data-color="#9B0000"></div>
                                    <div class="theme-label">
                                        Red Mars
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_pink_galaxy" type="radio" name="theme_enum"
                                    value="8" />
                                <label for="theme_pink_galaxy">
                                    <div class="theme-primary-color" style="background: #E93889;" data-color="#E93889">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #9F2256;" data-color="#9F2256"></div>
                                    <div class="theme-label">
                                        Pink Galaxy
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_turquoise" type="radio" name="theme_enum"
                                    value="9" />
                                <label for="theme_turquoise">
                                    <div class="theme-primary-color" style="background: #32BCAD;" data-color="#32BCAD">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #D3FAF5;"
                                        data-color="#D3FAF5"></div>
                                    <div class="theme-label">
                                        Turquoise
                                    </div>
                                </label>

                                <input class="theme-radio" id="theme_greener" type="radio" name="theme_enum"
                                    value="10" />
                                <label for="theme_greener">
                                    <div class="theme-primary-color" style="background: #23D07D;" data-color="#23D07D">
                                    </div>
                                    <div class="theme-secondary-color" style="background: #02AD5B;"
                                        data-color="#02AD5B"></div>
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
                                    <input type="checkbox" id="theme_ready_enabled" name="theme_ready_enabled"
                                        data-target="custom-theme-content" data-toggle="theme-ready-second-line"
                                        class='check switch-checkout-accordion'>
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
                                            <input class="color-picker" type="color" id="color_primary"
                                                name="color_primary" value="#4B8FEF" styles="height: 20px">
                                        </div>

                                        <div class="input-container" style="margin-right: 20px">
                                            <label for="color_secondary">Cor secundária</label>
                                            <input class="color-picker" type="color" id="color_secondary"
                                                name="color_secondary" value="#313C52" styles="height: 20px">
                                        </div>

                                        <div class="input-container" style="margin-right: 20px">
                                            <label for="color_buy_button">Cor do botão de compra</label>
                                            <input class="color-picker" type="color" id="color_buy_button"
                                                name="color_buy_button" value="#23d07d" styles="height: 20px">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="checkbox-container">
                            <input class="checkbox" id="default_finish_color" type="checkbox" />
                            <label for="default_finish_color">Manter “Finalizar compra” verde</label>
                        </div>
                    </div>

                    <hr>

                    <div class="banner-top-container">
                        <div class="title-buttons-group">
                            <h1 class="checkout-title">
                                Banner no topo
                            </h1>

                            <div
                                style=" display: flex; min-width: 140px; justify-content: space-between; align-items: center;">
                                <div>
                                    <div id="banner_type" class="radio-group" style="justify-self: end; display: none;">
                                        <input class="custom-icon-radio" id="banner_type_square" type="radio"
                                            name="checkout_banner_type" value="0" />
                                        <label for="banner_type_square"><img
                                                src="{{ asset('/modules/checkouteditor/img/svg/banner-square.svg') }}"></label>

                                        <input class="custom-icon-radio" id="banner_type_wide" type="radio"
                                            name="checkout_banner_type" value="1" />
                                        <label for="banner_type_wide"><img
                                                src="{{ asset('/modules/checkouteditor/img/svg/banner-wide.svg') }}"></label>
                                    </div>
                                </div>


                                <div class="switch-holder mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="checkout_banner_enabled"
                                            name="checkout_banner_enabled" data-target="banner-top-content" data-preview=".preview-banner" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="banner-top-content" style="display: none">
                            <div id='upload-banner'>
                                <input type="file" id="checkout_banner" name="checkout_banner" data-max-file-size="10M" data-allowed-file-extensions="jpg jpeg png">
                                <input type="hidden" id="has_checkout_banner" value="false">
                                <div id="checkout_banner_error" class="checkout-error" style="display: none">
                                    <p>Por favor,carregue uma imagem de formato válido (jpg, jpeg ou png).</p>
                                </div>
                            </div>



                            <div class="row-flex">
                                <div class="instrunctios">
                                    <p><b>Indicações</b>
                                    Banner container: 960x210px
                                    Banner tela inteira: 1280x280px
                                    <p>
                                </div>

                                <div class="instrunctios">
                                    <p>Resoluções menores não serão aceitos. <b>Formatos: JPG, JPEG ou PNG.</b></p>
                                </div>

                                <div class="button-template">
                                    <button id="download_template_banner" class="line-button" type="button"
                                        data-href="{{ asset('/modules/checkouteditor/files/Gabarito_EditorCheckout.zip') }}">
                                        <img class="icon-title download"
                                            src="{{ asset('/modules/checkouteditor/img/svg/download-icon.svg') }}">
                                        Baixar gabarito</button>
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
                                        <input type="checkbox" id="countdown_enabled" name="countdown_enabled"
                                            data-target="countdown-content" data-preview=".countdown-preview"
                                            class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="countdown-content" style="display: none">
                                <div class="input-container">
                                    <label for="countdown_time" class="checkout-label">Tempo</label>
                                    <div class="tagged-input-div">
                                        <input class="tagged-input" type="number" id="countdown_time" value="15"
                                            name="countdown_time" min="0" max="99" maxlength="2"
                                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                        <div class=" input-tag">min</div>
                                    </div>
                                    <div id="countdown_time_error" class="checkout-error" style="display: none">
                                        <p>Preencha o campo de tempo mínimo.</p>
                                    </div>
                                </div>

                                <div class="input-container">
                                    <label for="countdown-time" class="checkout-label">Descrição <span
                                            class="observation-span">Opcional</span></label>
                                    <textarea class="checkout-textarea" id="countdown_description"
                                        name="countdown_description" rows="4" maxlength="150"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"></textarea>

                                    <div id="countdown_description_error" class="checkout-error" style="display: none;">
                                        <p>Preencha o campo de descrição.</p>
                                    </div>

                                    <div class="textarea-observation">
                                        <img class="dot"
                                            src="{{ asset('/modules/checkouteditor/img/svg/info-icon.svg') }}"><span
                                            class="observation-span">Visível somente em desktop.</span>
                                    </div>
                                </div>

                                <div class="input-container">
                                    <label for="timeout-message" class="checkout-label">Mensagem ao encerrar o
                                        tempo</label>
                                    <textarea class="checkout-textarea" id="countdown_finish_message"
                                        name="countdown_finish_message" rows="3" maxlength="150"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"></textarea>

                                    <div id="countdown_finish_message_error" class="checkout-error"
                                        style="display: none">
                                        <p>Preencha o campo de mensagem.</p>
                                    </div>
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
                                        <input type="checkbox" id="topbar_enabled" name="topbar_enabled"
                                            data-target="textbar-content" data-preview=".textbar-preview"
                                            class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="textbar-content" style="display: none">
                                <label for="topbar_content" class="checkout-label">Texto na barra</label>
                                <div class="editor-container">
                                    <div id="topbar_content_toolbar_container" class="editor-toolbar-container">
                                        <button class="ql-bold" data-toggle="tooltip" data-placement="bottom"
                                            title="Negrito"></button>
                                        <button class="ql-italic" data-toggle="tooltip" data-placement="bottom"
                                            title="Itálico"></button>
                                        <button class="ql-underline" data-toggle="tooltip" data-placement="bottom"
                                            title="Sublinhar"></button>
                                    </div>
                                    <div id="topbar_content" class="quill-editor">
                                    </div>
                                    <div id="topbar_content_error" class="checkout-error" style="display: none">
                                        <p>Preencha o campo de texto na barra.</p>
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
                                        <input type="checkbox" id="notifications_enabled" name="notifications_enabled"
                                            data-target="sales-notifications-content" class='check switch-checkout'>
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

                                        <input class="custom-radio" id="notifications_interval_30" type="radio" name="notifications_interval" value="30" checked/>
                                        <label for="notifications_interval_30">30 segundos</label>

                                        <input class="custom-radio" id="notifications_interval_45" type="radio" name="notifications_interval" value="45" />
                                        <label for="notifications_interval_45">45 segundos</label>

                                        <input class="custom-radio" id="notifications_interval_60" type="radio" name="notifications_interval" value="60" />
                                        <label for="notifications_interval_60">1 minuto</label>
                                    </div>
                                </div>


                                <div id="notification-table">
                                    <label for="notification-interval" class="checkout-label">Configure as
                                        notificações</label>
                                    <div class="notification-table-cointainer">
                                        <table class="table table-hover selectable" id="notification-table"
                                            data-plugin="selectable" data-row-selectable="true">
                                            <thead>
                                                <tr>
                                                    <th class="th-notification">
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input id="selectable-all-notification" type="checkbox">
                                                            <label></label>
                                                        </span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr style="height: 90px; max-height: 90px">
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-notification" type="checkbox"
                                                                id="notification_buying_enabled"
                                                                name="notification_buying_enabled">
                                                            <label for="notification_buying_enabled"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-notification" type="checkbox"
                                                                id="notification_bought_30_minutes_enabled"
                                                                name="notification_bought_30_minutes_enabled">
                                                            <label for="notification_bought_30_minutes_enabled"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-notification" type="checkbox"
                                                                id="notification_bought_last_hour_enabled"
                                                                name="notification_bought_last_hour_enabled">
                                                            <label for="notification_bought_last_hour_enabled"></label>
                                                        </span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>
                                                        <span class="checkbox-custom checkbox-primary">
                                                            <input class="selectable-notification" type="checkbox"
                                                                id="notification_just_bought_enabled"
                                                                name="notification_just_bought_enabled">
                                                            <label for="notification_just_bought_enabled"></label>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <table class="table table-hover notification-counts"
                                            id="notification-table-count" data-row-selectable="true">
                                            <thead>
                                                <tr>
                                                    <th class="th-notification">
                                                        Mensagem
                                                    </th>
                                                    <th class="th-notification">
                                                        Qtd Mínima
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><b>XX</b> pessoas estão comprando <b>{produto}</b> nesse
                                                        momento.</td>
                                                    <td>
                                                        <input class="table-number-input" type="number"
                                                            id="notification_buying_minimum"
                                                            name="notification_buying_minimum" value="1" min="1"
                                                            max="99" maxlength="2"
                                                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>XX</b> pessoas compraram <b>{produto}</b> nos últimos 30
                                                        minutos.</td>
                                                    <td>
                                                        <input class="table-number-input" type="number"
                                                            id="notification_bought_30_minutes_minimum"
                                                            name="notification_bought_30_minutes_minimum" value="1"
                                                            min="1" max="99" maxlength="2"
                                                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>XX</b> pessoas compraram <b>{produto}</b> na última hora.
                                                    </td>
                                                    <td>
                                                        <input class="table-number-input" type="number"
                                                            id="notification_bought_last_hour_minimum"
                                                            name="notification_bought_last_hour_minimum" value="1"
                                                            min="1" max="99" maxlength="2"
                                                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td><b>{nome}</b> de <b>{cidade}</b> acabou de comprar esse produto
                                                    </td>
                                                    <td style="padding: 20px 20px 30px 10px;">
                                                        <input class="table-number-input" type="number"
                                                            id="notification_just_bought_minimum"
                                                            name="notification_just_bought_minimum" value="1" min="1"
                                                            max="99" maxlength="2"
                                                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                                    </td>
                                                </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div id="notification_error" class="checkout-error" style="display: none;">
                                        <p>Preencha os campos da tabela.</p>
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
                                        <input type="checkbox" id="social_proof_enabled" name="social_proof_enabled"
                                            data-target="social-proof-content" class='check switch-checkout'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="social-proof-content" style="display: none">
                                <div class="input-container">
                                    <label for="social_proof_message" class="checkout-label">Mensagem</label>
                                    <textarea class="checkout-textarea" id="social_proof_message" name="social_proof_message" rows="1">
                                        
                                    </textarea>
                                    <div id="social_proof_message_error" class="checkout-error" style="display: none;">
                                        <p>Preencha o campo de mensagem.</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="social-proof-vars" class="checkout-label">Adicionar variáveis</label>
                                    <div style="display: flex; margin-bottom: 10px;">
                                        <button id="" class='add-tag' data-input="#social_proof_message"
                                            data-tag="{ num-visitantes }">num-visitantes</button>
                                        <!-- <button id="" class='add-tag' data-input="#social_proof_message"
                                            data-tag="{ nome-produto }">nome-produto</button> -->
                                    </div>
                                </div>


                                <div class="input-container" style="width: 150px;">
                                    <label for="social_proof_minimum" class="checkout-label">Mínimo de vistantes</label>
                                    <div class="tagged-input-div">
                                        <input class="tagged-input" type="number" id="social_proof_minimum"
                                            name="social_proof_minimum" min="1" max="99" maxlength="2"
                                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                        <div class=" input-tag">visitantes</div>
                                    </div>
                                </div>
                                <div id="social_proof_minimum_error" class="checkout-error" style="display: none;">
                                    <p>Preencha o campo de mínimo de visitantes.</p>
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
                            <div class="input-container" style="flex: 3; min-width: 200px;">
                                <label for="invoice_description" class="checkout-label">Descrição na fatura <span
                                        class="observation-span">Opcional</span></label>
                                <input type="text" class="checkout-input-text" id="invoice_description"
                                    name="invoice_description" />
                            </div>

                            <div class="input-container" style="flex: 4">
                                <label for="company_billing" class="checkout-label">Empresa responsável pelo
                                    faturamento</label>
                                <div class='form-group'>
                                    <select id='companies' name='company_id' class="sirius-select"></select>
                                </div>
                            </div>
                        </div>


                        <div class="row-flex">
                            <div class="input-container" style="flex: 2">
                                <label class="checkout-label">Aceitar pagamentos de</label>
                                <div id="payment_type_accept" class="check-group" style="justify-self: end;">
                                    <input class="custom-bubble-check accept-payment-type" type="checkbox" id="cpf_enabled" name="cpf_enabled">
                                    <label for="cpf_enabled">CPF</label>

                                    <input class="custom-bubble-check accept-payment-type" type="checkbox" id="cnpj_enabled" name="cnpj_enabled">
                                    <label for="cnpj_enabled">CNPJ</label>
                                </div>
                            </div>


                            <div class="input-container" style="flex: 3">
                                <label class="checkout-label">Métodos aceitos</label>
                                <div id="payment_accept" class="check-group" style="justify-self: end;">
                                    <input class="custom-bubble-check accept-payment-method" type="checkbox"
                                        id="credit_card_enabled" name="credit_card_enabled"
                                        data-target="credit-card-container"
                                        data-preview=".accepted-payment-card-creditcard" checked>
                                    <label for="credit_card_enabled">Cartão de crédito</label>

                                    <input class="custom-bubble-check accept-payment-method" type="checkbox"
                                        id="bank_slip_enabled" name="bank_slip_enabled"
                                        data-target="bank-billet-container"
                                        data-preview=".accepted-payment-bank-billet">
                                    <label for="bank_slip_enabled">Boleto</label>

                                    <input class="custom-bubble-check accept-payment-method" type="checkbox"
                                        id="pix_enabled" name="pix_enabled" data-target="pix-container"
                                        data-preview=".accepted-payment-pix">
                                    <label for="pix_enabled">Pix</label>
                                </div>
                            </div>
                        </div>

                        <div class="row-flex">
                            <div class="input-container" style="flex: 2">
                                <label class="quantity-selector-label">Seletor de quantidade
                                    <div class="quantity-selector-tooltip">
                                        <img id="selector-tooltip"
                                            src="{{ asset('/modules/checkouteditor/img/svg/info-icon.svg') }}">
                                        <div id="selector-tooltip-container" class="tooltip-container"
                                            style="display: none">
                                            <div class="tooltip-content">
                                                <p>Ao ativar, você permite que seu cliente selecione a quantidade de um
                                                    mesmo produto no checkout.</p>

                                                <div class="input-example">
                                                    <div class="grey-cube"></div>
                                                    <p>Nome do produto</p>
                                                    <div class="counter-example">
                                                        <img
                                                            src="{{ asset('/modules/checkouteditor/img/svg/red-minus.svg') }}">
                                                        <p>1</p>
                                                        <img
                                                            src="{{ asset('/modules/checkouteditor/img/svg/green-plus.svg') }}">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="tooltip-arrow"></div>
                                        </div>
                                    </div>
                                </label>
                                <div class="switch-holder labeled mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="quantity_selector_enabled"
                                            name="quantity_selector_enabled"
                                            class='check switch-checkout switch-labeled'
                                            data-label="count-selector-label">
                                        <span class="slider round"></span>
                                    </label>
                                    <p id="count-selector-label" class="switch-label"></p>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 3">
                                <label>Exigir e-mail no checkout</label>
                                <div class="switch-holder labeled mb-3">
                                    <label class="switch" style='top:3px'>
                                        <input type="checkbox" id="email_required" name="email_required"
                                            class='check switch-checkout switch-labeled'
                                            data-label="checkout-email-label">
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
                                    <select id='installments_limit' name='installments_limit' class="sirius-select">
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
                                        <option value="12" select>12x</option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Sem juros até</label>
                                <div class='form-group'>
                                    <select id='interest_free_installments' name='interest_free_installments'
                                        class="sirius-select">
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
                                        <option value="12" select>12x </option>
                                    </select>
                                </div>
                            </div>

                            <div class="input-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Parcela pré-selecionada</label>
                                <div class='form-group'>
                                    <select id='preselected_installment' name='preselected_installment'
                                        class="sirius-select">
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
                                        <option value="12" select>12x</option>
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

                        <div>
                            <div class="input-container">
                                <label for="bank_slip_due_days" class="checkout-label">Dias para vencimento</label>
                                <div class="tagged-input-div">
                                    <input class="tagged-input" type="number" id="bank_slip_due_days"
                                        name="bank_slip_due_days" value="3" min="1" max="99" maxlength="2"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    <div class=" input-tag">dias</div>
                                </div>
                                <div id="bank_slip_due_days_error" class="checkout-error" style="display: none;">
                                    <p>Preencha o campo de dias para vencimento.</p>
                                </div>
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
                                <div class="checkout-subtitle">
                                    <p>O desconto em % será aplicado de acordo com o método de pagamento.</p>
                                </div>
                            </div>

                            <div>
                            </div>
                        </div>

                        <div style="display:flex; justify-content: center;">

                            <div class="input-container credit-card-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Cartão de crédito</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="automatic_discount_credit_card"
                                        name="automatic_discount_credit_card" value="0" min="0" max="99" maxlength="2"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    <div class=" input-tag">%</div>
                                </div>
                            </div>

                            <div class="input-container bank-billet-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">Boleto</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="automatic_discount_bank_slip"
                                        name="automatic_discount_bank_slip" value="0" min="0" max="99" maxlength="2"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    <div class=" input-tag">%</div>
                                </div>
                            </div>

                            <div class="input-container pix-container" style="flex: 1">
                                <label for="company_billing" class="checkout-label">PIX</label>
                                <div class="tagged-input-div" style="width: 100px;">
                                    <input class="tagged-input" type="number" id="automatic_discount_pix"
                                        name="automatic_discount_pix" value="0" min="0" max="99" maxlength="2"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    <div class=" input-tag">%</div>
                                </div>
                            </div>

                        </div>


                    </div>

                </div>

                <div class="checkout-content post-purchase-pages" id="post_purchase" style="margin-bottom: 500px;">

                    <span class="title-icon">
                        <img class="icon-title" src="{{ asset('/modules/checkouteditor/img/svg/paid-page.svg') }}">
                        <h1 class="checkout-title">
                            Página pós-compra
                        </h1>
                    </span>

                    <div class="checkout-subtitle">
                        <p>Personalize sua página de obrigado.</p>
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
                                    <input type="checkbox" id="post_purchase_message_enabled"
                                        name="post_purchase_message_enabled" data-target="thanks-page-content"
                                        data-preview=".shop-message-preview" class='check switch-checkout'>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="thanks-page-content" style="display: none">
                            <div style="margin-bottom: 15px;">
                                <label for="post_purchase_message_content" class="checkout-label">Título da sua
                                    mensagem</label>
                                <input type="text" class="checkout-input-text" id="post_purchase_message_title"
                                    name="post_purchase_message_title">
                                <div id="post_purchase_message_title_error" class="checkout-error"
                                    style="display: none;">
                                    <p>Preencha o campo de título da mensagem.</p>
                                </div>
                            </div>


                            <div class="editor-container">
                                <div id="post_purchase_message_content_toolbar_container"
                                    class="editor-toolbar-container">
                                    <button class="ql-bold" data-toggle="tooltip" data-placement="bottom"
                                        title="Negrito"></button>
                                    <button class="ql-italic" data-toggle="tooltip" data-placement="bottom"
                                        title="Itálico"></button>
                                    <button class="ql-underline" data-toggle="tooltip" data-placement="bottom"
                                        title="Sublinhar"></button>
                                </div>

                                <div id="post_purchase_message_content" class="quill-editor">
                                </div>
                                <div id="post_purchase_message_content_error" class="checkout-error"
                                    style="display: none;">
                                    <p>Preencha o campo de mensagem.</p>
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
                                    <input type="checkbox" id="whatsapp_enabled" name="whatsapp_enabled"
                                        data-target="whatsapp-content" data-preview=".whatsapp-preview"
                                        class='check switch-checkout'>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="checkout-subtitle">
                            <p>Ao ativar, seu cliente poderá receber o boleto via WhatsApp.</p>
                        </div>

                        <div class="whatsapp-content" style="display: none">
                            <label for="support_phone" class="checkout-label">Telefone do suporte <span
                                    class="observation-span">Opcional</span></label>
                            <div class="row-flex">
                                <input type="text" class="checkout-input-text" id="support_phone" name="support_phone"
                                    placeholder="Digite o telefone com DDD do suporte"
                                    data-mask="(00) 00000-0000"></input>
                                <button id="remove_phone" class="remove-button" type="button">Remover</button>
                                <button id="verify_phone_open" class="verify-button" type="button">Validar
                                    telefone</button>
                                <button id="verified_phone_open" class="verified-button" type="button"
                                    style="display: none;">Trocar telefone</button>
                            </div>
                            <div id="support_phone_error" class="checkout-error" style="display: none">
                                <p>Preencha o campo com um telefone válido.</p>
                            </div>

                            <div class="textarea-observation">
                                <img class="dot"
                                    src="{{ asset('/modules/checkouteditor/img/svg/info-icon.svg') }}"></span><span
                                    class="observation-span">Caso preenchido, esse número apareçerá para o cliente no
                                    envio da mensagem de WhatsApp.</span>
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
                                <input class="custom-icon-radio desktop preview-type" id="preview_visual_computer"
                                    type="radio" name="preview-visual-type" data-target="preview-desktop-visual"
                                    data-toggle="preview-mobile-visual" checked />
                                <label for="preview_visual_computer"><img
                                        src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>

                                <input class="custom-icon-radio mobile preview-type" id="preview_visual_mobile"
                                    type="radio" name="preview-visual-type" data-target="preview-mobile-visual"
                                    data-toggle="preview-desktop-visual" />
                                <label for="preview_visual_mobile"><img
                                        src="{{ asset('/modules/checkouteditor/img/svg/mobile-icon.svg') }}"></label>
                            </div>
                        </div>

                        <div class="preview-container">
                            <div id="preview-desktop-visual" class="preview-content desktop">

                                <div class="preview-header">
                                    <div class="header-colorbar desktop primary-color  countdown-preview"></div>
                                    <div class="header-colorbar desktop secondary-color textbar-preview"> </div>

                                    <div class="preview-banner wide-banner desktop" style="display: none">
                                        <img id="preview_banner_img_desktop" class="preview-banner-img" />
                                    </div>
                                </div>

                                <div id="logo_preview_desktop_div" class="logo-div logo-desktop-div desktop">
                                    <div class="logo-desktop logo-preview-container" style="display: none">
                                        <img id="logo_preview_desktop" class="preview-logo desktop" alt="Logo" />
                                    </div>
                                </div>


                                <div class="preview-body visual desktop">
                                    <div class="checkout-step-type">
                                        <div class="steps-lines">
                                            <div class="step-one primary-color"></div>
                                            <div class="step-two secondary-color"></div>
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
                                            <div id="finish_button_preview_desktop_visual"
                                                class="finish-button desktop"></div>
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

                            <div id="preview-mobile-visual" class="preview-content mobile" style="display: none;">
                                <div id="preview-mobile-visual-collapse" class="preview-mobile-collapse">
                                    <div class="preview-header">
                                        <div class="header-colorbar mobile primary-color   countdown-preview"></div>
                                        <div class="header-colorbar mobile secondary-color textbar-preview"> </div>


                                            <div class="menu-bar-mobile">
                                                <div class="menu">
                                                    <div class="menu-circle-mobile primary-color"></div>
                                                    <img class="arrow-icon-mobile" src="{{ asset('/modules/checkouteditor/img/svg/menu-arrow.svg') }}">
                                                </div>
                                            </div>


                                        <div class="preview-banner wide-banner mobile" style="display: none">
                                            <img id="preview_banner_img_mobile" class="preview-banner-img" />
                                        </div>
                                    </div>

                                    <div class="logo-div logo-menu-bar has-banner" style="overflow-y:hidden">
                                        <div>
                                            <div class="logo-mobile-div logo-preview-container" style="display: none">
                                                <img id="logo_preview_mobile" class="preview-logo mobile" />
                                            </div>
                                        </div>

                                        <div class="purchase-menu-mobile">
                                            <img class="arrow-icon-mobile" src="{{ asset('/modules/checkouteditor/img/svg/purchase-icon.svg') }}">
                                            <div class="menu-circle-mobile primary-color"></div>
                                            <img class="arrow-icon-mobile" src="{{ asset('/modules/checkouteditor/img/svg/menu-arrow.svg') }}">
                                        </div>
                                    </div>

                                    <div class="preview-body mobile visual">
                                        <div class="steps-lines mobile">
                                            <div class="step-one primary-color"></div>
                                            <div class="step-two"></div>
                                            <div class="step-three"></div>
                                        </div>

                                        <div class="preview-placeholder three-steps">
                                            <div id="finish_button_preview_mobile_visual" class="finish-button mobile">
                                            </div>
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
                                <input class="custom-icon-radio desktop preview-type" id="preview_payment_desktop"
                                    type="radio" name="preview-payment-type" data-target="preview-desktop-payment"
                                    data-toggle="preview-mobile-payment" checked readonly />
                                <label for="preview_payment_desktop"><img
                                        src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>

                                <input class="custom-icon-radio mobile preview-type" id="preview_payment_mobile"
                                    type="radio" name="preview-payment-type" data-target="preview-mobile-payment"
                                    data-toggle="preview-desktop-payment" />
                                <label for="preview_payment_mobile"><img
                                        src="{{ asset('/modules/checkouteditor/img/svg/mobile-icon.svg') }}"></label>
                            </div>
                        </div>

                        <div class="preview-container">
                            <div id="preview-desktop-payment" class="preview-content desktop">

                                <div class="preview-header">
                                    <div class="header-colorbar desktop primary-color countdown-preview"></div>
                                    <div class="header-colorbar desktop secondary-color textbar-preview"> </div>
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

                                                <div class="accepted-payment accepted-payment-card-creditcard"
                                                    id="accepted_payment_card_creditcard">
                                                    
                                                    <!-- <img src="{{ asset('/modules/checkouteditor/img/svg/icon-card.svg') }}" style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);"> -->

                                                        <svg width="34" height="24" viewBox="0 0 34 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 20px;">
                                                            <path d="M23.3744 16.0941C22.6703 16.0941 22.0994 16.663 22.0994 17.3647C22.0994 18.0664 22.6703 18.6353 23.3744 18.6353H27.6244C28.3286 18.6353 28.8994 18.0664 28.8994 17.3647C28.8994 16.663 28.3286 16.0941 27.6244 16.0941H23.3744ZM3.89583 0C1.74422 0 0 1.73819 0 3.88235V20.1176C0 22.2618 1.74422 24 3.89583 24H30.1042C32.2558 24 34 22.2618 34 20.1176V3.88235C34 1.73819 32.2558 0 30.1042 0H3.89583ZM2.125 20.1176V9.17647H31.875V20.1176C31.875 21.0923 31.0822 21.8824 30.1042 21.8824H3.89583C2.91783 21.8824 2.125 21.0923 2.125 20.1176ZM2.125 7.05882V3.88235C2.125 2.90773 2.91783 2.11765 3.89583 2.11765H30.1042C31.0822 2.11765 31.875 2.90773 31.875 3.88235V7.05882H2.125Z" fill="#2e85ec"/>
                                                        </svg>
                                                        
                                                    <span>Cartão</span>
                                                </div>

                                                <div class="accepted-payment accepted-payment-pix"
                                                    id="accepted_payment_pix">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-pix.svg') }}"
                                                        style="width: 35px;">
                                                    <span>Pix</span>
                                                </div>

                                                <div class="accepted-payment accepted-payment-bank-billet"
                                                    id="accepted_payment_bank_billet">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-boleto.svg') }}"
                                                        style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);">
                                                    <span>Boleto</span>
                                                </div>

                                            </div>

                                            <div class="accepted-payment-content credit-card">
                                                <div class="accepted-payment-credit-cards">

                                                </div>

                                                <div class="accepted-payment-form">

                                                    <div style="display: flex;">
                                                        <div class="input-form-placeholder"></div>
                                                        <div class="input-form-placeholder"
                                                            style="width: 50px; background: #E2E2E2;"></div>
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
                                        <div class="header-colorbar mobile primary-color countdown-preview"></div>
                                        <div class="header-colorbar mobile secondary-color textbar-preview"> </div>
                                    </div>

                                    <div class="preview-body mobile payment">
                                        <div class="preview-card">
                                            <div class="accepted-payment-list">
                                                <div class="accepted-payment accepted-payment-card-creditcard"
                                                    id="accepted_payment_card_creditcard">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-card.svg') }}"
                                                        style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);">
                                                </div>

                                                <div class="accepted-payment accepted-payment-pix"
                                                    id="accepted_payment_pix">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-pix.svg') }}"
                                                        style="width: 35px;">
                                                </div>

                                                <div class="accepted-payment accepted-payment-bank-billet"
                                                    id="accepted_payment_bank_billet">
                                                    <img src="{{ asset('/modules/checkouteditor/img/svg/icon-boleto.svg') }}"
                                                        style="width: 20px; filter: invert(100%) sepia(96%) saturate(15%) hue-rotate(209deg) brightness(150%) contrast(102%);">
                                                </div>
                                            </div>

                                            <div class="accepted-payment-content credit-card">
                                                <div class="accepted-payment-credit-cards"></div>
                                                <div class="accepted-payment-form">
                                                    <div style="display: flex;">
                                                        <div class="input-form-placeholder"></div>
                                                        <div class="input-form-placeholder"
                                                            style="width: 50px; background: #E2E2E2;"></div>
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
                                <input class="custom-icon-radio desktop preview-type" id="preview_postpurchase_desktop"
                                    type="radio" name="preview-post-purchase-type"
                                    data-target="preview-desktop-post-purchase"
                                    data-toggle="preview-mobile-post-purchase" checked />
                                <label for="preview_postpurchase_desktop"><img
                                        src="{{ asset('/modules/checkouteditor/img/svg/computer-icon.svg') }}"></label>

                                <input class="custom-icon-radio mobile preview-type" id="preview_postpurchase_mobile"
                                    type="radio" name="preview-post-purchase-type"
                                    data-target="preview-mobile-post-purchase"
                                    data-toggle="preview-desktop-post-purchase" />
                                <label for="preview_postpurchase_mobile"><img
                                        src="{{ asset('/modules/checkouteditor/img/svg/mobile-icon.svg') }}"></label>
                            </div>
                        </div>

                        <div class="preview-container">
                            <div id="preview-desktop-post-purchase" class="preview-content desktop">

                                <div class="preview-body desktop post-purchase">
                                    <img src="{{ asset('/modules/checkouteditor/img/svg/barcode-icon.svg') }}"
                                        style="margin: 10px 0">

                                    <div class="input-form-placeholder" style="margin: 10px 0"></div>

                                    <div class="shop-message-preview desktop" style="margin: 10px 0">
                                        <h1 class="shop-message-preview-title">Obrigado por comprar conosco!</h1>

                                        <div class="shop-message-preview-content">Aproveite o <strong>desconto
                                                extra</strong> ao comprar no <u>Cartão ou pelo PIX!</u> <strong>É por
                                                tempo limitado.</strong></div>
                                    </div>

                                    <div class="card-container" style="margin-bottom: 20px;">
                                        <div class="grey-container" style="margin-bottom: 10px; height: 60px"></div>

                                        <div style="display: flex; width: 100%; justify-content: space-between">
                                            <div
                                                style="border-radius: 12px; height: 25px; width: 70px; background-color: #F5F5F5; margin: 0 20px 0 0;border-radius: 4px;">
                                            </div>

                                            <div class="primary-color"
                                                style="border-radius: 12px; height: 25px; width: 80px; background-color: #2E85EC; border-radius: 4px; margin-right: 5px;">
                                            </div>

                                            <div class="whatsapp-preview"
                                                style="display: flex; padding: 6px; border-radius: 12px; height: 25px; width: 120px; background-color: #36DB8C; border-radius: 4px;">
                                                <img
                                                    src="{{ asset('/modules/checkouteditor/img/svg/whatsapp-icon.svg') }}">
                                            </div>
                                        </div>


                                    </div>

                                    <div class="card-container"
                                        style="display: flex; flex-direction: column; gap: 15px;">
                                        <div
                                            style="display: flex; width: 100%; justify-content: space-between; align-items: center; gap: 5px;">
                                            <div style="display: flex; width: 100%; align-items: center; gap: 5px;">
                                                <div class="grey-container"
                                                    style="border-radius: 4px; height: 50px; width: 50px; min-width: 50px; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);">
                                                </div>
                                                <div class="grey-container"
                                                    style="border-radius: 4px; height: 35px; width: 100%; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);">
                                                </div>
                                            </div>

                                            <div style="display: flex; width: 100%; align-items: center; gap: 5px;">
                                                <div class="grey-container"
                                                    style="border-radius: 4px; height: 50px; width: 50px; min-width: 50px; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);">
                                                </div>
                                                <div class="grey-container"
                                                    style="border-radius: 4px; height: 35px; width: 100%; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);">
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            style="display: flex; width: 100%; justify-content: space-between; align-items: center; gap: 5px;">
                                            <div style="display: flex; width: 100%; align-items: center; gap: 5px;">
                                                <div class="grey-container"
                                                    style="border-radius: 4px; height: 50px; width: 50px; min-width: 50px; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);">
                                                </div>
                                                <div class="grey-container"
                                                    style="border-radius: 4px; height: 35px; width: 100%; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);">
                                                </div>
                                            </div>

                                            <div style="display: flex; width: 100%; align-items: center; gap: 5px;">
                                                <div class="grey-container"
                                                    style="border-radius: 4px; height: 50px; width: 50px; min-width: 50px; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);">
                                                </div>
                                                <div class="grey-container"
                                                    style="border-radius: 4px; height: 35px; width: 100%; background: linear-gradient(90deg, #F7F7F7 2.82%, rgba(239, 239, 239, 0) 95.36%);">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="preview-mobile-post-purchase" class="preview-content mobile" style="display: none">
                                <div class="preview-mobile-collapse">
                                    <div class="preview-body mobile post-purchase">
                                        <img src="{{ asset('/modules/checkouteditor/img/svg/barcode-icon.svg') }}"
                                            style="height: 70px; margin: 5px 0">

                                        <div class="input-form-placeholder" style="margin: 10px 0"></div>

                                        <div class="shop-message-preview desktop" style="margin: 10px 0">
                                            <h1 class="shop-message-preview-title">Obrigado por comprar conosco!</h1>
                                            <div class="shop-message-preview-content">Aproveite o <strong>desconto
                                                    extra</strong> ao comprar no <u>Cartão ou pelo PIX!</u> <strong>É
                                                    por tempo limitado.</strong></div>
                                        </div>

                                        <div class="preview-card">
                                            <div class="grey-container" style="margin-bottom: 10px; height: 60px"></div>

                                            <div style="display: flex; width: 100%; justify-content: space-between">
                                                <div
                                                    style="border-radius: 12px; height: 35px; width: 70px; background-color: #F5F5F5; margin: 0 20px 0 0;border-radius: 8px;">
                                                </div>

                                                <div class="primary-color"
                                                    style="border-radius: 12px; height: 35px; width: 100px; background-color: #2E85EC; border-radius: 4px;">
                                                </div>
                                            </div>

                                            <div class="whatsapp-preview"
                                                style="display: flex; padding: 6px; border-radius: 8px; height: 35px; width: 100%; background-color: #36DB8C; border-radius: 4px;">
                                                <img
                                                    src="{{ asset('/modules/checkouteditor/img/svg/whatsapp-icon.svg') }}">
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                </div>

                <div class="editor-buttons position-fixed container page-content" style="width: inhe;">

                    <div class="save-changes" id="save_success" style="display: none;">
                        <p>
                            Alterações salvas com <strong>sucesso</strong>!
                        </p>

                        <div>
                            <img class="save-icon" src="{{ asset('/modules/checkouteditor/img/svg/save-check.svg') }}">
                        </div>
                    </div>

                    <div class="save-changes" id="save_error" style="display: none;">
                        <p>
                            <strong>Ops!</strong> Algo deu errado.
                        </p>

                        <div>
                            <img class="save-icon" src="{{ asset('/modules/checkouteditor/img/svg/save-error.svg') }}">
                        </div>
                    </div>

                    <div class="save-changes" id="save_empty_fields" style="display: none;">
                        <p>
                            <strong>Ops!</strong> Verifique as mensagem em vermelho.
                        </p>

                        <div>
                            <img class="save-icon" src="{{ asset('/modules/checkouteditor/img/svg/save-error.svg') }}">
                        </div>
                    </div>

                    <div class="save-changes " id="save_changes" style="display: none">

                        <p>
                            Você tem alterações que <strong>não estão salvas</strong>
                        </p>

                        <div class="save-changes-button-group">
                            <button id="cancel_button" type="button"
                                class="change-button cancel-changes-button">Cancelar</button>
                            <button type="submit" form="checkout_editor"
                                class="change-button save-changes-button">Salvar alterações</button>
                        </div>
                    </div>

                    <div class="save-changes" id="save_load" style="display: none;">
                        <p>
                            Um momento... <strong>Estamos salvando suas alterações.</strong>
                        </p>

                        <div class="lds-ring">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>

    <div class="modal fade" id="modal_banner" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-crop" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Banner no topo</h5>
                </div>
                <div class="modal-crop modal-body">
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
                        <button type="button" class="btn btn-crop-reset" id="crop-reset">Reset</button>
                    </div>

                    <div id='slider'></div>
                    <div>
                        <button type="button" class="btn btn-crop-cancel" id="button-cancel-crop"
                            data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-crop-cut" id="button-crop">Cortar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_verify_phone" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Validar telefone</h3>
                    <button type="button" class="close verify-phone" aria-hidden="true" data-dismiss="modal">x</button>
                </div>
                <div id="modal_verify_content" class="modal-body spaced">
                    <p>
                        Enviamos um código de confirmação para <br />
                        o seu telefone <b id="phone_modal"></b>
                    </p>

                    <p><b>Digite ou cole aqui:</b></p>

                    <fieldset class='number-code'>
                        <div class="code-input-container">
                            <input type="number" name='verify-phone-code' class='code-input' />
                            <input type="number" name='verify-phone-code' class='code-input' />
                            <input type="number" name='verify-phone-code' class='code-input' />
                            <input type="number" name='verify-phone-code' class='code-input' />
                            <input type="number" name='verify-phone-code' class='code-input' />
                            <input type="number" name='verify-phone-code' class='code-input' />
                        </div>
                    </fieldset>
                    <p class="verify-error" style="display: none">Código inválido ou vencido.</p>

                    <a id="resend_code" class="resend-code">Reenviar código</a>
                    <p id="timer" style="display: none"></p>

                    <button id="verify_phone" class="verify-button" type="button">Verificar</button>

                </div>


                <div id="modal_verified_content" class="modal-body centered" style="display: none">
                    <img class="icon-verified-modal"
                        src="{{ asset('/modules/checkouteditor/img/svg/verified-icon.svg') }}">

                    <div>
                        <h2> Seu telefone foi validado com sucesso! </h2>
                        <p> As mensagens de WhatsApp solicitadas pelo seu cliente <br /> terão o telefone de suporte da
                            sua loja. </p>
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="{{ asset('modules/global/adminremark/global/js/Plugin/cropper.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/dropify/dropify.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Plugin/dropify.js') }}"></script>
<script src="{{asset('modules/checkouteditor/js/quill.min.js?v=' . uniqid())}}"></script>
<script src="{{asset('modules/checkouteditor/js/cropper.min.js?v=' . uniqid())}}"></script>
<script src="{{asset('modules/checkouteditor/js/verifyPhone.js?v=' . uniqid())}}"></script>
<script src="{{asset('modules/checkouteditor/js/checkoutEditor.js?v=' . uniqid())}}"></script>
<script src="{{asset('modules/checkouteditor/js/loadCheckoutData.js?v=' . uniqid())}}"></script>
<script src="{{asset('modules/checkouteditor/js/scrollPreview.js?v=' . uniqid())}}"></script>

@endpush
