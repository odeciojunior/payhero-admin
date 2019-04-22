<div style="text-align: center">
    <h4>Adicionar mensagem</h4>
</div>
<form id='cadastrar_sms' method="post" action="#">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-12">
                        <label for="evento">Evento</label>
                        <select name="evento" type="text" class="form-control" id="evento" required>
                            <option value="">Selecione</option>
                            <option value="boleto_gerado">Boleto gerado</option>
                            <option value="boleto_vencendo">Boleto no dia do vencimento</option>
                            <option value="boleto_vencido">Boleto vencido</option>
                            <option value="venda_realizada">Venda realizada</option>
                            <option value="reembolso">Reembolso</option>
                            <option value="venda_realizada">Venda realizada</option>
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
                    <div class="form-group col-xl-3">
                        <input name="tempo" type="text" class="form-control" id="tempo" placeholder="Número">
                    </div>

                    <div class="form-group col-xl-9">
                        <select name="periodo" class="form-control" id="periodo" required>
                            <option value="" selected>Selecione</option>
                            <option value="minutos">Minutos</option>
                            <option value="horas">Horas</option>
                            <option value="dias">Dias</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="mensagem">Mensagem</label>
                        <textarea name="mensagem" class="form-control" rows="5" id="mensagem" placeholder="mensagem"></textarea>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>

