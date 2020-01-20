<form id="form-update-project-notification" method="PUT">
    @csrf
    @method('PUT')
    <input type="hidden" value="" class="project-notification-id" name="project-notification-id">

    <div class="row">
        <div class="form-group col-xl-6">
            <label for="type">Tipo</label>
            <select name="type" class="form-control project-notification-type" disabled>
                <option value="1">Email</option>
                <option value="2">SMS</option>
            </select>
        </div>
        <div class="form-group col-xl-6">
            <label for="value">Evento</label>
            <select name="event" class="form-control project-notification-event" disabled>
                <option value="1">Boleto gerado</option>
                <option value="2">Boleto compensado</option>
                <option value="3">Compra no cartão</option>
                <option value="4">Carrinho abandonado</option>
                <option value="5">Boleto vencendo</option>
                <option value="6">Código de Rastreio</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label for="code">tempo</label>
            <input value="" name="time" type="text" class="form-control project-notification-time" placeholder="Time" disabled>
        </div>
        <div class="form-group col-xl-6">
            <label for="status">Status</label>
            <select name="status" class="form-control project-notification-status" required>
                <option value="1">Ativo</option>
                <option value="0">Inativo</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-12">
            <label for="name">Mensagem</label>
            <textarea name="message" type="text" class="form-control project-notification-message"></textarea>
        </div>
    </div>

</form>