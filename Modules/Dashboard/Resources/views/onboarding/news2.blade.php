@push('css')
    <link rel="stylesheet" href="{{ asset('modules/dashboard/css/onboarding-details.css?v=01') }}">
@endpush
<div class="modal-dialog modal-simple modal-onboarding">
    <div class="modal-content">
        <div class="modal-header flex-wrap">
            <div class="w-p100">
                <img id="icon" src="{{asset('/modules/global/adminremark/assets/images/siriusM.svg')}}" width="40" alt="Image"> &#124; Novidades
            </div>
        </div>
        <div class="modal-body">
            <div class="row">
                <img id="icon" class="col-6" alt="Image"
                     src="{{asset('modules/global/img/onboarding-account-health.png')}}">
                <div class="col-6">
                    <div id="title-onboarding-2">UM GAME FEITO PRA GENTE GRANDE</div>
                    <div id="sub-title-onboarding-2">Agora você faz parte da tripulação <span style="color: #2E85EC">Sirius.</span></div>
                    <div id="description-onboarding-2">Você já deve ter percebido que o Sirius tem passado por diversas melhorias. Além de um novo visual, hoje queremos te apresentar algumas ferramentas e funcionalidades que acabaram de chegar por aqui.</div>

                    <div class="row">
                        <img class="col-3 icon-onboarding-2" alt="Image"
                             src="{{asset('modules/global/img/svg/bank-notes.svg')}}">
                        <span class="col-10">
                            São 6 níveis que sobem de acordo com o faturamento total de seus projetos.
                        </span>
                    </div>

                    <div class="row">
                        <img class="col-3 icon-onboarding-2" alt="Image"
                             src="{{asset('modules/global/img/svg/money-box.svg')}}">
                        <span class="col-10">
                            A cada nível alcançado, Sirius libera novos benefícios para sua conta.
                        </span>
                    </div>
                    <div class="row">
                        <img class="col-3 icon-onboarding-2" alt="Image"
                         src="{{asset('modules/global/img/svg/medal.svg')}}">
                        <span class="col-10">
                            Receba insígnias para cada nova conquista alcançada!
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer justify-content-center">
            <div class="btn btn-primary">Continuar</div>
        </div>
    </div>
</div>
