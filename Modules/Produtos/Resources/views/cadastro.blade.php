@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar novo produto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{ route('produtos') }}">
                    Meus produtos
                </a>
            </div>
        </div>

        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                <form method="post" action="/produtos/cadastrarproduto" enctype="multipart/form-data">
                    @csrf
                    <div class="page-content container-fluid">
                        <div style="width:100%">
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label for="nome">Nome</label>
                                    <input name="nome" type="text" class="form-control" id="nome" placeholder="Nome" required>
                                </div>
                                <div class="form-group col-xl-6">
                                    <label for="categoria">Categoria</label>
                                    <select name="categoria" class="form-control" id="categoria" required>
                                        <option value="">Selecione a categoria</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{!! $categoria['id'] !!}">{!! $categoria['nome'] !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="descricao">Descrição</label>
                                    <input name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label for="disponivel">Status</label>
                                    <select name="disponivel" type="text" class="form-control" id="disponivel" required>
                                        <option value="">Selecione o status</option>
                                        <option value="1">Disponível</option>
                                        <option value="0">Indisponível</option>
                                    </select>
                                </div>

                                <div class="form-group col-xl-6">
                                    <label for="formato">Formato</label>
                                    <select name="formato" type="text" class="form-control" id="formato" required>
                                        <option value="">Selecione o formato</option>
                                        <option value="1">Físico</option>
                                        <option value="0">Digital</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label for="custo_produto">Custo do produto</label>
                                    <input name="custo_produto" type="text" class="form-control" id="custo_produto" placeholder="Custo do produto" data-mask="0#">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label for="garantia">Garantia (em dias)</label>
                                    <input name="garantia" type="text" class="form-control" id="garantia" placeholder="Garantia" data-mask="0#">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label for="quantidade">Quantidade (em estoque)</label>
                                    <input name="quantidade" type="text" class="form-control" id="quantidade" placeholder="quantidade" data-mask="0#">
                                </div>

                                <div class="form-group col-xl-6">
                                    <label for="peso">Peso</label>
                                    <input name="peso" type="text" class="form-control" id="peso" placeholder="Peso" data-mask="0#">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label for="altura">Altura</label>
                                    <input name="altura" type="text" class="form-control" id="altura" placeholder="Altura" data-mask="0#">
                                </div>

                                <div class="form-group col-xl-6">
                                    <label for="largura">largura</label>
                                    <input name="largura" type="text" class="form-control" id="largura" placeholder="Largura" data-mask="0#">
                                </div>
                            </div>

                            <div class="row">

                                <div class="form-group col-12">
                                    <label for="selecionar_foto">Foto do produto</label><br>
                                    <input type="button" id="selecionar_foto" class="btn btn-default" value="Selecionar foto do produto">
                                    <input name="foto_produto" type="file" class="form-control" id="foto" style="display:none">
                                    <div  style="margin: 20px 0 0 30px;">
                                        <img id="previewimage" alt="Selecione a foto do produto" accept="image/*" style="max-height: 250px; max-width: 350px;"/>
                                    </div>
                                    <input type="hidden" name="foto_x1"/>
                                    <input type="hidden" name="foto_y1"/>
                                    <input type="hidden" name="foto_w"/>
                                    <input type="hidden" name="foto_h"/>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 30px">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

  <script>

    $(document).ready( function(){

        var p = $("#previewimage");
        $("#foto").on("change", function(){

            var imageReader = new FileReader();
            imageReader.readAsDataURL(document.getElementById("foto").files[0]);

            imageReader.onload = function (oFREvent) {
                p.attr('src', oFREvent.target.result).fadeIn();

                p.on('load', function(){

                    var img = document.getElementById('previewimage');
                    var x1, x2, y1, y2;
    
                    if (img.clientWidth > img.clientHeight) {
                        y1 = Math.floor(img.clientHeight / 100 * 10);
                        y2 = img.clientHeight - Math.floor(img.clientHeight / 100 * 10);
                        x1 = Math.floor(img.clientWidth / 2) - Math.floor((y2 - y1) / 2);
                        x2 = x1 + (y2 - y1);
                    }
                    else {
                        if (img.clientWidth < img.clientHeight) {
                            x1 = Math.floor(img.clientWidth / 100 * 10);;
                            x2 = img.clientWidth - Math.floor(img.clientWidth / 100 * 10);
                            y1 = Math.floor(img.clientHeight / 2) - Math.floor((x2 - x1) / 2);
                            y2 = y1 + (x2 - x1);
                        }
                        else {
                            x1 = Math.floor(img.clientWidth / 100 * 10);
                            x2 = img.clientWidth - Math.floor(img.clientWidth / 100 * 10);
                            y1 = Math.floor(img.clientHeight / 100 * 10);
                            y2 = img.clientHeight - Math.floor(img.clientHeight / 100 * 10);
                        }
                    }

                    $('input[name="foto_x1"]').val(x1);
                    $('input[name="foto_y1"]').val(y1);
                    $('input[name="foto_w"]').val(x2 - x1);
                    $('input[name="foto_h"]').val(y2 - y1);

                    $('#previewimage').imgAreaSelect({
                        x1: x1, y1: y1, x2: x2, y2: y2,
                        aspectRatio: '4:4',
                        onSelectEnd: function (img, selection) {
                            $('input[name="foto_x1"]').val(selection.x1);
                            $('input[name="foto_y1"]').val(selection.y1);
                            $('input[name="foto_w"]').val(selection.width);
                            $('input[name="foto_h"]').val(selection.height);
                        }
                    });
                })
            };

        });

        $("#selecionar_foto").on("click", function(){
            $("#foto").click();
        });

    });

  </script>


@endsection

