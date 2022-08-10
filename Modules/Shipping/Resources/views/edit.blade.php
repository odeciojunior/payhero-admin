@push('css')
    <link rel="stylesheet"
          href="{{ mix('build/layouts/shipping/edit.min.css') }}">
@endpush

<form id="form-update-shipping"
      enctype="multipart/form-data">
    @method('PUT')
    <input type="hidden"
           class="shipping-id"
           value="">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="type">Tipo</label>
                <select name="type"
                        id="shipping-type"
                        class="sirius-select shipping-type">
                    <option value="static">Frete fixo (você define um valor fixo para o frete)</option>
                    <option value="pac">PAC (Calculado automaticamente pela API)</option>
                    <option value="sedex">SEDEX (Calculado automaticamente pela API)</option>
                </select>
            </div>
            <div class="form-group name-shipping-row">
                <label for="name">Descrição no checkout</label>
                <input name="name"
                       type="text"
                       class="input-pad shipping-description"
                       value=""
                       placeholder="PAC"
                       maxlength="60">
            </div>
            <div class="form-group information-shipping-row">
                <label for="information">Tempo de entrega apresentado</label>
                <input name="information"
                       type="text"
                       class="input-pad shipping-info"
                       value=""
                       placeholder="10 até 20 dias"
                       maxlength="100">
            </div>
            <div class="form-group zip-code-origin-shipping-row"
                 style="display: block">
                <label for="zip-code-origin">CEP de origem</label>
                <input name="zip_code_origin"
                       type="text"
                       class="input-pad shipping-zipcode"
                       data-mask="00000-000"
                       value=""
                       placeholder="12345-678">
            </div>
            <div class="value-shipping-row">
                <div class="switch-holder">
                    <label>Valor único para todas as regiões</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               checked
                               class="check shipping-regions-edit"
                               value="1">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="form-group"
                     id="shipping-single-value-edit">
                    <label for="value">Valor do Frete</label>
                    <input name="value"
                           type="text"
                           class="input-pad shipping-value shipping-money-format"
                           value=""
                           placeholder="R$ 0,00"
                           maxlength="8">
                    <span id="shipping-value-error"
                          class="text-danger"></span>
                </div>
                <div id="shipping-multiple-value-edit"
                     style="display:none">
                    <div class="row">
                        <div class="form-group col-6"
                             id="shipping-region-">
                            <label for="value">Valor para o Norte</label>
                            <input name="value1"
                                   type="text"
                                   class="input-pad shipping-value1-edit shipping-money-format"
                                   value=""
                                   placeholder="R$ 0,00"
                                   maxlength="8">
                            <span id="shipping-value-error"
                                  class="text-danger"></span>
                        </div>
                        <div class="form-group col-6"
                             id="shipping-region-">
                            <label for="value">Valor para o Nordeste</label>
                            <input name="value2"
                                   type="text"
                                   class="input-pad shipping-value2-edit shipping-money-format"
                                   value=""
                                   placeholder="R$ 0,00"
                                   maxlength="8">
                            <span id="shipping-value-error"
                                  class="text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-6"
                             id="shipping-region-">
                            <label for="value3">Valor para o Centro-Oeste</label>
                            <input name="value3"
                                   type="text"
                                   class="input-pad shipping-value3-edit shipping-money-format"
                                   value=""
                                   placeholder="R$ 0,00"
                                   maxlength="8">
                            <span id="shipping-value-error"
                                  class="text-danger"></span>
                        </div>
                        <div class="form-group col-6"
                             id="shipping-region-">
                            <label for="value">Valor para o Sudeste</label>
                            <input name="value4"
                                   type="text"
                                   class="input-pad shipping-value4-edit shipping-money-format"
                                   value=""
                                   placeholder="R$ 0,00"
                                   maxlength="8">
                            <span id="shipping-value-error"
                                  class="text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-6"
                             id="shipping-region-">
                            <label for="value5">Valor para o Sul</label>
                            <input name="value5"
                                   type="text"
                                   class="input-pad shipping-value5-edit shipping-money-format"
                                   value=""
                                   placeholder="R$ 0,00"
                                   maxlength="8">
                            <span id="shipping-value-error"
                                  class="text-danger"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Disponível para compras acima de: </label>
                <input name="rule_value"
                       type="text"
                       class="input-pad rule-shipping-value shipping-money-format"
                       value=""
                       placeholder="R$ 0,00">
            </div>
            <div class="d-flex">
                <div class="switch-holder w-full">
                    <label for="status">Ativo</label>
                    <br>
                    <label class="switch">
                        <input name="status"
                               value="1"
                               class="check shipping-status"
                               type="checkbox"
                               checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="switch-holder w-full">
                    <label for="status">Pré-selecionado</label>
                    <br>
                    <label class="switch">
                        <input name="pre_selected"
                               value="1"
                               class="check shipping-pre-selected"
                               type="checkbox">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="switch-holder w-full">
                    <label for="use_variants">Usar variantes</label>
                    <br>
                    <label class="switch">
                        <input name="use_variants"
                               value="1"
                               class="check shipping-use-variants"
                               type="checkbox"
                               checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="form-group shipping-plans-edit-container">
                <label for="shipping-plans-edit">Oferecer o frete para os planos: </label>
                <select name="apply_on_plans[]"
                        id="shipping-plans-edit"
                        class="form-control shipping-plans-edit"
                        style="width:100%"
                        data-plugin="select2"
                        multiple="multiple"> </select>
            </div>
            <div class="form-group shipping-not-apply-plans-edit-container">
                <label for="shipping-not-apply-plans-edit">Não oferecer o frete para os planos: </label>
                <select name="not_apply_on_plans[]"
                        id="shipping-not-apply-plans-edit"
                        class="form-control shipping-not-apply-plans-edit"
                        style="width:100%"
                        data-plugin="select2"
                        multiple="multiple"></select>
            </div>
        </div>
    </div>
</form>
