<form id="form-update-pixel" method="post" action="/pixels">
    @csrf
    <input type="hidden" value="{{Hashids::encode($pixel->id)}}" name="id" id='pixelId'>
    <div class="row">
        <div class="form-group col-xl-12 mt-4">
            <label for="name">Descrição</label>
            <input value="{{$pixel->name != '' ? $pixel->name : ''}}" name="name" type="text" class="input-pad" id="name_pixel" placeholder="Descrição" maxlength='30'>
        </div>
        <div class="form-group col-6">
            <label for="platform">Plataforma</label>
            <select name="platform" type="text" class="form-control select-pad" id="platform">
                <option value="facebook" {{ ($pixel->platform == 'facebook') ? 'selected' : '' }}>Facebook</option>
                <option value="google" {{ ($pixel->platform == 'google') ? 'selected' : '' }}>Google</option>
                <option value="null" {{--{{ ($pixel->platform == 'taboola') ? 'selected' : '' }}--}} disabled='disabled'>Taboola (em breve)</option>
                <option value="null" {{--{{ ($pixel->platform == 'outbrain') ? 'selected' : '' }}--}} disabled='disabled'>Outbrain (em breve)</option>
            </select>
        </div>
        <div class="form-group col-6">
            <label for="status">Status</label>
            <select name="status" type="text" class="form-control select-pad" id="status">
                <option value="1" {{ ($pixel->status == '1') ? 'selected' : '' }}>Ativo</option>
                <option value="0" {{ ($pixel->status == '0') ? 'selected' : '' }}>Desativado</option>
            </select>
        </div>
        <div class="form-group col-xl-12">
            <label for="code">Código</label>
            <input value="{{$pixel->code != '' ? $pixel->code : ''}}" name="code" type="text" class="input-pad" id="code" placeholder="Código" maxlength='30'>
        </div>
    </div>
    {{--<div class="row">--}}
    {{--<div class="form-group col-xl-12">--}}
    {{--<label for="platform">Plataforma</label>--}}
    {{--<select name="platform" type="text" class="form-control select-pad" id="platform">--}}
    {{--<option value="facebook" {{ ($pixel->platform == 'facebook') ? 'selected' : '' }}>Facebook</option>--}}
    {{--<option value="google" {{ ($pixel->platform == 'google') ? 'selected' : '' }}>Google</option>--}}
    {{--<option value="taboola" {{ ($pixel->platform == 'taboola') ? 'selected' : '' }}>Taboola</option>--}}
    {{--<option value="outbrain" {{ ($pixel->platform == 'outbrain') ? 'selected' : '' }}>Outbrain--}}
    {{--</option>--}}
    {{--</select>--}}
    {{--</div>--}}
    {{--<div class="form-group col-xl-12">--}}
    {{--<label for="status">Status</label>--}}
    {{--<select name="status" type="text" class="form-control select-pad" id="status">--}}
    {{--<option value="1" {{ ($pixel->status == '1') ? 'selected' : '' }}>Ativo</option>--}}
    {{--<option value="0" {{ ($pixel->status == '0') ? 'selected' : '' }}>Inativo</option>--}}
    {{--</select>--}}
    {{--</div>--}}
    {{--</div>--}}
    <div class='mb-1'>
        <label>Rodar Pixel:</label>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="switch-holder">
                <label for="Checkout">Checkout:</label>
                <br>
                <label class='switch'>
                    <input type="checkbox" @if($pixel->checkout == '1') value="1" checked="" @else value="0" @endif name='checkout' id='checkout' class='check'>
                    <span class='slider round'></span>
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="switch-holder">
                <label for="cartao">Purchase (cartão):</label>
                <br>
                <label class='switch'>
                    <input type="checkbox" @if($pixel->purchase_card == '1') value="1" checked="" @else value="0" @endif name='purchase_card' id='purchase_card' class='check'>
                    <span class='slider round'></span>
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="switch-holder">
                <label for="boleto">Purchase (boleto):</label>
                <br>
                <label class='switch'>
                    <input type="checkbox" @if($pixel->purchase_boleto == '1') value="1" checked="" @else value="0" @endif name='purchase_boleto' id='purchase_boleto' class='check'>
                    <span class='slider round'></span>
                </label>
            </div>
        </div>
    </div>
    {{--<div class="row">--}}
    {{--<div class="form-group col-12">--}}
    {{--<label for="checkout">Rodar pixel no checkout</label>--}}
    {{--<select name="checkout" class="form-control select-pad" id="checkout">--}}
    {{--<option value="1" {{ ($pixel->checkout == '1') ? 'selected' : '' }}>Sim</option>--}}
    {{--<option value="0" {{ ($pixel->checkout == '0') ? 'selected' : '' }}>Não</option>--}}
    {{--</select>--}}
    {{--</div>--}}
    {{--<div class="form-group col-12">--}}
    {{--<label for="purchase_card">Rodar pixel no purchase (cartão)</label>--}}
    {{--<select name="purchase_card" class="form-control select-pad" id="purchase_card">--}}
    {{--<option value="1" {{ ($pixel->purchase_card == '1') ? 'selected' : '' }}>Sim</option>--}}
    {{--<option value="0" {{ ($pixel->purchase_card == '0') ? 'selected' : '' }}>Não</option>--}}
    {{--</select>--}}
    {{--</div>--}}
    {{--<div class="form-group col-12">--}}
    {{--<label for="purchase_boleto">Rodar pixel no purchase (boleto)</label>--}}
    {{--<select name="purchase_boleto" class="form-control select-pad" id="purchase_boleto">--}}
    {{--<option value="1" {{ ($pixel->purchase_boleto == '1') ? 'selected' : '' }}>Sim</option>--}}
    {{--<option value="0" {{ ($pixel->purchase_boleto == '0') ? 'selected' : '' }}>Não</option>--}}
    {{--</select>--}}
    {{--</div>--}}
    {{--</div>--}}
    </div>
</form>


