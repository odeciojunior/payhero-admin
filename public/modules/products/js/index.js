$(document).ready(function () {
    let regexp = /http(s?):\/\/[\w.-]+\/products\/\w{15}\/edit/;
    let lastPage = document.referrer;
    if (!lastPage.match(regexp)) {
        localStorage.clear();
    }

    var pageCurrent;
    let badgeList = {
        1: "#2E85EC",
        2: "#5EE2A1",
        3: "#F41C1C",
    };
    let statusList = {
        1: "Em análise",
        2: "Aprovado",
        3: "Recusado",
    };

    //VERIFICA SE HA FILTRO E PEGA O TIPO 
    let storeTypeProduct = () => {
        if(localStorage.getItem("filtersApplied")){
            let getProductValue = JSON.parse(localStorage.getItem("filtersApplied"));
            return getProductValue;

        }else{
            return 0;
        }
    }

    //REGASTA O FILTRO E O APLICA
    function handleLocalStorage() {
        if (localStorage.getItem('filtersApplied') != null) {
            let parseLocalStorage = JSON.parse(localStorage.getItem('filtersApplied'));

            $("#type-products").val(parseLocalStorage.getTypeProducts).trigger("change");

            $("#select-projects").val(parseLocalStorage.getProject);
            $("#name").val(parseLocalStorage.getName);
            $("#btn-filtro").trigger("click");
        }
    }

    function getProjects() {
        loadingOnScreen();
        $.ajax({
            method: "GET",
            url: "api/projects?select=true",
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadingOnScreenRemove();
                errorAjaxResponse(response);
            },
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    if (verifyAccountFrozen()) {
                        $("#div-create").hide();
                    } else {
                        $("#div-create").show();
                    }
                    //talvez verificar se ha filtro aqui
                    updateProducts();

                } else {
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                    $("#div-create").hide();
                }

                loadingOnScreenRemove();
            },
        });
    }
    
    function getTypeProducts() {
        $.ajax({
            method: "GET",
            url: "/api/projects?select=true",
            data: {
                status: "active",
            },
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                errorAjaxResponse(response);
            },
            success: function (response) {
                if (response.data) {
                    $("#select-projects").html("");
                    $.each(response.data, function (index, value) {
                        if (value.shopify) {
                            $("#select-projects").append(
                                $("<option>", {
                                    value: value.id,
                                    text: value.name,
                                })
                            );
                        }
                    });
                    $("#select-projects").prepend(
                        '<option value="0" id="opcao-vazia" selected></option>'
                    );
                }
                handleLocalStorage();
            },
        });
    }

    function updateProducts(link = null) {
        pageCurrent = link

        //RETONA O FILTRO SE HOUVER SENAO RETORNA NULL
        let existFilters = () => {
            if(localStorage.getItem('filtersApplied') != null){
                let getFilters = JSON.parse(localStorage.getItem('filtersApplied'))
                return getFilters;
            }else {
                return null;
            };
        };

        //GUARDA QUALQUER PAGINA DEPOIS DA 1
        if(link != null){
            let getPage = {
                atualPage: pageCurrent
            };
            localStorage.setItem("page", JSON.stringify(getPage));
        }
        
        //RESGATA PAGINA, SE HOUVER FILTRO SET PAGINA PARA NULL
        if(localStorage.getItem("page") != null){
            let parsePage = JSON.parse(localStorage.getItem("page"));

            if(existFilters() != null && existFilters().getName != ""){
                if(localStorage.getItem("page") != null){
                    localStorage.setItem("page", JSON.stringify(parsePage));
                }
            }else{
                pageCurrent = parsePage.atualPage;

            }
            parsePage = JSON.parse(localStorage.getItem("page"));
            pageCurrent = parsePage.atualPage;
        }

        link = pageCurrent
        loadOnAny(".page-content");

        let type = existFilters() != null ? existFilters().getTypeProducts : $("#type-products").val();
        let name = existFilters() != null ? existFilters().getName : $("#name").val();
        let project = existFilters() != null ? existFilters().getProject : $("#select-projects").val();

        if (link == null) {
            link = "/api/products?shopify=" + type + "&project=" + project + "&name=" + name;
            
        } else {
            link = "/api/products" + link + "&shopify=" + type + "&project=" + project + "&name=" + name;
        }

        $.ajax({
            method: "GET",
            url: link,
            dataType: "json",
            headers: {
                Authorization: $('meta[name="access-token"]').attr("content"),
                Accept: "application/json",
            },
            error: function error(response) {
                loadOnAny(".page-content", true);
                errorAjaxResponse(response);
            },
            success: function (response) {
                if (!isEmpty(response.data)) {
                    $(".products-is-empty").hide();
                    $("#data-table-products").html("");
                    let dados = "";

                    $.each(response.data, function (index, value) {
                        shopifyProduct = value.id != value.id_view;
                        dados = `
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                            <div class="card shadow mb-20 mx-0" style="flex: 1 1 100%">
                                <div style="margin: 10px 10px 0px 0px;position: absolute;right: 0px;">
                                    <button type="button" class="menu_product" data-id="${value.id}">&#8942;</button>
                                </div>
                                <img class="card-img-top product-image" src="${value.image}" alt="Imagem do produto" data-link="/products/${value.id}/edit">
                                ${value.type_enum == 2
                                ? `<span class="ribbon-inner ribbon-primary" style="background-color:${badgeList[value.status_enum]};border-radius: 0px 10px 10px 0px;"> ${statusList[value.status_enum]}
                                        </span>`
                                : ""
                            }
                                <hr class="mt-0">
                                <div class="card-body py-0">
                                    <h5 class="card-title">${value.name}</h5>
                                    <h5 class="card-description">${value.description}</h5>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <p class="text-muted card-text sm">Criado em ${value.created_at}</p>
                                </div>
                            </div>
                            <div class="menu_product_tooltip" data-id="${value.id}">
                                <a id="bt_editar" href="/products/${value.id}/edit" class="mx-20"><span class="o-edit-1 mr-10" />Editar produto</a>
                                ${shopifyProduct == false
                                ? `
                                    <hr class="my-5">
                                    <a href="#" class="mx-20 bt_excluir" data-id="${value.id}"><span class="o-bin-1 mr-10" />Excluir produto</a>
                                `
                                : ""
                            }
                            </div>
                        </div>
                        `;
                        $("#data-table-products").append(dados);
                    });

                    $(".product-image").off("click");
                    $(".product-image").on("click", function () {
                        window.location.href = $(this).attr('data-link');
                    });

                    function closeTooltips(except = "") {
                        $('.menu_product_tooltip[style*="display: block"]').each(function (_, tooltip) {
                            if (except[0] == tooltip) {
                                return;
                            }

                            tooltip.style.display = 'none';
                        });
                    }

                    $('.menu_product').off('click');
                    $('.menu_product').on('click', function () {
                        var tooltip = $(`.menu_product_tooltip[data-id="${this.dataset.id}"]`)

                        closeTooltips(tooltip);

                        tooltip.toggle();
                    });

                    $('.menu_product').off('focusout');
                    $('.menu_product').on('focusout', function (event) {
                        if ($(event.relatedTarget).hasClass('menu_product')) {
                            return;
                        }

                        setTimeout(() => closeTooltips(), 200);
                    });

                    $('.bt_excluir').off('click');
                    $('.bt_excluir').on('click', function (event) {
                        event.preventDefault();

                        $(".bt_excluir_modal").attr('data-id', this.dataset.id);

                        $("#modal-delete").modal();
                    });

                    $('.bt_excluir_modal').off('click');
                    $('.bt_excluir_modal').on('click', function (event) {
                        event.preventDefault();

                        loadingOnScreen();

                        $.ajax({
                            method: 'DELETE',
                            url: '/api/products/' + this.dataset.id,
                            dataType: "json",
                            headers: {
                                'Authorization': $('meta[name="access-token"]').attr('content'),
                                'Accept': 'application/json',
                            },
                            error: function (response) {
                                errorAjaxResponse(response);

                                $(".bt_excluir_modal").attr('data-id', "");
                                loadingOnScreenRemove();
                            },
                            success: function success(response) {
                                alertCustom('success', response.message);

                                window.location = "/products";
                            }
                        });
                    });

                    $("img").on("error", function () {
                        $(this).attr(
                            "src",
                            "https://cloudfox-files.s3.amazonaws.com/produto.svg"
                        );
                    });

                    pagination(response, "products", updateProducts);

                    $(".products-is-empty").hide();
                } else {

                    if(localStorage.getItem('filtersApplied') != null && localStorage.getItem('page') != null){
                        localStorage.removeItem('page');
                        $("#btn-filtro").trigger("click");

                    }else {
                        $("#data-table-products, #pagination-products").html("");
                        $(".products-is-empty").show();
                    }
                }
                setTimeout(() => {
                    loadOnAny(".page-content", true);
                }, 2000);
            },
        });
    }

    //PRECIONAR ENTER ATIVA O "APLICAR FILTRO"
    $(document).on("keypress", function (e) {
        if (e.key == "Enter") {
            $("#btn-filtro").trigger("click");
        }
    });

    //EXIBI OU ESCONDE O CAMPO PROJETO
    $("#type-products").on("change", function () {
        const type = $(this).val();
        if (type === "1") {
            $('#projects-list').removeClass('d-none');
            $("#projects-list select").prop("disabled", false).removeClass("disabled");
            $("#opcao-vazia").remove();

        } else {
            $("#projects-list select").prop("disabled", true).addClass("disabled");
            $("#projects-list").addClass("d-none");
        }
    });

    //SE ALGUM CAMPO FOR ALTERADO ZERA A PAGINA E GUARDA O NOVO FILTRO
    $("#btn-filtro").on("click", function () {
        if(storeTypeProduct().getTypeProducts != $("#type-products").val() || storeTypeProduct().getName != $('#name').val() || storeTypeProduct().getProject != $('#select-projects').val()){

            if(localStorage.getItem("page") != null){
                let getPageStored = JSON.parse(localStorage.getItem("page"));
                getPageStored.atualPage = null;
                localStorage.setItem("page", JSON.stringify(getPageStored));
            }
        }

        //GUARDA O NOVO FILTRO
        let filtersApplied = {
            getTypeProducts: $("#type-products option:selected").val(),
            getProject: $("#select-projects option:selected").val(),
            getName: $("#name").val()
        };
        localStorage.setItem('filtersApplied', JSON.stringify(filtersApplied));
        updateProducts();
    });

    $('#new-product-button').on('click', function (event) {
        event.preventDefault();
        $('#new-product-modal').show();
    });

    getProjects();
    getTypeProducts();
    //updateProducts(); //Funcao de update chamda pela 2x
});