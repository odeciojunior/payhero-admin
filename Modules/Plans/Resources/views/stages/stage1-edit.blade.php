<div class="informations-edit">
    <div class="box-breadcrumbs">
        <div class="d-flex" style="justify-content: space-between !important;">
            <div class="d-flex align-items-center">
                <div class="icon mr-15"><img src="/modules/global/img/icon-info-plans-c.svg" alt="Icon Informations"></div>
                <div class="title">Informações do plano</div>
            </div>
            <button class="btn btn-edit" id="btn-edit-informations-plan">
                <img src="/modules/global/img/icon-edit.svg" alt="Icon Edit">
            </button>
        </div>
    </div>

    <div class="informations-data">
        <div class="row mb-20">
            <div class="col-sm-6">
                <label for="name">Nome</label>
                <input type="text" class="form-control" id="name" readonly>
            </div>
            <div class="col-sm-6">
                <label for="price">Preço de venda</label>
                <input type="text" class="form-control" id="price" readonly>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <label for="description">Descrição</label>
                <input type="text" class="form-control" id="description" readonly>
            </div>
        </div>
    </div>
</div>

<div class="line"></div>

<div class="products-edit">
    <div>
        <div class="d-flex" style="justify-content: space-between !important;">
            <div class="d-flex align-items-center">
                <div class="icon mr-15"><img src="/modules/global/img/icon-products-plans.svg" alt="Icon Informations"></div>
                <div class="title">Produtos no plano <span></span></div>
            </div>
            <button class="btn btn-edit" id="btn-edit-products-plan">
                <img src="/modules/global/img/icon-edit.svg" alt="Icon Edit">
            </button>
        </div>
    </div>

    <div class="box-products products-data">
        {{-- js carrega --}}
    </div>
</div>

<div class="line"></div>

<div class="review-edit">
    <div>
        <div class="d-flex" style="justify-content: space-between !important;">
            <div class="d-flex align-items-center">
                <div class="icon mr-15"><img src="/modules/global/img/icon-review-plans-c.svg" alt="Icon Informations"></div>
                <div class="title">Revisão geral</div>
            </div>
        </div>
    </div>

    <div class="review-data" style="margin-top: 24px;">
        <div class="d-flex justify-content-between" style="margin-bottom: 24px;">
            <div class="price-plan">
                <small>Preço de venda</small>
                <p class="font-weight-bold m-0" style="line-height: 100%;"></p>
            </div>
            <div class="costs-plan">
                <small>Seu custo</small>
                <p class="font-weight-bold m-0" style="line-height: 100%;"></p>
            </div>
            <div class="tax-plan">
                <small>Taxas est.</small>
                <p class="font-weight-bold m-0" style="line-height: 100%;"></p>
            </div>
            <div class="comission-plan">
                <small>Comissão aprox.</small>
                <p class="font-weight-bold m-0" style="line-height: 100%;"></p>
            </div>
            <div class="profit-plan">
                <small>Lucro aprox.</small>
                <p class="font-weight-bold m-0" style="line-height: 100%; color: #41DC8F;"></p>
            </div>
        </div>
        <div class="text-center description-tax">
            <p class="m-0" style="line-height: 14px; font-size: 11px;">Simulação considerando compras à vista com taxa de <span></span> % + 1,00 (30D).</p>
            <p class="font-weight-bold m-0" style="line-height: 14px; font-size: 11px;">Valor estimado sujeito à mudanças de acordo com as condições de pagamento.</p>
        </div>
    </div>
</div>
