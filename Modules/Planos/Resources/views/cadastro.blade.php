@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar novo plano</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/planos">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/planos/cadastrarplano" enctype="multipart/form-data">
            @csrf
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <h4> Dados gerais </h4>
                    <div style="width:100%">
                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome</label>
                                <input name="nome" type="text" class="form-control" id="nome" placeholder="Nome" required>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="preco">Preço</label>
                                <input name="preco" type="text" class="form-control" id="preco" placeholder="Preço" required>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="descricao">Descrição</label>
                                <input name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="status">Status</label>
                                <select name="status" type="text" class="form-control" id="status" required>
                                    <option value="">Selecione</option>
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="disponivel">Status cumpons</label>
                                <select name="disponivel" type="text" class="form-control" id="disponivel" required>
                                    <option value="">Selecione o status</option>
                                    <option value="1">Disponível</option>
                                    <option value="0">Indisponível</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="frete">Frete</label>
                                <select name="frete" type="text" class="form-control" id="frete" required>
                                    <option value="">Selecione</option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="frete_fixo">Frete fixo</label>
                                <select name="frete_fixo" type="text" class="form-control" id="frete_fixo" required>
                                    <option value="">Selecione</option>
                                    <option value="1">Sim</option>
                                    <option value="0">Não</option>
                                </select>
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="valor_frete">Valor frete fixo</label>
                                <input name="valor_frete" type="text" class="form-control" id="valor_frete" placeholder="valor fixo">
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="transportadora">Transportadora</label>
                                <select name="transportadora" type="text" class="form-control" id="transportadora" required>
                                    <option value="">Selecione</option>
                                    @foreach($transportadoras as $transportadora)
                                        <option value="{{ $transportadora['id'] }}">{{ $transportadora['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="id_plano_transportadora">Id da transportadora</label>
                                <input name="id_plano_transportadora" type="text" class="form-control" id="id_plano_transportadora" placeholder="id da transportadora">
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="foto">Foto do plano</label>
                                <input name="foto" type="file" class="form-control" id="foto">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="quntidade">Quantidade</label>
                                <input name="quntidade" type="text" class="form-control" id="quntidade" placeholder="Quantidade" required>
                            </div>

                        </div>

                        <h4> Produtos do plano </h4>
                        <div id="produtos">
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
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <button type="button" id="add_produto" class="btn btn-primary">Adicionar produto</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
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

        var qtd_produtos = 1;

        var div_produtos = $('#produtos_div_1').parent().clone();

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

    });

  </script>


@endsection

