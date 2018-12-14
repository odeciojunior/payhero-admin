@extends("layouts.master")

@section('content')

  <!-- Page -->
<div class="page">

    <div class="page-header">
        <h1 class="page-title">Configuração do perfil</h1>
    </div>

    <div class="page-content container-fluid">
        <div class="panel pt-30 p-30" data-plugin="matchHeight">
            <div class="col-xl-12">
                <div class="example-wrap">
                    <div class="nav-tabs-horizontal" data-plugin="tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_usuario"
                                aria-controls="tab_usuario" role="tab">Usuário</a></li>
                        </ul>
                        <div class="tab-content pt-20">
                            <div class="tab-pane active" id="tab_usuario" role="tabpanel">

                                <form method="POST" action="{!! route('perfil.update') !!}" enctype="multipart/form-data">
                                    @csrf
                        
                                    <input type="hidden" name="id" value="{!! $user->id !!}">
                        
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Informações básicas</h3>
                                    </div>
                        
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="name">Nome</label>
                                            <input name="name" value="{!! $user->name !!}" type="text" class="form-control" id="name">
                                        </div>
                        
                                        <div class="form-group col-xl-6">
                                            <label for="email">Email</label>
                                            <input name="email" value="{!! $user->email !!}" type="text" class="form-control" id="email">
                                        </div>
                                    </div>
                        
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="cpf">CPF</label>
                                            <input name="cpf" value="{!! $user->cpf !!}" type="text" class="form-control" id="cpf">
                                        </div>
                        
                                        <div class="form-group col-xl-6">
                                            <label for="celular">Celular</label>
                                            <input name="celular" value="{!! $user->celular !!}" type="text" class="form-control" id="celular">
                                        </div>
                                    </div>
                        
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="telefone1">Telefone</label>
                                            <input name="telefone1" value="{!! $user->telefone1 !!}" type="text" class="form-control" id="telefone1">
                                        </div>
                        
                                        <div class="form-group col-xl-6">
                                            <label for="data_nascimento">Data de nascimento</label>
                                            <input name="data_nascimento" value="{!! $user->data_nascimento !!}" type="date" class="form-control" id="data_nascimento">
                                        </div>
                                    </div>
                        
                                    <div class="row">
                        
                                        <div class="form-group col-12">
                                            <label for="foto">Foto</label>
                                            <input name="foto" type="file" class="form-control" id="foto">
                                            <img src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_FOTO_USER.$user->foto)!!}" alt="" style="margin-top: 20px; max-height: 250px">
                                        </div>
                        
                                    </div>
                        
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Endereço</h3>
                                    </div>
                        
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="cep">CEP</label>
                                            <input name="cep" value="{!! $user->cep !!}" type="text" class="form-control" id="cep">
                                        </div>
                        
                                        <div class="form-group col-xl-6">
                                            <label for="pais">País</label>
                                            <input name="pais" value="{!! $user->pais !!}" type="text" class="form-control" id="pais">
                                        </div>
                                    </div>
                        
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="estado">Estado</label>
                                            <input name="estado" value="{!! $user->estado !!}" type="text" class="form-control" id="estado">
                                        </div>
                        
                                        <div class="form-group col-xl-6">
                                            <label for="cidade">Cidade</label>
                                            <input name="cidade" value="{!! $user->cidade !!}" type="text" class="form-control" id="cidade">
                                        </div>
                                    </div>
                        
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="bairro">Bairro</label>
                                            <input name="bairro" value="{!! $user->bairro !!}" type="text" class="form-control" id="bairro">
                                        </div>
                        
                                        <div class="form-group col-xl-6">
                                            <label for="logradouro">Rua</label>
                                            <input name="logradouro" value="{!! $user->logradouro !!}" type="text" class="form-control" id="logradouro">
                                        </div>
                                    </div>
                        
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            <label for="numero">Número</label>
                                            <input name="numero" value="{!! $user->numero !!}" type="text" class="form-control" id="numero">
                                        </div>
                        
                                        <div class="form-group col-xl-6">
                                            <label for="complemento">Complemento</label>
                                            <input name="complemento" value="{!! $user->complemento !!}" type="text" class="form-control" id="complemento">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group" style="margin-top: 30px">

                                        <input type="submit" class="form-control btn btn-success" value="Atualizar" style="width: 30%">

                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    });

</script>


@endsection

