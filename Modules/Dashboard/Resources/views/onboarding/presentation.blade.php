@push('css')
    <link rel="stylesheet" href="{{ asset('modules/dashboard/css/onboarding-details.css?v=01') }}">
@endpush

{{--<div class="modal fade modal-fade-in-scale-up show">--}}
    <div class="modal-dialog modal-simple modal-onboarding">
        <div class="modal-content">
            <div id="header-onboarding-1" class="modal-header flex-wrap">
                <div class="w-p100 text-center">
                    <img id="icon" src="{{ asset('modules/global/img/onboarding-presentation.png') }}" alt="Image">
                </div>
            </div>
            <div class="modal-body text-center">
                <div id="title-onboarding-1">Temos novidades pra você, Vitor!</div>
                <div id="description-onboarding-1">Você já deve ter percebido que o Sirius tem passado por diversas melhorias. Além de um novo visual, hoje queremos te apresentar algumas ferramentas e funcionalidades que acabaram de chegar por aqui.</div>
                <div id="onboarding-next-presentation" class="btn btn-primary">Vamos lá!</div>
            </div>
        </div>

            <div class="d-block">
                @include('dashboard::onboarding.gamification')
            </div>

            <div class="d-block">
                @include('dashboard::onboarding.account-health')
            </div>

        <div class="d-block">
            @include('dashboard::onboarding.news-summary')
        </div>
    </div>
{{--</div>--}}
