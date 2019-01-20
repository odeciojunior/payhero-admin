<div style="text-align: center">
    <h4>Adicionar mensagem</h4>
</div>
<div class="page-content container-fluid">
    <form id='cadastrar_sms' method="post" action="#">
        @csrf
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-12">
                        <label for="plano">Plano</label>
                        <select name="plano" type="text" class="form-control" id="plano_sms" required>
                            <option value="todos">Todos planos</option>
                            @foreach($planos as $plano)
                                <option value="{!! $plano['id'] !!}">{!! $plano['nome'] !!}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label for="evento">Evento</label>
                        <select name="evento" type="text" class="form-control" id="evento_sms" required>
                            <option value="boleto_vencendo">Boleto no dia do vencimento</option>
                            <option value="boleto_gerado">Boleto gerado</option>
                            <option value="boleto_vencido">Boleto vencido</option>
                            <option value="venda_realizada">Venda realizada</option>
                            <option value="reembolso">Reembolso</option>
                            <option value="cartao_recusado">Cartão recusado</option>
                            <option value="abandono_checkout">Abandono do checkout</option>
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
                        <input id="numero_tempo" name="tempo" type="text" class="form-control" id="tempo_sms" value="0" placeholder="0">
                    </div>

                    <div class="form-group col-9">
                        <select name="periodo" class="form-control" id="periodo_sms" required>
                            <option value="minutes">Minutos</option>
                            <option value="hours">Horas</option>
                            <option value="days">Dias</option>
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
                        <label for="mensagem">Mensagem</label>
                        <textarea name="mensagem" class="form-control" rows="5" id="mensagem_sms" maxlength="120" placeholder="mensagem"></textarea>
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
    </form>
</div>