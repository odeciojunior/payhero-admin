<form id='form-register-link'>
    @csrf
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <label for="link-affiliate">Link</label>
                <div class="input-group mb-3">
                    <input name="link-affiliate" type="text" id='link-affiliate' class="form-control" placeholder="https:\\" maxlength='254' aria-describedby="link-affiliate">
                </div>
            </div>
        </div>
    </div>
</form>
