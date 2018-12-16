@extends("layouts.master")

@section('content')

    <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar projeto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/projetos">
                    Meus projetos
                </a>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                <form method="post" action="/projetos/editarprojeto" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{!! $projeto->id !!}">
                    <h4> Dados gerais </h4>
                    <div style="width:100%">
                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="nome">Nome</label>
                                <input name="nome" value="{!! $projeto->nome !!}" type="text" class="form-control" id="nome" placeholder="Nome do projeto" required>
                            </div>
                            <div class="form-group col-xl-6">
                                <label for="emrpesa">Empresa</label>
                                <select name="empresa" class="form-control" id="empresa" required>
                                    <option value="">Selecione</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{!! $empresa->id !!}" {!! ($empresa->id == $projeto->empresa) ? 'selected' : '' !!}>{!! $empresa->nome_fantasia !!}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-12">
                                <label for="descricao">Descrição</label>
                                <input name="descricao" value="{!! $projeto->descricao !!}" type="text" class="form-control" id="descricao" placeholder="Descrição">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="visibilidade">Visibilidade</label>
                                <select name="visibilidade" class="form-control" id="visibilidade" required>
                                    <option value="">Selecione</option>
                                    <option value="publico" {!! $projeto->visibilidade == 'publico' ? 'selected' : '' !!}>Projeto público</option>
                                    <option value="privado" {!! $projeto->visibilidade == 'privado' ? 'selected' : '' !!}>Projeto privado</option>
                                </select>
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="status">Status</label>
                                <select name="status" class="form-control" id="status" required>
                                    <option value="">Selecione</option>
                                    <option value="1" {!! $projeto->status == '1' ? 'selected' : '' !!}>Ativo</option>
                                    <option value="0" {!! $projeto->status == '0' ? 'selected' : '' !!}>Inativo</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="imagem">Imagem do projeto</label>
                                <input name="imagem" type="file" class="form-control" id="imagem">
                                <img src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto->foto)!!}" style="margin-top: 20px; height:200px">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Atualizar</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
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
