<form id="editar_brinde" method="post" action="/brindes/editarbrinde" enctype="multipart/form-data">
    @csrf
    <input type="hidden" value="{!! $brinde->id !!}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="titulo">Título</label>
                        <input value="{!! $brinde->titulo != '' ? $brinde->titulo : '' !!}" name="titulo" type="text" class="form-control" id="titulo" placeholder="Título">
                    </div>

                    <div class="form-group col-xl-12">
                        <label for="descricao">Descrição</label>
                        <input value="{!! $brinde->descricao != '' ? $brinde->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-12">
                        <label for="selecionar_foto_brinde_editar">Foto do brinde</label><br>
                        <input type="button" id="selecionar_foto_brinde_editar" class="btn btn-default" value="Alterar foto do brinde">
                        <input name="foto_brinde_editar" type="file" class="form-control" id="foto_brinde_editar" accept="image/*" style="display:none">
                        <div  style="margin: 20px 0 0 30px;">
                            <img id="previewimage_brinde_editar" src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO.$brinde->foto)!!}?dummy={!! uniqid() !!}" alt="Selecione a foto do brinde" style="max-height: 250px; max-width: 350px;"/>
                        </div>
                        <input type="hidden" name="foto_brinde_editar_x1"/>
                        <input type="hidden" name="foto_brinde_editar_y1"/>
                        <input type="hidden" name="foto_brinde_editar_w"/>
                        <input type="hidden" name="foto_brinde_editar_h"/>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="tipo_brinde">Tipo de brinde</label>
                        <select name="tipo_brinde" type="text" class="form-control" id="tipo_brinde">
                            @foreach($tipo_brindes as $tipo_brinde)
                                <option value="{{ $tipo_brinde['id'] }}" {!! ($brinde->tipo_brinde == $tipo_brinde['id']) ? 'selected' : '' !!}>{{ $tipo_brinde['descricao'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="div_input_arquivo" class="form-group col-xl-12" style="display: none">
                        <label for="link">Arquivo</label>
                        <input name="link" type="file" class="form-control" id="link">
                    </div>

                    <div id="div_input_link" class="form-group col-xl-12" style="display: none">
                        <label for="link">Link</label>
                        <input value="{!! $brinde->link != '' ? $brinde->link : '' !!}" name="link" type="text" class="form-control" id="link" placeholder="Link">
                    </div>

                </div>

            </div>
        </div>
    </div>
</form>
