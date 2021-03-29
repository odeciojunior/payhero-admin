@push('css')
    <link rel="stylesheet" href="{{ asset('modules/dashboard/css/onboarding-details.css?v=01') }}">
@endpush

<div class="modal-content">
    <div class="modal-header flex-wrap">
        <div class="w-p100">
            <img id="icon" src="{{asset('/modules/global/adminremark/assets/images/siriusM.svg')}}" width="40"
                 alt="Image"> &#124; Novidades
        </div>
    </div>
    <div class="modal-body">
        <div class="row">
            <img id="icon" class="col-6" alt="Image"
                 src="{{asset('modules/global/img/onboarding/performance.png')}}">
            <div class="col-6">
                <div id="title-onboarding-2">MAIS TRANSPARÊNCIA E CONTROLE</div>
                <div id="sub-title-onboarding-2">Como anda a <span style="color: #2E85EC">saúde da sua conta?</span>
                </div>
                <div id="description-onboarding-2">
                    Apresentamos a Saúde da Conta: uma área para você acompanhar tudo sobre o seu negócio, de forma
                    ainda mais prática e eficiente.
                </div>

                <div class="row mb-2">
                    <div class="col-2 icon-onboarding-2 rounded-circle">
                        <img class="img-fluid m-50" alt="Image"
                             src="{{asset('modules/global/img/onboarding/statistic.svg')}}">
                    </div>
                    <span class="col-10">
                        O Saúde da Conta <b>já está disponível</b> em contas com mais de 100 vendas aprovadas.
                    </span>
                </div>

                <div class="row mb-2">
                    <img class="col-3 icon-onboarding-2" alt="Image"
                         src="{{asset('modules/global/img/onboarding/pay.svg')}}">
                    <span class="col-9">
                        Serão avaliadas três métricas: <b>Chargebacks, Atendimentos e Códigos de Rastreio.</b>
                    </span>
                </div>
                <div class="row mb-2">
                    <img class="col-3 icon-onboarding-2" alt="Image"
                         src="{{asset('modules/global/img/onboarding/arrow-right-down.svg')}}">
                    <span class="col-9">
                        Usuários com <b>pontuação baixa</b> poderão sofrer <b>sanções e bloqueios</b> na plataforma.
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer justify-content-center">
        <div id="onboarding-next-account-health" class="btn btn-primary">Continuar</div>
    </div>
</div>
