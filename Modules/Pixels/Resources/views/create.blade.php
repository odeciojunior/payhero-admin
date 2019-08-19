<form id='form-register-pixel' method="post" action="/pixels">
    @csrf
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-12 mt-4">
                        <label for="name">Descrição</label>
                        <input name="name" type="text" class="form-control" id="name" placeholder="Descrição" maxlength='30'>
                    </div>
                    <div class="form-group col-6">
                        <label for="platform">Plataforma</label>
                        <select name="platform" type="text" class="form-control" id="platform">
                            <option value="facebook">Facebook</option>
                            <option value="google">Google</option>
                            <option value="null" disabled='disabled'>Taboola (em breve)</option>
                            <option value="null" disabled='disabled'>Outbrain (em breve)</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status_pixel">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="code">Código</label>
                        <input name="code" type="text" class="form-control" id="code" placeholder="Código" maxlength='30'>
                    </div>
                </div>
                <div class='mb-1'>
                    <label>Rodar Pixel:</label>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <div class="switch-holder">
                            <label for="checkout" class='mb-10'>Checkout:</label>
                            <br>
                            <label class="switch">
                                <input type="checkbox" value="" name='checkout' id='checkout' class='check' checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="switch-holder">
                            <label for="cartao">Purchase (cartão):</label>
                            <br>
                            <label class='switch'>
                                <input type="checkbox" value="" name='purchase_card' id='purchase_card' class='check' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="switch-holder">
                            <label for="boleto">Purchase (boleto):</label>
                            <br>
                            <label class='switch'>
                                <input type="checkbox" value="" name='purchase_boleto' id='purchase_boleto' class='check' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                    </div>
                </div>
                {{--<div class="row">--}}
                {{--<div class="form-group col-12">--}}
                {{--<label for="checkout">Rodar pixel no checkout</label>--}}
                {{--<select name="checkout" class="form-control" id="checkout">--}}
                {{--<option value="1">Sim</option>--}}
                {{--<option value="0">Não</option>--}}
                {{--</select>--}}
                {{--</div>--}}
                {{--<div class="form-group col-12">--}}
                {{--<label for="purchase_card">Rodar pixel no purchase (cartão)</label>--}}
                {{--<select name="purchase_card" class="form-control" id="purchase_card">--}}
                {{--<option value="1">Sim</option>--}}
                {{--<option value="0">Não</option>--}}
                {{--</select>--}}
                {{--</div>--}}
                {{--<div class="form-group col-12">--}}
                {{--<label for="purchase_boleto">Rodar pixel no purchase (boleto)</label>--}}
                {{--<select name="purchase_boleto" class="form-control" id="purchase_boleto">--}}
                {{--<option value="1">Sim</option>--}}
                {{--<option value="0">Não</option>--}}
                {{--</select>--}}
                {{--</div>--}}
                {{--</div>--}}
            </div>
        </div>
    </div>
</form>
