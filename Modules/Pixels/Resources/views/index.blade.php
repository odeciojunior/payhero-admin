<style>
    .tooltip-inner {
        background: #f5f7f8;
    }

    .logo-pixels:hover {
        padding: 5px;
        border-radius: 50px;
        border: 2px solid #2DA6F6;
        -webkit-transition: all 0.3s;
        -moz-transition: all 0.3s;
        -ms-transition: all 0.3s;
        -o-transition: all 0.3s;
        transition: all 0.3s;
    }

    .font-text {
        font: normal normal normal 16px Muli;
    }
</style>

<div class='row no-gutters mb-10'>
    <div style='position:absolute; width:50%' class="d-flex align-items-center">
        <a class="rounded-info btn ml-8 d-flex justify-content-center align-items-center btn-default btn-outline"
           data-toggle="modal" data-target="#modal-info-pixel" style="border-color: #76838f;">
            <span class="o-info-1" style="font-size: 24px;"></span>
        </a>
        <span class="link-button-dependent blue-50 pointer" data-toggle="modal" data-target="#modal-info-pixel"
              style='margin-left:5px'>Como configurar os eventos pixel?</span>
    </div>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        <div class="d-flex align-items-center justify-content-end">
            <div id="add-pixel" class="d-flex align-items-center justify-content-end pointer" data-toggle="modal"
                 data-target="#modal-create-pixel">
                <span class="link-button-dependent red"> Adicionar Pixel </span>
                <a class="ml-10 rounded-add pointer"><i class="o-add-1" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='table-pixel' class='table text-left table-pixels table-striped unify' style='width:100%'>
                <thead>
                <tr>
                    <td class='table-title'>Nome</td>
                    <td class='table-title'>Código</td>
                    <td class='table-title'>Plataforma</td>
                    <td class='table-title'>Status</td>
                    <td class='table-title options-column-width text-center'>Opções</td>
                </tr>
                </thead>
                <tbody id='data-table-pixel' class='min-row-height'>
                {{-- js carregando dados --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
<ul id="pagination-pixels" class="pagination-sm margin-chat-pagination"
    style="margin-top:10px;position:relative;float:right">
    {{-- js carrega... --}}
</ul>

<!-- Create -->
<div id="modal-create-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        @include('pixels::create')
    </div>
</div>

<!-- Create -->
{{--<div id="modal-create-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">--}}
{{--    <div class="modal-dialog modal-dialog-centered modal-simple">--}}
{{--        <div class="modal-content p-10">--}}
{{--            <div class="modal-header simple-border-bottom mb-10">--}}
{{--                <h4 class="modal-title" id="modal-title">Novo pixel</h4>--}}
{{--                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal"--}}
{{--                   aria-label="Close">--}}
{{--                    <i class="material-icons md-16">close</i>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div style='min-height: 100px'>--}}
{{--                @include('pixels::create')--}}
{{--            </div>--}}
{{--            <div class="modal-footer">--}}
{{--                <a id="btn-mobile-modal-close"--}}
{{--                   class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none"--}}
{{--                   style='color:white' role="button" data-dismiss="modal" aria-label="Close">--}}
{{--                    Fechar--}}
{{--                </a>--}}
{{--                <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-save" data-dismiss="modal">--}}
{{--                    <i class="material-icons btn-fix"> save </i> Salvar--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

<!-- Edit -->
<div id="modal-edit-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Editar pixel</h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal"
                   aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('pixels::edit')
            </div>
            <div class="modal-footer">
                <a id="btn-mobile-modal-close"
                   class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none"
                   style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-update">
                    <i class="material-icons btn-fix"> save </i> Atualizar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Details -->
<div id="modal-detail-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Detalhes do pixel</h4>
                <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal"
                   aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body" style='min-height: 100px'>
                @include('pixels::show')
            </div>
        </div>
    </div>
</div>

<!-- Delete -->
<div id="modal-delete-pixel" class="modal fade example-modal-lg modal-3d-flip-vertical" aria-hidden="true" role="dialog"
     tabindex="-1">
    <div class="modal-dialog  modal-dialog-centered  modal-simple">
        <div class="modal-content">
            <div class="modal-header text-center">
                <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close"
                   id="fechar_modal_excluir">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class="modal-body text-center p-20">
                <div class="d-flex justify-content-center">
                    <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                </div>
                <h3 class="black"> Você tem certeza? </h3>
                <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
            </div>
            <div class="modal-footer d-flex align-items-center justify-content-center">
                <button type="button"
                        class="col-4 btn border-0 btn-gray btn-cancel-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        data-dismiss="modal" style="width: 20%;">
                    <b>Cancelar</b>
                </button>
                <button pixel="" type="button"
                        class="col-4 btn border-0 btn-outline btn-delete btn-delete-modal form-control d-flex justify-content-center align-items-center align-self-center flex-row"
                        data-dismiss="modal" style="width: 20%;">
                    <b class="mr-2">Excluir </b>
                    <span class="o-bin-1"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<!--Modal Informações de pixels-->
<div class="modal fade modal-3d-flip-vertical" id="modal-info-pixel" aria-hidden='true'
     aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="panel-group panel-group-continuous m-0" id="exampleAccrodion1" aria-multiselectable="true"
                 role="tablist">
                <!-- Facebook -->
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingFirst" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse"
                           href="#facebook-details" aria-controls="facebook-details" aria-expanded="false">
                            <strong>Facebook</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="facebook-details" aria-labelledby="exampleHeadingFirst"
                         role="tabpanel" style="">
                        <div class="panel-body">
                            Nós enviamos os seguintes eventos:
                            <ul>
                                <li>InitiateCheckout</li>
                                <li>BasicDataComplete</li>
                                <li>DeliveryComplete</li>
                                <li>AddPaymentInfo</li>
                                <li>addCouponDiscount</li>
                                <li>InitiateUpsell</li>
                                <li>Purchase</li>
                                <li>CardPurchase</li>
                                <li>BoletoPurchase</li>
                                <li>UpsellPurchase</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Google Analytics -->
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingSecond" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse"
                           href="#google-analytics-details" aria-controls="google-analytics-details"
                           aria-expanded="false">
                            <strong>Google Analytics</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="google-analytics-details"
                         aria-labelledby="exampleHeadingSecond" role="tabpanel" style="">
                        <div class="panel-body">
                            Nós enviamos os seguintes eventos:
                            <ul>
                                <li>begin_checkout</li>
                                <li>basic_data_complete</li>
                                <li>delivery_complete</li>
                                <li>add_payment_info</li>
                                <li>add_coupon_discount</li>
                                <li>initiate_upsell</li>
                                <li>purchase</li>
                                <li>boleto_purchase</li>
                                <li>card_purchase</li>
                                <li>upsell_purchase</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Google Analytics 4.0 -->
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingThird" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse"
                           href="#google-analytics-four-details" aria-controls="google-analytics-four-details"
                           aria-expanded="false">
                            <strong>Google Analytics 4.0</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="google-analytics-four-details"
                         aria-labelledby="exampleHeadingThird"
                         role="tabpanel" style="">
                        <div class="panel-body">
                            Para os eventos do pixel funcionarem normalmente você precisa cadastrar os seguintes eventos
                            no seu <strong>google analytics 4.0</strong>:
                            <ul>
                                <li>begin_checkout</li>
                                <li>basic_data_complete</li>
                                <li>delivery_complete</li>
                                <li>add_payment_info</li>
                                <li>add_coupon_discount</li>
                                <li>initiate_upsell</li>
                                <li>purchase</li>
                                <li>boleto_purchase</li>
                                <li>card_purchase</li>
                                <li>upsell_purchase</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Google Adwords -->
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingFourth" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse"
                           href="#googles-adwords" aria-controls="googles-adwords" aria-expanded="false">
                            <strong>Google Adwords</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="googles-adwords"
                         aria-labelledby="exampleHeadingFourth" role="tabpanel" style="">
                        <div class="panel-body">
                            Nós enviamos os seguintes eventos:
                            <ul>
                                <li>conversion</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Facebook -->
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingFifth" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse"
                           href="#taboola-details" aria-controls="taboola-details" aria-expanded="false">
                            <strong>Taboola</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="taboola-details" aria-labelledby="exampleHeadingFifth"
                         role="tabpanel" style="">
                        <div class="panel-body">
                            Para os eventos do pixel funcionarem normalmente, você deve cadastrar os seguintes eventos
                            no Taboola:
                            <ul>
                                <li>start_checkout</li>
                                <li>basic_data_complete</li>
                                <li>delivery_complete</li>
                                <li>add_payment_info</li>
                                <li>initiate_upsell</li>
                                <li>purchase</li>
                                <li>upsell_purchase</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Outbrain -->
                <div class="panel">
                    <div class="panel-heading" id="exampleHeadingSixth" role="tab">
                        <a class="panel-title collapsed" data-parent="#exampleAccrodion1" data-toggle="collapse"
                           href="#outbrain-details" aria-controls="outbrain-details" aria-expanded="false">
                            <strong>Outbrain</strong>
                        </a>
                    </div>
                    <div class="panel-collapse collapse" id="outbrain-details" aria-labelledby="exampleHeadingSixth"
                         role="tabpanel" style="">
                        <div class="panel-body">
                            Para os eventos do pixel funcionarem normalmente, você deve cadastrar os seguintes eventos
                            no Outbrain:
                            <ul>
                                <li>Categoria Purchase - nome <strong>Purchase</strong></li>
                                <li>Categoria Checkout - nome <strong>Checkout</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Modal Informações de pixels-->
