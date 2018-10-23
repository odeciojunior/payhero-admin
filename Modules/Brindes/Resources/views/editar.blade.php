@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Editar brinde</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/brindes">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>

        <form method="post" action="/brindes/editarbrinde"  enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{!! $brinde->id !!}" name="id">
            <div class="page-content container-fluid">
                <div class="panel" data-plugin="matchHeight">
                    <div style="width:100%">

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="titulo">Título</label>
                                <input value="{!! $brinde->titulo != '' ? $brinde->titulo : '' !!}" name="titulo" type="text" class="form-control" id="titulo" placeholder="Título">
                            </div>

                            <div class="form-group col-xl-6">
                                <label for="descricao">Descrição</label>
                                <input value="{!! $brinde->descricao != '' ? $brinde->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                            </div>
                        </div>

                        <div class="row">

                            <div class="form-group col-xl-12">
                                <label for="foto">Foto do brinde</label>
                                <input name="foto" type="file" class="form-control" id="foto">
                                <img src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO.$brinde->foto)!!}" style="margin-top: 20px">
                            </div>

                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6">
                                <label for="tipo_brinde">Tipo de brinde</label>
                                <select name="tipo_brinde" type="text" class="form-control" id="tipo_brinde">
                                    <option value="">Selecione</option>
                                    @foreach($tipo_brindes as $tipo_brinde)
                                        <option value="{{ $tipo_brinde['id'] }}" {!! ($brinde->tipo_brinde == $tipo_brinde['id']) ? 'selected' : '' !!}>{{ $tipo_brinde['descricao'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="div_input_arquivo" class="form-group col-xl-6" style="display: none">
                                <label for="link">Arquivo</label>
                                <input name="link" type="file" class="form-control" id="link">
                            </div>

                            <div id="div_input_link" class="form-group col-xl-6" style="display: none">
                                <label for="link">Link</label>
                                <input value="{!! $brinde->link != '' ? $brinde->link : '' !!}" name="link" type="text" class="form-control" id="link" placeholder="Link">
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

        $("#foto").change(function(e) {

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


        $('#tipo_brinde').on('change', function(){

            if($(this).val() == 1){
                $('#div_input_arquivo').show();
                $('#div_input_link').hide();
            }
            if($(this).val() == 2){
                $('#div_input_arquivo').hide();
                $('#div_input_link').show();

            }
        });

        var tipo_brinde = '{{ $brinde->tipo_brinde }}';

        if(tipo_brinde == '1'){
            $('#div_input_arquivo').show();
        }
        if(tipo_brinde == '2'){
            $('#div_input_link').show();
        }

    });

  </script>


@endsection

