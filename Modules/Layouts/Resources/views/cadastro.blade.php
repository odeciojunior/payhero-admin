{{--  @extends("layouts.master")

@section('content')

  <!-- Page -->
  <div class="page">

        <div class="page-header">
            <h1 class="page-title">Cadastrar novo layout</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{ route('layouts') }}">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i>
                    Voltar
                </a>
            </div>
        </div>  --}}

        {{--  <div class="row">  --}}
            <div style="text-align: center">
                <h4> Cadastrar layout </h4>
            </div>

            <div class="row">
                <div class="col-6">
                    <form id="form-cadastro" method="post" action="/layouts/cadastrarlayout" enctype='multipart/form-data'>
                        @csrf
                        <div class="panel" data-plugin="matchHeight">
                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="descricao">Descrição</label>
                                    <input name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="logo">Logo</label>
                                    <input name="logo" id="logo" type="file" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="estilo">Estilo</label>
                                    <select name="estilo" id="estilo" class="form-control" required>
                                        <option value="">Selecione</option>
                                        <option value="Padrao">Padrão</option>
                                        <option value="Backgoud Multi Camada">Background multi-camadas</option>
                                    </select>
                                </div>
                            </div>
                            <div id="cores_multi_camada" style="display: none">
                                <div class="row">
                                    <div class="form-group col-xl-12">
                                        <label for="cor1">Cor 1</label><br>
                                        <input id="cor1" name="cor1-multi-camadas" type="text" style="width: 100%" class="asColorpicker form-control colorInputUi-input" data-plugin="asColorPicker" data-mode="simple" value="#ff666b">
                                        <a href="#" class="colorInputUi-clear">
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-xl-12">
                                        <label for="cor2">Cor 2</label><br>
                                        <input id="cor2" name="cor2-multi-camadas" type="text" style="width: 100%" class="asColorpicker form-control colorInputUi-input" data-plugin="asColorPicker" data-mode="simple" value="#ff666b">
                                        <a href="#" class="colorInputUi-clear">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div id="cores_padrao" style="display: none">
                                <div class="row">
                                    <div class="form-group col-xl-12">
                                        <label for="cor1-padrao">Background</label>
                                        <select name="cor1-padrao" id="cor1-padrao" class="form-control" id="cor1-padrao">
                                            <option value="">Selecione</option>
                                            <option value="bg-azul">Azul 1</option>
                                            <option value="bg-azul2">Azul 2</option>
                                            <option value="bg-vermelho">Vermelho</option>
                                            <option value="bg-vermelho2">Vermelho2</option>
                                            <option value="bg-roxo">Roxo</option>
                                            <option value="bg-roxo2">Roxo2</option>
                                            <option value="bg-verde">Verde</option>
                                            <option value="bg-verde2">Verde2</option>
                                            <option value="bg-pink">Rosa</option>
                                            <option value="bg-laranja">Laranja</option>
                                            <option value="bg-cinza">Cinza</option>
                                            <option value="bg-cinzaescuro">Cinza escuro</option>
                                            <option value="bg-preto">Preto</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-12">
                                    <label for="botao">Botões</label>
                                    <select name="botao" id="botoes" class="form-control" id="botao" required>
                                        <option value="">Selecione</option>
                                        <option value="btn-laranja">Laranja</option>
                                        <option value="btn-roxo">Roxo</option>
                                        <option value="btn-vermelho">Vermelho</option>
                                        <option value="btn-azul">Azul</option>
                                    </select>
                                </div>
                            </div>
                            {{--  <div class="row" style="margin-top: 30px">
                                <div class="form-group col-xl-12">
                                    <input type="submit" class="form-control btn btn-primary" value="Salvar">
                                </div>
                            </div>  --}}
                        </div>
                    </form>
                </div>

                <div class="col-6">
                    <div id="view_checkout" class="card card-shadow">
                        <iframe id="view_checkout" name="iframe-preview"src="#" style="height: 650px"></iframe>
                    </div>
                </div>
            </div>

            <form id="form-preview" target="iframe-preview" action="/layouts/preview" method="POST" enctype='multipart/form-data' style="display: none">
                <input id="preview_logo" type="hidden" name="tipo" value="cadastrar"/>
                <input id="preview_estilo" type="hidden" name="estilo"/>
                <input id="preview_cor1" type="hidden" name="cor1"/>
                <input id="preview_cor2" type="hidden" name="cor2"/>
                <input id="preview_botoes" type="hidden" name="botoes"/>
                {{ csrf_field() }}
                <input type="submit">
            </form>

        {{--  </div>  --}}
    {{--</div>

  <script>

    $(document).ready( function(){

        atualizarPreView();

        function atualizarPreView(){

            $('#form-preview').submit();
        }

        $('#estilo').on('change',function(){

            $('#cores_multi_camada').hide();
            $('#cores_padrao').hide();

            if($(this).val() == 'Backgoud Multi Camada'){
                $('#cor1-padrao').prop('required', false);
                $('#cor1').prop('required', true);
                $('#cor2').prop('required', true);
                $('#cores_multi_camada').show();
            }
            else if($(this).val() == 'Padrao'){
                $('#cor1-padrao').prop('required', true);
                $('#cor1').prop('required', false);
                $('#cor2').prop('required', false);
                $('#cores_padrao').show();
            }

            $('#preview_estilo').val($(this).val());

            atualizarPreView();
        });

        $('#botoes').on('change',function(){
            $('#preview_botoes').val($(this).val());
            atualizarPreView();
        });

        $('#cor1').on('blur',function(){
            $('#preview_cor1').val($(this).val());
            atualizarPreView();
        });

        $('#cor1-padrao').on('blur', function(){
            $('#preview_cor1').val($(this).val());
            atualizarPreView();
        });

        $('#cor2').on('blur',function(){
            $('#preview_cor2').val($(this).val());
            atualizarPreView();
        });

        $('#logo').on('change', function(){
            var input = $(this).clone();
            $('#form-preview').append(input);
            atualizarPreView();
        });
    });

  </script>


@endsection
  --}}
