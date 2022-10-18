<form id='form_config_upsell'
      method="post"
      action="#">
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row">
            <div class='form-group col-12 mb-20'>
                <label for="header_config">Cabeçalho</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="form-control"
                           name="header"
                           id="header_config"
                           placeholder="Digite o cabeçalho"
                           maxlength='255'
                           required>
                </div>
            </div>
        </div>
        <div class="row mb-10">
            <div class='form-group col-md-4 div-countdown-time'>
                <label for="countdown_time_config">Contador (em minutos)</label>
                <div class="d-flex input-group">
                    <input type="number"
                           min="0"
                           class="form-control"
                           name="countdown_time_config"
                           id="countdown_time_config"
                           placeholder="Digite o tempo do contador em minutos"
                           maxlength='2'
                           data-mask="0#">
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="countdown_flag"
                       class='mb-10'>Habilitar Contador</label>
                <div class="switch-holder">
                    <label class="switch">
                        <input type="checkbox"
                               value='1'
                               name="countdown_flag"
                               id="countdown_flag"
                               class='check'
                               checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <a id="btn-preview-config"
                   class="btn btn-primary mt-md-20 text-center"
                   href="#"
                   target="_blank"
                   style="display: none">
                    <span class="o-eye-1 font-size-16 text-white mr-1"
                          style="-webkit-text-stroke: unset;"></span>
                    <span class="text-white">Visualizar</span>
                </a>
            </div>
        </div>
    </div>
</form>
