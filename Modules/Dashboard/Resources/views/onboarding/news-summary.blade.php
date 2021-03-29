@push('css')
    <link rel="stylesheet" href="{{ asset('modules/dashboard/css/onboarding-details.css?v=01') }}">
@endpush

{{--<div class="modal fade modal-fade-in-scale-up show">--}}
    <div class="modal-dialog modal-simple modal-onboarding">
        <div class="modal-content">
            <div class="modal-header flex-wrap">
                <div class="w-p100">
                    <img id="icon" src="{{asset('/modules/global/adminremark/assets/images/siriusM.svg')}}" width="40"
                         alt="Image"> &#124; Novidades
                </div>
            </div>
            <div class="modal-body p-50">
                <div class="summary-title-primary">Talvez você ainda não saiba...</div>
                <div class="row">
                    <div class="col-6">
                        <div class="summary-title">CASHBACK <span class="summary-new">NOVO</span></div>
                        <div class="summary-description">O primeiro gateway de pagamentos a oferecer cashback em cada compra.</div>
                    </div>
                    <div class="col-6">
                        <div class="summary-title">ORDER-BUMP <span class="summary-new">NOVO</span></div>
                        <div class="summary-description">Além do upsell, agora você pode utilizar order-bump em nosso checkout.</div>
                    </div>

                    <div class="col-6">
                        <div class="summary-title">CONTESTAÇÕES <span class="summary-new">NOVO</span></div>
                        <div class="summary-description">Uma nova área para cuidar de perto as contestações e chargebacks.</div>
                    </div>
                    <div class="col-6">
                        <div class="summary-title">ATENDIMENTO</div>
                        <div class="summary-description">Nova classificação de atendimentos. Melhorias gerais no SAC das lojas.</div>
                    </div>

                    <div class="col-6">
                        <div class="summary-title">CHECKOUT 2.0 <span class="summary-soon">EM BREVE</span></div>
                        <div class="summary-description">O novo checkout do Sirius para aumentar ainda mais a sua conversão.</div>
                    </div>
                    <div class="col-6">
                        <div class="summary-title">APP SIRIUS <span class="summary-soon">EM BREVE</span></div>
                        <div class="summary-description">Um app prático, rápido e fácil para consultar na tela do seu celular.</div>
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <div id="onboarding-finish" class="btn btn-primary">Finalizar</div>
                </div>
            </div>
        </div>
    </div>
{{--</div>--}}