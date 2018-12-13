@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar produto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/produtos">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/produtos/editarproduto" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{!! $produto->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome</label>
                                <input value="{!! $produto->nome != '' ? $produto->nome : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Nome" required>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="categoria">Categoria</label>
                                <select name="categoria" class="form-control" id="categoria" required>
                                    <option value="">Selecione a categoria</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{!! $categoria['id'] !!}"  {!! ($produto->categoria == $categoria['id']) ? 'selected' : '' !!} >{!! $categoria['nome'] !!}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-xl-12">
                                <label for="descricao">Descrição</label>
                                <input value="{!! $produto->descricao != '' ? $produto->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="disponivel">Status</label>
                                <select name="disponivel" type="text" class="form-control" id="disponivel" required>
                                    <option value="">Selecione o status</option>
                                    <option value="1" {!! ($produto->disponivel == '1') ? 'selected' : '' !!}>Disponível</option>
                                    <option value="0"{!! ($produto->disponivel == '0') ? 'selected' : '' !!}>Indisponível</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="formato">Formato</label>
                                <select name="formato" type="text" class="form-control" id="formato" required>
                                    <option value="">Selecione o formato</option>
                                    <option value="1"{!! ($produto->formato == '1') ? 'selected' : '' !!}>Físico</option>
                                    <option value="0"{!! ($produto->formato == '0') ? 'selected' : '' !!}>Digital</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="custo_produto">Custo do produto</label>
                                <input value="{!! $produto->custo_produto != '' ? $produto->custo_produto : '' !!}" name="custo_produto" type="text" class="form-control" id="custo_produto" placeholder="Custo do produto">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="garantia">Garantia</label>
                                <input value="{!! $produto->garantia != '' ? $produto->garantia : '' !!}" name="garantia" type="text" class="form-control" id="garantia" placeholder="Garantia">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="foto">Foto do produto</label>
                                <input name="foto" type="file" class="form-control" id="foto">
                                <img src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO.$produto->foto)!!}" style="margin-top: 20px">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="quantidade">Quantidade</label>
                                <input value="{!! $produto->quantidade != '' ? $produto->quantidade : '' !!}" name="quantidade" type="text" class="form-control" id="quantidade" placeholder="quantidade">
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

