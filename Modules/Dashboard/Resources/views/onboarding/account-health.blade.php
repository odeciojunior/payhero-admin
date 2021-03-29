@push('css')
    <link rel="stylesheet" href="{{ asset('modules/dashboard/css/onboarding-details.css?v=01') }}">
@endpush
<div class="modal-content">
    <div class="modal-header flex-wrap">
        <div>
            <img id="icon" src="{{asset('/modules/global/adminremark/assets/images/siriusM.svg')}}" width="40"
                 alt="Image"> &#124; Novidades
        </div>
        <div class="pages">
            <span id="page-gamification"></span>
            <span id="page-account-health" class="active"></span>
            <span id="page-news-summary"></span>
        </div>
    </div>
    <div class="modal-body">
        <div class="row">

            <div class="col-12 col-sm-7 order-sm-1">
                <div id="title-onboarding-2" class="text-sm-left text-center">MAIS TRANSPARÊNCIA E CONTROLE</div>
                <div id="sub-title-onboarding-2" class="text-center text-sm-left">
                    Como anda a <span style="color: #2E85EC">saúde da sua conta?</span>
                </div>
                <div id="description-onboarding-2" class="text-center text-sm-left">
                    Apresentamos a Saúde da Conta: uma área para você acompanhar tudo sobre o seu negócio, de forma
                    ainda mais prática e eficiente.
                </div>

                <div class="row no-gutters align-items-center my-3">
                    <div class="col-12 col-sm-2 text-center text-sm-left d-flex justify-content-center">
                        <div class="icon-onboarding-2">
                            <img alt="Image" src="{{asset('modules/global/img/onboarding/statistic.svg')}}">
                        </div>
                    </div>
                    <span class="col-12 col-sm-10 text-center text-sm-left pl-sm-2">
                            O Saúde da Conta <b>já está disponível</b> em contas com mais de 100 vendas aprovadas.
                        </span>
                </div>

                <div class="row no-gutters align-items-center my-3">
                    <div class="col-12 col-sm-2 text-center text-sm-left d-flex justify-content-center">
                        <div class="icon-onboarding-2">
                            <img alt="Image" src="{{asset('modules/global/img/onboarding/pay.svg')}}">
                        </div>
                    </div>
                    <span class="col-12 col-sm-10 text-center text-sm-left pl-sm-2">
                            Serão avaliadas três métricas: <b>Chargebacks, Atendimentos e Códigos de Rastreio.</b>
                        </span>
                </div>

                <div class="row no-gutters align-items-center my-3">
                    <div class="col-12 col-sm-2 text-center text-sm-left d-flex justify-content-center">
                        <div class="icon-onboarding-2">
                            <img alt="Image" src="{{asset('modules/global/img/onboarding/arrow-right-down.svg')}}">
                        </div>
                    </div>
                    <span class="col-12 col-sm-10 text-center text-sm-left pl-sm-2">
                            Usuários com <b>pontuação baixa</b> poderão sofrer <b>sanções e bloqueios</b> na plataforma.
                        </span>
                </div>
            </div>

            <div class="col-12 col-sm-5 order-sm-0 d-flex justify-content-center mt-sm-5 mt-0">
                <img class="img-fluid" alt="Image"
                     src="{{asset('modules/global/img/onboarding/account-health@2x.png')}}">
            </div>
        </div>
    </div>
    <div class="modal-footer justify-content-center">
        <div id="onboarding-next-account-health" class="btn btn-primary">Continuar</div>
    </div>
</div>
