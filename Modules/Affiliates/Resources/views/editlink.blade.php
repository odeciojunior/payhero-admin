<form id='form-update-link'>
    @csrf
    <div class="container-fluid">
        <div class="panel"
             data-plugin="matchHeight">
            <input type="hidden"
                   name="link-id"
                   class="link-id">
            <div style="width:100%">
                <div class="row form-group">
                    <div class="col-md-12">
                        <label for="link-affiliate">Descrição Link</label>
                        <div class="input-group mb-3">
                            <input name="link-affiliate-name"
                                   type="text"
                                   id='link-affiliate-name-update'
                                   class="form-control"
                                   placeholder="Descrição do Link"
                                   maxlength='254'
                                   aria-describedby="link-affiliate-name">
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <label for="link-affiliate">Link</label>
                        <div class="input-group mb-3">
                            <input name="link-affiliate"
                                   type="text"
                                   id='link-affiliate-update'
                                   class="form-control"
                                   placeholder="https://"
                                   maxlength='254'
                                   aria-describedby="link-affiliate">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
