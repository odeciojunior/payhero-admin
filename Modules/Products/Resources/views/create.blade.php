@extends("layouts.master")

@section('content')

    <!-- Page -->
    <div class="page">
        <div class="page-header">
            <h1 class="page-title">Cadastrar novo produto</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{ route('products.index') }}">
                    Meus produtos
                </a>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                <form method="post" action="/products" enctype="multipart/form-data">
                    @csrf
                    <div class="page-content container-fluid">
                        <div style="width:100%">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label class='' for="">Foto do produto</label>
                                    <br>
                                    <div class='row'>
                                        <div class='col-md-6'>
                                            <input name="product_photo" type="file" class="form-control" id="foto" style="display:none">
                                            <div style="margin: 20px 0 0 30px;">
                                                <img id="previewimage" alt="Selecione a foto do produto" accept="image/*" src='{{asset('assets/img/produto.png')}}' style="max-height: 250px; max-width: 350px;"/>
                                            </div>
                                            <input type="hidden" name="foto_x1"/> <input type="hidden" name="foto_y1"/>
                                            <input type="hidden" name="foto_w"/> <input type="hidden" name="foto_h"/>
                                            <br>
                                            <input type="button" id="selecionar_foto" class="btn btn-default" value="Selecionar foto do produto">
                                        </div>
                                        <div class='col-md-6'>
                                            <div class="form-group col-xl-12">
                                                <label for="name">Nome</label>
                                                <input name="name" type="text" class="form-control" id="name" placeholder="Nome" required>
                                            </div>
                                            <div class="form-group col-xl-12">
                                                <label for="weight">Peso</label>
                                                <input name="weight" type="text" class="form-control" id="weight" placeholder="Peso" data-mask="0#">
                                            </div>
                                            <div class="form-group col-xl-12">
                                                <label for="width">Altura</label>
                                                <input name="width" type="text" class="form-control" id="width" placeholder="Altura" data-mask="0#">
                                            </div>
                                            <div class="form-group col-xl-12">
                                                <label for="height">largura</label>
                                                <input name="height" type="text" class="form-control" id="height" placeholder="Largura" data-mask="0#">
                                            </div>
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
        $(document).ready(function () {

            var p = $("#previewimage");
            $("#foto").on("change", function () {

                var imageReader = new FileReader();
                imageReader.readAsDataURL(document.getElementById("foto").files[0]);

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

            $("#selecionar_foto").on("click", function () {
                $("#foto").click();
            });

            $('.dinheiro').mask('#.###,#0', {reverse: true});

        });
    </script>


@endsection

