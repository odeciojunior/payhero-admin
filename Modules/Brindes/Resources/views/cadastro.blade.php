<div style="text-align: center">
    <h4>Cadastrar brinde</h4>
</div>

<form id="cadastrar_brinde" method="post" action="/brindes/cadastrarbrinde"  enctype="multipart/form-data">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="titulo">Título</label>
                        <input name="titulo" type="text" class="form-control" id="titulo_brinde" placeholder="Título">
                    </div>

                    <div class="form-group col-xl-6">
                        <label for="descricao">Descrição</label>
                        <input name="descricao" type="text" class="form-control" id="descricao_brinde" placeholder="Descrição">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-12">
                        <label for="selecionar_foto_brinde_cadastrar">Foto do brinde</label><br>
                        <input type="button" id="selecionar_foto_brinde_cadastrar" class="btn btn-default" value="Selecionar foto do brinde">
                        <input name="foto_brinde_cadastrar" type="file" class="form-control" id="foto_brinde_cadastrar" accept="image/*" style="display:none">
                        <div  style="margin: 20px 0 0 30px;">
                            <img id="previewimage_brinde_cadastrar" alt="Selecione a foto do brinde" style="max-height: 250px; max-width: 350px;"/>
                        </div>
                        <input type="hidden" name="foto_brinde_cadastrar_x1"/>
                        <input type="hidden" name="foto_brinde_cadastrar_y1"/>
                        <input type="hidden" name="foto_brinde_cadastrar_w"/>
                        <input type="hidden" name="foto_brinde_cadastrar_h"/>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="tipo_brinde">Tipo do brinde</label>
                        <select name="tipo_brinde" type="text" class="form-control" id="tipo_brinde">
                            <option value="" selected>Selecione</option>
                            @foreach($tipo_brindes as $tipo_brinde)
                                <option value="{{ $tipo_brinde['id'] }}">{{ $tipo_brinde['descricao'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="div_input_arquivo" class="form-group col-xl-6" style="display: none">
                        <label for="link">Arquivo</label>
                        <input name="link" type="file" class="form-control" id="link">
                    </div>

                    <div id="div_input_link" class="form-group col-xl-6" style="display: none">
                        <label for="link">Link</label>
                        <input name="link" type="text" class="form-control" id="link" placeholder="Link">
                    </div>

                </div>

            </div>
        </div>
    </div>
</form>


