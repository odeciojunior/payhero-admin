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

        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">

                <form method="post" action="/produtos/editarproduto" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{!! $product->id !!}" name="id">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome</label>
                                <input value="{!! $product->name != '' ? $product->name : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Nome" required>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="categoria">Categoria</label>
                                <select name="categoria" class="form-control" id="categoria" required>
                                    <option value="">Selecione a categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{!! $category['id'] !!}"  {!! ($product->category == $category['id']) ? 'selected' : '' !!} >{!! $category['name'] !!}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="row">

                            <div class="form-group col-xl-12">
                                <label for="descricao">Descrição</label>
                                <input value="{!! $product->description != '' ? $product->description : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-xl-6">
                                <label for="formato">Formato</label>
                                <select name="formato" type="text" class="form-control" id="formato" required>
                                    <option value="">Selecione o formato</option>
                                    <option value="1"{!! ($product->format == '1') ? 'selected' : '' !!}>Físico</option>
                                    <option value="0"{!! ($product->format == '0') ? 'selected' : '' !!}>Digital</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="custo_produto">Custo do produto</label>
                                <input value="{!! $product->cost != '' ? $product->cost : '' !!}" name="custo_produto" type="text" class="form-control" id="custo_produto" placeholder="Custo do produto" data-mask="0#">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="recebedor_custo">Recebedor do custo</label>
                                <select name="recebedor_custo" class="form-control" id="recebedor_custo">
                                    <option value="" {!! $product->cost_receiver == '' ? 'selected' : '' !!}>Produtor</option>
                                    <option value="kapsula" {!! $product->cost_receiver == 'kapsula' ? 'selected' : '' !!}>Kapsula</option>
                                    <option value="liftgold" {!! $product->cost_receiver == 'liftgold' ? 'selected' : '' !!}>Lift Gold</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="garantia">Garantia</label>
                                <input value="{!! $product->guarantee != '' ? $product->guarantee : '' !!}" name="garantia" type="text" class="form-control" id="garantia" placeholder="Garantia" data-mask="0#">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="quantidade">Quantidade</label>
                                <input value="{!! $product->amount != '' ? $product->amount : '0' !!}" name="quantidade" type="text" class="form-control" id="quantidade" placeholder="quantidade" data-mask="0#">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="peso">Peso</label>
                                <input value="{!! $product->weight != '' ? $product->weight : '' !!}" name="peso" type="text" class="form-control" id="peso" placeholder="Peso" data-mask="0#">
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="altura">Altura</label>
                                <input value="{!! $product->height != '' ? $product->height : '' !!}" name="altura" type="text" class="form-control" id="altura" placeholder="Altura" data-mask="0#">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="largura">largura</label>
                                <input value="{!! $product->width != '' ? $product->width : '' !!}" name="largura" type="text" class="form-control" id="largura" placeholder="Largura" data-mask="0#">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="selecionar_foto">Foto do produto</label><br>
                                <input type="button" id="selecionar_foto" class="btn btn-default" value="Alterar foto do produto">
                                <input name="foto_produto" type="file" class="form-control" id="foto" style="display:none">
                                <div  style="margin: 20px 0 0 30px;">
                                    <img id="previewimage" src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PRODUTO.$product->photo)!!}" alt="Selecione a foto do produto" accept="image/*" style="max-height: 250px; max-width: 350px;"/>
                                </div>
                                <input type="hidden" name="foto_x1"/>
                                <input type="hidden" name="foto_y1"/>
                                <input type="hidden" name="foto_w"/>
                                <input type="hidden" name="foto_h"/>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 30px">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Atualizar dados</button>
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
    
                    if (img.naturalWidth > img.naturalHeight) {
                        y1 = Math.floor(img.naturalHeight / 100 * 10);
                        y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                        x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                        x2 = x1 + (y2 - y1);
                    }
                    else {
                        if (img.naturalWidth < img.naturalHeight) {
                            x1 = Math.floor(img.naturalWidth / 100 * 10);;
                            x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                            y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                            y2 = y1 + (x2 - x1);
                        }
                        else {
                            x1 = Math.floor(img.naturalWidth / 100 * 10);
                            x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                        }
                    }

                    $('input[name="foto_x1"]').val(x1);
                    $('input[name="foto_y1"]').val(y1);
                    $('input[name="foto_w"]').val(x2 - x1);
                    $('input[name="foto_h"]').val(y2 - y1);

                    $('#previewimage').imgAreaSelect({
                        x1: x1, y1: y1, x2: x2, y2: y2,
                        aspectRatio: '1:1',
                        handles: true,
                        imageHeight: this.naturalHeight,
                        imageWidth: this.naturalWidth,
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

