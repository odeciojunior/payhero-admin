<div class="modal-body">

    <form id="form-add-domain" method="post">
        @csrf
        <div class="row">
            <div class="form-group">
                <label for="name">Domínio</label>
                <input name="name" type="text" class="input-pad fildName"
                       id="name" placeholder="seudominio.com">
            </div>
        </div>
    </form>

</div>

