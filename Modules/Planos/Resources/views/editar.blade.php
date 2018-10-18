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

        <form method="post" action="/planos/editarplano">
            @csrf
            <input type="hidden" value="{!! $plano->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
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
                                <label for="quntidade">Quantidade</label>
                                <input value="{!! $plano->quntidade != '' ? $plano->quntidade : '' !!}" name="quntidade" type="text" class="form-control" id="quntidade" placeholder="Quantidade" required>
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
        
    });

  </script>


@endsection

