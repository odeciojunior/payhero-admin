<form id="form-update-affiliate"
      method="PUT">
    @csrf
    @method('PUT')
    <input type="hidden"
           value=""
           class="affiliate-id"
           name="affiliateId">
    <div class="row">
        <div class="form-group col-xl-12">
            <label>Nome</label>
            <input type="text"
                   class="form-control affiliate-name"
                   readonly>
        </div>
    </div>
    {{-- <div class="row">
        <div class="form-group col-xl-12">
            <label>Email</label>
            <input type="text" class="form-control affiliate-email" readonly>
        </div>
    </div> --}}
    <div class="row">
        <div class="form-group col-xl-6">
            <label for="status_enum">Status</label>
            <select name="status_enum"
                    class="sirius-select affiliate-status"
                    required>
                <option value="1">Ativo</option>
                <option value="2">Desativado</option>
            </select>
        </div>
        <div class="form-group col-xl-6">
            <label for="percentage">Porcentagem</label>
            <input name="percentage"
                   type="text"
                   class="form-control affiliate-percentage"
                   data-mask="0#"
                   maxlength="2"
                   style="height: 50px !important;border-radius: 8px !important;">
        </div>
    </div>
</form>
