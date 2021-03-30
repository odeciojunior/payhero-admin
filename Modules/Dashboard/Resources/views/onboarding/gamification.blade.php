@push('css')
    <link rel="stylesheet" href="{{ asset('modules/dashboard/css/onboarding-details.css?v=01') }}">
@endpush

<div class="modal-content">
    <div class="modal-header flex-wrap">
        <div id="title-news-onboarding">
            <img src="{{asset('/modules/global/adminremark/assets/images/siriusM.svg')}}" width="40"
                 alt="Image"> <span>Novidades</span>
        </div>
        <div class="pages">
            <span id="page-gamification" class="active"></span>
            <span id="page-account-health"></span>
            <span id="page-news-summary"></span>
        </div>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-12 col-sm-6 order-sm-1">
                <div id="title-onboarding-2">UM GAME FEITO PRA GENTE GRANDE</div>
                <div id="sub-title-onboarding-2">Agora você faz parte da tripulação <span style="color: #2E85EC">Sirius.</span></div>
                <div id="description-onboarding-2">Você já deve ter percebido que o Sirius tem passado por diversas melhorias. Além de um novo visual, hoje queremos te apresentar algumas ferramentas e funcionalidades que acabaram de chegar por aqui.</div>

                <div class="row no-gutters align-items-center">
                    <div class="col-12 col-sm-2 text-center text-sm-left d-flex justify-content-center">
                        <div class="icon-onboarding-2 text-center">
                            <img alt="Image" src="{{asset('modules/global/img/svg/bank-notes.svg')}}">
                        </div>
                    </div>
                    <span class="col-12 col-sm-10 text-center text-sm-left pl-sm-2 mt-4">
                        São 6 níveis que sobem de acordo com o faturamento total de seus projetos.
                    </span>
                </div>

                <div class="row no-gutters align-items-center">
                    <div class="col-12 col-sm-2 text-center text-sm-left d-flex justify-content-center">
                        <div class="icon-onboarding-2 text-center">
                            <img alt="Image" src="{{asset('modules/global/img/svg/money-box.svg')}}">
                        </div>
                    </div>
                    <span class="col-12 col-sm-10 text-center text-sm-left pl-sm-2 mt-4">
                        A cada nível alcançado, Sirius libera novos benefícios para sua conta.
                    </span>
                </div>
                <div class="row no-gutters align-items-center">
                    <div class="col-12 col-sm-2 text-center text-sm-left d-flex justify-content-center">
                        <div class="icon-onboarding-2 text-center">
                            <img alt="Image" src="{{asset('modules/global/img/svg/medal.svg')}}">
                        </div>
                    </div>
                    <span class="col-12 col-sm-10 text-center text-sm-left pl-sm-2 mt-4">
                        Receba insígnias para cada nova conquista alcançada!
                    </span>
                </div>
            </div>

            <div class="col-12 col-sm-5 order-sm-0 d-flex justify-content-center">
                <img class="img-fluid" alt="Image" src="{{asset('modules/global/img/onboarding/performance@2x.png')}}">
            </div>
        </div>
    </div>
    <div class="modal-footer justify-content-center">
        <div id="onboarding-next-gamification" class="btn btn-primary">Continuar</div>
    </div>
</div>
