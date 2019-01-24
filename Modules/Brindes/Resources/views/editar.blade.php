<form id="editar_brinde" method="post" action="/brindes/editarbrinde"  enctype="multipart/form-data">
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

                    <div class="form-group col-xl-12">
                        <label for="foto_editar_brinde">Foto do brinde</label>
                        <input name="foto" type="file" class="form-control" id="foto_editar_brinde">
                        <img src="{!! url(\Modules\Core\Helpers\CaminhoArquivosHelper::CAMINHO_BRINDES_FOTO.$brinde->foto)!!}" style="margin-top: 20px; height: 200px">
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="tipo_brinde">Tipo de brinde</label>
                        <select name="tipo_brinde" type="text" class="form-control" id="tipo_brinde">
                            <option value="">Selecione</option>
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
