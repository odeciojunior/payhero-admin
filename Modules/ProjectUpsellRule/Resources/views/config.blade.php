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
                <textarea class='form-control' name="description" id="description_config" placeholder="Digite a descrição" rows='6'>
                </textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-6 mt-5">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Habilitar contagem:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="countdown_flag" id="countdown_flag" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class='form-group col-12 div-countdown-time mt-10' style='display:none;'>
                <label for="link">Contagem</label>
                <div class="d-flex input-group">
                    <input type="text" class="form-control" name="countdown_time" id="countdown_time" placeholder="Digite o tempo da contagem em minutos" maxlength='2' data-mask="0#">
                </div>
            </div>
        </div>
    </div>
</form>
