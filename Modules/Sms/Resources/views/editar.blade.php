<div style="text-align: center">
    <h4>Editar mensagem</h4>
</div>
<form id='editar_sms' method="post" action="#">
    @csrf
    <input type="hidden" name="id" value="{!! $sms->id !!}">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
 
                <div class="row">
                    <div class="form-group col-12">
                        <label for="plano">Plano</label>
                        <select name="plano" type="text" class="form-control" id="plano" required>
                            <option value="todos">Todos planos</option>
                            @foreach($planos as $plano)
                                <option value="{!! $plano['id'] !!}" {!! $sms->plano == $plano['id'] ? 'selected' : '' !!}>{!! $plano['nome'] !!}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                        
                <div class="row">
                    <div class="form-group col-12">
                        <label for="evento">Evento</label>
                        <select name="evento" type="text" class="form-control" id="evento" required>
                            <option value="boleto_gerado" {!! ($sms->evento == 'boleto_gerado') ? 'selected' : '' !!}>Boleto gerado</option>
                            <option value="boleto_vencendo" {!! ($sms->evento == 'boleto_vencendo') ? 'selected' : '' !!}>Boleto no dia do vencimento</option>
                            <option value="boleto_vencido" {!! ($sms->evento == 'boleto_vencido') ? 'selected' : '' !!}>Boleto vencido</option>
                            <option value="reembolso" {!! ($sms->evento == 'reembolso') ? 'selected' : '' !!}>Reembolso</option>
                            <option value="venda_realizada" {!! ($sms->evento == 'venda_realizada') ? 'selected' : '' !!}>Venda realizada</option>
                            <option value="cartao_recusado" {!! ($sms->evento == 'cartao_recusado') ? 'selected' : '' !!}>Cartão recusado</option>
                            <option value="abandono_checkout" {!! ($sms->evento == 'abandono_checkout') ? 'selected' : '' !!}>Abandono do checkout</option>
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
                        <input value="{!! $sms->tempo != '' ? $sms->tempo : '' !!}" name="tempo" type="text" class="form-control" id="tempo_sms_editar" placeholder="Número">
                    </div>

                    <div class="col-9">
                        <select name="periodo" class="form-control" id="periodo" required>
                            <option value="minutes" {!! ($sms->periodo == 'minutos') ? 'selected' : '' !!}>Minutos</option>
                            <option value="hours" {!! ($sms->periodo == 'horas') ? 'selected' : '' !!}>Horas</option>
                            <option value="days" {!! ($sms->periodo == 'dias') ? 'selected' : '' !!}>Dias</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status" required>
                            <option value="1" {!! $sms->status ? 'selected' : '' !!}>Ativo</option>
                            <option value="0" {!! !$sms->status ? 'selected' : '' !!}>Inativo</option>
                        </select>
                    </div>
                </div>
    
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="mensagem">Mensagem</label>
                        <textarea name="mensagem" class="form-control" rows="5" id="mensagem" maxlength="120" placeholder="mensagem">
                            {!! $sms->mensagem != '' ? $sms->mensagem : '' !!}
                        </textarea>
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
                        </p>
                    </div>
                </div>
            </div>
    
        </div>
    </div>
</form>



