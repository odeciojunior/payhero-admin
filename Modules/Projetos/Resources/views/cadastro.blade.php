@extends("layouts.master")

@section('content')

  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar novo projeto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/projetos">
                    Meus projetos
                </a>
            </div>
        </div>

        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                @if(count($empresas) == 0)
                    <div class="alert alert-warning" role="alert">
                        <strong>Ops!</strong> Você ainda não possui empresas cadastradas.
                    </div>
                @else
                    <form method="post" action="/projetos/cadastrarprojeto" enctype="multipart/form-data">
                        @csrf
                        <h4> Dados gerais </h4>
                        <div style="width:100%">
                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label for="nome">Nome</label>
                                    <input name="nome" type="text" class="form-control" id="nome" placeholder="Nome do projeto" required>
                                </div>
                                <div class="form-group col-xl-6">
                                    <label for="emrpesa">Empresa</label>
                                    <select name="empresa" class="form-control" id="empresa" required>
                                        <option value="">Selecione</option>
                                        @foreach($empresas as $empresa)
                                            <option value="{!! $empresa->id !!}">{!! $empresa->nome_fantasia !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="descricao">Descrição</label>
                                    <input name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-xl-6">
                                    <label for="visibilidade">Visibilidade</label>
                                    <select name="visibilidade" class="form-control" id="visibilidade" required>
                                        <option value="">Selecione</option>
                                        <option value="publico">Projeto público</option>
                                        <option value="privado">Projeto privado</option>
                                    </select>
                                </div>

                                <div class="form-group col-xl-6">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control" id="status" required>
                                        <option value="">Selecione</option>
                                        <option value="1">Ativo</option>
                                        <option value="0">Inativo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="selecionar_foto">Imagem do projeto</label><br>
                                    <input type="button" id="selecionar_foto" class="btn btn-default" value="Selecionar foto do projeto">
                                    <input name="foto_projeto" type="file" class="form-control" id="foto" style="display:none" accept="image/*">
                                    <div  style="margin: 20px 0 0 30px;">
                                        <img id="previewimage" alt="Selecione a foto do projeto" style="max-height: 250px; max-width: 350px;"/>
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
                    </form>
                @endif
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
