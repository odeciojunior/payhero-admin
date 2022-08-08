<form id="form_config_review"
      method="post"
      action="#">
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row">
            <div class='form-group col-12 mb-20'>
                <label for="header_config">Tipo do Ícone</label>
                <div class="row input-group">
                    <div class="col-4">
                        <div class="radio-custom radio-primary">
                            <input type="radio"
                                   id="starIcon"
                                   name="reviews_config_icon_type"
                                   value="star">
                            <label for="starIcon">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star-half-o"></i>
                                <i class="fa fa-star-o"></i>
                                <i class="fa fa-star-o"></i>
                            </label>
                        </div>
                    </div>

                    <div class="col-4">
                        <div class="radio-custom radio-primary">
                            <input type="radio"
                                   id="heartIcon"
                                   name="reviews_config_icon_type"
                                   value="heart">
                            <label for="heartIcon">
                                <i class="fa fa-heart"></i>
                                <i class="fa fa-heart"></i>
                                <i class="fa fa-heart"></i>
                                <i class="fa fa-heart-o"></i>
                                <i class="fa fa-heart-o"></i>
                            </label>
                        </div>
                    </div>

                    <div class='col-12 w-100 info pt-5'
                         style='font-size: 10px;'>
                        <i class='icon wb-info-circle'
                           aria-hidden='true'></i>
                        Tipo do ícone da classificação.
                    </div>
                </div>
            </div>

            <div class="form-group col-12 pt-4">
                <label class="control-label">Cor do Ícone</label>
                <input type="hidden"
                       name="reviews_config_icon_color"
                       value="#f8e34a" />

                <div class="color-options">
                    <div data-color="#f8ce1c"
                         style="background: #f8ce1c;"
                         title="Amarelo">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#f78d1e"
                         style="background: #f78d1e;"
                         title="Laranja">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#ff0000"
                         style="background: #ff0000;"
                         title="Vermelho">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#ff3366"
                         style="background: #ff3366;"
                         title="Rosa">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#111111"
                         style="background: #111111;"
                         title="Preto">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#555555"
                         style="background: #555555;"
                         title="Cinza">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#aaaaaa"
                         style="background: #aaaaaa;"
                         title="Prata">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#d4af37"
                         style="background: #d4af37;"
                         title="Dourado">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#3e8ef7"
                         style="background: #3e8ef7;"
                         title="Azul Claro">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#0bb2d4"
                         style="background: #0bb2d4;"
                         title="Azul Escuro">
                        <i class="fa fa-check text-white"></i>
                    </div>

                    <div data-color="#11c26d"
                         style="background: #11c26d;"
                         title="Verde">
                        <i class="fa fa-check text-white"></i>
                    </div>
                </div>

                <p class='info pt-5'
                   style='font-size: 10px;'>
                    <i class='icon wb-info-circle'
                       aria-hidden='true'></i>
                    Cor do ícone da classificação.
                </p>
            </div>
        </div>
    </div>
</form>
