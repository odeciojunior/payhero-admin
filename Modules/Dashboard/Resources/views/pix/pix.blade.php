@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
    <link rel="stylesheet" href="{{ mix('modules/dashboard/css/pix.min.css') }}">
@endpush

<div id="modal-pix-onboarding" class="modal fade modal-fade-in-scale-up show">
    <div class="modal-dialog modal-simple modal-pix-onboarding">
        <div id="loader-onboarding"></div>
        <div class="modal-content">
            <div id="modal-content-pix">
                <div id="modal-pix-content-0">
                    <div id="header-pix-onboarding-1" class="modal-header flex-wrap">
                        <div class="w-p100 d-flex flex-column justify-content-center align-items-center">
                            <img id="icon" class="mb-20" src="{{ asset('modules/global/img/pix/sirius-icon.svg') }}" width="35">
                            <img class="img-fluid" src="{{ asset('modules/global/img/pix/presentation.png') }}" width="350">
                        </div>
                    </div>
                    <div class="modal-body text-center">
                        <div id="title-onboarding-1">O PIX chegou no Sirius!</div>
                        <div id="description-onboarding-1">
                            O futuro dos pagamentos já está entre nós. <br/>
                            A partir de hoje, você já pode vender com o PIX e receber<br/>
                            o valor das vendas diretamente na sua conta bancária!
                        </div>
                        <div class="btn btn-primary pix-onboarding-next">Descubra</div>
                    </div>
                </div>

                <div id="modal-pix-content-1">
                    @include('dashboard::pix.pix1')
                </div>

                <div id="modal-pix-content-2">
                    @include('dashboard::pix.pix2')
                </div>

                <div id="modal-pix-content-3">
                    @include('dashboard::pix.pix3')
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
@endpush
