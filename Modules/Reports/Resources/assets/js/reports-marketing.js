$(function() {
    loadingOnScreen();
    exportReports();

    changeCompany();
    changeCalendar();
    changeOrigin();
    changeMap();

    if(sessionStorage.info) {
        let info = JSON.parse(sessionStorage.getItem('info'));
        $('input[name=daterange]').val(info.calendar);
    }

    getCompaniesAndProjects().done( function (data2){             
        getProjects(data2.companies);
    });
});

let resumeUrl = '/api/reports/resume';
let mktUrl = '/api/reports/marketing';

let company = '';
let date = '';
let origin = 'src';

$('#company-navbar').change(function () {
    if (verifyIfCompanyIsDefault()) return;
    loadingOnScreen();
	$("#select_projects").val($("#select_projects option:first").val());
    $(
        "#revenue-generated, #qtd-aproved, #qtd-boletos, #qtd-recusadas, #qtd-chargeback, #qtd-dispute, #qtd-reembolso, #qtd-pending, #qtd-canceled, #percent-credit-card, #percent-values-boleto,#credit-card-value,#boleto-value, #percent-boleto-convert#percent-credit-card-convert, #percent-desktop, #percent-mobile, #qtd-cartao-convert, #qtd-boleto-convert, #ticket-medio"
    ).html("<span>" + "<span class='loaderSpan' >" + "</span>" + "</span>");

    $("#select_projects").html('');
    sessionStorage.removeItem('info');

    updateCompanyDefault().done(function(data1){
        getCompaniesAndProjects().done(function(data2){            
            getProjects(data2.companies);
        });
	});
});

window.fillProjectsSelect = function(){
    return $.ajax({
        method: "GET",
        url: "/api/sales/projects-with-sales",
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            console.log('erro')
            console.log(response)
        },
        success: function success(response) {
            return response;
        }
    });
}

function changeMap() {
    $("input[name='brazil_map_filter']").on("change", function() {
        $('.back-list').trigger('click');

        $('.sirius-select-container').addClass('disabled');
        $('input[name="daterange"]').attr('disabled', 'disabled');
        $('#brazil-map-filter').addClass('disabled');
        $('#brazil-map-filter').find('input').attr('disabled', 'disabled');

        loadBrazilMap();
    });
}

function loadOrigins(link = null) {
    $('#card-origin').css('height', '358px');

    $("#card-origin .ske-load").show();
    $('.origin-report').hide();

    var link = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

    link = `${resumeUrl}/origins?paginate=false&limit=all&date_range=${$("input[name='daterange']").val()}&origin=${$("#origin").val()}&project_id=${$("#select_projects option:selected").val()}`;

    let td = `
        <table class="table-vendas table table-striped "style="width:100%; height: 100%; margin: auto;">
            <tbody>
                <td>
                    ${noWithdrawal}
                </td>
                <td>
                    <p class='no-data-origin'>
                        <strong>Sem dados, por enquanto...</strong>
                        Ainda faltam dados suficientes a comparação, continue rodando!
                    </p>
                </td>
            </tbody>
        </table>
    `;

    $("#block-origins").html("");

    $.ajax({
        url: link,
        type: "GET",
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $('.sirius-select-container').removeClass('disabled');
            $('input[name="daterange"]').removeAttr('disabled');
            $('#brazil-map-filter').removeClass('disabled');
            $('#brazil-map-filter').find('input').removeAttr('disabled');

            $("#block-origins").html(td);
            $('#card-origin').css('height', 'auto');

            errorAjaxResponse(response);
        },
        success: function success(response) {
            $('#card-origin').css('height', 'auto');

            $('.sirius-select-container').removeClass('disabled');
            $('input[name="daterange"]').removeAttr('disabled');
            $('#brazil-map-filter').removeClass('disabled');
            $('#brazil-map-filter').find('input').removeAttr('disabled');

            if (response.data.length == 0) {
                $('.table-vendas').height('100%');
                $("#card-origin .ske-load").hide();
                $("#block-origins").html(td);
                $("#pagination").html("");
                $("#pagination-origins").hide();
                $(".origin-report").show();
            } else {
                $("#block-origins").prepend(`
                    <footer class="footer-origins scroll-212" style="${ response.data.length > 10 ? 'height: 510px; ' : 'height: 100%; ' }display: block; padding: 0; margin: 0;">
                        <table class="table-vendas table table-striped "style="width:100%;margin: auto;">
                            <tbody id="origins-table"  class="origin-report" img-empty="{!! asset('/build/global/img/reports/img-nodata.svg')!!}">

                            </tbody>
                        </table>
                    </footer>
                `);

                if (response.data.length < 10) {
                    $('.footer-origins').removeClass('scroll-212');
                }

                var table_data = "";

                $.each(response.data, function (index, data) {
                    table_data += "<tr>";
                    table_data += "<td>" + (data.origin.length > 10 ? '<div data-placement="top" data-toggle="tooltip" title="'+ data.origin +'">'+data.origin.substring(0,10)+'...</div>' : data.origin) + "</td>";
                        table_data += "<td>" + data.sales_amount + "</td>";
                        table_data += "<td style='text-align: right;'>" + data.value + "</td>";
                    table_data += "</tr>";
                });

                $("#origins-table").html("");
                $("#origins-table").append(table_data);

                $('[data-toggle="tooltip"]').tooltip({
                    container: '.footer-origins'
                });

                $("#card-origin .ske-load").hide();
                $(".table-vendas").addClass("table-striped");

                //pagination(response, "origins", loadOrigins);
                $(".origin-report").show();
            }
        }
    });
}

function loadResume() {
    let checkouts = `
        <span class="title">Acessos</span>
        <div class="d-flex">
            <strong class="number">0</strong>
        </div>
    `;
    let salesCount = `
        <span class="title">Vendas</span>
        <div class="d-flex">
            <strong class="number">0</strong>
            <small class="percent">(0%)</small>
        </div>
    `;
    let salesValue = `
        <span class="title">Receita</span>
        <div class="d-flex">
            <span class="detail">R$</span>
            <strong class="number">0,00</strong>
        </div>
    `;
    $("#checkouts_count, #sales_count, #sales_value").html(skeLoad);

    return $.ajax({
        method: "GET",
        url: mktUrl + "/resume?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#checkouts_count").html(checkouts);
            $("#sales_count").html(salesCount);
            $("#sales_value").html(salesValue);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            if(response.data !== null) {
                checkouts = `
                    <span class="title">Acessos</span>
                    <div class="d-flex">
                        <strong class="number">${response.data.checkouts_count}</strong>
                    </div>
                `;
                salesCount = `
                    <span class="title">Vendas</span>
                    <div class="d-flex">
                        <strong class="number">${response.data.sales_count}</strong>
                        <small class="percent">(${response.data.conversion})</small>
                    </div>
                `;
                salesValue = `
                    <span class="title">Receita</span>
                    <div class="d-flex">
                        <span class="detail">R$</span>
                        <strong class="number">${removeMoneyCurrency(response.data.sales_value)}</strong>
                    </div>
                `;
            }

            $("#checkouts_count").html(checkouts);
            $("#sales_count").html(salesCount);
            $("#sales_value").html(salesValue);
        }
    });
}

function loadCoupons() {
    $('#card-coupon .onPreLoad *' ).remove();
    $("#block-coupons").html(skeLoad);
    let couponList = '';
    let cuponsHtml = `
        <div class="d-flex align-items justify-around" style="width: 100%;">
            <div class="no-coupon">${emptyCoupons}</div>
            <div class="msg-coupon">Nenhum cupom utilizado</div>
        </div>

    `;

    return $.ajax({
        method: "GET",
        url: resumeUrl + "/coupons?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-coupons").html(cuponsHtml);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            let { coupons, total } = response.data;

            if( total > 0 ) {
                cuponsHtml = `
                    <div class="container d-flex justify-content-between box-donut">
                        <div class="new-graph-pie graph" style="height: 117px;"></div>
                        <div class="data-pie data-coupon"><ul></ul></div>
                    </div>
                `;
                $("#block-coupons").html(cuponsHtml);
                $('.new-graph-pie').html("<div class='graph-pie pie-coupon'></div>");
                let arr = [];
                let seriesArr = [];

                $.each(coupons, function (i, coupon) {
                    arr.push(coupon);
                });

                for(let i = 0; i < arr.length; i++) {
                    if(arr[i].amount != undefined) {
                        seriesArr.push(arr[i].amount);
                        $('.data-pie ul').append(
                            `
                                <li>
                                    <div class="donut-pie ${arr[i].color}">
                                        <figure>
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect x="1.5" y="1.5" width="13" height="13" rx="6.5" stroke-width="3"/>
                                            </svg>
                                        </figure>
                                        <div>${arr[i].coupon}</div>
                                    </div>
                                    <div class="grey bold">${arr[i].amount}</div>
                                </li>
                            `
                        );
                        removeDuplcateItem('.data-pie li');
                    }
                }
                new Chartist.Pie('.graph-pie',
                { series: seriesArr },
                {
                    donut: true,
                    donutWidth: 20,
                    donutSolid: true,
                    startAngle: 270,
                    showLabel: false,
                    chartPadding: 0,
                    labelOffset: 0,
                });

            } else {
                $("#block-coupons").html(cuponsHtml);
            }

            $('#card-coupons .ske-load').hide();
        }
    });
}

function loadFrequenteSales() {
    let salesBlock = '';
    $('#card-most-sales .onPreLoad *' ).remove();
    $("#block-sales").html(skeLoad);
    let noData = `
        <div class="d-flex" style="justify-content: center; margin: auto;" >
            <div class="info-graph">
                <div class="no-sell">
                    <svg width="111" height="111" viewBox="0 0 111 111" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M55.5 111C86.1518 111 111 86.1518 111 55.5C111 24.8482 86.1518 0 55.5 0C24.8482 0 0 24.8482 0 55.5C0 86.1518 24.8482 111 55.5 111Z" fill="#F4F6FB"/>
                        <path d="M88.7999 111H22.2V39.22C25.339 39.2165 28.3485 37.9679 30.5682 35.7483C32.7879 33.5286 34.0364 30.5191 34.04 27.38H76.96C76.9566 28.935 77.2617 30.4753 77.8576 31.9116C78.4534 33.3479 79.3282 34.6519 80.4313 35.7479C81.5273 36.8513 82.8313 37.7264 84.2678 38.3224C85.7043 38.9184 87.2447 39.2235 88.7999 39.22V111Z" fill="white"/>
                        <path d="M55.5 75.48C65.3086 75.48 73.26 67.5286 73.26 57.72C73.26 47.9114 65.3086 39.96 55.5 39.96C45.6914 39.96 37.74 47.9114 37.74 57.72C37.74 67.5286 45.6914 75.48 55.5 75.48Z" fill="#E8EAEB"/>
                        <path d="M61.7791 66.0922L55.5 59.8131L49.2209 66.0922L47.1279 63.9992L53.407 57.7201L47.1279 51.441L49.2209 49.348L55.5 55.6271L61.7791 49.348L63.8721 51.441L57.593 57.7201L63.8721 63.9992L61.7791 66.0922Z" fill="white"/>
                        <path d="M65.1199 79.92H45.8799C44.6538 79.92 43.6599 80.9139 43.6599 82.14C43.6599 83.3661 44.6538 84.36 45.8799 84.36H65.1199C66.346 84.36 67.3399 83.3661 67.3399 82.14C67.3399 80.9139 66.346 79.92 65.1199 79.92Z" fill="#f4f4f4"/>
                        <path d="M71.78 88.8H39.22C37.9939 88.8 37 89.7939 37 91.02C37 92.2461 37.9939 93.24 39.22 93.24H71.78C73.0061 93.24 74 92.2461 74 91.02C74 89.7939 73.0061 88.8 71.78 88.8Z" fill="#f4f4f4"/>
                    </svg>
                    <footer>
                        <h4>Nada por aqui...</h4>
                        <p>
                            Não há dados suficientes
                            para gerar este relatório.
                        </p>
                    </footer>
                </div>
            </div>
        </div>
    `;

    return $.ajax({
        method: "GET",
        url: mktUrl + "/most-frequent-sales?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-sales").append(noData);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            $("#block-sales").html('');

            if(response.data !== null) {
                $.each(response.data, function (i, item) {
                    let value = removeMoneyCurrency(item.value);
                    let newV = formatCash(String(parseFloat(value)).replace('.',''));
                    salesBlock = `
                        <div class="box-payment-option pad-0">
                            <div class="d-flex justify-content-between align-items list-sales">
                                <div class="d-flex justify-content-between  align-items">
                                    <div
                                        class="box-ico figure-ico"
                                        data-container="body"
                                        data-viewport=".container"
                                        data-placement="top"
                                        data-toggle="tooltip"
                                        title="${item.name}"
                                    >
                                        <img width="37px" height="37px" onerror=this.src='https://cloudfox-files.s3.amazonaws.com/produto.svg' src="${item.photo}" alt="${item.description}">
                                    </div>
                                    <div>
                                        <span class="desc-product">${item.name}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items" style="min-width: 123px;">
                                    <div class="grey font-size-14">${item.sales_amount}</div>
                                    <div class="grey font-size-14 value"><strong>R$ ${newV}</strong></div>
                                </div>
                            </div>
                        </div>
                    `;
                    $("#block-sales").append(salesBlock);
                    $('[data-toggle="tooltip"]').tooltip({
                        container: '#block-sales'
                    });
                });

                if(response.data.length < 4 ) {
                    salesBlock = `<div>${noListProducts}</div>`;
                    $("#block-sales").append(salesBlock);
                }
            } else {
                $("#block-sales").append(noData);
            }
        }
    });
}

function getProjects(companies) 
{
    loadingOnScreen();
    $(".div-filters").hide();
    $("#project-empty").hide();
    $("#project-not-empty").show();
    $("#export-excel > div >").show();

    window.fillProjectsSelect()
    .done(function(dataSales)
    {
        $(".div-filters").show();        

        $.each(companies, function (c, company) {
            $.each(company.projects, function (i, project) {
                console.log(`comparando ${project.id}`)
                if( dataSales.includes(project.id) ){
                    $("#select_projects").append($("<option>", {value: project.id,text: project.name,}));
                    console.log(project.name);
                }
            });
        });
        
        $("#select_projects option:first").attr('selected','selected');

        if(sessionStorage.info) {   
            console.log('setando da sessao');         
            $("#select_projects").val(JSON.parse(sessionStorage.getItem('info')).company);
            $("#select_projects").find('option:selected').text(JSON.parse(sessionStorage.getItem('info')).companyName);
        }

        company = $("#select_projects").val();

        updateReports();
    }); 

    loadingOnScreenRemove();

    // $.ajax({
    //     method: "GET",
    //     url: "/api/projects?select=true",
    //     dataType: "json",
    //     headers: {
    //         Authorization: $('meta[name="access-token"]').attr("content"),
    //         Accept: "application/json",
    //     },
    //     error: function error(response) {
    //         loadingOnScreenRemove();
    //         $("#modal-content").hide();
    //         errorAjaxResponse(response);
    //     },
    //     success: function success(response) {
    //         if (!isEmpty(response.data)) {
    //             $(".div-filters").show();
    //             $("#project-empty").hide();
    //             $("#project-not-empty").show();
    //             $("#export-excel").show();

    //             $.each(response.data, function (i, project) {
    //                 $("#select_projects").append(
    //                     $("<option>", {
    //                         value: project.id,
    //                         text: project.name,
    //                     })
    //                 );

    //                 removeDuplcateItem("#select_projects option");
    //             });

    //             if(sessionStorage.info) {
    //                 $("#select_projects").val(JSON.parse(sessionStorage.getItem('info')).company);
    //                 $("#select_projects").find('option:selected').text(JSON.parse(sessionStorage.getItem('info')).companyName);
    //             }

    //             company = $("#select_projects").val();

    //             updateReports();
    //         } else {
    //             $(".div-filters").hide();
    //             $("#export-excel").hide();
    //             $("#project-not-empty").hide();
    //             $("#project-empty").show();
    //         }

    //         loadingOnScreenRemove();
    //     }
    // });
}

function changeCompany() {
    $("#select_projects").on("change", function () {
        $.ajaxQ.abortAll();

        if (company !== $(this).val()) {
            company = $(this).val();

            updateStorage({company: $(this).val(), companyName: $(this).find('option:selected').text()});
            updateReports();
        }
    });
}

function changeOrigin() {
    $("#origin").on("change", function () {
        if (origin !== $(this).val()) {
            origin = $(this).val();

            $("#origin").val($(this).val());

            $('.sirius-select-container').addClass('disabled');
            $('input[name="daterange"]').attr('disabled', 'disabled');
            $('#brazil-map-filter').addClass('disabled');
            $('#brazil-map-filter').find('input').attr('disabled', 'disabled');

            loadOrigins();
        }
    });
}

function loadDevices() {
    let deviceBlock = `
        <div class="empty-products pad-0" style="width: 100%;">
            ${emptyData}
            <p class="noone">Sem dados</p>
        </div>
    `;

    let deviceInfoBlock = '';

    $('#card-devices .onPreLoad *' ).remove();
    $("#block-devices").html(skeLoad);

    return $.ajax({
        method: "GET",
        url: mktUrl + "/devices?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $("#block-devices").html(deviceBlock);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            if(response.data !== null) {
                let { desktop, mobile } = response.data;
                const numbers = [desktop.total, mobile.total].map(Number).reduce((prev, value) => prev + value,0);

                deviceBlock = `
                <div class="row container-devices">
                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <span class="ico-cart align-items justify-around">
                                            <svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.5 15.7143C4.08579 15.7143 3.75 16.0341 3.75 16.4286C3.75 16.8231 4.08579 17.1429 4.5 17.1429H7.5C7.91421 17.1429 8.25 16.8231 8.25 16.4286C8.25 16.0341 7.91421 15.7143 7.5 15.7143H4.5ZM2.625 0C1.17525 0 0 1.11929 0 2.5V17.5C0 18.8807 1.17525 20 2.625 20H9.375C10.8247 20 12 18.8807 12 17.5V2.5C12 1.11929 10.8247 0 9.375 0H2.625ZM1.5 2.5C1.5 1.90827 2.00368 1.42857 2.625 1.42857H9.375C9.99632 1.42857 10.5 1.90827 10.5 2.5V17.5C10.5 18.0917 9.99632 18.5714 9.375 18.5714H2.625C2.00368 18.5714 1.5 18.0917 1.5 17.5V2.5Z" fill="#636363"/></svg>
                                        </span>
                                    </div>Smartphones
                                </div>

                                <div class="box-payment-option option">
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span class="silver">${mobile.percentage_approved}</span>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-14">${mobile.value}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <div class="data-holder b-bottom">
                            <div class="box-payment-option pad-0">
                                <div class="col-payment grey box-image-payment">
                                    <div class="box-ico">
                                        <span class="ico-cart align-items justify-around">
                                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 2.83333C0 1.26853 1.26853 0 2.83333 0H14.1667C15.7315 0 17 1.26853 17 2.83333V11.3333C17 12.8981 15.7315 14.1667 14.1667 14.1667H11.3333V14.875C11.3333 15.2662 11.6505 15.5833 12.0417 15.5833H12.75C13.1412 15.5833 13.4583 15.9005 13.4583 16.2917C13.4583 16.6829 13.1412 17 12.75 17H4.25C3.8588 17 3.54167 16.6829 3.54167 16.2917C3.54167 15.9005 3.8588 15.5833 4.25 15.5833H4.95833C5.34953 15.5833 5.66667 15.2662 5.66667 14.875V14.1667H2.83333C1.26853 14.1667 0 12.8981 0 11.3333V2.83333ZM10.0376 15.5833C9.95928 15.3618 9.91667 15.1234 9.91667 14.875V14.1667H7.08333V14.875C7.08333 15.1234 7.04072 15.3618 6.96242 15.5833H10.0376ZM14.1667 12.75C14.9491 12.75 15.5833 12.1157 15.5833 11.3333H1.41667C1.41667 12.1157 2.05093 12.75 2.83333 12.75H14.1667ZM15.5833 2.83333C15.5833 2.05093 14.9491 1.41667 14.1667 1.41667H2.83333C2.05093 1.41667 1.41667 2.05093 1.41667 2.83333V9.91667H15.5833V2.83333Z" fill="#636363"/></svg>
                                        </span>
                                    </div> Desktop
                                </div>
                                <div class="box-payment-option option">
                                    <div class="col-payment">
                                        <div class="box-payment center">
                                            <span class="silver">${desktop.percentage_approved}</span>
                                        </div>
                                    </div>
                                    <div class="col-payment">
                                        <div class="box-payment right">
                                            <strong class="grey font-size-14">${desktop.value}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `;

                if( mobile.total > desktop.total ) {
                    deviceInfoBlock = `
                        <div>
                            <span class="ico-coin mkt">
                                <svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.5 15.7143C4.08579 15.7143 3.75 16.0341 3.75 16.4286C3.75 16.8231 4.08579 17.1429 4.5 17.1429H7.5C7.91421 17.1429 8.25 16.8231 8.25 16.4286C8.25 16.0341 7.91421 15.7143 7.5 15.7143H4.5ZM2.625 0C1.17525 0 0 1.11929 0 2.5V17.5C0 18.8807 1.17525 20 2.625 20H9.375C10.8247 20 12 18.8807 12 17.5V2.5C12 1.11929 10.8247 0 9.375 0H2.625ZM1.5 2.5C1.5 1.90827 2.00368 1.42857 2.625 1.42857H9.375C9.99632 1.42857 10.5 1.90827 10.5 2.5V17.5C10.5 18.0917 9.99632 18.5714 9.375 18.5714H2.625C2.00368 18.5714 1.5 18.0917 1.5 17.5V2.5Z" fill="#2E85EC"/>
                                </svg>
                            </span>
                        </div>
                        <div>
                            <span class="mkt-msg-conversion">
                                Smartphones são os dispositivos mais usados e representam <strong>${mobile.percentage_conversion} das suas conversões.</strong>
                            </span>
                        </div>
                        `;
                } else if( desktop.total > mobile.total ) {
                    deviceInfoBlock = `
                        <div>
                            <span class="ico-coin mkt">
                                <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 2.83333C0 1.26853 1.26853 0 2.83333 0H14.1667C15.7315 0 17 1.26853 17 2.83333V11.3333C17 12.8981 15.7315 14.1667 14.1667 14.1667H11.3333V14.875C11.3333 15.2662 11.6505 15.5833 12.0417 15.5833H12.75C13.1412 15.5833 13.4583 15.9005 13.4583 16.2917C13.4583 16.6829 13.1412 17 12.75 17H4.25C3.8588 17 3.54167 16.6829 3.54167 16.2917C3.54167 15.9005 3.8588 15.5833 4.25 15.5833H4.95833C5.34953 15.5833 5.66667 15.2662 5.66667 14.875V14.1667H2.83333C1.26853 14.1667 0 12.8981 0 11.3333V2.83333ZM10.0376 15.5833C9.95928 15.3618 9.91667 15.1234 9.91667 14.875V14.1667H7.08333V14.875C7.08333 15.1234 7.04072 15.3618 6.96242 15.5833H10.0376ZM14.1667 12.75C14.9491 12.75 15.5833 12.1157 15.5833 11.3333H1.41667C1.41667 12.1157 2.05093 12.75 2.83333 12.75H14.1667ZM15.5833 2.83333C15.5833 2.05093 14.9491 1.41667 14.1667 1.41667H2.83333C2.05093 1.41667 1.41667 2.05093 1.41667 2.83333V9.91667H15.5833V2.83333Z" fill="#2E85EC" />
                                </svg>
                            </span>
                        </div>
                        <div>
                            <span class="mkt-msg-conversion">
                                Desktops são os dispositivos mais usados e representam <strong>${desktop.percentage_conversion} das suas conversões.</strong>
                            </span>
                        </div>
                        `;
                } else {
                    deviceInfoBlock = `
                        <div>
                            <span class="ico-coin mkt">
                                <svg width="12" height="20" viewBox="0 0 12 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.5 15.7143C4.08579 15.7143 3.75 16.0341 3.75 16.4286C3.75 16.8231 4.08579 17.1429 4.5 17.1429H7.5C7.91421 17.1429 8.25 16.8231 8.25 16.4286C8.25 16.0341 7.91421 15.7143 7.5 15.7143H4.5ZM2.625 0C1.17525 0 0 1.11929 0 2.5V17.5C0 18.8807 1.17525 20 2.625 20H9.375C10.8247 20 12 18.8807 12 17.5V2.5C12 1.11929 10.8247 0 9.375 0H2.625ZM1.5 2.5C1.5 1.90827 2.00368 1.42857 2.625 1.42857H9.375C9.99632 1.42857 10.5 1.90827 10.5 2.5V17.5C10.5 18.0917 9.99632 18.5714 9.375 18.5714H2.625C2.00368 18.5714 1.5 18.0917 1.5 17.5V2.5Z" fill="#2E85EC"/>
                                </svg>
                            </span>
                        </div>
                        <div>
                            <span class="mkt-msg-conversion">
                                São iguais ${desktop.percentage_conversion}
                            </span>
                        </div>
                        `;
                }

                $('#card-info-conversion').show();
            } else {
                deviceInfoBlock = '';

                $('#card-info-conversion').hide();
            }

            $("#block-info-card-conversion").html(deviceInfoBlock).removeClass('pad-0');
            $("#block-devices").html(deviceBlock);
        }
    });
}

function loadOperationalSystems() {
    $('#card-system .onPreLoad *' ).remove();
    $('#container-operational-systems').html(skeLoad);
    let stateNoData = `
        <div class="empty-products pad-0" style="width: 100%;">
            ${emptyData}
            <p class="noone">Sem dados</p>
        </div>
    `;

    $.ajax({
        method: "GET",
        url: mktUrl + "/operational-systems?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $('#container-operational-systems').html(stateNoData);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            $('#container-operational-systems').html('');

            if(response.data !== null) {
                let systemsHtml = `<div class="contentSystems"></div>`
                $('#container-operational-systems').html(systemsHtml);
                $.each(response.data, function(i, data){

                    if(data.percentage == '0.0%') {
                        return true;
                    }

                    $('.contentSystems').append(`
                        <div class="container">
                            <div class="data-holder b-bottom">
                                <div class="box-payment-option pad-0">
                                    <div class="col-payment grey box-image-payment">
                                        <div class="box-ico">
                                            <span class="ico-cart align-items justify-around">
                                                ${getOperationalSystemSvg(data.description)}
                                            </span>
                                        </div> ${data.description}
                                    </div>
                                    <div class="box-payment-option option">
                                        <div class="col-payment col-graph">
                                            <div class="bar blue" style="width:${data.percentage || '0$'}">barrinha</div>
                                        </div>
                                        <div class="col-payment">
                                            <span class="money-td green bold grey font-size-14 value-percent">${data.percentage}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                });
            } else {
                $('#container-operational-systems').html(stateNoData);
                return;
            }

            function getOperationalSystemSvg(operationalSystem) {

                if(operationalSystem == 'IOS') {
                    return `
                        <svg width="15" height="18" viewBox="0 0 15 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.5275 9.56283C12.5534 12.2868 14.9732 13.1933 15 13.2048C14.9795 13.2688 14.6134 14.4966 13.7252 15.765C12.9573 16.8615 12.1604 17.954 10.9051 17.9766C9.6716 17.9988 9.27497 17.2619 7.86473 17.2619C6.45492 17.2619 6.01423 17.954 4.84659 17.9988C3.63487 18.0436 2.71215 16.8131 1.93795 15.7206C0.355974 13.4858 -0.852985 9.4057 0.770342 6.65155C1.57678 5.28383 3.01794 4.41773 4.58218 4.39552C5.77207 4.37334 6.89517 5.1777 7.62256 5.1777C8.34949 5.1777 9.7143 4.21039 11.1491 4.35245C11.7497 4.37688 13.4358 4.58952 14.5184 6.13794C14.4312 6.19078 12.5066 7.2855 12.5275 9.56284V9.56283ZM10.2093 2.87397C10.8526 2.1131 11.2856 1.0539 11.1675 0C10.2402 0.0364151 9.11892 0.603766 8.4538 1.36422C7.85772 2.03763 7.33569 3.11548 7.47654 4.14852C8.51011 4.22665 9.56598 3.63532 10.2093 2.87397" fill="#636363"/>
                        </svg>
                    `;
                }
                else if(operationalSystem == 'Android') {
                    return `
                        <svg width="19" height="11" viewBox="0 0 19 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M13.8798 8.22017C13.7228 8.22017 13.5693 8.1723 13.4387 8.08263C13.3081 7.99296 13.2063 7.8655 13.1462 7.71638C13.0861 7.56725 13.0704 7.40316 13.101 7.24486C13.1317 7.08655 13.2073 6.94114 13.3183 6.827C13.4294 6.71287 13.5709 6.63514 13.7249 6.60365C13.879 6.57216 14.0386 6.58833 14.1837 6.65009C14.3288 6.71186 14.4528 6.81646 14.5401 6.95067C14.6274 7.08488 14.6739 7.24266 14.6739 7.40407C14.6739 7.62051 14.5903 7.82809 14.4413 7.98114C14.2924 8.13419 14.0904 8.22017 13.8798 8.22017ZM5.1367 8.22017C4.97965 8.22017 4.82612 8.1723 4.69553 8.08263C4.56494 7.99296 4.46317 7.8655 4.40306 7.71638C4.34296 7.56725 4.32724 7.40316 4.35788 7.24486C4.38852 7.08655 4.46415 6.94114 4.5752 6.827C4.68626 6.71287 4.82775 6.63514 4.98178 6.60365C5.13582 6.57216 5.29548 6.58833 5.44058 6.65009C5.58568 6.71186 5.7097 6.81646 5.79695 6.95067C5.88421 7.08488 5.93078 7.24266 5.93078 7.40407C5.93078 7.62051 5.84712 7.82809 5.6982 7.98114C5.54928 8.13419 5.3473 8.22017 5.1367 8.22017ZM14.1611 3.32358L15.7492 0.509745C15.7707 0.471217 15.7845 0.428716 15.7899 0.384686C15.7953 0.340655 15.7922 0.295962 15.7807 0.253173C15.7693 0.210384 15.7497 0.170343 15.7231 0.13535C15.6966 0.100358 15.6635 0.0711024 15.6259 0.0492653C15.5884 0.0274282 15.5469 0.0134396 15.5041 0.00810309C15.4612 0.00276656 15.4177 0.00618727 15.3762 0.0181687C15.3346 0.0301501 15.2957 0.0504561 15.2618 0.0779202C15.2279 0.105384 15.1996 0.139465 15.1785 0.178206L13.5738 3.02605C12.2993 2.42749 10.9137 2.12006 9.51241 2.12494C8.1095 2.12141 6.72203 2.42573 5.44275 3.01755L3.83805 0.169705C3.79504 0.0923793 3.72399 0.0357041 3.64043 0.0120738C3.55687 -0.0115564 3.46761 -0.000218677 3.39216 0.0436075C3.31671 0.0874337 3.26122 0.160182 3.23783 0.245942C3.21443 0.331702 3.22503 0.423494 3.26731 0.501244L4.84719 3.31508C3.48116 4.08075 2.32251 5.18448 1.47615 6.52635C0.629778 7.86821 0.12242 9.40583 0 11H19C18.8813 9.40696 18.3763 7.86983 17.5311 6.52892C16.6858 5.18802 15.5272 4.08604 14.1611 3.32358Z" fill="#636363"/>
                        </svg>
                    `;
                }
                else if(operationalSystem == 'Windows') {
                    return `
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.63593 0H0.000244141V6.63569H6.63593V0Z" fill="#636363"/>
                            <path d="M13.9997 -6.10352e-05H7.36401V6.63563H13.9997V-6.10352e-05Z" fill="#636363"/>
                            <path d="M6.63593 7.36432H0.000244141V14H6.63593V7.36432Z" fill="#636363"/>
                            <path d="M13.9997 7.36432H7.36401V14H13.9997V7.36432Z" fill="#636363"/>
                        </svg>
                    `;
                }
                else if(operationalSystem == 'Linux') {
                    return `
                        <svg width="13" height="16" viewBox="0 0 13 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.28817 4.06562C6.28817 4.1169 6.2393 4.1169 6.2393 4.1169H6.19042C6.14155 4.1169 6.14155 4.06562 6.09268 4.01434C6.09268 4.01434 6.04381 3.96306 6.04381 3.91177C6.04381 3.86049 6.04381 3.86049 6.09268 3.86049L6.19042 3.91177C6.2393 3.96306 6.28817 4.01434 6.28817 4.06562ZM5.40847 3.5528C5.40847 3.29639 5.31072 3.14254 5.16411 3.14254C5.16411 3.14254 5.16411 3.19383 5.11523 3.19383V3.29639H5.26185C5.26185 3.39895 5.31072 3.45024 5.31072 3.5528H5.40847ZM7.11899 3.29639C7.21674 3.29639 7.26561 3.39895 7.31448 3.5528H7.41223C7.36335 3.50152 7.36335 3.45024 7.36335 3.39895C7.36335 3.34767 7.36335 3.29639 7.31448 3.24511C7.26561 3.19383 7.21674 3.14254 7.16787 3.14254C7.16787 3.14254 7.11899 3.19383 7.07012 3.19383C7.07012 3.24511 7.11899 3.24511 7.11899 3.29639ZM5.65283 4.1169C5.60396 4.1169 5.60396 4.1169 5.60396 4.06562C5.60396 4.01434 5.60396 3.96306 5.65283 3.91177C5.75057 3.91177 5.79945 3.86049 5.79945 3.86049C5.84832 3.86049 5.84832 3.91177 5.84832 3.91177C5.84832 3.96306 5.79945 4.01434 5.7017 4.1169H5.65283ZM5.11523 4.06562C4.91975 3.96306 4.87087 3.80921 4.87087 3.5528C4.87087 3.39895 4.87087 3.29639 4.96862 3.19383C5.01749 3.09126 5.11523 3.03998 5.21298 3.03998C5.31072 3.03998 5.3596 3.09126 5.45734 3.19383C5.50621 3.34767 5.55508 3.50152 5.55508 3.65536V3.70665V3.75793H5.60396V3.70665C5.65283 3.70665 5.65283 3.60408 5.65283 3.39895C5.65283 3.24511 5.65283 3.09126 5.55508 2.93742C5.45734 2.78357 5.3596 2.68101 5.16411 2.68101C5.01749 2.68101 4.87087 2.78357 4.822 2.93742C4.72426 3.14254 4.70471 3.29639 4.70471 3.5528C4.70471 3.75793 4.77313 3.96306 4.96862 4.16818C5.01749 4.1169 5.06636 4.1169 5.11523 4.06562ZM11.2243 11.2964C11.2731 11.2964 11.2731 11.2759 11.2731 11.2297C11.2731 11.1169 11.2243 10.9836 11.0776 10.8349C10.931 10.681 10.6867 10.5836 10.3934 10.5425C10.3446 10.5374 10.2957 10.5374 10.2957 10.5374C10.2468 10.5272 10.2468 10.5272 10.1979 10.5272C10.1491 10.522 10.0513 10.5118 10.0025 10.5015C10.1491 10.0246 10.1979 9.60408 10.1979 9.23485C10.1979 8.72203 10.1002 8.36306 9.90471 8.05536C9.70922 7.74767 9.51373 7.59383 9.26937 7.54254C9.2205 7.59383 9.2205 7.59383 9.2205 7.64511C9.46486 7.74767 9.70922 7.9528 9.85584 8.26049C10.0025 8.61947 10.0513 8.92716 10.0513 9.28613C10.0513 9.57331 10.0025 9.99895 9.80696 10.5425C9.61147 10.6246 9.41599 10.8143 9.26937 11.1118C9.26937 11.1579 9.26937 11.1836 9.31824 11.1836C9.31824 11.1836 9.36711 11.1374 9.41599 11.0502C9.51373 10.9631 9.5626 10.8759 9.66035 10.7887C9.80696 10.7015 9.90471 10.6554 10.0513 10.6554C10.2957 10.6554 10.54 10.6913 10.6867 10.7631C10.8822 10.8297 10.9799 10.9015 11.0288 10.9836C11.0776 11.0605 11.1265 11.1323 11.1754 11.199C11.1754 11.2656 11.2243 11.2964 11.2243 11.2964ZM6.72802 3.86049C6.67914 3.80921 6.67914 3.70665 6.67914 3.60408C6.67914 3.39895 6.67914 3.29639 6.77689 3.14254C6.87463 3.03998 6.97238 2.9887 7.07012 2.9887C7.21674 2.9887 7.31448 3.09126 7.41223 3.19383C7.4611 3.34767 7.50997 3.45024 7.50997 3.60408C7.50997 3.86049 7.41223 4.01434 7.21674 4.06562C7.21674 4.06562 7.26561 4.1169 7.31448 4.1169C7.41223 4.1169 7.4611 4.16818 7.55884 4.21947C7.60772 3.91177 7.65659 3.70665 7.65659 3.45024C7.65659 3.14254 7.60772 2.93742 7.50997 2.78357C7.36335 2.62972 7.21674 2.57844 7.02125 2.57844C6.87463 2.57844 6.72802 2.62972 6.5814 2.73229C6.48366 2.88613 6.43478 2.9887 6.43478 3.14254C6.43478 3.39895 6.48366 3.60408 6.5814 3.80921C6.63027 3.80921 6.67914 3.86049 6.72802 3.86049ZM7.31448 4.68101C6.67914 5.14254 6.19042 5.34767 5.79945 5.34767C5.45734 5.34767 5.11523 5.19383 4.822 4.93742C4.87087 5.03998 4.91975 5.14254 4.96862 5.19383L5.26185 5.50152C5.45734 5.70665 5.7017 5.80921 5.94606 5.80921C6.28817 5.80921 6.67914 5.60408 7.16787 5.24511L7.60772 4.93742C7.70546 4.83485 7.8032 4.73229 7.8032 4.57844C7.8032 4.52716 7.8032 4.47588 7.75433 4.47588C7.70546 4.37331 7.4611 4.21947 6.97238 4.06562C6.53253 3.86049 6.19042 3.75793 5.99493 3.75793C5.84832 3.75793 5.60396 3.86049 5.26185 4.06562C4.96862 4.27075 4.77313 4.47588 4.77313 4.68101C4.77313 4.68101 4.822 4.73229 4.87087 4.83485C5.16411 5.09126 5.45734 5.24511 5.75057 5.24511C6.14155 5.24511 6.63027 5.03998 7.26561 4.52716V4.62972C7.31448 4.62972 7.31448 4.68101 7.31448 4.68101ZM8.43854 15.04C8.63403 15.4256 8.97614 15.6195 9.36711 15.6195C9.46486 15.6195 9.5626 15.6041 9.66035 15.5733C9.75809 15.5528 9.85584 15.5169 9.90471 15.4759C9.95358 15.44 10.0025 15.4041 10.0513 15.3631C10.1491 15.3272 10.1491 15.3015 10.1979 15.2759L11.0288 14.522C11.2243 14.3584 11.4197 14.2154 11.6641 14.0913C11.8596 13.9682 12.0551 13.8861 12.1528 13.84C12.2994 13.799 12.3972 13.7374 12.4949 13.6554C12.5438 13.5784 12.5927 13.481 12.5927 13.3579C12.5927 13.2092 12.4949 13.0964 12.3972 13.0143C12.2994 12.9323 12.2017 12.8759 12.104 12.84C12.0062 12.8041 11.9085 12.722 11.7619 12.5836C11.6641 12.4502 11.5664 12.2656 11.5175 12.0246L11.4686 11.7272C11.4197 11.5887 11.4197 11.4861 11.3709 11.4297C11.3709 11.4143 11.3709 11.4092 11.322 11.4092C11.2731 11.4092 11.1754 11.4554 11.1265 11.5425C11.0288 11.6297 10.931 11.7272 10.8333 11.8297C10.7844 11.9323 10.6378 12.0246 10.54 12.1118C10.3934 12.199 10.2468 12.2451 10.1491 12.2451C9.75809 12.2451 9.5626 12.1323 9.41599 11.9118C9.31824 11.7477 9.26937 11.5579 9.2205 11.3425C9.12275 11.2554 9.07388 11.2092 8.97614 11.2092C8.73178 11.2092 8.63403 11.4759 8.63403 12.0143V12.1836V12.7784V13.2349V13.4554V13.6092C8.63403 13.6554 8.58516 13.7579 8.58516 13.9169C8.53629 14.0759 8.53629 14.2564 8.53629 14.4605L8.43854 15.0297V15.0384V15.04ZM1.35208 14.7672C1.80659 14.8369 2.32952 14.9861 2.92087 15.2138C3.51223 15.4395 3.87388 15.5574 4.00584 15.5574C4.34794 15.5574 4.6314 15.3984 4.86599 15.0913C4.91486 14.9918 4.91486 14.8749 4.91486 14.7405C4.91486 14.2559 4.63629 13.6431 4.07914 12.8995L3.74681 12.4328C3.67839 12.3354 3.59531 12.1866 3.48779 11.9866C3.38516 11.7866 3.2923 11.6328 3.21899 11.5251C3.15546 11.4072 3.05283 11.2892 2.92087 11.1713C2.79381 11.0533 2.64719 10.9764 2.48591 10.9354C2.28065 10.9764 2.13892 11.0482 2.0705 11.1456C2.00208 11.2431 1.96298 11.3507 1.9532 11.4636C1.93854 11.5713 1.90922 11.6431 1.86035 11.679C1.81148 11.7097 1.72839 11.7354 1.61599 11.761C1.59155 11.761 1.54757 11.761 1.48403 11.7661H1.35208C1.09305 11.7661 0.917115 11.7969 0.824257 11.8482C0.702077 11.9969 0.638543 12.1661 0.638543 12.3456C0.638543 12.4277 0.658092 12.5661 0.69719 12.761C0.736287 12.9507 0.755836 13.1046 0.755836 13.2123C0.755836 13.4225 0.69719 13.6328 0.575009 13.8431C0.452829 14.0636 0.389295 14.2277 0.389295 14.3446C0.438167 14.5436 0.760724 14.6836 1.35208 14.7656V14.7672ZM2.97952 10.1041C2.97952 9.75024 3.06749 9.36049 3.24832 8.89895C3.42426 8.43742 3.6002 8.12972 3.77125 7.92459C3.76148 7.87331 3.73704 7.87331 3.69794 7.87331L3.64907 7.82203C3.50734 7.97588 3.33629 8.33485 3.13102 8.84767C2.92576 9.30921 2.81824 9.73485 2.81824 10.0477C2.81824 10.2784 2.872 10.4784 2.96975 10.6528C3.07727 10.822 3.33629 11.0682 3.74681 11.381L4.26486 11.7349C4.81711 12.2374 5.11035 12.5861 5.11035 12.7913C5.11035 12.899 5.06148 13.0066 4.91486 13.1246C4.81711 13.2477 4.68516 13.3092 4.57275 13.3092C4.56298 13.3092 4.55809 13.3195 4.55809 13.3451C4.55809 13.3502 4.60696 13.4528 4.7096 13.6528C4.91486 13.9451 5.35471 14.0887 5.94117 14.0887C7.01636 14.0887 7.84719 13.6272 8.48253 12.7041C8.48253 12.4477 8.48253 12.2887 8.43366 12.222V12.0323C8.43366 11.699 8.48253 11.4477 8.58027 11.2836C8.67802 11.1195 8.77576 11.0425 8.92238 11.0425C9.02012 11.0425 9.11787 11.0784 9.21561 11.1554C9.26448 10.7605 9.26448 10.4169 9.26448 10.1092C9.26448 9.64254 9.26448 9.25793 9.16674 8.89895C9.11787 8.59126 9.02012 8.33485 8.92238 8.12972C8.82463 7.97588 8.72689 7.82203 8.62914 7.66818C8.5314 7.51434 8.48253 7.36049 8.38478 7.20665C8.33591 7.00152 8.28704 6.84767 8.28704 6.59126C8.14042 6.33485 8.04268 6.07844 7.89606 5.82203C7.79832 5.56562 7.70057 5.30921 7.60283 5.10408L7.16298 5.46306C6.67426 5.82203 6.28328 5.97588 5.94117 5.97588C5.64794 5.97588 5.40358 5.92459 5.25696 5.71947L4.96373 5.46306C4.96373 5.6169 4.91486 5.82203 4.81711 6.02716L4.50922 6.64254C4.37238 7.00152 4.29907 7.20665 4.28441 7.36049C4.26486 7.46306 4.2502 7.56562 4.24042 7.56562L3.87388 8.33485C3.47802 9.10408 3.27764 9.8169 3.27764 10.4066C3.27764 10.5246 3.28742 10.6477 3.30696 10.7707C3.08704 10.6118 2.97952 10.3913 2.97952 10.1041ZM6.47877 14.9554C5.84343 14.9554 5.35471 15.0456 5.0126 15.2246V15.2092C4.76824 15.5169 4.49456 15.6759 4.11336 15.6759C3.87388 15.6759 3.49757 15.5784 2.9893 15.3836C2.47614 15.199 2.02163 15.0574 1.62576 14.9641C1.58666 14.9523 1.49869 14.9348 1.35696 14.9113C1.22012 14.8882 1.09305 14.8646 0.980648 14.841C0.878017 14.8179 0.760724 14.7831 0.633656 14.7359C0.511475 14.6954 0.413731 14.6425 0.340423 14.5784C0.272979 14.5138 0.239746 14.441 0.239746 14.3595C0.239746 14.2774 0.256363 14.1897 0.289596 14.0964C0.320874 14.04 0.355084 13.9836 0.389295 13.9323C0.423506 13.8759 0.452829 13.8246 0.472378 13.7733C0.501701 13.7272 0.52125 13.681 0.540799 13.6297C0.560348 13.5836 0.579896 13.5374 0.589671 13.481C0.599445 13.4297 0.60922 13.3784 0.60922 13.3272C0.60922 13.2759 0.589671 13.122 0.550573 12.8502C0.511475 12.5836 0.491927 12.4143 0.491927 12.3425C0.491927 12.1169 0.540799 11.9374 0.648318 11.8092C0.755836 11.681 0.858468 11.6143 0.965987 11.6143H1.52802C1.572 11.6143 1.64042 11.5887 1.74305 11.5272C1.77726 11.4451 1.80659 11.3784 1.82614 11.3169C1.85057 11.2554 1.86035 11.2092 1.87012 11.1887C1.8799 11.1579 1.88967 11.1272 1.89945 11.1015C1.91899 11.0656 1.94343 11.0246 1.97764 10.9836C1.93854 10.9323 1.91899 10.8656 1.91899 10.7836C1.91899 10.7272 1.91899 10.6759 1.92877 10.6451C1.92877 10.4605 2.01185 10.199 2.18779 9.85536L2.35884 9.53229C2.50057 9.25536 2.60809 9.05024 2.68629 8.84511C2.76937 8.63998 2.85734 8.33229 2.95508 7.92203C3.03328 7.56306 3.21899 7.20408 3.51223 6.84511L3.87877 6.38357C4.1329 6.07588 4.29907 5.81947 4.39193 5.61434C4.48478 5.40921 4.53366 5.1528 4.53366 4.94767C4.53366 4.84511 4.50922 4.53742 4.45546 4.02459C4.40659 3.51177 4.38215 2.99895 4.38215 2.53742C4.38215 2.17844 4.41148 1.92203 4.47501 1.66562C4.53854 1.40921 4.65095 1.1528 4.81711 0.947672C4.96373 0.742544 5.15922 0.537416 5.45245 0.434852C5.74569 0.332288 6.08779 0.281006 6.47877 0.281006C6.62538 0.281006 6.772 0.281006 6.91862 0.332288C7.06523 0.332288 7.26072 0.38357 7.50508 0.486134C7.70057 0.588698 7.89606 0.691262 8.04268 0.845108C8.23817 0.998955 8.38478 1.25536 8.5314 1.51177C8.62914 1.81947 8.72689 2.12716 8.77576 2.53742C8.82463 2.79383 8.82463 3.05024 8.8735 3.40921C8.8735 3.7169 8.92238 3.92203 8.92238 4.07588C8.97125 4.22972 8.97125 4.43485 9.02012 4.69126C9.06899 4.89639 9.11787 5.10152 9.21561 5.25536C9.31335 5.46049 9.4111 5.66562 9.55772 5.87075C9.70433 6.12716 9.89982 6.38357 10.0953 6.69126C10.5352 7.20408 10.8773 7.76818 11.0728 8.33229C11.3171 8.84511 11.4637 9.51177 11.4637 10.2246C11.4637 10.5784 11.4149 10.922 11.3171 11.2554C11.4149 11.2554 11.4637 11.2964 11.5126 11.3682C11.5615 11.44 11.6103 11.5938 11.6592 11.8349L11.7081 12.2143C11.757 12.3272 11.8058 12.4349 11.9525 12.5272C12.0502 12.6195 12.1479 12.6964 12.2946 12.7579C12.3923 12.8092 12.5389 12.881 12.6367 12.9733C12.7344 13.0759 12.7833 13.1836 12.7833 13.2964C12.7833 13.4707 12.7344 13.599 12.6367 13.6913C12.5389 13.7938 12.4412 13.8656 12.2946 13.9118C12.1968 13.9631 12.0013 14.0656 11.7081 14.2102C11.4637 14.362 11.2194 14.5461 10.975 14.7641L10.4863 15.2005C10.2908 15.4005 10.0953 15.5441 9.94869 15.6313C9.80208 15.7236 9.60659 15.7697 9.4111 15.7697L9.06899 15.7287C8.67802 15.621 8.43366 15.4159 8.28704 15.1031C7.50508 15.0036 6.86975 14.9543 6.47877 14.9543" fill="#636363"/>
                        </svg>
                    `;
                }
            }
        }
    });
}

function exportReports() {
    // show/hide modal de exportar relatórios
    $(".lk-export").on('click', function(e) {
        e.preventDefault();
        $('.inner-reports').addClass('focus');
        $('.line-reports').addClass('d-flex');
    });

    $('.reports-remove').on('click', function (e) {
        e.preventDefault();
        $('.inner-reports').removeClass('focus');
        $('.line-reports').removeClass('d-flex');
    });

}

function updateStorage(v){
    var existing = sessionStorage.getItem('info');
    existing = existing ? JSON.parse(existing) : {};
    Object.keys(v).forEach(function(val, key){
        existing[val] = v[val];
   })
    sessionStorage.setItem('info', JSON.stringify(existing));
}

function changeCalendar() {
    $('.onPreLoad *, .onPreLoadBig *').remove();

    var startDate = moment().subtract(30, "days").format("DD/MM/YYYY");
    var endDate = moment().format("DD/MM/YYYY");

    data = sessionStorage.getItem('info') ? JSON.parse(sessionStorage.getItem('info')).calendar : `${startDate}-${endDate}`;

    $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
    $('input[name="daterange"]').dateRangePicker({
        setValue: function (s) {
            if (s) {
                let normalize = s.replace(/(\d{2}\/\d{2}\/)(\d{2}) à (\d{2}\/\d{2}\/)(\d{2})/, "$120$2-$320$4");
                $(this).html(s).data('value', normalize);
                $('input[name="daterange"]').attr('value', normalize);
                $('input[name="daterange"]').val(normalize);
            } else {
                $('input[name="daterange"]').attr('value', `${startDate}-${endDate}`);
                $('input[name="daterange"]').val(`${startDate}-${endDate}`);
            }
        }
    })
    .on('datepicker-change', function () {
        $.ajaxQ.abortAll();

        if (data !== $(this).val()) {
            data = $(this).val();

            updateStorage({calendar: $(this).val()});
            updateReports();
        }
    })
    .on('datepicker-open', function () {
        $('.filter-badge-input').removeClass('show');
    })
    .on('datepicker-close', function () {
        $(this).removeClass('focused');
        if ($(this).data('value')) {
            $(this).addClass('active');
        }
    });
}

function updateReports() {
    $('.sirius-select-container').addClass('disabled');
    $('input[name="daterange"]').attr('disabled', 'disabled');
    $('#brazil-map-filter').addClass('disabled');
    $('#brazil-map-filter').find('input').attr('disabled', 'disabled');

    Promise.all([
        loadResume(),
        loadCoupons(),
        loadDevices(),
        loadOperationalSystems(),
        loadFrequenteSales(),
        loadBrazilMap(),
        loadOrigins(),
    ])
    .then(() => {
        $('.sirius-select-container').removeClass('disabled');
        $('input[name="daterange"]').removeAttr('disabled');
        $('#brazil-map-filter').removeClass('disabled');
        $('#brazil-map-filter').find('input').removeAttr('disabled');
    })
    .catch(() => {
        $('.sirius-select-container').removeClass('disabled');
        $('input[name="daterange"]').removeAttr('disabled');
        $('#brazil-map-filter').removeClass('disabled');
        $('#brazil-map-filter').find('input').removeAttr('disabled');
    });
}

function kFormatter(num) {
    return Math.abs(num) > 999 ? Math.sign(num)*((Math.abs(num)/1000).toFixed(1)) + 'k' : Math.sign(num)*Math.abs(num);
}

function nFormatter(num, digits) {
    const lookup = [
      { value: 1, symbol: "" },
      { value: 1e3, symbol: "k" },
      { value: 1e6, symbol: "M" },
      { value: 1e9, symbol: "G" },
      { value: 1e12, symbol: "T" },
      { value: 1e15, symbol: "P" },
      { value: 1e18, symbol: "E" }
    ];
    const rx = /\.0+$|(\.[0-9]*[1-9])0+$/;
    var item = lookup.slice().reverse().find(function(item) {
      return num >= item.value;
    });
    return item ? (num / item.value).toFixed(digits).replace(rx, "$1") + item.symbol : "0";
  }

$('.state').on('click', function(e){
    e.preventDefault();

    if(!$($('#' + $(this).attr('id') + '-position')).length) {
        return;
    }

    $('a').removeClass('state-choose');
    $(this).addClass('state-choose');
    $('#list-states').hide();
    $('#inside-state').show();
    $('.name-state').text($(this).attr('rel'));
    $('#state-position').text($('#' + $(this).attr('id') + '-position').text());

    $('#state-total-value').html("");
    $('#state-sales-amount').html("");
    $('#state-accesses').html("");
    $('#state-conversion').html("");

    $('#state-total-value').html(skeLoadStateMetric);
    $('#state-sales-amount').html(skeLoadStateMetric);
    $('#state-accesses').html(skeLoadStateMetric);
    $('#state-conversion').html(skeLoadStateMetric);


    $.ajax({
        method: "GET",
        url: mktUrl + "/state-details?state=" + $(this).children('text').text() + "&project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            errorAjaxResponse(response);
        },
        success: function success(response) {
            $('#state-total-value').html(response.data.total_value);
            $('#state-sales-amount').html(response.data.total_sales);
            $('#state-accesses').html(response.data.accesses);
            $('#state-conversion').html(response.data.conversion);
        }
    });
});

$('.back-list').on('click', function(e){
    e.preventDefault();
    $('#list-states').show();
    $('#inside-state').hide();
    $('a').removeClass('state-choose');
});

function loadBrazilMap() {
    $('#list-states').html('');
    $("#list-states").prepend(skeLoadStatesList);
    $('.state path').css({ fill: '#F1F1F1', stroke: '#F1F1F1' });
    $('.state text').css({ fill: '#F1F1F1' });
    $(".state").addClass('skeleton');

    $('[data-toggle="tooltip"]').tooltip({container: '#parent-more'});

    let noData = `
        <div class="d-flex justify-content-center align-items-center px-5" style="margin: auto;">
            <div>
                <img src=${$("#origins-table").attr("img-empty")}>
            </div>
            <div class="px-10">
                <p class='no-data-origin'>
                    <strong>Sem dados, por enquanto...</strong>
                    Ainda faltam dados suficientes a comparação, continue rodando!
                </p>
            </div>
        </div>
    `;

    $.ajax({
        method: "GET",
        url: mktUrl + "/sales-by-state?project_id=" + $("#select_projects option:selected").val() + "&date_range=" + $("input[name='daterange']").val() + "&map_filter="+ $("input[name='brazil_map_filter']:checked").val(),
        dataType: "json",
        headers: {
            Authorization: $('meta[name="access-token"]').attr("content"),
            Accept: "application/json",
        },
        error: function error(response) {
            $('.sirius-select-container').removeClass('disabled');
            $('input[name="daterange"]').removeAttr('disabled', 'disabled');
            $('#brazil-map-filter').removeClass('disabled');
            $('#brazil-map-filter').find('input').removeAttr('disabled', 'disabled');

            $("#list-states").html(noData);

            errorAjaxResponse(response);
        },
        success: function success(response) {
            $(".state").removeClass('skeleton');
            $("#list-states").html('');

            $('.sirius-select-container').removeClass('disabled');
            $('input[name="daterange"]').removeAttr('disabled', 'disabled');
            $('#brazil-map-filter').removeClass('disabled');
            $('#brazil-map-filter').find('input').removeAttr('disabled', 'disabled');

            if(response.data.length == 0) {
                let noData = `
                <div class="d-flex justify-content-center align-items-center px-5" style="margin: auto;">
                    <div>
                        ${noWithdrawal}
                    </div>
                    <div class="px-10">
                        <p class='no-data-origin'>
                            <strong>Sem dados, por enquanto...</strong>
                            Ainda faltam dados suficientes a comparação, continue rodando!
                        </p>
                    </div>
                </div>
                    `;

                $("#list-states").append(noData);
                return;
            }

            $('.state path').css({ fill: '#FFFFFF' });
            $('.state text').css({ fill: '#6C757D' });

            let maxValue = null;
            $.each(response.data, function(i, data){
                if(maxValue == null) {
                    if($("input[name='brazil_map_filter']:checked").val() == 'density') {
                        maxValue = data.percentage;
                    } else {
                        maxValue = onlyNumbers(data.value);
                    }
                }

                if($("input[name='brazil_map_filter']:checked").val() == 'density') {
                    setCustomMapCss('#state-' + data.state, maxValue, data.percentage);
                } else {
                    setCustomMapCss('#state-' + data.state, maxValue, onlyNumbers(data.value));
                }

                appendStateDataToStateList(data, i + 1);
            });
        }
    });
}

function setCustomMapCss(selector, maxValue, value) {

    if(maxValue == value) {
        $(selector + ' path').css({ fill: '#15034C' });
        $(selector + ' text').css({ fill: '#FFFFFF' });
        return;
    }

    let percentage = (100 * value) / maxValue;

    if(percentage > 0 && percentage <= 12){
        $(selector + ' path').css({ fill: '#F2F8FF' });
        $(selector + ' text').css({ fill: '#3089F2' });
        return;
    }
    else if(percentage > 12 && percentage <= 25) {
        $(selector + ' path').css({ fill: '#BFDCFF' });
        $(selector + ' text').css({ fill: '#3089F2' });
        return;
    }
    else if(percentage > 25 && percentage <= 37) {
        $(selector + ' path').css({ fill: '#A6CFFF' });
        $(selector + ' text').css({ fill: '##1F5DA7' });
        return;
    }
    else if(percentage > 37 && percentage <= 50) {
        $(selector + ' path').css({ fill: '#73B2FF' });
        $(selector + ' text').css({ fill: '#FFFFFF' });
        return;
    }
    else if(percentage > 50 && percentage <= 62) {
        $(selector + ' path').css({ fill: '#59A5FF' });
        $(selector + ' text').css({ fill: '#FFFFFF' });
        return;
    }
    else if(percentage > 62 && percentage <= 75) {
        $(selector + ' path').css({ fill: '#3089F2' });
        $(selector + ' text').css({ fill: '#FFFFFF' });
        return;
    }
    else if(percentage > 75 && percentage <= 99) {
        $(selector + ' path').css({ fill: '#1F5DA7' });
        $(selector + ' text').css({ fill: '#FFFFFF' });
        return;
    }

    $(selector + ' text').css({ fill: '#6C757D' });
}

function appendStateDataToStateList(data, index) {
    const statesName = {
        "AC": "Acre",
        "AL": "Alagoas",
        "AP": "Amapá",
        "AM": "Amazonas",
        "BA": "Bahia",
        "CE": "Ceará",
        "DF": "Distrito Federal",
        "ES": "Espírito Santo",
        "GO": "Goiás",
        "MA": "Maranhão",
        "MT": "Mato Grosso",
        "MS": "Mato Grosso do Sul",
        "MG": "Minas Gerais",
        "PA": "Pará",
        "PB": "Paraíba",
        "PR": "Paraná",
        "PE": "Pernambuco",
        "PI": "Piauí",
        "RR": "Roraima",
        "RO": "Rondônia",
        "RJ": "Rio de Janeiro",
        "SP": "São Paulo",
        "SC": "Santa Catarina",
        "RN": "Rio Grande do Norte",
        "RS": "Rio Grande do Sul",
        "SE": "Sergipe",
        "TO": "Tocantins"
    }

    let stateData = `
            <li class="states-list">
                <div class="d-flex container">
                    <ul style="list-style: none;">
                        <li class="item-state">
                            <dl class="d-flex">
                                <dd id="state-${data.state}-position">${index}°</dd>
                                <dd class="dd-state">${data.state}</dd>
                                <dd class="state-name">${statesName[data.state]}</dd>
                            </dl>
                        </li>
                        <li class="item-state">
                            <dl class="d-flex justify-content-between">
                                <dd><span>${data.percentage}</span></dd>
                                <dd><strong>${data.value}</strong></dd>
                            </dl>
                        </li>
                    </ul>
                </div>
            </li>
        `;

    $("#list-states").append(stateData);
}

const formatCash = n => {
    if (n < 1e3) return n;
    if (n >= 1e3 && n < 1e6) return +(n / 1e3).toFixed(1) + "K";
    if (n >= 1e6 && n < 1e9) return +(n / 1e6).toFixed(1) + "M";
    if (n >= 1e9 && n < 1e12) return +(n / 1e9).toFixed(1) + "B";
    if (n >= 1e12) return +(n / 1e12).toFixed(1) + "T";
};

let skeLoad = `
    <div class="ske-load">
        <div class="px-20 py-0">
            <div class="skeleton skeleton-gateway-logo" style="height: 30px"></div>
        </div>
        <div class="px-20 py-0">
            <div class="row align-items-center mx-0 py-10">
                <div class="skeleton skeleton-circle"></div>
                <div class="skeleton skeleton-text mb-0" style="height: 15px; width:50%"></div>
            </div>
            <div class="skeleton skeleton-text ske"></div>
        </div>
    </div>
`;

let skeLoadStatesList = `
    <div class="ske-load">
        <div class="p-10">
            <div class="d-flex" style="width: 100%; margin-bottom: 5px;">
                <div class="skeleton skeleton-li-item"></div>
                <div class="skeleton skeleton-li"></div>
            </div>
            <div class="d-flex" style="width: 100%; margin-bottom: 5px;">
            <div class="skeleton skeleton-li-item"></div>
            <div class="skeleton skeleton-li"></div>
            </div>
            <div class="d-flex" style="width: 100%; margin-bottom: 5px;">
                <div class="skeleton skeleton-li-item"></div>
                <div class="skeleton skeleton-li"></div>
            </div>
            <div class="d-flex" style="width: 100%; margin-bottom: 5px;">
                <div class="skeleton skeleton-li-item"></div>
                <div class="skeleton skeleton-li"></div>
            </div>
        </div>
    </div>
`;

let skeLoadOriginTable = `
    <div class="skeleton skeleton-li" style="width: 100%; margin-bottom: 17px;"></div>
    <div class="skeleton skeleton-li" style="width: 100%; margin-bottom: 17px;"></div>
    <div class="skeleton skeleton-li" style="width: 100%; margin-bottom: 17px;"></div>
    <div class="skeleton skeleton-li" style="width: 100%; margin-bottom: 17px;"></div>
`;

let skeLoadStateMetric = `
    <div class="skeleton skeleton-li" style="width: 100%; height: 20px; margin-bottom: 0px;"></div>
`;

let emptyCoupons = `
<svg width="132" height="126" viewBox="0 0 132 126" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M123.632 78.3097C120.484 90.0588 113.922 100.611 104.777 108.63C95.6322 116.65 84.3144 121.779 72.2549 123.366C60.1954 124.954 47.9359 122.93 37.0268 117.55C26.1176 112.17 17.0487 103.676 10.9669 93.1423L32.2712 80.8423C35.9202 87.1627 41.3616 92.2591 47.9071 95.4869C54.4526 98.7148 61.8082 99.9293 69.0439 98.9767C76.2796 98.0241 83.0703 94.9472 88.5574 90.1352C94.0444 85.3232 97.9813 78.9922 99.8702 71.9428L123.632 78.3097Z" fill="#F4F6FB" fill-opacity="0.8"/>
<path d="M64.2275 0.892341C77.7658 0.892339 90.9257 5.35954 101.666 13.6011C112.407 21.8427 120.128 33.398 123.632 46.475L99.8702 52.8419C97.7678 44.9958 93.1352 38.0625 86.6908 33.1176C80.2464 28.1727 72.3505 25.4923 64.2275 25.4923L64.2275 0.892341Z" fill="#F4F6FB" fill-opacity="0.25"/>
<path d="M123.632 46.475C126.426 56.9026 126.426 67.8821 123.632 78.3097L99.8702 71.9428C101.547 65.6862 101.547 59.0985 99.8702 52.8419L123.632 46.475Z" fill="#E8EAEB"/>
<path d="M10.9669 93.1423C5.56919 83.7932 2.72751 73.1878 2.72751 62.3923C2.72751 51.5968 5.5692 40.9915 10.9669 31.6423C16.3647 22.2932 24.1283 14.5295 33.4775 9.13177C42.8267 3.73402 53.432 0.892339 64.2275 0.892341L64.2275 25.4923C57.7502 25.4923 51.387 27.1974 45.7775 30.436C40.168 33.6747 35.5098 38.3328 32.2712 43.9423C29.0325 49.5518 27.3275 55.915 27.3275 62.3923C27.3275 68.8696 29.0325 75.2328 32.2712 80.8423L10.9669 93.1423Z" fill="#F4F6FB" fill-opacity="0.6"/>
</svg>
`;

let emptyData = `
<svg width="275" height="122" viewBox="0 0 275 122" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="48" y="94" width="187" height="25" rx="4" fill="url(#paint0_linear_2696_543)"/>
<rect x="48" y="5" width="227" height="25" rx="4" fill="url(#paint1_linear_2696_543)"/>
<rect x="48" y="50" width="227" height="24" rx="4" fill="url(#paint2_linear_2696_543)"/>
<path opacity="0.6" d="M29 89C31.2091 89 33 90.7909 33 93L33 117C33 119.209 31.2091 121 29 121L5 121C2.79086 121 0.999999 119.209 0.999999 117L1 93C1 90.7909 2.79086 89 5 89L29 89Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 1C31.2091 1 33 2.79086 33 5L33 29C33 31.2091 31.2091 33 29 33L5 33C2.79086 33 0.999999 31.2091 0.999999 29L1 5C1 2.79086 2.79086 0.999999 5 0.999999L29 1Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 45C31.2091 45 33 46.7909 33 49L33 73C33 75.2091 31.2091 77 29 77L5 77C2.79086 77 0.999999 75.2091 0.999999 73L1 49C1 46.7909 2.79086 45 5 45L29 45Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<defs>
<linearGradient id="paint0_linear_2696_543" x1="141.5" y1="94" x2="235" y2="94" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint1_linear_2696_543" x1="161.5" y1="5" x2="275" y2="4.99999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint2_linear_2696_543" x1="75.2025" y1="74" x2="250.612" y2="73.9999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;

// abort all ajax
$.ajaxQ = (function(){
    var id = 0, Q = {};

    $(document).ajaxSend(function(e, jqx){
      jqx._id = ++id;
      Q[jqx._id] = jqx;
    });
    $(document).ajaxComplete(function(e, jqx){
      delete Q[jqx._id];
    });

    return {
      abortAll: function(){
        var r = [];
        $.each(Q, function(i, jqx){
          r.push(jqx._id);
          jqx.abort();
        });
        return r;
      }
    };

  })();
  function removeDuplcateItem(item) {
    for (i = 0; i < $(item).length; i++) {
        text = $(item).get(i);
        for (j = i + 1; j < $(item).length; j++) {
          text_to_compare = $(item).get(j);
          if (text.innerHTML == text_to_compare.innerHTML) {
            $(text_to_compare).remove();
            j--;
            maxlength = $(item).length;
          }
        }
    }
}

let noWithdrawal = `
<svg width="111" height="138" viewBox="0 0 111 138" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M55.5 132C86.1518 132 111 107.152 111 76.5C111 45.8482 86.1518 21 55.5 21C24.8482 21 0 45.8482 0 76.5C0 107.152 24.8482 132 55.5 132Z" fill="#F4F6FB"/>
<path d="M87.32 52.8199H23.68C21.6365 52.8199 19.98 54.4765 19.98 56.5199V134.22C19.98 136.263 21.6365 137.92 23.68 137.92H87.32C89.3634 137.92 91.02 136.263 91.02 134.22V56.5199C91.02 54.4765 89.3634 52.8199 87.32 52.8199Z" fill="white"/>
<path d="M48.0999 63.9199H28.8599C27.6338 63.9199 26.6399 64.9138 26.6399 66.1399C26.6399 67.3659 27.6338 68.3599 28.8599 68.3599H48.0999C49.326 68.3599 50.3199 67.3659 50.3199 66.1399C50.3199 64.9138 49.326 63.9199 48.0999 63.9199Z" fill="#ededed"/>
<path d="M61.4199 73.5397H28.8599C27.6338 73.5397 26.6399 74.5336 26.6399 75.7597C26.6399 76.9857 27.6338 77.9797 28.8599 77.9797H61.4199C62.646 77.9797 63.6399 76.9857 63.6399 75.7597C63.6399 74.5336 62.646 73.5397 61.4199 73.5397Z" fill="#f4f4f4"/>
<path d="M48.0999 83.8999H28.8599C27.6338 83.8999 26.6399 84.8938 26.6399 86.1199C26.6399 87.346 27.6338 88.3399 28.8599 88.3399H48.0999C49.326 88.3399 50.3199 87.346 50.3199 86.1199C50.3199 84.8938 49.326 83.8999 48.0999 83.8999Z" fill="#ededed"/>
<path d="M61.4199 93.5199H28.8599C27.6338 93.5199 26.6399 94.5138 26.6399 95.7399C26.6399 96.966 27.6338 97.9599 28.8599 97.9599H61.4199C62.646 97.9599 63.6399 96.966 63.6399 95.7399C63.6399 94.5138 62.646 93.5199 61.4199 93.5199Z" fill="#f4f4f4"/>
<path d="M48.0999 103.88H28.8599C27.6338 103.88 26.6399 104.874 26.6399 106.1C26.6399 107.326 27.6338 108.32 28.8599 108.32H48.0999C49.326 108.32 50.3199 107.326 50.3199 106.1C50.3199 104.874 49.326 103.88 48.0999 103.88Z" fill="#ededed"/>
<path d="M61.4199 113.5H28.8599C27.6338 113.5 26.6399 114.494 26.6399 115.72C26.6399 116.946 27.6338 117.94 28.8599 117.94H61.4199C62.646 117.94 63.6399 116.946 63.6399 115.72C63.6399 114.494 62.646 113.5 61.4199 113.5Z" fill="#f4f4f4"/>
<g filter="">
<path d="M87.32 15.08H23.68C21.6365 15.08 19.98 16.7365 19.98 18.78V40.98C19.98 43.0235 21.6365 44.68 23.68 44.68H87.32C89.3634 44.68 91.02 43.0235 91.02 40.98V18.78C91.02 16.7365 89.3634 15.08 87.32 15.08Z" fill="#E8EAEB"/>
</g>
<path d="M48.0999 23.2201H28.8599C27.6338 23.2201 26.6399 24.214 26.6399 25.4401C26.6399 26.6661 27.6338 27.6601 28.8599 27.6601H48.0999C49.326 27.6601 50.3199 26.6661 50.3199 25.4401C50.3199 24.214 49.326 23.2201 48.0999 23.2201Z" fill="#ededed"/>
<path d="M61.4199 32.8401H28.8599C27.6338 32.8401 26.6399 33.834 26.6399 35.0601C26.6399 36.2862 27.6338 37.2801 28.8599 37.2801H61.4199C62.646 37.2801 63.6399 36.2862 63.6399 35.0601C63.6399 33.834 62.646 32.8401 61.4199 32.8401Z" fill="white"/>
<defs>
<filter id="filter0_d_1640_468" x="1.78146" y="0.521187" width="107.437" height="65.997" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
<feFlood flood-opacity="0" result="BackgroundImageFix"/>
<feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
<feOffset dy="3.6397"/>
<feGaussianBlur stdDeviation="9.09926"/>
<feComposite in2="hardAlpha" operator="out"/>
<feColorMatrix type="matrix" values="0 0 0 0 0.180392 0 0 0 0 0.521569 0 0 0 0 0.92549 0 0 0 0.17 0"/>
<feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1640_468"/>
<feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1640_468" result="shape"/>
</filter>
</defs>
</svg>
`;

let noListProducts = `
<svg width="200" height="90" viewBox="0 0 275 122" fill="none" xmlns="http://www.w3.org/2000/svg">
<rect x="48" y="94" width="187" height="25" rx="4" fill="url(#paint0_linear_2696_543)"/>
<rect x="48" y="5" width="227" height="25" rx="4" fill="url(#paint1_linear_2696_543)"/>
<rect x="48" y="50" width="227" height="24" rx="4" fill="url(#paint2_linear_2696_543)"/>
<path opacity="0.6" d="M29 89C31.2091 89 33 90.7909 33 93L33 117C33 119.209 31.2091 121 29 121L5 121C2.79086 121 0.999999 119.209 0.999999 117L1 93C1 90.7909 2.79086 89 5 89L29 89Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 1C31.2091 1 33 2.79086 33 5L33 29C33 31.2091 31.2091 33 29 33L5 33C2.79086 33 0.999999 31.2091 0.999999 29L1 5C1 2.79086 2.79086 0.999999 5 0.999999L29 1Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<path opacity="0.6" d="M29 45C31.2091 45 33 46.7909 33 49L33 73C33 75.2091 31.2091 77 29 77L5 77C2.79086 77 0.999999 75.2091 0.999999 73L1 49C1 46.7909 2.79086 45 5 45L29 45Z" stroke="#CCCCCC" stroke-dasharray="2 2"/>
<defs>
<linearGradient id="paint0_linear_2696_543" x1="141.5" y1="94" x2="235" y2="94" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint1_linear_2696_543" x1="161.5" y1="5" x2="275" y2="4.99999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
<linearGradient id="paint2_linear_2696_543" x1="75.2025" y1="74" x2="250.612" y2="73.9999" gradientUnits="userSpaceOnUse">
<stop stop-color="#F4F6FB"/>
<stop offset="1" stop-color="#F4F6FB" stop-opacity="0"/>
</linearGradient>
</defs>
</svg>
`;
