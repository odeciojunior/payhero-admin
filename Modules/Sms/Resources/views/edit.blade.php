<form id='editar_sms' method="post" action="#">
    @csrf
    <input type="hidden" name="id" value="{{Hashids::encode($sms->id)}}">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-12">
                        <label for="event">Evento</label>
                        <select name="event" type="text" class="form-control" id="event" required>
                            <option value="boleto_generated"{{$sms->event == 'boleto_generated' ? 'selected' : ''}}>Boleto gerado</option>
                            <option value="boleto_expiring" {{$sms->event == 'boleto_expiring' ? 'selected' : ''}} >Boleto no dia do vencimento</option>
                            <option value="boleto_expired" {{$sms->event == 'boleto_expired' ? 'selected' : ''}}>Boleto vencido</option>
                            <option value="refund" {{$sms->event == 'refund' ? 'selected' : ''}} >Reembolso</option>
                            <option value="sale_realized" {{$sms->event == 'sale_realized' ? 'selected' : ''}}>Venda realizada</option>
                            <option value="card_refused" {{$sms->event == 'card_refused' ? 'selected' : ''}} {!! ($sms->evento == 'card_refused') ? 'selected' : '' !!}>Cartão recusado</option>
                            <option value="checkout_abandoned" {{$sms->event == 'checkout_abandoned' ? 'selected' : ''}} {!! ($sms->evento == 'checkout_abandoned') ? 'selected' : '' !!}>Abandono do checkout</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <label>Tempo</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <input value="{{$sms->time}}" name="time" type="text" class="form-control" id="time" placeholder="Número">
                    </div>
                    <div class="col-9">
                        <select name="period" class="form-control" id="period" required>
                            <option value="minutes" {{$sms->period == 'minutes' ? 'selected' : ''}}>Minuto(s)</option>
                            <option value="hours" {{$sms->period == 'hours' ? 'selected' : ''}}>Hora(s)</option>
                            <option value="days" {{$sms->period == 'days' ? 'selected' : ''}}>Dia(s)</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status" required>
                            <option value="1" {{$sms->status? 'selected' : ''}}>Ativo</option>
                            <option value="0" {{!$sms->status? 'selected' : ''}}>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="message">Mensagem</label>
                        <textarea name="message" class="form-control" rows="5" id="message" maxlength="120" placeholder="mensagem">
                           {{$sms->message ?? 'Mensagem'}}
                        </textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="panel-body" style="border: 1px solid gray">
                        <i class="site-menu-icon wb-info-circle" aria-hidden="true"></i> Variáveis (ajuda)
                        <p style="margin-top: 20px">
                            {primeiro_nome} = Primeiro nome do cliente<br> {nome_completo} = Nome completo do cliente<br> {email} = Email do cliente<br> {url_checkout} = Link para o checkout do produto<br> {url_boleto} = Url com o boleto<br> {data_vencimento} = Data de vencimento do boleto<br> {linha_digitavel} = Linha digitável do boleto<br> {url_carrinho_abandonado} = Link do carrinho abandonado<br>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>



