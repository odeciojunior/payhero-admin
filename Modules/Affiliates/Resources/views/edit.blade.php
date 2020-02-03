<form id="form-update-affiliate" method="PUT">
    @csrf
    @method('PUT')
    <input type="hidden" value="" class="affiliate-id" name="affiliateId">

    <div class="row">
        <div class="form-group col-xl-12">
            <label for="name">Nome</label>
            <input value="" name="name" type="text" class="form-control affiliate-name">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-12">
            <label for="name">Email</label>
            <input value="" name="name" type="text" class="form-control affiliate-email" >
        </div>
    </div>


    <div class="row">
        <div class="form-group col-xl-12">
            <label for="name">Empresa</label>
            <input value="" name="company" type="text" class="form-control affiliate-company">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label for="type">Status</label>
            <select name="type" class="form-control affiliate-status" required>
                <option value="1">Porcentagem</option>
                <option value="2">Valor</option>
                <option value="3">Valor</option>
                <option value="4">Valor</option>
            </select>
        </div>
        <div class="form-group col-xl-6">
            <label for="percentage">Porcentagem</label>
            <input value="" name="percentage" type="text" class="form-control affiliate-percentage" placeholder="Porcentagem">
        </div>
    </div>

</form>
