<link rel="stylesheet" href="{{ asset('modules/global/css/upsell.css?v=1') }}">
<div class="row justify-content-center">
    <div class="col pb-5">
        <div class="topbar" id="upsell-header"></div>
        <div class="message">
            <div class="container">
                <h1 class="title" id="upsell-title"></h1>
                <p id="upsell-description"></p>
            </div>
        </div>
        <div class="timer" id="timer">
            <span class="timer-title">Oferta por tempo limitado:</span>
            <div class="d-flex justify-content-center">
                <div class="d-flex flex-column">
                    <span class="digit" id="minutes">--</span>
                    <span class="timer-text">Minutos</span>
                </div>
                <span class="digit separator">:</span>
                <div class="d-flex flex-column">
                    <span class="digit" id="seconds">--</span>
                    <span class="timer-text">Segundos</span>
                </div>
            </div>
        </div>
        <div id="div-upsell-products">
        </div>
        <div class="text-center px-3 my-5">
            <a id="skip-offert" class="text-success pointer">Não, obrigado, vou passar essa oferta</a>
        </div>
    </div>
</div>
