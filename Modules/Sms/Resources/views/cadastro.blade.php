<div style="text-align: center">
    <h4>Adicionar mensagem</h4>
</div>
<div class="page-content container-fluid">
    <form id='cadastrar_sms' method="post" action="#">
        @csrf
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                {{--<div class="row">
                    <div class="form-group col-12">
                        <label for="plano">Plano</label>
                        <select name="plan" type="text" class="form-control" id="plano_sms" required>
                            <option value="all">Todos planos</option>
                            @foreach($plans as $plan)
                                <option value="{!! $plan['id'] !!}">{!! $plan['name'] !!}</option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                <div class="row">
                    <div class="form-group col-12">
                        <label for="event">Evento</label>
                        <select name="event" type="text" class="form-control" id="evento_sms" required>
                            <option value="boleto_expiring">Boleto no dia do vencimento</option>
                            <option value="boleto_generated">Boleto gerado</option>
                            <option value="boleto_expired">Boleto vencido</option>
                            <option value="sale_realized">Venda realizada</option>
                            <option value="refung">Reembolso</option>
                            <option value="card_refused">Cartão recusado</option>
                            <option value="checkout_abandoned">Abandono do checkout</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label>Tempo</label>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-3">
                        <input name="time" type="text" class="form-control" id="tempo_sms_cadastrar" value="0" placeholder="0">
                    </div>

                    <div class="form-group col-9">
                        <select name="period" class="form-control" id="periodo_sms" required>
                            <option value="minutes">Minuto(s)</option>
                            <option value="hours">Hora(s)</option>
                            <option value="days">Dia(s)</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-12">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status_sms" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>
    
                <div class="row">
                    <div class="form-group col-12">
                        <label for="message">Mensagem</label>
                        <textarea name="message" class="form-control" rows="5" id="mensagem_sms" maxlength="120" placeholder="mensagem"></textarea>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-12">
                    <div class="panel-body" style="border: 1px solid gray">
                        <i class="site-menu-icon wb-info-circle" aria-hidden="true"></i>  Variáveis (ajuda)
                        <p style="margin-top: 20px">
                            {primeiro_nome} = Primeiro nome do cliente<br>
                            {nome_completo} = Nome completo do cliente<br>
                            {email} = Email do cliente<br>
                            {url_checkout} = Link para o checkout do produto<br>
                            {url_boleto} = Url com o boleto<br>
                            {data_vencimento} = Data de vencimento do boleto<br>
                            {linha_digitavel} = Linha digitável do boleto<br>
                            {url_carrinho_abandonado} = Link do carrinho abandonado<br>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


