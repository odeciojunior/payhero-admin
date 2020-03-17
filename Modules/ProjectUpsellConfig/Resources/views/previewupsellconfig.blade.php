<head>
    <link rel="stylesheet" href="{{ asset('modules/global/css/upsell.css') }}">
</head>
<div class="row justify-content-center">
    <div class="col pb-5">
        <div class="topbar upsell-config-header">
            {{--            {{ $upsellData['header'] }}--}}
        </div>
        <div class="message">
            <div class="container upsell-config-discription">
                {{--                <h1 class="title">{{ $upsellData['title'] }}</h1>--}}
                {{--                <p>{!! $upsellData['description'] !!}</p>--}}
            </div>
        </div>
        <div class="timer upsell-config-timer" style='display:none;'>
            <span class="timer-title">Por apenas um Tempo Limitado:</span>
            <div class="d-flex justify-content-center">
                <div class="d-flex flex-column">
                    <span class="digit" id="minutes">--</span>
                    <span class="timer-text">Minutos</span>
                </div>
                <span class="digit separator">:</span>
                <div class="d-flex flex-column">
                    <span class="digit" id="seconds">--</span>
                    <span class="timer-text">Segundos</span>
                </div>
            </div>
        </div>
        <div class='div-upsell-products'>

        </div>
{{--        @foreach($upsellData['plans'] as $key => $plan)--}}
{{--            <div class="product-info">--}}
{{--                <div class="d-flex flex-column">--}}
{{--                    @foreach($plan->products as $product)--}}
{{--                        <div class="product-row">--}}
{{--                            <img src="{{$product->photo}}" class="product-img">--}}
{{--                            <h3>{{$product->amount}}x {{$product->name}}</h3>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--                <div class="d-flex flex-column mt-4 mt-md-0">--}}
{{--                    <input type="hidden" class="plan-id" value="{{$plan->code}}">--}}
{{--                    @if(!empty($plan->installments))--}}
{{--                        <div class="form-group">--}}
{{--                            <select class="installments">--}}
{{--                                @foreach($plan->installments as $installment)--}}
{{--                                    <option value="{{$installment['amount']}}">{{$installment['amount']}}X DE R$ {{$installment['value']}}</option>--}}
{{--                                @endforeach--}}
{{--                                <h4 class="mb-md-4">Total R$ {{$plan->price}}</h4>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    @else--}}
{{--                        <h2 class="text-primary mb-md-4"><b>R$ {{$plan->price}}</b></h2>--}}
{{--                    @endif--}}
{{--                    <button class="btn btn-success btn-lg btn-buy">COMPRAR AGORA</button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            @if($key != count($upsellData['plans']) - 1)--}}
{{--                <hr class="plan-separator">--}}
{{--            @endif--}}
{{--        @endforeach--}}
        <div class="text-center px-3 mb-5">
            <a id="skip-offert" class="text-success pointer">Não obrigado, vou passar essa oferta</a>
        </div>
    </div>
</div>

