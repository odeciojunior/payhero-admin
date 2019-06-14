@extends("layouts.master") 

@section('content')

<!-- Page -->
<div class="page">
    <div class="page-header container">
        <h1 class="page-title">Cadastrar novo produto</h1>
        <p class="desc mt-10"> Preencha os dados sobre seu produto atentamente.  </p>
        <div class="page-header-actions">
            <a class="d-none d-lg-block btn btn-primary float-right" href="{{ route('products.index') }}">
                    Meus produtos
                </a>
        </div>
    </div>
    <div class="page-content container">
    <form method="post" action="/products" enctype="multipart/form-data">
        @csrf
        <div class="panel pt-30 p-30" data-plugin="matchHeight">

            <nav>
                <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">1. Informações Básicas</a>
                    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">2. Logística</a>
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active p-30" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">



                    <div class="row justify-content-between align-items-baseline">

                    <div class="col-lg-12">
                        <h5 class="page-title"> 1. Informações Básicas </h5>
                        <p class="pt-10"> Preencha atentamente as informações sobre seu produto </p>
                    </div>
                
                        <div class="col-lg-4">

                    
                            <div class="d-flex flex-column">
                                <input name="product_photo" type="file" class="form-control" id="foto" style="display:none">
                                <label for="name">Selecione uma imagem</label>
                                <button style="max-width: 250px;" type="button" class="btn btn-primary mt-10 mb-10" id="selecionar_foto"><i class="icon wb-upload" aria-hidden="true"></i> Selecionar foto</button>
                                <img id="previewimage" alt="Selecione a foto do produto" accept="image/*" src="{{ asset('assets/img/produto.png') }}" style="max-height: 250px; max-width: 300px;">
                                <input type="hidden" name="foto_x1"> <input type="hidden" name="foto_y1">
                                <input type="hidden" name="foto_w"> <input type="hidden" name="foto_h">
                                <p class="info mt-5" style="font-size:10px;"> <i class="icon wb-info-circle" aria-hidden="true"></i> A imagem escolhida deve estar no formato JPG, ou PNG. <br> Dimensões ideais: 600x500 pixels.
                    
                            </p></div>


                        </div>


                        <div class="col-lg-8">

                        <div class="row">

                        <div class="form-group col-lg-12">
                                <label for="name">Nome</label>
                                <input name="name" type="text" class="input-pad" id="name" placeholder="O nome do seu produto" required="">
                            </div>

                            <div class="form-group col-lg-12">
                                <label for="description">Descrição</label>
                                <textarea style="height: 100px;" name="description" type="text" class="input-pad" id="description" placeholder="Fale um pouco sobre seu produto" required=""></textarea>
                                <p> Máximo 150 caracteres. </p>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="name">Tipo</label>

                                <div class="d-flex justify-content-start">
                                    <div class="radio-custom radio-primary pr-20">
                                        <input type="radio" id="fisico" name="inputRadios">
                                        <label for="fisico">Físico</label>
                                    </div>

                                    <div class="radio-custom radio-primary d-flex">
                                        <input type="radio" id="digital" name="inputRadios">
                                        <label for="digital">Digital</label>
                                    </div>
                                </div>
                                 
                            </div>

                            <div class="form-group col-lg-6">
                            <label for="category">Categoria</label>
                                <select class="form-control select-pad">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>


                            <div class="form-group col-lg-6">
                                <label for="cost">Custo</label>
                                <input name="cost" type="text" class="input-pad" id="cost" placeholder="Digite o custo" data-mask="0#" autocomplete="off">
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="price">Preço</label>
                                <input name="price" type="text" class="input-pad" id="price" placeholder="Digite o preço" data-mask="0#" autocomplete="off">
                            </div>

                            <div class="form-group col-lg-12 text-right">
                                <button type="submit" class="btn btn-success">Prosseguir  <i class="icon wb-chevron-right" aria-hidden="true"></i></button>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <div class="tab-pane fade p-30" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

                <div class="row mt50">
                    <div class="col-lg-12">

                        <div class="row">

                        <div class="col-lg-12">
                            <h5 class="page-title"> 2. Logística </h5>
                            <p class="pt-10"> Preencha atentamente as informações sobre seu produto </p>
                            </div>

                            <div class="form-group col-lg-4">
                            <label for="width">Altura</label>
                            <input name="width" type="text" class="input-pad" id="width" placeholder="Ex: 150cm" data-mask="0#">
                        </div>

                        <div class="form-group col-lg-4">
                            <label for="height">Largura</label>
                            <input name="height" type="text" class="input-pad" id="height" placeholder="Ex: 135cm" data-mask="0#">
                        </div>

                        <div class="form-group col-lg-4">
                            <label for="weight">Peso</label>
                            <input name="weight" type="text" class="input-pad" id="weight" placeholder="Ex: 950g" data-mask="0#">
                        </div> 

                        <div class="form-group col-lg-6">
                            <label for="shipping">Transportadora:</label>
                                <select class="form-control select-pad">
                                    <option value="">Selecione sua transportadora</option>
                                    <option value="proprio">Envio próprio</option>
                                    <option value="kapsula">Kapsula</option>
                                    <option value="hubsmart">Hubsmart</option>
                                    <option value="cosmarca">Cosmarca</option>
                                    <option value="nutreno">Nutreno</option>
                                    <option value="nutracaps">Nutracaps</option>
                                    <option value="biosupra">Biosupra</option>
                                </select>
                            </div>


                            <div class="form-group col-lg-6">
                                <label for="id_shipping">ID na Transportadora:</label>
                                <input name="id_shipping" type="text" class="input-pad" id="id_shipping" placeholder="ID do seu produto na transportadora" data-mask="0#">
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </form>
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