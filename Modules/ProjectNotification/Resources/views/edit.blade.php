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
            <label for="code">Tempo</label>
            <input value="" name="time" type="text" class="form-control project-notification-time" placeholder="Tempo" disabled>
        </div>
        <div class="form-group col-xl-6">
            <label for="status">Status</label>
            <select name="status" class="form-control project-notification-status" required>
                <option value="1">Ativo</option>
                <option value="0">Inativo</option>
            </select>
        </div>
    </div>

    <div class="row project-notification-field-email">
        <div class="form-group col-xl-12">
            <label for="code">Assunto</label>
            <input value="" name="subject" type="text" class="form-control project-notification-subject" placeholder="Assunto">
        </div>
    </div>

    <div class="row  project-notification-field-email">
        <div class="form-group col-xl-12">
            <label for="code">Título</label>
            <input value="" name="title" type="text" class="form-control project-notification-title" placeholder="Título">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-12">
            <label for="name">Mensagem</label>
            <textarea id="txt-project-notification" name="message" type="text" class="form-control project-notification-message" rows="8"></textarea>
            <div id="customs" class="">
                <label for="name">Parâmetros</label><br>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{primeiro_nome}'>{primeiro_nome}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{url_boleto}'>{url_boleto}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{projeto_nome}'>{projeto_nome}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{link_carrinho_abandonado}'>{link_carrinho_abandonado}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{codigo_pedido}'>{codigo_pedido}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{nome_produto}'>{nome_produto}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{qtde_produto}'>{qtde_produto}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{valor_compra}'>{valor_compra}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{codigo_venda}'>{codigo_venda}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{codigo_rastreio}'>{codigo_rastreio}</a>
                <a class='inc-param btn btn-sm m-1 btn-link btn-outline btn-default' data-value='{link_rastreamento}'>{link_rastreamento}</a>
            </div>
        </div>
    </div>

</form>