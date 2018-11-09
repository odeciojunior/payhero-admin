@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar plano</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/planos">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/planos/editarplano" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{!! $plano->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <h4> Dados gerais </h4>
                    <div style="width:100%">
                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome</label>
                                <input value="{!! $plano->nome != '' ? $plano->nome : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Nome" required>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="preco">Preço</label>
                                <input value="{!! $plano->preco != '' ? $plano->preco : '' !!}" name="preco" type="text" class="form-control" id="preco" placeholder="Preço" required>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="descricao">Descrição</label>
                                <input value="{!! $plano->descricao != '' ? $plano->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="status">Status</label>
                                <select name="status" type="text" class="form-control" id="status" required>
                                    <option value="">Selecione</option>
                                    <option value="1" {!! ($plano->status == '1') ? 'selected' : '' !!}>Ativo</option>
                                    <option value="0" {!! ($plano->status == '0') ? 'selected' : '' !!}>Inativo</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="disponivel">Status cumpons</label>
                                <select name="disponivel" type="text" class="form-control" id="disponivel" required>
                                    <option value="">Selecione o status</option>
                                    <option value="1" {!! ($plano->status_cupom == '1') ? 'selected' : '' !!}>Disponível</option>
                                    <option value="0" {!! ($plano->status_cupom == '0') ? 'selected' : '' !!}>Indisponível</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="frete">Frete</label>
                                <select name="frete" type="text" class="form-control" id="frete" required>
                                    <option value="">Selecione</option>
                                    <option value="1" {!! ($plano->frete == '1') ? 'selected' : '' !!}>Sim</option>
                                    <option value="0" {!! ($plano->frete == '0') ? 'selected' : '' !!}>Não</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="frete_fixo">Frete fixo</label>
                                <select name="frete_fixo" type="text" class="form-control" id="frete_fixo" required>
                                    <option value="">Selecione</option>
                                    <option value="1" {!! ($plano->frete_fixo == '1') ? 'selected' : '' !!}>Sim</option>
                                    <option value="0" {!! ($plano->frete_fixo == '0') ? 'selected' : '' !!}>Não</option>
                                </select>
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="valor_frete">Valor frete fixo</label>
                                <input value="{!! $plano->valor_frete != '' ? $plano->valor_frete : '' !!}" name="valor_frete" type="text" class="form-control" id="valor_frete" placeholder="valor fixo">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="transportadora">Transportadora</label>
                                <select name="transportadora" type="text" class="form-control" id="transportadora" required>
                                    <option value="">Selecione</option>
                                    @foreach($transportadoras as $transportadora)
                                        <option value="{{ $transportadora['id'] }}" {!! ($plano->transportadora == $transportadora['id']) ? 'selected' : '' !!}>{{ $transportadora['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="id_plano_transportadora">Id da transportadora</label>
                                <input value="{!! $plano->id_plano_transportadora != '' ? $plano->id_plano_transportadora : '' !!}" name="id_plano_transportadora" type="text" class="form-control" id="id_plano_transportadora" placeholder="id da transportadora">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="foto">Foto do produto</label>
                                <input name="foto" type="file" class="form-control" id="foto">
                                @if($foto != null)
                                    <img src="{{ $foto }}" style="margin-top: 20px;height: 250px">
                                @endif
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="quntidade">Quantidade</label>
                                <input value="{!! $plano->quntidade != '' ? $plano->quntidade : '' !!}" name="quntidade" type="text" class="form-control" id="quntidade" placeholder="Quantidade" required>
                            </div>
                        </div>

                        <h4> Produtos </h4>
                        <div id="produtos">
                            @if(count($produtos_planos) > 0)
                                @foreach($produtos_planos as $key => $produto_plano)
                                    <div id="produtos_div_{{ $key + 1 }}" class="row">
                                        <div class="form-group col-xl-10">
                                            <select id="produto_{{ $key + 1 }}" name="produto_{{ $key + 1 }}" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach($produtos as $produto)
                                                    <option value="{{ $produto['id'] }}"  {!! ($produto['id'] == $produto_plano['produto']) ? 'selected' : '' !!}>{{ $produto['nome'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <input value="{!! $produto_plano['quantidade_produto'] != '' ? $produto_plano['quantidade_produto'] : '' !!}" class="form-control qtd-produtos" type="text" name="produto_qtd_{{ $key + 1 }}" placeholder="quantidade">
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="produtos_div_1" class="row">
                                    <div class="form-group col-xl-10">
                                        <select id="produto_1" name="produto_1" class="form-control">
                                            <option value="" selected>Selecione</option>
                                            @foreach($produtos as $produto)
                                                <option value="{{ $produto['id'] }}">{{ $produto['nome'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-xl-2">
                                        <input class="form-control qtd-produtos" type="text" name="produto_qtd_1" placeholder="quantidade">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <button type="button" id="add_produto" class="btn btn-primary">Adicionar produto</button>
                            </div>
                        </div>

                        <h4> Pixels </h4>
                        <div id="pixels">
                            @if(count($planoPixels) > 0)
                                @foreach($planoPixels as $key => $planoPixel)
                                    <div id="pixels_div_{{ $key + 1 }}" class="row">
                                        <div class="form-group col-xl-12">
                                            <select id="pixel_{{ $key + 1 }}" name="pixel_{{ $key + 1 }}" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach($pixels as $pixel)
                                                    <option value="{{ $pixel['id'] }}"  {!! ($pixel['id'] == $planoPixel['pixel']) ? 'selected' : '' !!}>{{ $pixel['nome'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="pixels_div_1" class="row">
                                    <div class="form-group col-xl-12">
                                        <select id="pixel_1" name="pixel_1" class="form-control">
                                            <option value="" selected>Selecione</option>
                                            @foreach($pixels as $pixel)
                                                <option value="{{ $pixel['id'] }}">{{ $pixel['nome'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <button type="button" id="add_pixel" class="btn btn-primary">Adicionar pixel</button>
                            </div>
                        </div>

                        <h4> Brindes </h4>
                        <div id="brindes">
                            @if(count($planoBrindes) > 0)
                                @foreach($planoBrindes as $key => $planoBrinde)
                                    <div id="brindes_div_{{ $key + 1 }}" class="row">
                                        <div class="form-group col-xl-12">
                                            <select id="brinde_{{ $key + 1 }}" name="brinde_{{ $key + 1 }}" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach($brindes as $brinde)
                                                    <option value="{{ $brinde['id'] }}"  {!! ($brinde['id'] == $planoBrinde['brinde']) ? 'selected' : '' !!}>{{ $brinde['descricao'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="brindes_div_1" class="row">
                                    <div class="form-group col-xl-12">
                                        <select id="brinde_1" name="brinde_1" class="form-control">
                                            <option value="" selected>Selecione</option>
                                            @foreach($brindes as $brinde)
                                                <option value="{{ $brinde['id'] }}">{{ $brinde['descricao'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <button type="button" id="add_brinde" class="btn btn-primary">Adicionar brinde</button>
                            </div>
                        </div>

                        <h4> Cupons de desconto </h4>
                        <div id="cupons">
                            @if(count($planoCupons) > 0)
                                @foreach($planoCupons as $key => $planoCupom)
                                    <div id="cupons_div_{{ $key + 1 }}" class="row">
                                        <div class="form-group col-xl-12">
                                            <select id="cupom_{{ $key + 1 }}" name="cupom_{{ $key + 1 }}" class="form-control">
                                                <option value="">Selecione</option>
                                                @foreach($cupons as $cupom)
                                                    <option value="{{ $cupom['id'] }}"  {!! ($cupom['id'] == $planoCupom['cupom']) ? 'selected' : '' !!}>{{ $cupom['nome'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div id="cupons_div_1" class="row">
                                    <div class="form-group col-xl-12">
                                        <select id="cupom_1" name="cupom_1" class="form-control">
                                            <option value="" selected>Selecione</option>
                                            @foreach($cupons as $cupom)
                                                <option value="{{ $cupom['id'] }}">{{ $cupom['nome'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <button type="button" id="add_cupom" class="btn btn-primary">Adicionar cupom</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <button type="submit" class="btn btn-success">Salvar</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>

  <script>

    $(document).ready( function(){

        $("input:file").change(function(e) {

            for (var i = 0; i < e.originalEvent.srcElement.files.length; i++) {

                var file = e.originalEvent.srcElement.files[i];

                if($('img').length != 0){
                    $('img').remove();
                }

                var img = document.createElement("img");
                var reader = new FileReader();

                reader.onloadend = function() {

                    img.src = reader.result;

                    $(img).on('load', function (){

                        var width = img.width, height = img.height;

                        if (img.width > img.height) {
                            if (width > 400) {
                              height *= 400 / img.width;
                              width = 400;
                            }
                        } else {
                            if (img.height > 200) {
                              width *= 200 / img.height;
                              height = 200;
                            }
                        }

                        $(img).css({
                            'width' : width+'px',
                            'height' : height+'px',
                            'margin-top' : '30px',
                        });

                    })    
                }
                reader.readAsDataURL(file);

                $(this).after(img);
            }
        });

        var qtd_produtos = '{{ count($produtos_planos) == 0 ? 1 : count($produtos_planos) }}';

        var div_produtos = $('#produtos_div_'+qtd_produtos).parent().clone();

        $('#add_produto').on('click', function(){

            qtd_produtos++;

            var nova_div = div_produtos.clone();

            var select = nova_div.find('select');
            var input = nova_div.find('.qtd-produtos');

            select.attr('id', 'produto_'+qtd_produtos);            
            select.attr('name', 'produto_'+qtd_produtos);            
            input.attr('name', 'produto_qtd_'+qtd_produtos);            

            div_produtos = nova_div;

            $('#produtos').append(nova_div.html());

        });

        var qtd_pixels = '{{ count($planoPixels) == 0 ? 1 : count($planoPixels) }}';

        var div_pixels = $('#pixels_div_'+qtd_pixels).clone();

        $('#add_pixel').on('click', function(){

            qtd_pixels++;

            var nova_div = div_pixels;

            var select = nova_div.find('select');

            select.attr('id', 'pixel_'+qtd_pixels);            
            select.attr('name', 'pixel_'+qtd_pixels);         
            select.val('');

            div_pixels = nova_div;

            $('#pixels').append('<div class="row">'+nova_div.html()+'</div>');
        });

        var qtd_brindes = '{{ count($planoBrindes) == 0 ? 1 : count($planoBrindes) }}';

        var div_brindes = $('#brindes_div_'+qtd_brindes).clone();

        $('#add_brinde').on('click', function(){

            qtd_brindes++;

            var nova_div = div_brindes;

            var select = nova_div.find('select');

            select.attr('id', 'brinde_'+qtd_brindes);            
            select.attr('name', 'brinde_'+qtd_brindes);            

            div_brindes = nova_div;

            $('#brindes').append('<div class="row">'+nova_div.html()+'</div>');
        });

        var qtd_cupons = '{{ count($planoCupons) == 0 ? 1 : count($planoCupons) }}';

        var div_cupons = $('#cupons_div_'+qtd_cupons).clone();

        $('#add_cupom').on('click', function(){

            qtd_cupons++;

            var nova_div = div_cupons.clone();

            var select = nova_div.find('select');

            select.attr('id', 'cupom_'+qtd_cupons);            
            select.attr('name', 'cupom_'+qtd_cupons);            

            div_cupons = nova_div;

            $('#cupons').append('<div class="row">'+nova_div.html()+'</div>');
        });

    });

  </script>


@endsection
