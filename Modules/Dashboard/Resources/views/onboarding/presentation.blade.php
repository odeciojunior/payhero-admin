@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

    <link rel="stylesheet" href="{{ asset('modules/dashboard/css/onboarding-details.css?v=07') }}">
@endpush

<div id="modal-onboarding" class="modal fade modal-fade-in-scale-up show">
    <div class="modal-dialog modal-simple modal-onboarding">
        <div id="loader-onboarding"></div>
            <div class="modal-content">
                <div id="modal-content-onboarding">
                    <div id="modal-presentation">
                        <div id="header-onboarding-1" class="modal-header flex-wrap">
                            <div class="w-p100 d-flex justify-content-center">
                                <img id="icon" src="{{ asset('modules/global/img/onboarding-presentation.png') }}" alt="Image">
                            </div>
                        </div>
                        <div class="modal-body text-center">
                            <div id="title-onboarding-1">Temos novidades para você, <span id="user-name"></span></div>
                            <div id="description-onboarding-1">Você já deve ter percebido que o Sirius tem passado por diversas melhorias. Além de um novo visual, hoje queremos te apresentar algumas ferramentas e funcionalidades que acabaram de chegar por aqui.</div>
                            <div id="onboarding-next-presentation" class="btn btn-primary">Vamos lá!</div>
                        </div>
                    </div>

                    <div id="modal-gamification">
                        @include('dashboard::onboarding.gamification')
                    </div>

                    <div id="modal-account-health">
                        @include('dashboard::onboarding.account-health')
                    </div>

                    <div id="modal-news-summary">
                        @include('dashboard::onboarding.news-summary')
                    </div>
                </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
@endpush
