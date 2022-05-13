<div id="modal-refund-transaction" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-simple">
        <div class="modal-content p-10" id="modal-refund">
            <div class="modal-header simple-border-bottom mb-10">
                <h4 class="modal-title" id="modal-title">Estornar transação</h4>
                <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
                    <i class="material-icons md-16">close</i>
                </a>
            </div>
            <div class='my-20 mx-20 text-center'>
                <h3 class="black"> Você tem certeza? </h3>
                <p class="gray"> Após confirmada, essa operação não poderá ser desfeita!</p>
            </div>
            <div id="asaas_message" align="center"></div>
            <div class="row d-none">
                <div class="col-3"></div>
                <div class="col-3">
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="radioTotalRefund" name="radio-stacked" required checked>
                        <label class="custom-control-label" for="radioTotalRefund">Estorno total</label>
                    </div>
                </div>
                <div class="col-3">
                    <div class="custom-control custom-radio mb-3">
                        <input type="radio" class="custom-control-input" id="radioPartialRefund" name="radio-stacked" disabled>
                        <label class="custom-control-label" for="radioPartialRefund">Estorno Parcial</label>
                    </div>
                </div>
                <div class="col-3"></div>
            </div>
            <div class="text-center pt-20 d-none" style="min-height:62px;">
                <div class="value-partial-refund" style="display: none;">
                    <strong class="font-size-14">Valor a ser estornado: </strong> R$
                    <input type="text" name="refundAmount" id="refundAmount" style="width: 200px;" maxlength="9">
                </div>
            </div>
            <div class="form-group">
                <label for="refund_observation">Causa do estorno</label>
                <textarea class="form-control" id="refund_observation" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                    Fechar
                </a>
                <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-confirm-refund-transaction" total="" >
                    Estornar
                </button>
            </div>
        </div>
    </div>
</div>
