<form id='form_config_upsell' method="post" action="#">
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row">
            <div class='form-group col-12 mb-20'>
                <label for="link">Cabeçalho</label>
                <div class="d-flex input-group">
                    <input type="text" class="form-control" name="header" id="header_config" placeholder="Digite o cabeçalho">
                </div>
            </div>
            <div class='form-group col-12 mb-20'>
                <label for="link">Título</label>
                <div class="d-flex input-group">
                    <input type="text" class="form-control" name="title" id="title_config" placeholder="Digite o título">
                </div>
            </div>
            <div class='form-group col-12 mb-20'>
                <label for="link">Descrição</label>
                <textarea class='form-control' name="description" id="description_config" placeholder="Digite a descrição">
                </textarea>
            </div>
        </div>
        <div class="row mb-10">
            <div class='form-group col-md-4 div-countdown-time'>
                <label for="link">Contador (em minutos)</label>
                <div class="d-flex input-group">
                    <input type="number" min="0" class="form-control" name="countdown_time" id="countdown_time" placeholder="Digite o tempo do contador em minutos" maxlength='2' data-mask="0#">
                </div>
            </div>
            <div class="form-group col-md-4">
                <label for="token" class='mb-10'>Habilitar Contador</label>
                <div class="switch-holder pt-2">
                    <label class="switch">
                        <input type="checkbox" value='1' name="countdown_flag" id="countdown_flag" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <button class='btn btn-primary btn-sm btn-view-config mt-md-20' style='display:none;'>
                    <i class="material-icons">remove_red_eye</i>
                    Visualizar
                </button>
            </div>
        </div>
    </div>
</form>
