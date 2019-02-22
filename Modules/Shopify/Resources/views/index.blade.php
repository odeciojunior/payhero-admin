@extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

    <div class="page-header">
        <h1 class="page-title">Integrações com shopify</h1>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight" style="min-height: 400px">
          <div class="row">
              <div class="col-12">
                  <button class="btn btn-primary"  data-toggle='modal' data-target='#modal_add_integracao' style="float:right">
                      Adicionar integração
                  </button>
              </div>      
          </div>

          @if(count($projetos) == 0)
            <div class="row" style="margin-top: 40px">
                <h4>Nenhuma integração encontrada</h4>
            </div>
          @else

            <div class="row" style="margin-top: 50px">
                @foreach($projetos as $projeto)
                  <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3">
                    <div class="card" style="border: 1px solid gray">
                        <a href='/projetos/projeto/{!! $projeto['id'] !!}'>
                            <img class="card-img-top img-fluid w-full" src="{!! '/'.Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_PROJETO.$projeto['foto'] !!}" alt="Imagem não encontrada" style="height: 180px;width: 90%; margin: 8px 0 8px 0">
                        </a>
                        <div class="card-block">
                          <a href='/projetos/projeto/{!! $projeto['id'] !!}'>
                              <div class="text-center">
                                  <h4 class="card-title">{!! $projeto['nome'] !!}</h4>
                              </div>
                          </a>
                          <hr>
                          <div class="text-center">
                              <button class="btn btn-success sincronizar" type="button" projeto="{!! $projeto['id'] !!}">Sincronizar dados</button>
                          </div>
                        </div>
                    </div>
                  </div>
                @endforeach
            </div>
          @endif

        <!-- Modal add integração -->
        <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_add_integracao" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="conteudo_modal_add">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" style="width: 100%; text-align:center">Adicionar nova integração com Shopify</h4>
                </div>
                <div class="modal-body" style="padding: 30px">
                    <form id='form_add_integracao' method="post" action="#">
                        @csrf
                        <div style="width:100%">
                            <div class="row">
                                <div class="col-12">
                                    <label for="token">Token</label>
                                    <input type="text" class="form-control" name="token" id="token" placeholder="Digite seu token">
                                </div>
                            </div>
                            <div class="row" style="margin-top:30px">
                                <div class="col-12">
                                    <label for="token">URL da sua loja no Shopify</label>
                                    <input type="text" class="form-control" name="url_loja" id="url_loja" placeholder="Digite a URL da sua loja">
                                </div>
                            </div>
                            <div class="row" style="margin-top:30px">
                                <div class="col-12">
                                    <label for="empresa">Selecione sua empresa</label>
                                    <select class="form-control" id="empresa" name="empresa">
                                        @foreach($empresas as $empresa)
                                        <option value="{!! $empresa['id'] !!}">{!! $empresa['nome_fantasia'] !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="margin-top:30px">
                                <div class="form-group col-12">
                                    <label for="selecionar_foto">Foto do projeto</label><br>
                                    <input type="button" id="selecionar_foto" class="btn btn-default" value="Selecionar foto do projeto">
                                    <input name="foto_projeto" type="file" class="form-control" id="foto" style="display:none">
                                    <div  style="margin: 20px 0 0 30px;">
                                        <img id="previewimage" alt="Selecione a foto do projeto" accept="image/*" style="max-height: 250px; max-width: 350px;"/>
                                    </div>
                                    <input type="hidden" name="foto_x1"/>
                                    <input type="hidden" name="foto_y1"/>
                                    <input type="hidden" name="foto_w"/>
                                    <input type="hidden" name="foto_h"/>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="bt_adicionar_integracao" type="button" class="btn btn-success" data-dismiss="modal">Salvar</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->

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
                        },
                        parent: $('#conteudo_modal_add')
                    });
                })
            };

        });

        $("#selecionar_foto").on("click", function(){
            $("#foto").click();
        });

        $("#bt_adicionar_integracao").on("click", function(){

            if($('#token').val() == '' || $('#url_loja').val() == '' || $('#foto_projeto').val() == '' || $('#empresa').val() == ''){
                alertPersonalizado('error','Dados informados inválidos');
                return false;
            }
            $('.loading').css("visibility", "visible");

            var form_data = new FormData(document.getElementById('form_add_integracao'));

            $.ajax({
                method: "POST",
                url: "/ferramentas/shopify/adicionarintegracao",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                cache: false,
                data: form_data,
                error: function(){
                    $('.loading').css("visibility", "hidden");
                    alertPersonalizado('error','Ocorreu algum erro');
                    $('#previewimage_brinde_cadastrar').imgAreaSelect({remove:true});
                },
                success: function(data){
                    $('.loading').css("visibility", "hidden");
                    if(data == 'Sucesso'){
                      alertPersonalizado('success','Integração adicionada!');
                      window.location.reload(true); 
                    }
                    else{
                      alertPersonalizado('error',data);
                    }
                    $('#previewimage_brinde_cadastrar').imgAreaSelect({remove:true});
                },
            });

        });

        $(".sincronizar").on("click", function(){

            var id_projeto = $(this).attr('projeto');

            $.ajax({
                method: "POST",
                url: "/ferramentas/shopify/sincronizarintegracao",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {projeto: id_projeto},
                error: function(){
                    alertPersonalizado('error','Ocorreu algum erro');
                },
                success: function(data){
                    if(data == 'Sucesso'){
                      alertPersonalizado('success','Dados do projeto sincronizados!');
                    }
                    else{
                      alertPersonalizado('error',data);
                    }
                },
            });

        });

        function alertPersonalizado(tipo, mensagem){

            swal({
                position: 'bottom',
                type: tipo,
                toast: 'true',
                title: mensagem,
                showConfirmButton: false,
                timer: 6000
            });
        }


    });

  </script>

@endsection

