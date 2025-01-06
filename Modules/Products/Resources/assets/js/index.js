jQuery(function () {
    $(".company-navbar").change(function () {
        if (verifyIfCompanyIsDefault($(this).val())) return;
        $("#type-products").find("option").not(":first").remove();
        showFiltersLoadingSkeleton();
        showProductsLoadingSkeleton();
        updateCompanyDefault().done(function (data1) {
            getCompaniesAndProjects().done(function (data2) {
                renderSiriusSelect("#type-products");
                $("#select-projects-1").find("option").remove();
                $("#select-projects-2").find("option").remove();
                $("#select-projects-3").find("option").remove();
                $("#projects-list select").prop("disabled", true).addClass("disabled");
                $("#projects-list, .box-projects").addClass("d-none");
                localStorage.removeItem("page");
                localStorage.removeItem("filtersApplied");
                getProjects("n");
            });
        });
    });

    showProductsLoadingSkeleton();

    let regexp = /http(s?):\/\/[\w.-]+\/products\/\w{15}\/edit/;
    let lastPage = document.referrer;
    if (!lastPage.match(regexp)) {
        localStorage.removeItem("filtersApplied");
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
    let appsList = {
        1: "Produtos Shopify",
        2: "Produtos Woocommerce",
        3: "Produtos Nuvemshop",
    };

    let storeTypeProduct = () => {
        if (localStorage.getItem("filtersApplied")) {
            let getProductValue = JSON.parse(localStorage.getItem("filtersApplied"));
            return getProductValue;
        } else {
            return 0;
        }
    };

    function getProjects(loading = "y") {
        $.ajax({
            method: "GET",
            url: "/api/projects?select=true&status=active&company=" + $(".company-navbar").val(),
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
            success: function success(response) {
                if (!isEmpty(response.data)) {
                    $("#project-empty").hide();
                    $("#project-not-empty").show();
                    if (verifyAccountFrozen()) {
                        $("#div-create").hide();
                    } else {
                        $("#div-create").show();
                    }
                    if (response.data != "api sales") {
                        $.each(appsList, function (index, value) {
                            let exist_shopify = "n";
                            let exist_woocommerce = "n";
                            let exist_nuvemshop = "n";
                            $.each(response.data, function (index, value) {
                                if (value.shopify) {
                                    exist_shopify = "s";
                                } else if (value.woocommerce) {
                                    exist_woocommerce = "s";
                                } else if (value.nuvemshop) {
                                    exist_nuvemshop = "s";
                                }
                            });
                            if (index == 1 && exist_shopify == "s") {
                                $("#type-products").append(
                                    $("<option>", {
                                        value: index,
                                        text: value,
                                    })
                                );
                            }
                            if (index == 2 && exist_woocommerce == "s") {
                                $("#type-products").append(
                                    $("<option>", {
                                        value: index,
                                        text: value,
                                    })
                                );
                            }

                            if (index == 3 && exist_nuvemshop == "s") {
                                $("#type-products").append(
                                    $("<option>", {
                                        value: index,
                                        text: value,
                                    })
                                );
                            }
                        });
                        $.each(response.data, function (index, value) {
                            if (value.shopify) {
                                $("#select-projects-1").append(
                                    $("<option>", {
                                        value: value.id,
                                        text: value.name,
                                    })
                                );
                            } else if (value.woocommerce) {
                                $("#select-projects-2").append(
                                    $("<option>", {
                                        value: value.id,
                                        text: value.name,
                                    })
                                );
                            } else if (value.nuvemshop) {
                                $("#select-projects-3").append(
                                    $("<option>", {
                                        value: value.id,
                                        text: value.name,
                                    })
                                );
                            }
                        });
                    }
                    handleLocalStorage();
                    updateProducts();
                } else {
                    removeFilterLoadingSkeleton();
                    removeLoadingSkeletonCards();
                    $("#project-empty").show();
                    $("#project-not-empty").hide();
                    $("#div-create").hide();
                }
            },
        });
    }

    function updateProducts(link = null) {
        pageCurrent = link;
        let existFilters = () => {
            if (localStorage.getItem("filtersApplied") != null) {
                let getFilters = JSON.parse(localStorage.getItem("filtersApplied"));
                return getFilters;
            } else {
                return null;
            }
        };
        if (link != null) {
            let getPage = {
                atualPage: pageCurrent,
            };
            localStorage.setItem("page", JSON.stringify(getPage));
        }
        if (localStorage.getItem("page") != null) {
            let parsePage = JSON.parse(localStorage.getItem("page"));
            if (existFilters() != null && existFilters().getName != "") {
                if (localStorage.getItem("page") != null) {
                    localStorage.setItem("page", JSON.stringify(parsePage));
                }
            } else {
                pageCurrent = parsePage.atualPage;
            }
            parsePage = JSON.parse(localStorage.getItem("page"));
            pageCurrent = parsePage.atualPage;
        }
        link = pageCurrent;
        showProductsLoadingSkeleton();
        let type = existFilters() != null ? existFilters().getTypeProducts : $("#type-products").val();
        let name = existFilters() != null ? existFilters().getName : $("#name").val();
        let project = "";
        if (existFilters() != null) {
            if (type == 1 && existFilters().getProject_1) {
                project = existFilters().getProject_1;
            } else if (type == 2 && existFilters().getProject_2) {
                project = existFilters().getProject_2;
            }
        } else {
            if ($("#select-projects-1 option:selected").val() != "")
                project = $("#select-projects-1 option:selected").val();
            else if ($("#select-projects-2 option:selected").val() != "")
                project = $("#select-projects-2 option:selected").val();
            else if ($("#select-projects-3 option:selected").val() != "")
                project = $("#select-projects-3 option:selected").val();
        }
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
                errorAjaxResponse(response);
            },
            success: function (response) {
                removeFilterLoadingSkeleton();
                $("#filter-products").show();

                if (!isEmpty(response.data)) {
                    $(".products-is-empty").hide();
                    $("#data-table-products").html("");
                    let dados = "";
                    $.each(response.data, function (index, value) {
                        shopifyProduct = value.id_view; //value.id != value.id_view;
                        dados = `
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
                            <div class="card shadow mb-20 mx-0" style="flex: 1 1 100%">
                                <div style="margin: 10px 10px 0px 0px;position: absolute;right: 0px;">
                                    <button type="button" class="menu_product" data-id="${value.id}">&#8942;</button>
                                </div>
                                <img class="card-img-top product-image pointer" src="${
                                    value.image
                                }" alt="Imagem do produto" data-link="/products/${value.id}/edit">
                                ${
                                    value.type_enum == 2
                                        ? `<span class="ribbon-inner ribbon-primary" style="background-color:${
                                              badgeList[value.status_enum]
                                          };border-radius: 0px 10px 10px 0px;"> ${statusList[value.status_enum]}
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
                                <a id="bt_editar" href="/products/${value.id}/edit" class="mx-20"><span>
                                    <img class="mr-10" src="/build/global/img/pencil-icon.svg"></span>
                                    Editar produto</a>
                                ${
                                    shopifyProduct == false
                                        ? `
                                    <hr class="my-5">
                                    <a href="#" class="mx-20 bt_excluir" data-id="${value.id}"><span><img class="mr-15" src="/build/global/img/icon-trash-tale.svg"></span>Excluir produto</a>
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
                        window.location.href = $(this).attr("data-link");
                    });

                    function closeTooltips(except = "") {
                        $('.menu_product_tooltip[style*="display: block"]').each(function (_, tooltip) {
                            if (except[0] == tooltip) {
                                return;
                            }

                            tooltip.style.display = "none";
                        });
                    }

                    $(".menu_product").off("click");
                    $(".menu_product").on("click", function () {
                        var tooltip = $(`.menu_product_tooltip[data-id="${this.dataset.id}"]`);
                        closeTooltips(tooltip);
                        tooltip.toggle();
                    });
                    $(".menu_product").off("focusout");
                    $(".menu_product").on("focusout", function (event) {
                        if ($(event.relatedTarget).hasClass("menu_product")) {
                            return;
                        }
                        setTimeout(() => closeTooltips(), 200);
                    });
                    $(".bt_excluir").off("click");
                    $(".bt_excluir").on("click", function (event) {
                        event.preventDefault();
                        $(".bt_excluir_modal").attr("data-id", this.dataset.id);
                        $("#modal-delete").modal();
                    });
                    $(".bt_excluir_modal").off("click");
                    $(".bt_excluir_modal").on("click", function (event) {
                        event.preventDefault();
                        loadingOnScreen();
                        $.ajax({
                            method: "DELETE",
                            url: "/api/products/" + this.dataset.id,
                            dataType: "json",
                            headers: {
                                Authorization: $('meta[name="access-token"]').attr("content"),
                                Accept: "application/json",
                            },
                            error: function (response) {
                                errorAjaxResponse(response);

                                $(".bt_excluir_modal").attr("data-id", "");
                                loadingOnScreenRemove();
                            },
                            success: function success(response) {
                                alertCustom("success", response.message);

                                window.location = "/products";
                            },
                        });
                    });
                    $("img").on("error", function () {
                        $(this).attr("src", "https://azcend-digital-products.s3.amazonaws.com/admin/produto.svg");
                    });
                    pagination(response, "products", updateProducts);
                    $(".products-is-empty").hide();
                } else {
                    if (localStorage.getItem("filtersApplied") != null && localStorage.getItem("page") != null) {
                        localStorage.removeItem("page");
                        $("#btn-filtro").trigger("click");
                    } else {
                        $("#data-table-products, #pagination-products").html("");
                        $(".products-is-empty").show();
                    }
                }
            },
            complete: (response) => {
                unlockSearch($("#btn-filtro"));
            },
        });
    }

    function loadData() {
        elementButton = $("#btn-filtro");
        if (searchIsLocked(elementButton) != "true") {
            lockSearch(elementButton);
            updateProducts();
        }
    }

    function handleLocalStorage() {
        if (localStorage.getItem("filtersApplied") != null) {
            let parseLocalStorage = JSON.parse(localStorage.getItem("filtersApplied"));
            $("#type-products").val(parseLocalStorage.getTypeProducts).trigger("change");
            $("#select-projects-1").val(parseLocalStorage.getProject_1).trigger("change");
            $("#select-projects-2").val(parseLocalStorage.getProject_2).trigger("change");
            $("#select-projects-3").val(parseLocalStorage.getProject_3).trigger("change");
            $("#name").val(parseLocalStorage.getName);
            $("#projects-list, .box-projects").addClass("d-none");
            type = parseLocalStorage.getTypeProducts;
            if (type != 0) {
                $("#projects-list #select-projects-" + type)
                    .prop("disabled", false)
                    .removeClass("disabled");
                //$("#projects-list #box-projects-" + type + " .opcao-vazia").remove();
                $("#box-projects-" + type).removeClass("d-none");
                $("#projects-list").removeClass("d-none");
            }
        }
    }

    $(document).on("keypress", function (e) {
        if (e.key == "Enter") {
            $("#btn-filtro").trigger("click");
        }
    });

    $("#type-products").on("change", function () {
        $("#name").val("");
        //$("#select-projects-1 .opcao-vazia, #select-projects-2 .opcao-vazia").remove();
        //$("#select-projects-1, #select-projects-2").prepend("<option class='opcao-vazia'>");
        $("#select-projects-1").val($("#select-projects-1 option:first").val());
        $("#select-projects-2").val($("#select-projects-2 option:first").val());
        $("#select-projects-3").val($("#select-projects-3 option:first").val());
        $("#projects-list select").prop("disabled", true).addClass("disabled");
        $("#projects-list, .box-projects").addClass("d-none");
        const type = $(this).val();
        if (type != 0) {
            $("#projects-list").removeClass("d-none");
            $("#box-projects-" + type).removeClass("d-none");
            $("#box-projects-" + type + " select")
                .prop("disabled", false)
                .removeClass("disabled");
            //$("#select-projects-" + type + " .opcao-vazia").remove();
        } else {
            $("#projects-list select").prop("disabled", true).addClass("disabled");
            $("#projects-list").addClass("d-none");
        }
    });

    $("#new-product-button").on("click", function (event) {
        event.preventDefault();
        $("#new-product-modal").show();
    });

    $(".new-product-btn").on("click", function () {
        $(".new-product-btn").removeClass("active");
        $(this).addClass("active");

        if ($(this).attr("data-add-url") === "/products/create/physical") {
            $("#selected-option-desc").text(
                "Em vendas de produtos físicos, será solicitado o rastreio para liberação da comissão da venda."
            );
        }

        if ($(this).attr("data-add-url") === "/products/create/digital") {
            $("#selected-option-desc").text("Ao selecionar produto digital, avance e preencha os dados.");
        }

        $("#selected-option-url").attr("href", $(this).attr("data-add-url"));

        $("#next-btn-container").addClass("d-flex flex-column").show();
    });

    $("#btn-filtro").on("click", function () {
        if (
            storeTypeProduct().getTypeProducts != $("#type-products option:selected").val() ||
            storeTypeProduct().getName != $("#name").val() ||
            storeTypeProduct().getProject_1 != $("#select-projects-1 option:selected").val() ||
            storeTypeProduct().getProject_2 != $("#select-projects-2 option:selected").val() ||
            storeTypeProduct().getProject_3 != $("#select-projects-3 option:selected").val()
        ) {
            if (localStorage.getItem("page") != null) {
                let getPageStored = JSON.parse(localStorage.getItem("page"));
                getPageStored.atualPage = null;
                localStorage.setItem("page", JSON.stringify(getPageStored));
            }
        }
        let filtersApplied = {
            getTypeProducts: $("#type-products option:selected").val(),
            getProject_1: $("#select-projects-1 option:selected").val(),
            getProject_2: $("#select-projects-2 option:selected").val(),
            getProjects_3: $("#select-projects-3 option:selected").val(),
            getName: $("#name").val(),
        };
        localStorage.setItem("filtersApplied", JSON.stringify(filtersApplied));
        loadData();
    });

    getCompaniesAndProjects().done(function (data) {
        getProjects();
    });

    function showProductsLoadingSkeleton() {
        $("#data-table-products").empty();
        loadingSkeletonCards($("#data-table-products"));
    }

    function showFiltersLoadingSkeleton() {
        $("#filter-products").hide();
        $("#filter-products-loading").show();
    }

    function removeFilterLoadingSkeleton() {
        $("#filter-products-loading").hide();
    }
});
