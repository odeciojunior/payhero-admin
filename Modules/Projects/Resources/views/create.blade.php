@extends("layouts.master")

@section('content')

    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Cadastrar novo projeto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/projects">
                    Meus projetos
                </a>
            </div>
        </div>
        <div class="page-content container">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                @if($companies->count() > 0)
                    <form method="POST" action="/projects" enctype="multipart/form-data">
                        @csrf
                        @method('POST')
                        <h4> Dados gerais </h4>
                        <div style="width:100%">
                            <div class='row'>
                                <div class='form-group col-12'>
                                    <label for='select-photo'>Foto do produto</label>
                                    <br>
                                    <div class='row'>
                                        <div class='col-md-6'>
                                            <input name='project-photo' type='file' class='form-control' id='photo' style="display:none">
                                            <div style="margin: 20px 0 0 30px;">
                                                <img id='preview-image-project' alt='Selecione a foto do produto' src='{{asset('modules/projects/img/projeto.png')}}'>
                                            </div>
                                            <input type='hidden' name='foto_x1'/> <input type='hidden' name='foto_y1'/>
                                            <input type='hidden' name='foto_w'/> <input type='hidden' name='foto_h'/>
                                            <br>
                                            <div class='col-md-8'>
                                                <input type='button' id='select-photo' class='btn btn-default' value='Selecionar foto do projeto'>
                                            </div>
                                        </div>
                                        <div class='col-md-6'>
                                            <div class='form-group col-xl-12'>
                                                <label for='name'>Nome</label>
                                                <input name='name' type='text' class='form-controll' id='name' placeholder='Nome do projeto' required>
                                                @if ($errors->has('name'))
                                                    <div class="invalid-feedback d-block">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class='form-group col-xl-12'>
                                                <label for='company'>Empresa</label>
                                                <select name='company' class='form-control' id='company' required>
                                                    <option value=''>Selecione</option>
                                                    @foreach($companies as $company)
                                                        <option value='{{Hashids::encode($company->id)}}'>{{$company->fantasy_name}}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('company'))
                                                    <div class="invalid-feedback d-block">
                                                        <strong>{{ $errors->first('company') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class='form-group col-xl-12'>
                                                <label for='description'>Descrição</label>
                                                <textarea name='description' class='form-control' id='description' placeholder='Descrição' rows='4' cols='50'></textarea>
                                                @if ($errors->has('description'))
                                                    <div class="invalid-feedback d-block">
                                                        <strong>{{ $errors->first('description') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type='hidden' value='private' name='visibility'>
                                            <input type='hidden' value='1' name='status'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 30px">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Salvar</button>
                                </div>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning" role="alert">
                        <strong>Ops!</strong> Você ainda não possui empresas cadastradas.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            var p = $("#preview-image-project");
            $("#photo").on("change", function () {

                var imageReader = new FileReader();
                imageReader.readAsDataURL(document.getElementById("photo").files[0]);

                imageReader.onload = function (oFREvent) {
                    p.attr('src', oFREvent.target.result).fadeIn();

                    p.on('load', function () {

                        var img = document.getElementById('previewimage');
                        var x1, x2, y1, y2;

                        if (img.naturalWidth > img.naturalHeight) {
                            y1 = Math.floor(img.naturalHeight / 100 * 10);
                            y2 = img.naturalHeight - Math.floor(img.naturalHeight / 100 * 10);
                            x1 = Math.floor(img.naturalWidth / 2) - Math.floor((y2 - y1) / 2);
                            x2 = x1 + (y2 - y1);
                        } else {
                            if (img.naturalWidth < img.naturalHeight) {
                                x1 = Math.floor(img.naturalWidth / 100 * 10);
                                ;
                                x2 = img.naturalWidth - Math.floor(img.naturalWidth / 100 * 10);
                                y1 = Math.floor(img.naturalHeight / 2) - Math.floor((x2 - x1) / 2);
                                y2 = y1 + (x2 - x1);
                            } else {
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

            $("#select-photo").on("click", function () {
                $("#photo").click();
            });

        });
    </script>

@endsection
