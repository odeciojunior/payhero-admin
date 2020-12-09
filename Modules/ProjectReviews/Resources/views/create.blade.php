<form id='form_add_review' method="post" action="#" style="display:none">
    @csrf
    <div style="width:100%">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class='col-md-4 col-sm-12'>
                        <input name='photo' type='file' class='form-control' id='review-photo'
                               style="display:none">
                        <label for='preview-review-photo'>Selecione a foto</label>
                        <br>
                        <img id="preview-review-photo" alt='Selecione a foto do review' class='img-fluid mb-sm-2'
                             src="{{asset('modules/global/img/projeto.png')}}" style="cursor:pointer;">
                        <br>
                        <input type='hidden' name='photo_x1'/>
                        <input type='hidden' name='photo_y1'/>
                        <input type='hidden' name='photo_w'/>
                        <input type='hidden' name='photo_h'/>
                        <input type='hidden' name='photo'/>
                    </div>
                    <div class="col-12 col-sm-8">
                        <div class='form-group col-12 mb-20'>
                            <label for="link">Nome</label>
                            <div class="d-flex input-group">
                                <input type="text" class="form-control" name="name" id="add_name"
                                       maxlength="255" placeholder="Informe o nome">
                            </div>
                        </div>

                        <div class='form-group col-12 mb-20'>
                            <label for="add_description_review">Descrição</label>
                            <div class="d-flex input-group">
                                <textarea type="text" class="form-control" name="description"
                                          id="add_description_review" maxlength="255"
                                          placeholder="Digite a descrição"></textarea>
                            </div>
                        </div>

                        <div class='form-group col-12 col-sm-6 mb-20'>
                            <label for="link">Classificação</label>
                            <div id="review_add_stars" style="font-size: 25px;"></div>
                        </div>

                        <div class='form-group col-12 mb-20'>
                            <label for="link">Mostrar nos planos</label>
                            <select name="apply_on_plans[]" id="add_review_apply_on_plans" class="form-control"
                                    style='width:100%'
                                    data-plugin="select2" multiple='multiple'> </select>
                        </div>

                        <div class='form-group col-12 col-sm-6 mb-20'>
                            <label for="link">Status</label>
                            <select name="active_flag" id="add_active_flag" class='form-control'>
                                <option value='1' selected='selected'>Ativo</option>
                                <option value='0'>Inativo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
