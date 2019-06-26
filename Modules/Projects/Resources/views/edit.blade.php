<div class='page-content container'>
    <form method='post' action='/project/' enctype='multipart/form-data'>
        @method('PUT')
        @csrf
        <div class='row justify-content-between align-items-baseline'>
            <div class='col-lg-12'>
                <h3>Configurações Básicas</h3>
                <p class='pt-10'>Preencha atentamente as informações</p>
            </div>
            <div class='col-lg-4'>
                <div class='d-flex flex-column' id='div-img-project' style='position: relative;'>
                    <input name='photo' type='file' class='form-control' id='photoProject' style='display:none;' accept='image/*'>
                    <label for='photo'>Selecione uma imagem capa do projeto</label>
                    <img id='previewimage' alt='Selecione a foto do projeto' src='{{$project->photo ?? asset('modules/global/assets/img/projeto.png')}}' style='max-height: 250px; max-width: 250px;'>
                    <input type='hidden' name='photo_x1'><input type='hidden' name='photo_y1'>
                    <input type='hidden' name='photo_w'><input type='hidden' name='photo_h'>
                    <p class='info mt-5' style='font-size: 10px;'>
                        <i class='icon wb-info-circle' aria-hidden='true'></i> A imagem escolhida deve estar no formato JPG, JPEG ou PNG.
                        <br> Dimensões ideais: 300 x 300 pixels.
                    </p>
                </div>
            </div>
            <div class='col-lg-8'>
                <div class='row'>
                    <div class='form-group col-lg-12'>
                        <label for='name'>Nome do projeto</label>
                        <input name='name' value='{{$project->name}}' type='text' class='form-control' id='name' placeholder='Nome do Projeto' required>
                    </div>
                    <div class='form-group col-lg-12'>
                        <label for='description'>Descrição</label>
                        <textarea style='height:100px;' name='description' type='text' class='input-pad' id='description' placeholder='Fale um pouco sobre seu Projeto' required=''>{{$project->description}}</textarea>
                    </div>
                    <div class='form-group col-lg-12'>
                        <label for='visibility'>Visibilidade</label>
                        <select name='visibility' class='form-control' id='visibility' required>
                            <option type='hidden' disabled value='public' {{$project->visibility == 'public' ? 'selected': ''}}>Projeto público (visível na vitrine e disponivel para afiliações) Em Breve</option>
                            <option value='private' {{$project->visibility == 'private' ? 'selected': ''}}>Projeto privado (completamente invisivel para outros usuários, afiliações somente por convite)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class=''>
            <div class="row">
                <div class="form-group col-xl-6 col-lg-6">
                    <label for="url_page">URL da página principal</label>
                    <input name="url_page" value="{{$project->url_page}}" type="url" class="form-control" id="url-page" placeholder="URL da página">
                </div>
                <div class="form-group col-xl-6 col-lg-6">
                    <label for="contact">Email de Contato (checkout)</label>
                    <input name="contact" value="{{$project->contact}}" type="email" class="form-control" id="contact" placeholder="Contato">
                </div>
            </div>
            <h4>Configuração Frete</h4>
            <div class='row'>
                <div class='form-group col-xl-6 col-lg-6'>
                    <label for='shipping_project'>Possui Frete</label>
                    <select name='shippement' class='form-control' id='shippement'>
                        <option value='1' {{$project->shippement == '1' ? 'selected': ''}}>Sim</option>
                        <option value='0' {{$project->shippement == '0' ? 'selected': ''}}>Não</option>
                    </select>
                </div>
                <div id='div-carrier' class='form-group col-xl-6 col-lg-6' style='{{$project->frete? 'display:block;' : ''}}'>
                    <label for='carrier-transport'>Transportadora</label>
                    <select name='carrier' type='text' class='form-control' id='carrier-transport' required>
                        <option value='1'{{$project->carrier == '1'?'selected' : ''}}>Despacho próprio</option>
                    </select>
                </div>
                <div id='div-shipment-responsible' class='form-group col-xl-6 col-lg-6' style='{{$project->frete? 'display:block;' : ''}}'>
                    <label for='shipment_responsible'>Responsável pelo frete</label>
                    <select name='shipment_responsible' type='text' class='form-control' id='shipment_responsible'>
                        <option value='owner' {{$project->shipment_responsible == 'owner'?'selected':''}}>Proprietário</option>
                        <option value='partners' {{$project->shipment_responsible == 'partners'? 'selected':''}}>Proprietário + parceiros</option>
                    </select>
                </div>
            </div>
        </div>
        <div class=''>
            <h4>Configurações Avançadas</h4>
            <div class='row'>
                <div class='form-group col-4 col-xs-12'>
                    <label for='invoice-description'>Descrição da Fatura</label>
                    <input name='invoice_description' value='{{$project->invoice_description}}' maxlength='13' type='text' class='form-control' id='invoice-description' placeholder='Descrição da fatura'>
                </div>
                <div class='form-group col-4 col-xs-12'>
                    <label for='url-redirection'>Url Redirecionamento</label>
                    <input name='url-redirection' value='{{$project->url_finish}}' maxlength='13' type='text' class='form-control' id='url-redirection' placeholder='Descrição da fatura'>
                </div>
                <div class='form-group col-4 col-xs-12'>
                    <label for='company'>Empresas</label>
                    <select>
                       @foreach($companies as $company)
                            <option value='{{Hashids::encode($company->id)}}'>{{$company->fantasy_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class='row'>
                <div class='form-group col-4 col-xs-12'>
                    <label for='quantity-installment_amount'>Quantidade de parcelas (cartão de crédito)</label>
                    <select class='installment_amount' name='installment_amount' class='form-control'>
                        @for($x=1;$x <= 12; $x++)
                            <option value='{{$x}}' {{$project->installments_amount == $x ? 'selected' : ''}}>{{$x}}</option>
                        @endfor
                    </select>
                </div>
                <div class='col-4 col-xs-12'>
                    <label for="parcelas_sem_juros">Quantidade de parcelas sem juros</label>
                    <select class='parcelas-juros'>
                        @for($x=1; $x <=12; $x++)
                            <option value='{{$x}}' {{$project->installments_interest_free == $x ? 'selected' : ''}}>{{$x}}</option>
                        @endfor
                    </select>
                    <span id='error-juros' class='text-danger' style='display: none'>A quantidade de parcelas sem juros deve ser menor ou igual que a quantidade de parcelas</span>
                </div>
                <div class='col-4 col-xs-12'>
                    <label for="parcelas_sem_juros">Boleto no checkout</label>
                    <select>
                        <option>Sim</option>
                        <option>Não</option>
                    </select>
                </div>
            </div>
        </div>
        <div>
            <label for='name'>Selecione uma imagem para pagina do checkout e para emails</label>
            <div class='col-lg-4 row'>
                <div class='d-flex flex-column' id='div-img-project' style='position: relative;'>
                    <input name='photo-logo' type='file' class='form-control' id='photo-logo-email' style='display:none;'>
                    <img id='image-logo-email' alt='Selecione a foto do projeto' src='{{asset('modules/global/assets/img/projeto.png')}}' style='max-height: 200px; max-width: 300px;'>
                    <input type='hidden' name='logo_x1'><input type='hidden' name='logo_y1'>
                    <input type='hidden' name='logo_w'><input type='hidden' name='logo_h'>
                    <p class='info mt-5' style='font-size: 10px;'>
                        <i class='icon wb-info-circle' aria-hidden='true'></i> A imagem escolhida deve estar no formato JPG, JPEG ou PNG.
                        <br> Dimensões ideais: 300 x 300 pixels.
                    </p>
                </div>
                <div class='container-image' style='display:none;'>
                    Selecione o tamanho da imagem
                    <div class='form-group'>
                        <select name='ratioImage' id='ratioImage'>
                            <option value='1:1' selected>quadrado</option>
                            <option value='4:3'> 4:3</option>
                            <option value='25:9'> 25:9</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="margin-top: 30px">
                <div class="form-group" style="width:100%">
                    <button id="bt-update-project" type="button" class="btn btn-success">Atualizar dados do projeto</button>
                    <button id="bt-delete-project" type="button" class="btn btn-danger" style="float: right" data-toggle='modal' data-target='#modal_excluir'>Deletar projeto</button>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
    var p = $("#previewimage");
    $("#photoProject").on('change', function () {
        var imageReader = new FileReader();
        imageReader.readAsDataURL(document.getElementById("photoProject").files[0]);

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

                $('input[name="photo_x1"]').val(x1);
                $('input[name="photo_y1"]').val(y1);
                $('input[name="photo_w"]').val(x2 - x1);
                $('input[name="photo_h"]').val(y2 - y1);

                $('#previewimage').imgAreaSelect({
                    x1: x1, y1: y1, x2: x2, y2: y2,
                    aspectRatio: '1:1',
                    handles: true,
                    imageHeight: this.naturalHeight,
                    imageWidth: this.naturalWidth,
                    onSelectEnd: function (img, selection) {
                        $('input[name="photo_x1"]').val(selection.x1);
                        $('input[name="photo_y1"]').val(selection.y1);
                        $('input[name="photo_w"]').val(selection.width);
                        $('input[name="photo_h"]').val(selection.height);
                    }
                });
            })
        };
    });
</script>
