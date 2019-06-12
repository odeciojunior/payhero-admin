@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header container">
            <h1 class="page-title">Criar novo produto</h1>
            <p class="mt-10"> Os dados abaixo são muito importantes para seu produto. Preencha-os com atenção. </p>
            <!-- <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{ route('products') }}">
                    Meus produtos
                </a>
            </div> -->
        </div>

        <div class="page-content container">
            <div class="panel p-15" data-plugin="matchHeight">
                <form method="post" action="/produtos/cadastrarproduto" enctype="multipart/form-data">
                    @csrf
                    <div class="page-content container">
                        <div style="width:100%">

                        <div class="col-lg-12">
              <!-- Example Tabs Line Left -->
              <div class="example-wrap">

                <div class="nav-tabs-horizontal" data-plugin="tabs">

                <ul class="nav nav-tabs nav-tabs-reverse" role="tablist">
                    <li class="nav-item" role="presentation" style=""><a class="nav-link active" data-toggle="tab" href="#exampleTabsReverseOne" aria-controls="exampleTabsReverseOne" role="tab" aria-selected="false">1. Informações</a></li>
                    <li class="nav-item" role="presentation" style=""><a class="nav-link" data-toggle="tab" href="#exampleTabsReverseTwo" aria-controls="exampleTabsReverseTwo" role="tab" aria-selected="false">2. Logística</a></li>
                    <li class="nav-item" role="presentation" style=""><a class="nav-link" data-toggle="tab" href="#exampleTabsReverseThree" aria-controls="exampleTabsReverseThree" role="tab" aria-selected="false">3. Visual</a></li>

                  </ul>
                  <!-- <ul class="nav nav-tabs nav-tabs-line mr-25" role="tablist">
                    <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#exampleTabsLineLeftOne" aria-controls="exampleTabsLineLeftOne" role="tab" aria-selected="true">1. Informações</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsLineLeftTwo" aria-controls="exampleTabsLineLeftTwo" role="tab" aria-selected="false">2. Logística</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#exampleTabsLineLeftThree" aria-controls="exampleTabsLineLeftThree" role="tab" aria-selected="false">3. Visual</a></li>
                  </ul> -->

                  <div class="tab-content py-15">
                    <div class="tab-pane active" id="exampleTabsReverseOne" role="tabpanel">
                        
                        <h3 class="form-sub"> 1. Informações Básicas </h3>

                        <div class="row">
                            <div class="form-group col-lg-8">
                                <label class="label-pad" for="nome">Nome</label>
                                <input name="nome" type="text" class="input-pad" id="nome" placeholder="Escolha um nome que chame atenção de seus compradores" required>
                            </div>

                            <div class="form-group col-lg-8">
                                <label class="label-pad" for="descricao">Descrição</label>
                                <textarea name="descricao" type="text" class="input-pad" id="descricao" placeholder="Descreva sobre o que se trata seu produto, de forma sucinta e clara, para apresentá-lo para possíveis afiliados e compradores" style="height: 150px;" required></textarea>
                                <p class="input-info"> <i class="icon wb-info m-0" aria-hidden="true"></i> Mínimo 200 caracteres. Máximo 1000 caracteres. </p>
                            </div>

                        </div>


                        <div class="row">

                            <div class="form-group col-lg-4">
                                    <label class="label-pad" for="categoria">Categoria</label>
                                    <select name="categoria" class="input-pad form-control select-pad" id="categoria" required>
                                        <option value="">Selecione a categoria</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{!! $categoria['id'] !!}">{!! $categoria['nome'] !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                        

                            
                            <div class="form-group col-xl-4 ">
                                <label class="label-pad" for="formato">Formato</label>
                                <select name="formato" type="text" class="input-pad form-control select-pad" id="formato" required>
                                    <option value="">Selecione o formato</option>
                                    <option value="1">Físico</option>
                                    <option value="0">Digital</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-5 ">
                            <label class="label-pad" for="formato">Visibilidade</label>

                            <div class="d-flex">
                                <div class="radio-custom radio-primary col-4">
                                    <input type="radio" id="inputRadiosUnchecked" name="inputRadios">
                                    <label for="inputRadiosUnchecked">Público</label>
                                </div>

                                <div class="radio-custom radio-primary col-4">
                                    <input type="radio" id="inputRadiosChecked" name="inputRadios">
                                    <label for="inputRadiosChecked">Privado</label>
                                </div>
                            </div>
                           
                            <p class="input-info"> <i class="icon wb-info m-0" aria-hidden="true"></i> Mínimo 200 caracteres. Máximo 1000 caracteres. </p>

                            </div>

                            
                            
                        </div>
                    </div>
                    <div class="tab-pane" id="exampleTabsReverseTwo" role="tabpanel">

                                 <h3 class="form-sub"> 2. Logística e Precificação </h3>


                                <div class="row">
                                    <div class="form-group col-xl-4">
                                        <label class="label-pad" for="custo_produto">Custo do produto</label>
                                        <input name="custo_produto" type="text" class="input-pad dinheiro" id="custo_produto" placeholder="Custo do produto">
                                    </div>
                                    <div class="form-group col-xl-4">
                                        <label class="label-pad" for="recebedor_custo">Recebedor do custo</label>
                                        <select name="recebedor_custo" class="input-pad form-control select-pad" id="recebedor_custo">
                                            <option value="">Produtor</option>
                                            <option value="kapsula">Kapsula</option>
                                            <option value="liftgold">Lift Gold</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-xl-4">
                                        <label class="label-pad" for="garantia">Garantia (em dias)</label>
                                        <input name="garantia" type="text" class="input-pad" id="garantia" placeholder="Garantia" data-mask="0#">
                                    </div>

                                    <div class="form-group col-xl-4">
                                        <label  class="label-pad" for="quantidade">Quantidade (em estoque)</label>
                                        <input name="quantidade" type="text" class="input-pad" id="quantidade" placeholder="Quantidade" data-mask="0#">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-xl-4">
                                        <label class="label-pad" for="peso">Peso (gramas)</label>
                                        <input name="peso" type="text" class="input-pad" id="peso" placeholder="Peso" data-mask="0#">
                                    </div>

                                    <div class="form-group col-xl-4">
                                        <label class="label-pad" for="altura">Altura (cm)</label>
                                        <input name="altura" type="text" class="input-pad" id="altura" placeholder="Altura" data-mask="0#">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xl-4">
                                        <label class="label-pad" for="largura">Largura (cm) </label>
                                        <input name="largura" type="text" class="input-pad" id="largura" placeholder="Largura" data-mask="0#">
                                    </div>
                                </div>


                    </div>
                    <div class="tab-pane" id="exampleTabsReverseThree" role="tabpanel">
                    <h3 class="form-sub"> 3. Visual do Produto </h3>


                    <div class="row">
                                <div class="form-group col-xl-6">
                                    <div class="input-group input-group-file" data-plugin="inputGroupFile">
                                        <input type="text" class="form-control" readonly="">
                                        <span class="input-group-btn">
                                        <span class="btn btn-success btn-file">
                                            <i class="icon wb-upload" aria-hidden="true"></i>
                                            <input type="file" name="" multiple="">
                                        </span>
                                        </span>
                                    </div>

                                     <!-- <div class="form-group col-12">
                                    <label class="label-pad" for="selecionar_foto">Foto do produto</label><br>
                                    <input type="button" id="selecionar_foto" class="btn btn-default" value="Selecionar foto do produto">
                                    <input name="foto_produto" type="file" class="input-pad" id="foto" style="display:none">
                                    <div  style="margin: 20px 0 0 30px;">
                                        <img id="previewimage" alt="Selecione a foto do produto" accept="image/*" style="max-height: 250px; max-width: 350px;"/>
                                    </div>
                                    <input type="hidden" name="foto_x1"/>
                                    <input type="hidden" name="foto_y1"/>
                                    <input type="hidden" name="foto_w"/>
                                    <input type="hidden" name="foto_h"/> -->

                                </div>
                                   
                                    
                        </div>
                              
                    <div class="form-group col-6">
                                    <button type="submit" class="btn btn-success float-right">Criar produto</button>
                                </div>
                    
                  </div>
                </div>
              </div>
              <!-- End Example Tabs Line Left -->
            </div>

                        <!-- <h3 class="form-sub"> 1. Informações Básicas </h3>

                            <div class="row">
                                <div class="form-group col-lg-8">
                                    <label class="label-pad" for="nome">Nome</label>
                                    <input name="nome" type="text" class="input-pad" id="nome" placeholder="Nome" required>
                                </div>

                                <div class="form-group col-lg-8">
                                    <label class="label-pad" for="descricao">Descrição</label>
                                    <textarea name="descricao" type="text" class="input-pad" id="descricao" placeholder="Descrição" style="height: 150px;" required></textarea>
                                </div>
                            
                            </div>


                            <div class="row">

                                <div class="form-group col-lg-4">
                                        <label class="label-pad" for="categoria">Categoria</label>
                                        <select name="categoria" class="input-pad form-control select-pad" id="categoria" required>
                                            <option value="">Selecione a categoria</option>
                                            @foreach($categorias as $categoria)
                                                <option value="{!! $categoria['id'] !!}">{!! $categoria['nome'] !!}</option>
                                            @endforeach
                                        </select>
                                    </div>
                               

                                
                                <div class="form-group col-xl-4 ">
                                    <label class="label-pad" for="formato">Formato</label>
                                    <select name="formato" type="text" class="input-pad form-control select-pad" id="formato" required>
                                        <option value="">Selecione o formato</option>
                                        <option value="1">Físico</option>
                                        <option value="0">Digital</option>
                                    </select>
                                </div>
                                
                        </div>

                            <h3 class="form-sub"> 2. Logística e Precificação </h3>


                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label class="label-pad" for="custo_produto">Custo do produto</label>
                                    <input name="custo_produto" type="text" class="input-pad dinheiro" id="custo_produto" placeholder="Custo do produto">
                                </div>
                                <div class="form-group col-xl-6">
                                    <label class="label-pad" for="recebedor_custo">Recebedor do custo</label>
                                    <select name="recebedor_custo" class="input-pad form-control select-pad" id="recebedor_custo">
                                        <option value="">Produtor</option>
                                        <option value="kapsula">Kapsula</option>
                                        <option value="liftgold">Lift Gold</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label class="label-pad" for="garantia">Garantia (em dias)</label>
                                    <input name="garantia" type="text" class="input-pad" id="garantia" placeholder="Garantia" data-mask="0#">
                                </div>
        
                                <div class="form-group col-xl-6">
                                    <label  class="label-pad" for="quantidade">Quantidade (em estoque)</label>
                                    <input name="quantidade" type="text" class="input-pad" id="quantidade" placeholder="Quantidade" data-mask="0#">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label class="label-pad" for="peso">Peso (gramas)</label>
                                    <input name="peso" type="text" class="input-pad" id="peso" placeholder="Peso" data-mask="0#">
                                </div>
        
                                <div class="form-group col-xl-6">
                                    <label class="label-pad" for="altura">Altura (cm)</label>
                                    <input name="altura" type="text" class="input-pad" id="altura" placeholder="Altura" data-mask="0#">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label class="label-pad" for="largura">Largura (cm) </label>
                                    <input name="largura" type="text" class="input-pad" id="largura" placeholder="Largura" data-mask="0#">
                                </div>
                            </div>

                            <h3 class="form-sub"> 3. Visual do Produto </h3>

                            <div class="row">
                                <div class="form-group col-12">
                                    <label class="label-pad" for="selecionar_foto">Foto do produto</label><br>
                                    <input type="button" id="selecionar_foto" class="btn btn-default" value="Selecionar foto do produto">
                                    <input name="foto_produto" type="file" class="input-pad" id="foto" style="display:none">
                                    <div  style="margin: 20px 0 0 30px;">
                                        <img id="previewimage" alt="Selecione a foto do produto" accept="image/*" style="max-height: 250px; max-width: 350px;"/>
                                    </div>
                                    <input type="hidden" name="foto_x1"/>
                                    <input type="hidden" name="foto_y1"/>
                                    <input type="hidden" name="foto_w"/>
                                    <input type="hidden" name="foto_h"/>
                                </div>
                            </div>

                            <div class="form-group">
                                    <button type="submit" class="btn btn-success">Salvar</button>
                                </div> -->
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

        $('.dinheiro').mask('#.###,#0', {reverse: true});

    });

  </script>


@endsection

